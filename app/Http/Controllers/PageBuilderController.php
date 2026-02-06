<?php
namespace App\Http\Controllers;

use App\Models\Page;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PageBuilderController extends Controller
{
    // Show create page form
    public function create()
    {
        return view('builder.create');
    }

    // Store new page
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'slug'  => 'nullable|string|max:255',
        ]);

        $slug = $request->slug ? Str::slug($request->slug) : Str::slug($request->title);

        // Ensure slug is unique
        $originalSlug = $slug;
        $count        = 1;
        while (Page::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $count;
            $count++;
        }

        $page = Page::create([
            'title'    => $request->title,
            'slug'     => $slug,
            'html'     => $request->html ?? '',
            'css'      => $request->css ?? '',
            'js'       => $request->js ?? '',
            'gjs_json' => $request->gjs_json ?? '',
        ]);

        return redirect()->route('builder.edit', $page->id)
            ->with('success', 'Page created successfully');
    }

    // Show page editor
    public function edit($id)
    {
        $page = Page::findOrFail($id);
        $pages = Page::all();
        return view('builder.edit', compact('page', 'pages'));
    }

    // Update page content
    public function update(Request $request, $id)
    {
        $request->validate([
            'html' => 'nullable|string',
            'css' => 'nullable|string',
            'js' => 'nullable|string',
            'gjs_json' => 'nullable|string',
        ]);

        $page = Page::findOrFail($id);

        $page->html     = $request->html;
        $page->css      = $request->css;
        $page->js       = $request->js;
        $page->gjs_json = $request->gjs_json;
        $page->save();

        if ($request->ajax()) {
            return response()->json(['message' => 'Page saved successfully!']);
        }

        return redirect()->back()->with('success', 'Page saved successfully!');
    }

    // List all pages
    public function index()
    {
        $pages = Page::all(); // Fetch all pages
        return view('builder.index', compact('pages'));
    }

    // Delete a page
    public function destroy($id)
    {
        $page = Page::findOrFail($id);
        $page->delete();

        return redirect()->route('pages.index')->with('success', 'Page deleted successfully!');
    }

    // View page by slug
    public function view($slug)
    {
        $page = Page::where('slug', $slug)->firstOrFail();
        $page->html = $this->fixInternalLinks($page->html);
        return view('builder.view', compact('page'));
    }

    // View page by ID
    public function viewById($id)
    {
        $page = Page::findOrFail($id);
        $page->html = $this->fixInternalLinks($page->html);
        return view('builder.view', compact('page'));
    }

    // API: Get all pages for dropdowns
    public function getPages()
    {
        $pages = Page::select('id', 'slug', 'title')->get();
        return response()->json($pages);
    }

    /**
     * Replaces any /builder/ID/edit links with /page/SLUG links dynamically
     * and ensures the sidebar/navbar navigation is synced with all current pages.
     */
    private function fixInternalLinks($html)
    {
        if (empty($html)) return $html;

        $pages = Page::all();
        $doc = new \DOMDocument();
        // Use mb_convert_encoding to handle UTF-8 correctly
        @$doc->loadHTML('<?xml encoding="utf-8" ?>' . $html, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);

        // 1. Fix hardcoded editor links in all anchors
        $anchors = $doc->getElementsByTagName('a');
        foreach ($anchors as $a) {
            $href = $a->getAttribute('href');
            
            // Rewrite /builder/ID/edit -> /page/SLUG
            if (preg_match('/\/builder\/(\d+)\/edit/', $href, $matches)) {
                $pageId = $matches[1];
                $linkedPage = $pages->firstWhere('id', $pageId);
                if ($linkedPage) {
                    $a->setAttribute('href', url('/page/' . $linkedPage->slug));
                }
            }

            // 2. Auto-link Navbar/Sidebar items that don't have a URL yet
            // If the text matches a page title and href is # or empty
            $text = trim($a->nodeValue);
            if (($href === '#' || empty($href)) && !empty($text)) {
                $linkedPage = $pages->first(function($p) use ($text) {
                    return strtolower($p->title) === strtolower($text);
                });
                if ($linkedPage) {
                    $a->setAttribute('href', url('/page/' . $linkedPage->slug));
                }
            }
        }

        // 3. Sync Sidebars & Navbars
        $xpath = new \DOMXPath($doc);
        
        // --- SYNC SIDEBARS ---
        $sidebars = $xpath->query("//aside[contains(@class, 'admin-sidebar')] | //div[contains(@class, 'admin-sidebar')]");
        foreach ($sidebars as $sidebar) {
            $ul = $xpath->query(".//ul[contains(@class, 'nav')]", $sidebar)->item(0);
            if ($ul) {
                while ($ul->hasChildNodes()) { $ul->removeChild($ul->firstChild); }
                foreach ($pages as $p) {
                    $li = $doc->createElement('li');
                    $li->setAttribute('class', 'nav-item');
                    $a = $doc->createElement('a');
                    $a->setAttribute('class', 'nav-link text-white ' . (request()->is('page/' . $p->slug) ? 'active' : ''));
                    $a->setAttribute('href', url('/page/' . $p->slug));
                    $a->nodeValue = $p->title;
                    $li->appendChild($a);
                    $ul->appendChild($li);
                }
            }
        }

        // --- SYNC NAVBARS ---
        $navbars = $xpath->query("//nav//ul | //ul[contains(@class, 'navbar-nav')]");
        foreach ($navbars as $nav) {
            // ONLY clear items that are purely navigation links, preserve things like Icons or Search Groups
            $children = iterator_to_array($nav->childNodes);
            foreach ($children as $child) {
                // If it's a standard nav-item link without an icon, we can replace it
                // But if it contains an icon group or a specific trigger class, we skip it
                if ($child instanceof \DOMElement && !str_contains($child->getAttribute('class'), 'action-wrapper') && !$xpath->query(".//i", $child)->length) {
                    $nav->removeChild($child);
                }
            }

            // Inject fresh list of all pages (only if they don't already exist to avoid duplicates)
            foreach ($pages as $p) {
                $li = $doc->createElement('li');
                $li->setAttribute('class', 'nav-item dynamic-page-link');
                $a = $doc->createElement('a');
                $shortTitle = str_replace([' Page', ' Us', ' - '], '', $p->title);
                $a->setAttribute('class', 'nav-link ' . (request()->is('page/' . $p->slug) ? 'active' : ''));
                $a->setAttribute('href', url('/page/' . $p->slug));
                $a->nodeValue = $shortTitle;
                $li->appendChild($a);
                
                // Append it to the start of the navbar so icons stay on the right
                if ($nav->firstChild) {
                    $nav->insertBefore($li, $nav->firstChild);
                } else {
                    $nav->appendChild($li);
                }
            }
        }

        $result = $doc->saveHTML();
        // Remove the xml encoding tag we added
        $result = str_replace('<?xml encoding="utf-8" ?>', '', $result);
        
        // Final cleanup for any leftover blade strings that GrapesJS might have saved
        $result = preg_replace('/@foreach.*?@endforeach/s', '', $result);
        $result = str_replace(['@foreach', '@endforeach', '@csrf', '{{', '}}'], '', $result);

        return $result;
    }
}