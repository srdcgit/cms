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
        return view('builder.edit', compact('page'));
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
        return view('builder.view', compact('page'));
    }

    // View page by ID
    public function viewById($id)
    {
        $page = Page::findOrFail($id);
        return view('builder.view', compact('page'));
    }
}
