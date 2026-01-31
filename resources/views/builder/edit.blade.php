<!doctype html>
<html>

<head>
    <title>Editor - {{ $page->title }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">


    <!-- GrapesJS Core CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/grapesjs/dist/css/grapes.min.css">

    <!-- Plugins CSS -->
    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/grapesjs-preset-webpage/dist/grapesjs-preset-webpage.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/grapesjs-blocks-basic/dist/grapesjs-blocks-basic.min.css">



    <style>
        /* ====== GENERAL STYLES ====== */
        body {
            margin: 0;
        }

        /* GrapesJS Canvas */
        #gjs {
            height: calc(100vh - 50px) !important;
            margin-top: 50px;
            /* Push down so top bar doesn't overlap */
        }

        .editable-text {
            cursor: text !important;
        }

        /* Top Bar Container */
        .editor-topbar {
            position: fixed;
            top: 1px;
            left: 10px;
            right: 10px;
            z-index: 9999;
            background: #ffffff;
            border-radius: 5px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.25rem 1rem;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            gap: 5px;
        }

        /* Back Button */
        .editor-topbar .btn-back {
            display: flex;
            align-items: center;
            gap: 5px;
            font-weight: 500;
            color: #495057;
        }

        /* Save Button */
        .editor-topbar .btn-save {
            display: flex;
            align-items: center;
            gap: 4px;
            background: linear-gradient(90deg, #4e9af1, #1a73e8);
            border: none;
            color: #fff;
            font-weight: 500;
            padding: 0.5rem 1.2rem;
            border-radius: 5px;
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .editor-topbar .btn-save:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        img {
            max-width: 100%;
            height: auto;
        }

        .container,
        .container-fluid {
            max-width: 100%;
        }

        .row {
            margin-left: 0;
            margin-right: 0;
        }

        /* ====== SIDEBAR STYLES ====== */
        .admin-sidebar {
            width: 250px;
            min-height: 100vh;
            background-color: #1e1e2f;
            color: #fff;
            padding: 20px;
            box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
        }

        .admin-sidebar h5 {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 20px;
        }

        .admin-sidebar ul {
            list-style: none;
            padding-left: 0;
        }

        .admin-sidebar .nav-link {
            display: block;
            padding: 10px 15px;
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
            transition: all 0.2s ease-in-out;
            font-weight: 500;
            font-size: 14px;
        }

        .admin-sidebar .nav-link:hover {
            background-color: #007bff;
            color: #fff;
            transform: translateX(5px);
        }

        .admin-sidebar .nav-link.active {
            background-color: #0056b3;
            font-weight: 600;
        }

        .admin-sidebar .nav-link i {
            margin-right: 8px;
        }

        /* ====== MOBILE RESPONSIVE ====== */
        @media (max-width: 576px) {
            .row>[class*="col-"] {
                width: 100% !important;
                max-width: 100% !important;
                flex: 0 0 100% !important;
            }

            h1,
            h2,
            h3,
            h4,
            h5,
            h6,
            p {
                text-align: center;
            }

            .btn {
                width: 100%;
            }

            .navbar-nav {
                text-align: center;
            }

            .admin-layout {
                flex-direction: column !important;
            }

            .admin-sidebar {
                width: 100% !important;
                min-height: auto !important;
            }

            .admin-content {
                width: 100% !important;
            }

            img {
                margin-bottom: 1rem;
            }

            section {
                padding-top: 2rem !important;
                padding-bottom: 2rem !important;
            }

            .accordion-button {
                transition: transform 0.2s ease;
            }

            .accordion-button::after {
                content: '\f107';
                display: inline-block;
                transition: transform 0.2s ease;
            }

            .accordion-button.collapsed::after {
                transform: rotate(0deg);
            }

            .accordion-button:not(.collapsed)::after {
                transform: rotate(180deg);
            }
        }
    </style>


    <!-- GrapesJS -->
    <script src="https://cdn.jsdelivr.net/npm/grapesjs/dist/grapes.js"></script>

    <!-- Plugins JS -->
    <script src="https://cdn.jsdelivr.net/npm/grapesjs-preset-webpage/dist/grapesjs-preset-webpage.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/grapesjs-blocks-basic/dist/grapesjs-blocks-basic.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>


</head>

<body>
    @if (session('success'))
        <div id="saveMessage">
            {{ session('success') }}
        </div>

        <style>
            #saveMessage {
                position: fixed;
                top: 80px;
                /* below top bar */
                right: 20px;
                background: #28a745;
                /* green success color */
                color: #fff;
                padding: 12px 20px;
                border-radius: 6px;
                z-index: 10000;
                /* above editor and topbar */
                box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
                font-weight: 500;
                opacity: 0;
                transform: translateY(-20px);
                transition: all 0.4s ease-in-out;
            }

            #saveMessage.show {
                opacity: 0.95;
                transform: translateY(0);
            }
        </style>

        <script>
            const msg = document.getElementById('saveMessage');
            if (msg) {
                // Add class to animate in
                setTimeout(() => msg.classList.add('show'), 50);

                // Remove after 3 seconds
                setTimeout(() => {
                    msg.classList.remove('show');
                    // Remove from DOM after animation
                    setTimeout(() => msg.remove(), 400);
                }, 3000);
            }
        </script>
    @endif



    <!-- Save Form -->
    <form id="saveForm" action="{{ route('builder.update', $page->id) }}" method="POST">
        @csrf
        <input type="hidden" name="html" id="html">
        <input type="hidden" name="css" id="css">
        <input type="hidden" name="js" id="js">
        <input type="hidden" name="gjs_json" id="gjs_json">

        <!-- Back & Save buttons -->
        <div class="editor-topbar">
            <a href="{{ route('pages.index') }}" class="btn-back">
                ← Back
            </a>
            <button type="submit" class="btn-save">
                Save Page
            </button>
        </div>
    </form>


    <!-- Editor Canvas -->
    <div id="gjs">{!! $page->html !!}</div>

    <script>
        const editor = grapesjs.init({
            container: '#gjs',
            height: '100%',
            fromElement: true,
            storageManager: false,
            codeManager: {
                theme: 'hopscotch',
                readOnly: false
            },
            plugins: ['gjs-preset-webpage', 'gjs-blocks-basic'],
            pluginsOpts: {
                'gjs-preset-webpage': {
                    showDevices: true
                },
                'gjs-blocks-basic': {
                    flexGrid: true
                }
            },
            deviceManager: {
                devices: [{
                        name: 'Desktop',
                        width: '',
                    },
                    {
                        name: 'Tablet',
                        width: '1024px', // increased from 768px
                        widthMedia: '1024px',
                    },
                    {
                        name: 'Mobile',
                        width: '480px', // increased from 320px
                        widthMedia: '480px',
                    }
                ]
            },
            canvas: {
                styles: [
                    'https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css',
                    'https://cdn.jsdelivr.net/npm/grapesjs/dist/css/grapes.min.css',
                    'https://cdn.jsdelivr.net/npm/grapesjs-preset-webpage/dist/grapesjs-preset-webpage.min.css',
                    'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css',
                    'https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;800&display=swap',
                    'https://unpkg.com/aos@2.3.1/dist/aos.css',
                    `
     /* ===== ADMIN LAYOUT FIX ===== */
            .admin-layout { display: flex; min-height: 100vh; overflow: hidden; background: #f5f6fa; }
            .admin-sidebar { width: 240px; min-height: 100vh; background: #212529; color: #fff; flex-shrink: 0; }
            .admin-sidebar ul { list-style: none; padding-left: 0; }
            .admin-sidebar li { list-style: none; }
            .admin-content { flex: 1; min-height: 100vh; overflow-y: auto; background: #f5f6fa; }
            .admin-navbar { position: sticky; top: 0; z-index: 10; background: #fff; }
            .admin-layout .container { max-width: 100%; }
            `
                ]
            }
        });

        

        // IMPORTANT: Enable GrapesJS Panels for devices
        const panelManager = editor.Panels;
        panelManager.addButton('options', [{
            id: 'device-desktop',
            className: 'fa fa-desktop',
            command: 'set-device-desktop',
            attributes: {
                title: 'Desktop'
            }
        }, {
            id: 'device-tablet',
            className: 'fa fa-tablet',
            command: 'set-device-tablet',
            attributes: {
                title: 'Tablet'
            }
        }, {
            id: 'device-mobile',
            className: 'fa fa-mobile',
            command: 'set-device-mobile',
            attributes: {
                title: 'Mobile'
            }
        }]);

        // Register commands for switching devices
        editor.Commands.add('set-device-desktop', {
            run: editor => editor.setDevice('Desktop')
        });
        editor.Commands.add('set-device-tablet', {
            run: editor => editor.setDevice('Tablet')
        });
        editor.Commands.add('set-device-mobile', {
            run: editor => editor.setDevice('Mobile')
        });





        // ------------------ DEFINE applyFloatSupport ------------------
        function applyFloatSupport(block) {
            const el = block.el || block.getEl(); // Get block DOM element
            if (!el) return;

            const floatTargets = el.querySelectorAll('.float-target');
            floatTargets.forEach(target => {
                target.querySelectorAll(':scope > .float-item, :scope > *').forEach(child => {
                    // Example float logic
                    child.style.position = 'relative';
                    // Add more logic here if needed
                });
            });
        }

        // The default GrapesJS RTE works best without manual event interference.
        // We've removed the custom rte:enable listener to restore Bold, Italic, Underline, and Link functionality.



        editor.on('load', () => {

            const bm = editor.BlockManager;
            const dc = editor.DomComponents;

            editor.Canvas.getBody().addEventListener('click', e => {
                // Allow clicks inside RTE toolbar
                if (e.target.closest('.gjs-rte-actionbar')) {
                    return;
                }

                // Catch any editor link (sidebar, navbar, buttons) with data-href
                const link = e.target.closest('[data-href]');
                if (!link) return;

                // Allow when Rich Text Editor is active (link editing)
                if (editor.RichTextEditor.isActive()) return;

                // Get the URL from data-href attribute
                const href = link.getAttribute('data-href');
                if (href && href !== '#' && href !== '') {
                    // Open in new tab when clicked in editor
                    e.preventDefault();
                    e.stopPropagation();
                    window.open(href, '_blank');
                } else {
                    // Block empty or invalid links
                    e.preventDefault();
                    e.stopPropagation();
                }
            });


            // ----------- REMOVE DUPLICATE FORMS BLOCKS -----------
            const existingForms = bm.getAll().filter(block => block.attributes.category?.id === 'forms');
            existingForms.forEach(block => bm.remove(block.id));

            // ---------------- LAYOUT BLOCKS ----------------

            // Re-run scripts for all blocks (sections, forms, etc.)
            runBlockScripts(editor.getComponents().models);

            // Fix file inputs for editing
            fixFileInputs();

            // ---------------- LAYOUT BLOCKS ----------------
            const layoutBlocks = [{
                    id: 'row-1col',
                    label: '1 Column',
                    category: 'Layout',
                    content: `<div class="row"><div class="col">Column</div></div>`
                },
                {
                    id: 'row-2col',
                    label: '2 Columns',
                    category: 'Layout',
                    content: `<div class="row">
        <div class="col">Col 1</div>
        <div class="col">Col 2</div>
      </div>`
                },
                {
                    id: 'row-3col',
                    label: '3 Columns',
                    category: 'Layout',
                    content: `<div class="row">
        <div class="col">Col 1</div>
        <div class="col">Col 2</div>
        <div class="col">Col 3</div>
      </div>`
                },
                {
                    id: 'row-2-3col',
                    label: '2/3 Columns',
                    category: 'Layout',
                    content: `<div class="row">
        <div class="col-8">Col 1</div>
        <div class="col-4">Col 2</div>
      </div>`
                }
            ];

            // ---------------- BASIC CONTENT ----------------
            const contentBlocks = [{
                    id: 'text',
                    label: 'Text',
                    category: 'Content',
                    content: `<p class="p-2 editable-text">Editable text here</p>`
                },
                {
                    id: 'heading',
                    label: 'Heading',
                    category: 'Content',
                    content: `<h2 class="fw-bold editable-text">Section heading</h2>`
                },
                {
                    id: 'link',
                    label: 'Link',
                    category: 'Content',
                    content: `<a href="#" class="text-decoration-underline editable-text">Click here</a>`
                },
                {
                    id: 'quote',
                    label: 'Quote',
                    category: 'Content',
                    content: `<blockquote class="blockquote p-3 border-start border-primary">
        <p class="mb-0 editable-text">"Quote here"</p>
      </blockquote>`
                },
                {
                    id: 'sidebar-link-item',
                    label: 'Sidebar Link',
                    category: 'Layout',
                    content: `<a class="nav-link text-white" href="#" style="width: 100%; display: block;">New Link</a>`

                }

            ];

            // ---------------- NAVBAR / FOOTER ----------------
            const chromeBlocks = [{
                    id: 'navbar',
                    label: 'Navbar',
                    category: 'Layout',
                    content: `<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
          <a class="navbar-brand editable-text" href="#">Brand</a>
          <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                  data-bs-target="#mainNav">
            <span class="navbar-toggler-icon"></span>
          </button>
          <div class="collapse navbar-collapse" id="mainNav">
            <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
              <li class="nav-item"><a class="nav-link active editable-text" href="#">Home</a></li>
              <li class="nav-item"><a class="nav-link editable-text" href="#">Features</a></li>
              <li class="nav-item"><a class="nav-link editable-text" href="#">Pricing</a></li>
            </ul>
          </div>
        </div>
      </nav>`
                },
                {
                    id: 'footer-main',
                    label: 'Footer',
                    category: 'Layout',
                    content: `<footer class="py-4 bg-dark text-white">
        <div class="container text-center small editable-text">
          © 2025 Your Company. All rights reserved.
        </div>
      </footer>`
                },

            ];
            const sidebarBlocks = [{
                    id: 'admin-sidebar-layout',
                    label: 'Sidebar Layout',
                    category: 'Layout',
                    content: `
<div class="admin-layout d-flex">
  <!-- SIDEBAR -->
  <aside class="admin-sidebar bg-dark text-white p-3">
    <h5 class="mb-4">CMS Panel</h5>
    <ul class="nav flex-column gap-2">
      @foreach ($pages as $p)
      <li>
        <a class="nav-link text-white {{ request()->route('id') == $p->id ? 'active' : '' }}"
           href="{{ route('builder.edit', $p->id) }}"
           target="_top">
           {{ $p->title }}
        </a>
      </li>
      @endforeach
    </ul>
  </aside>

  <!-- DYNAMIC CONTENT AREA -->
  <div class="admin-content flex-grow-1 p-4">
    <!-- You can drag any components (tables, forms, etc.) here -->
  </div>
</div>
`
                }
            ];

            // Add this at the top of your JS file
            window.__gjsIsEditor = !!document.querySelector('.gjs-frame');


            function initSidebar(root, defaultPage = '/dashboard') {
                setTimeout(() => {
                    const links = root.querySelectorAll('.sidebar-link');
                    const content = root.querySelector('.admin-content');

                    if (!links.length || !content) return;

                    async function loadPage(url) {
                        try {
                            if (!window.__gjsIsEditor) {
                                const res = await fetch(url);
                                if (!res.ok) throw new Error('Page not found');
                                content.innerHTML = await res.text();
                                window.history.pushState({}, '', url);
                            } else {
                                // Editor-safe preview
                                content.innerHTML = `<div class="p-4 text-muted">Loaded: ${url}</div>`;
                            }

                            links.forEach(l => l.classList.remove('active'));
                            const active = [...links].find(l => l.dataset.page === url);
                            if (active) active.classList.add('active');

                        } catch (e) {
                            console.error(e);
                            window.location.href = url;
                        }
                    }

                    links.forEach(link => {
                        link.addEventListener('click', e => {
                            e.preventDefault();
                            e.stopImmediatePropagation();
                            loadPage(link.dataset.page);
                        });
                    });

                    if (defaultPage) loadPage(defaultPage);

                }, 100);
            }


            editor.BlockManager.add('admin-sidebar-layout', {
                label: 'Sidebar Layout',
                category: 'Layout',
                content: sidebarBlocks[0].content,
                script: function() {
                    initSidebar(this, '/dashboard'); // auto-load default page
                }
            });

            // ---------------- HERO / MARKETING SECTIONS (Responsive) ----------------
            const sectionBlocks = [{
                    id: 'hero-main',
                    label: 'Hero Section',
                    category: 'Sections',
                    content: `
<section class="py-5 bg-light hero-section">
  <div class="container">
   <div class="row align-items-center">

      <div class="col-12 col-md-6">

        <h1 class="fw-bold display-5 editable-text">
          The next generation<br>
          <span class="text-primary">website builder</span>
        </h1>
        <p class="mt-3 text-muted editable-text">
          Powerful and easy to use drag & drop website builder for blogs, business sites or e-commerce stores.
        </p>
        <div class="mt-4 d-flex flex-wrap gap-2">
          <a href="#" class="btn btn-primary btn-lg editable-text">Free Download</a>
          <a href="#" class="btn btn-outline-secondary btn-lg editable-text">Live Demo</a>
        </div>
        <div class="mt-4 row g-3">
          <div class="col-6 col-md-3 text-center"><div class="fw-bold editable-text">Security</div></div>
          <div class="col-6 col-md-3 text-center"><div class="fw-bold editable-text">Customization</div></div>
          <div class="col-6 col-md-3 text-center"><div class="fw-bold editable-text">E-commerce</div></div>
          <div class="col-6 col-md-3 text-center"><div class="fw-bold editable-text">Localization</div></div>
        </div>
      </div>
      <div class="col-md-6 text-center">
        <img src="https://via.placeholder.com/600x400" class="img-fluid rounded shadow" alt="Hero Image">
      </div>
    </div>
  </div>
</section>`,
                    script() {
                        applyFloatSupport(this);
                    }
                },
                {
                    id: 'features-4col',
                    label: 'Features Grid',
                    category: 'Sections',
                    content: `
<section class="py-5 bg-white">
  <div class="container">
    <div class="text-center mb-5">
      <h2 class="fw-bold editable-text">Why choose our builder?</h2>
      <p class="text-muted editable-text">Design and launch beautiful websites in minutes.</p>
    </div>
    <div class="row g-4">
      ${[1,2,3,4].map(()=>`
                                                                                                                                                                      <div class="col-6 col-lg-3">
                                                                                                                                                                        <div class="card h-100 shadow-sm border-0">
                                                                                                                                                                          <div class="card-body text-center">
                                                                                                                                                                            <h5 class="fw-bold editable-text">Feature title</h5>
                                                                                                                                                                            <p class="text-muted editable-text">Short description here.</p>
                                                                                                                                                                          </div>
                                                                                                                                                                        </div>
                                                                                                                                                                      </div>`).join('')}
    </div>
  </div>
</section>`,
                    script() {
                        applyFloatSupport(this);
                    }
                },
                {
                    id: 'cta-section',
                    label: 'Call To Action',
                    category: 'Sections',
                    content: `
<section class="py-5 bg-primary text-white text-center">
  <div class="container">
    <h2 class="fw-bold editable-text">Ready to build your next website?</h2>
    <p class="mb-4 editable-text">Start for free and upgrade anytime.</p>
    <a href="#" class="btn btn-light btn-lg editable-text">Get Started</a>
  </div>
</section>`,
                    script() {
                        applyFloatSupport(this);
                    }
                },
                {
                    id: 'testimonials',
                    label: 'Testimonials',
                    category: 'Sections',
                    content: `
<section class="py-5 bg-light">
  <div class="container">
    <div class="text-center mb-5">
      <h2 class="fw-bold editable-text">What customers say</h2>
    </div>
    <div class="row g-4">
      ${[1,2,3].map(()=>`
                                                                                                                                                                      <div class="col-12 col-lg-4">
                                                                                                                                                                        <div class="card h-100 shadow-sm border-0">
                                                                                                                                                                          <div class="card-body">
                                                                                                                                                                            <p class="text-muted editable-text">“Amazing builder and very easy to use.”</p>
                                                                                                                                                                            <div class="fw-bold editable-text">Customer Name</div>
                                                                                                                                                                            <small class="text-muted editable-text">Company</small>
                                                                                                                                                                          </div>
                                                                                                                                                                        </div>
                                                                                                                                                                      </div>`).join('')}
    </div>
  </div>
</section>`,
                    script() {
                        applyFloatSupport(this);
                    }
                },

                {
                    id: 'stats-section-pro',
                    label: 'Stats / Counters (Pro)',
                    category: 'Sections',
                    content: `
<section class="stats-section py-5" style="background: linear-gradient(135deg, #0f2027, #203a43, #2c5364);">
  <div class="container">
    
    <!-- Section Heading -->
    <div class="text-center mb-5">
      <h2 class="fw-bold text-white editable-text">Our Impact in Numbers</h2>
      <p class="text-white-50 editable-text">
        Trusted by thousands of customers worldwide
      </p>
    </div>

    <!-- Stats -->
    <div class="row g-4 text-center">
      
      <div class="col-6 col-md-3">
        <div class="stat-card p-4 rounded-4 bg-white bg-opacity-10 h-100">
          <div class="fs-1 mb-2">👥</div>
          <h2 class="fw-bold text-white editable-text" data-count="10000">10K+</h2>
          <p class="text-white-50 editable-text mb-0">Active Users</p>
        </div>
      </div>

      <div class="col-6 col-md-3">
        <div class="stat-card p-4 rounded-4 bg-white bg-opacity-10 h-100">
          <div class="fs-1 mb-2">⬇️</div>
          <h2 class="fw-bold text-white editable-text" data-count="50000">50K+</h2>
          <p class="text-white-50 editable-text mb-0">Downloads</p>
        </div>
      </div>

      <div class="col-6 col-md-3">
        <div class="stat-card p-4 rounded-4 bg-white bg-opacity-10 h-100">
          <div class="fs-1 mb-2">🚀</div>
          <h2 class="fw-bold text-white editable-text" data-count="1200">1.2K</h2>
          <p class="text-white-50 editable-text mb-0">Projects Completed</p>
        </div>
      </div>

      <div class="col-6 col-md-3">
        <div class="stat-card p-4 rounded-4 bg-white bg-opacity-10 h-100">
          <div class="fs-1 mb-2">🌍</div>
          <h2 class="fw-bold text-white editable-text" data-count="25">25+</h2>
          <p class="text-white-50 editable-text mb-0">Countries Served</p>
        </div>
      </div>

    </div>
  </div>
</section>
`
                },

                {
                    id: 'team-section-pro',
                    label: 'Team Section (Pro)',
                    category: 'Sections',
                    content: `
<section class="py-5 bg-light">
  <div class="container">

    <!-- Heading -->
    <div class="text-center mb-5">
      <h2 class="fw-bold editable-text">Meet Our Team</h2>
      <p class="text-muted editable-text">
        Professionals who make everything possible
      </p>
    </div>

    <div class="row g-4">
      ${[1,2,3,4].map(()=>`
                                                                                                          <div class="col-6 col-md-3">
                                                                                                            <div class="card border-0 shadow-sm h-100 text-center">

                                                                                                              <!-- Image wrapper for ratio -->
                                                                                                              <div class="ratio ratio-1x1">
                                                                                                                <img 
                                                                                                                  src="https://via.placeholder.com/400"
                                                                                                                  class="img-fluid object-fit-cover rounded-top"
                                                                                                                  alt="Team Member">
                                                                                                              </div>

                                                                                                              <div class="card-body">
                                                                                                                <h6 class="fw-bold mb-1 editable-text">Member Name</h6>
                                                                                                                <small class="text-muted editable-text">Role / Position</small>
                                                                                                              </div>

                                                                                                            </div>
                                                                                                          </div>`).join('')}
    </div>

  </div>
</section>
`
                }





            ];
            editor.BlockManager.add('faq-section-pro', {
                label: 'FAQ Section (Pro)',
                category: 'Sections',

                content: `
<section class="py-5" style="background:#f8fafc;">
  <div class="container">
    <div class="text-center mb-5">
      <h2 class="fw-bold editable-text">Frequently Asked Questions</h2>
      <p class="text-muted editable-text">Everything you need to know</p>
    </div>

    <div class="accordion faq-root">

      <div class="accordion-item mb-3 border rounded-3 shadow-sm">
        <button class="accordion-button"
                aria-expanded="true">
          <span class="editable-text">Question 1</span>
        </button>
        <div class="accordion-collapse show">
          <div class="accordion-body editable-text">
            Answer for question 1
          </div>
        </div>
      </div>

      <div class="accordion-item mb-3 border rounded-3 shadow-sm">
        <button class="accordion-button collapsed"
                aria-expanded="false">
          <span class="editable-text">Question 2</span>
        </button>
        <div class="accordion-collapse">
          <div class="accordion-body editable-text">
            Answer for question 2
          </div>
        </div>
      </div>

    </div>
  </div>
</section>
`,
                script() {
                    const root = this;

                    const items = root.querySelectorAll('.accordion-item');

                    items.forEach(item => {
                        const btn = item.querySelector('.accordion-button');
                        if (!btn) return;

                        // ✅ Stop GrapesJS drag/focus issues
                        ['mousedown', 'mouseup', 'focus'].forEach(evt =>
                            btn.addEventListener(evt, e => e.stopPropagation())
                        );
                    });
                }
            });
            /*************************************************
             * BANNER BLOCK
             *************************************************/
            editor.BlockManager.add('banner-section', {
                label: 'Banner Section',
                category: 'Sections',
                attributes: {
                    class: 'fa fa-image'
                }, // icon for block panel
                content: `
<section class="banner-section position-relative text-center text-white" style="background:#007bff; padding:80px 20px;">
  <div class="container">
    <h1 class="editable-text mb-3">Your Banner Title</h1>
    <p class="editable-text mb-4">Your catchy subtitle goes here.</p>
    <a href="#" class="btn btn-light editable-text">Call to Action</a>
  </div>
</section>
`,
                script: function() {
                    const root = this;

                    // Make all editable text editable in editor
                    root.querySelectorAll('.editable-text').forEach(el => {
                        el.setAttribute('data-gjs-editable', 'true');
                    });

                    // Optional: make button clickable only live
                    root.querySelectorAll('a').forEach(link => {
                        link.addEventListener('click', e => {
                            if (window.__gjsIsEditor) {
                                e.preventDefault();
                                e.stopImmediatePropagation();
                            }
                        });
                    });
                }
            });


            // ---------------- MEDIA BLOCKS ----------------
            const mediaBlocks = [{
                    id: 'image',
                    label: 'Image',
                    category: 'Media',
                    content: `<img src="https://via.placeholder.com/800x400"
                     class="img-fluid rounded shadow" alt="Image">`
                },
                {
                    id: 'video',
                    label: 'Video',
                    category: 'Media',
                    content: `
<div class="ratio ratio-16x9 video-wrapper">
  <iframe src="https://www.youtube.com/embed/dQw4w9WgXcQ"
    frameborder="0" allowfullscreen
    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture">
  </iframe>
</div>`,
                    script: function() {
                        const iframe = this.querySelector('iframe');
                        iframe && iframe.addEventListener('mousedown', e => e
                            .stopPropagation());
                    }
                },
                {
                    id: 'media-gallery',
                    label: 'Image Gallery',
                    category: 'Media',
                    content: `
<div class="row g-3">
  <div class="col-md-4"><img src="https://via.placeholder.com/400"
    class="img-fluid rounded shadow" alt=""></div>
  <div class="col-md-4"><img src="https://via.placeholder.com/400"
    class="img-fluid rounded shadow" alt=""></div>
  <div class="col-md-4"><img src="https://via.placeholder.com/400"
    class="img-fluid rounded shadow" alt=""></div>
</div>`
                }
            ];

            // ---------------- E‑COMMERCE BLOCKS ----------------
            const ecommerceBlocks = [{
                    id: 'product-card',
                    label: 'Product Card',
                    category: 'E‑commerce',
                    content: `
<div class="card border-0 shadow-soft rounded-4 hover-lift overflow-hidden m-2" style="width:18rem;">
  <div class="ratio ratio-4x3">
     <img src="https://via.placeholder.com/400x300/f8fafc/64748b?text=Product+Image" class="card-img-top object-fit-cover" alt="">
  </div>
  <div class="card-body p-4">
    <div class="text-primary small fw-semibold mb-2 text-uppercase tracking-widest">Category</div>
    <h5 class="fw-bold text-dark editable-text mb-2">Product Title</h5>
    <p class="card-text text-muted small editable-text mb-4">A brief catchphrase about the product features.</p>
    <div class="d-flex justify-content-between align-items-center">
      <span class="fs-4 fw-bold text-dark editable-text">$49.00</span>
      <button class="btn btn-dark rounded-pill px-4 btn-sm">Add to Cart</button>
    </div>
  </div>
</div>`
                },
                {
                    id: 'product-grid',
                    label: 'Product Grid',
                    category: 'E‑commerce',
                    content: `
<section class="py-5 bg-white">
  <div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h2 class="fw-bold editable-text mb-0">Featured Products</h2>
      <a href="#" class="text-decoration-none editable-text">View all</a>
    </div>
    <div class="row g-4">
      ${[1,2,3,4].map(()=>`
          <div class="col-6 col-lg-3">
            <div class="card h-100 shadow-soft border-0 rounded-4 overflow-hidden">
              <img src="https://via.placeholder.com/300x180" class="card-img-top" alt="">
              <div class="card-body">
                <h6 class="card-title editable-text fw-bold">Product name</h6>
                <p class="card-text small text-muted editable-text">Short description.</p>
                <div class="d-flex justify-content-between align-items-center">
                  <span class="fw-bold editable-text">$39</span>
                  <button class="btn btn-sm btn-outline-primary rounded-pill">Add to cart</button>
                </div>
              </div>
            </div>
          </div>`).join('')}
    </div>
  </div>
</section>`
                },
                {
                    id: 'pricing-section',
                    label: 'Pricing Table',
                    category: 'E‑commerce',
                    content: `
<section class="py-5 bg-light">
  <div class="container">
    <div class="text-center mb-5">
      <h2 class="fw-bold display-6 editable-text">Simple Pricing</h2>
      <p class="text-muted editable-text">Choose the plan that fits you best.</p>
    </div>
    <div class="row g-4">
      ${['Starter','Pro','Business'].map((plan,i)=>`
          <div class="col-md-4">
            <div class="card h-100 shadow-soft border-0 text-center rounded-4 ${i===1?'border border-primary bg-white':'bg-light'}">
              <div class="card-body p-4">
                <h4 class="fw-bold editable-text">${plan}</h4>
                <h2 class="my-4 fw-bold editable-text">$${i===0?19:i===1?49:99}<span class="fs-6 text-muted fw-normal">/mo</span></h2>
                <ul class="list-unstyled mb-5 text-muted small">
                  <li class="mb-2 editable-text">✔ Unlimited Projects</li>
                  <li class="mb-2 editable-text">✔ Essential Analytics</li>
                  <li class="mb-0 editable-text">✔ 24/7 Support</li>
                </ul>
                <a href="#" class="btn ${i===1?'btn-primary':'btn-outline-dark'} w-100 rounded-pill hover-lift editable-text">Get Started</a>
              </div>
            </div>
          </div>`).join('')}
    </div>
  </div>
</section>`
                }
            ];

            const backgroundBlocks = [{
                id: 'bg-section',
                label: 'Background Section',
                category: 'Background',
                content: `
<section class="py-5 text-white"
  style="position:relative;background-image:url('https://via.placeholder.com/1600x600');
         background-size:cover;background-position:center;">
  <div class="container text-center">
    <h2 class="fw-bold mb-3 editable-text">Beautiful background section</h2>
    <p class="mb-4 editable-text">
      Write something catchy over a full‑width image to grab attention.
    </p>
    <a href="#" class="btn btn-light btn-lg editable-text">Primary action</a>
  </div>
</section>`
            }];

           // ----------- FORMS BLOCKS -----------
            const formBlocks = [{
                    id: 'form-wrapper',
                    label: 'Form',
                    category: 'Forms',
                    content: {
                        type: 'form-wrapper',
                        tagName: 'form',
                        classes: ['p-3', 'border', 'rounded', 'bg-light'],
                    }
                },

                {
                    id: 'form-input',
                    label: 'Input',
                    category: 'Forms',
                    content: `<div class="mb-3">
                        <label class="form-label">Label</label>
                        <input type="text" class="form-control" placeholder="Enter text">
                      </div>`
                },
                {
                    id: 'form-textarea',
                    label: 'Textarea',
                    category: 'Forms',
                    content: `<div class="mb-3">
                        <label class="form-label">Label</label>
                        <textarea class="form-control" rows="4" placeholder="Enter text"></textarea>
                      </div>`
                },
                {
                    id: 'form-select',
                    label: 'Select',
                    category: 'Forms',
                    content: {
                        type: 'default',
                        content: `
        <div class="mb-3">
            <label class="form-label editable-text">Select</label>
            <select class="form-select">
                <option value="" disabled>Select...</option>
                <option value="A" class="editable-text">Option A</option>
                <option value="B" class="editable-text">Option B</option>
            </select>
        </div>
        `,
                        script() {
                            const select = this.querySelector('select');
                            ['mousedown', 'mouseup', 'click', 'focus'].forEach(evt =>
                                select.addEventListener(evt, e => e.stopPropagation())
                            );
                            if (this.dataset.selected) select.value = this.dataset.selected;
                            select.addEventListener('change', e => this.dataset.selected = e
                                .target.value);
                        }
                    }
                },

                {
                    id: 'form-checkbox',
                    label: 'Checkbox',
                    category: 'Forms',
                    content: `<div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" id="check1">
                        <label class="form-check-label" for="check1">Checkbox Label</label>
                      </div>`,
                    script() {
                        // Get the select element inside the block
                        const select = this.querySelector('select');

                        // Stop GrapesJS from selecting parent component when interacting with select
                        ['click', 'mousedown', 'mouseup', 'focus'].forEach(evt =>
                            select.addEventListener(evt, e => e.stopPropagation())
                        );

                        // If there is a previously selected value, show it
                        if (this.dataset.selected)
                            select.value = this.dataset.selected;

                        // Listen to changes
                        select.addEventListener('change', e => {
                            this.dataset.selected = e.target
                                .value; // store the selected value
                            select.value = e.target.value; // update the visible option
                        });
                    }

                },
                {
                    id: 'form-radio',
                    label: 'Radio',
                    category: 'Forms',
                    content: `<div class="mb-3">
                        <label class="form-label d-block">Choose Option</label>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="radioGroup" id="radio1" value="1">
                            <label class="form-check-label" for="radio1">Option 1</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="radioGroup" id="radio2" value="2">
                            <label class="form-check-label" for="radio2">Option 2</label>
                        </div>
                      </div>`,
                    script: function() {
                        const radios = this.querySelectorAll('input[type="radio"]');
                        radios.forEach(radio => {
                            ['click', 'mousedown', 'mouseup', 'focus'].forEach(
                                evt => radio
                                .addEventListener(evt, e => e.stopPropagation())
                            );
                            if (this.dataset.selected) radio.checked = this.dataset
                                .selected ===
                                radio.value;
                            radio.addEventListener('change', e => this.dataset
                                .selected = radio
                                .value);
                        });
                    }
                },
                {
                    id: 'newsletter-section',
                    label: 'Newsletter Signup',
                    category: 'Forms',
                    content: `
<section class="py-5 bg-primary text-white">
  <div class="container text-center">
    <h2 class="fw-bold editable-text">Subscribe to our newsletter</h2>
    <p class="mb-4 editable-text">Get updates straight to your inbox</p>

    <div class="row justify-content-center">
      <div class="col-md-6">
        <form class="d-flex gap-2">
          <input type="email" class="form-control" placeholder="Email address">
          <button class="btn btn-dark editable-text">Subscribe</button>
        </form>
      </div>
    </div>
  </div>
</section>`
                },

                {
                    id: 'form-button',
                    label: 'Button',
                    category: 'Forms',
                    content: `<button type="button" class="btn btn-primary w-100">Submit</button>`
                },
                {
                    id: 'form-password',
                    label: 'Password',
                    content: `<div class="mb-3">
            <label class="form-label editable-text">Password</label>
            <input type="password" class="form-control" placeholder="Enter password">
        </div>`,
                    category: 'Forms',
                    icon: 'fa-lock'
                },
                {
                    id: 'form-hidden',
                    label: 'Hidden Field',
                    content: `<input type="hidden" value="hidden_value">`,
                    category: 'Forms',
                    icon: 'fa-eye-slash'
                },
                {
                    id: 'form-url',
                    label: 'Website URL',
                    content: `<div class="mb-3">
        <label class="form-label editable-text">Website</label>
        <input type="url" class="form-control" placeholder="https://example.com">
    </div>`,
                    category: 'Forms',
                    icon: 'fa-link'
                },
                {
                    id: 'form-color',
                    label: 'Color Picker',
                    content: `<div class="mb-3">
        <label class="form-label editable-text">Choose Color</label>
        <input type="color" class="form-control form-control-color">
    </div>`,
                    category: 'Forms',
                    icon: 'fa-paint-brush'
                },
                {
                    id: 'form-otp',
                    label: 'OTP / Verification Code',
                    content: `<div class="mb-3">
        <label class="form-label editable-text">Verification Code</label>
        <input type="text" class="form-control" maxlength="6" placeholder="Enter OTP">
    </div>`,
                    category: 'Forms',
                    icon: 'fa-key'
                },

                // ---------- DYNAMIC BOOTSTRAP FORM (allows drop-ins & dynamic fields) ----------
                {
                    id: 'form-bootstrap-full',
                    label: 'Bootstrap Form (Dynamic)',
                    category: 'Forms',
                    icon: 'fa-wpforms',
                    content: {
                        type: 'default',
                        components: [{
                            tagName: 'div',
                            classes: ['container'], // ✅ IMPORTANT: Bootstrap container
                            components: [{
                                tagName: 'form',
                                classes: ['p-4', 'shadow-sm', 'bg-light',
                                    'rounded', 'mx-auto'
                                ],
                                attributes: {
                                    attributes: {
                                        style: "max-width:700px; width:100%; height:auto;"
                                    }

                                },
                                components: [{
                                        tagName: 'h3',
                                        classes: ['mb-4', 'fw-bold',
                                            'text-center',
                                            'editable-text'
                                        ],
                                        content: 'Contact Us'
                                    },
                                    {
                                        tagName: 'div',
                                        classes: ['mb-3'],
                                        components: [{
                                                tagName: 'label',
                                                classes: ['form-label',
                                                    'editable-text'
                                                ],
                                                content: 'Name'
                                            },
                                            {
                                                tagName: 'input',
                                                attributes: {
                                                    type: 'text',
                                                    class: 'form-control',
                                                    placeholder: 'Enter name'
                                                }
                                            }
                                        ]
                                    },
                                    {
                                        tagName: 'div',
                                        classes: ['mb-3'],
                                        components: [{
                                                tagName: 'label',
                                                classes: ['form-label',
                                                    'editable-text'
                                                ],
                                                content: 'Email'
                                            },
                                            {
                                                tagName: 'input',
                                                attributes: {
                                                    type: 'email',
                                                    class: 'form-control',
                                                    placeholder: 'Enter email'
                                                }
                                            }
                                        ]
                                    },
                                    {
                                        tagName: 'button',
                                        classes: ['btn', 'btn-primary',
                                            'w-100',
                                            'editable-text'
                                        ],
                                        attributes: {
                                            type: 'submit'
                                        },
                                        content: 'Submit'
                                    }
                                ],
                                script: function() {
                                    // Prevent GrapesJS from hijacking form events
                                    const protect = el => {
                                        ['click', 'mousedown',
                                            'mouseup', 'focus'
                                        ]
                                        .forEach(evt =>
                                            el.addEventListener(evt,
                                                e => e
                                                .stopPropagation())
                                        );
                                    };

                                    this.querySelectorAll(
                                        'input, textarea, select, option, button'
                                    ).forEach(protect);

                                    // Editable text helper
                                    const makeEditable = el => {
                                        if (el.dataset.edit) return;
                                        el.dataset.edit = '1';
                                        el.addEventListener('dblclick',
                                            e => {
                                                e.stopPropagation();
                                                el.setAttribute(
                                                    'contenteditable',
                                                    'true');
                                                el.focus();
                                            });
                                        el.addEventListener('blur',
                                            () =>
                                            el.removeAttribute(
                                                'contenteditable')
                                        );
                                    };

                                    this.querySelectorAll('.editable-text')
                                        .forEach(
                                            makeEditable);

                                    // Observe dynamic changes
                                    const observer = new MutationObserver(
                                        () => {
                                            this.querySelectorAll(
                                                'input, textarea, select, option, button'
                                            ).forEach(protect);
                                            this.querySelectorAll(
                                                    '.editable-text')
                                                .forEach(makeEditable);
                                        });

                                    observer.observe(this, {
                                        childList: true,
                                        subtree: true
                                    });
                                }
                            }]
                        }]
                    }
                },


                // ---------------- EXTRA CUSTOM FIELDS ----------------
                {
                    id: 'form-phone',
                    label: 'Phone Number',
                    content: `<div class="mb-3">
                            <label class="form-label editable-text">Phone Number</label>
                            <input type="tel" class="form-control" placeholder="9876543210">
                        </div>`,
                    category: 'Forms',
                    icon: 'fa-phone'
                },
                {
                    id: 'form-email-field',
                    label: 'Email Field',
                    content: `<div class="mb-3">
                            <label class="form-label editable-text">Email</label>
                            <input type="email" class="form-control" placeholder="example@mail.com">
                        </div>`,
                    category: 'Forms',
                    icon: 'fa-envelope'
                },
                {
                    id: 'form-number',
                    label: 'Number Field',
                    content: `<div class="mb-3">
                            <label class="form-label editable-text">Number</label>
                            <input type="number" class="form-control" placeholder="0">
                        </div>`,
                    category: 'Forms',
                    icon: 'fa-hashtag'
                },
                {
                    id: 'form-date',
                    label: 'Date Picker',
                    content: `<div class="mb-3">
                            <label class="form-label editable-text">Choose Date</label>
                            <input type="date" class="form-control">
                        </div>`,
                    category: 'Forms',
                    icon: 'fa-calendar'
                },
                {
                    id: 'form-time',
                    label: 'Time Picker',
                    content: `<div class="mb-3">
                            <label class="form-label editable-text">Choose Time</label>
                            <input type="time" class="form-control">
                        </div>`,
                    category: 'Forms',
                    icon: 'fa-clock-o'
                },
                {
                    id: 'form-file',
                    label: 'File Upload',
                    content: `<div class="mb-3">
                            <label class="form-label editable-text">Upload File</label>
                            <input type="file" class="form-control">
                        </div>`,
                    category: 'Forms',
                    icon: 'fa-upload'
                },
                {
                    id: 'form-switch',
                    label: 'Switch Toggle',
                    content: `<div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" id="switch1">
                            <label class="form-check-label editable-text" for="switch1">Enable Option</label>
                        </div>`,
                    category: 'Forms',
                    icon: 'fa-toggle-on'
                },
                {
                    id: 'form-range',
                    label: 'Range Slider',
                    content: `<div class="mb-3">
                            <label class="form-label editable-text">Range</label>
                            <input type="range" class="form-range" min="0" max="100">
                        </div>`,
                    category: 'Forms',
                    icon: 'fa-sliders'
                },
                {
                    id: 'form-address',
                    label: 'Address Group',
                    content: `<div class="mb-3">
                            <label class="form-label editable-text">Address</label>
                            <input type="text" class="form-control mb-2" placeholder="Street Address">
                            <input type="text" class="form-control mb-2" placeholder="City">
                            <input type="text" class="form-control" placeholder="State / Province">
                        </div>`,
                    category: 'Forms',
                    icon: 'fa-map-marker'
                },
                {
                    id: 'form-rating',
                    label: 'Star Rating',
                    content: `<div class="mb-3 rating-group">
                            <label class="form-label editable-text">Rating</label><br>
                            <span class="rating-stars" style="font-size:26px; cursor:pointer; user-select:none;">☆☆☆☆☆</span>
                        </div>`,
                    category: 'Forms',
                    icon: 'fa-star',
                    script: function() {
                        // star rating interactive behavior
                        const stars = this.querySelector('.rating-stars');
                        if (!stars) return;
                        let current = 0;
                        stars.addEventListener('click', e => e.stopPropagation());
                        stars.addEventListener('mousemove', function(e) {
                            const rect = this.getBoundingClientRect();
                            const x = e.clientX - rect.left;
                            const starCount = Math.min(5, Math.max(1, Math.floor((
                                x / this
                                .offsetWidth) * 5) + 1));
                            this.textContent = '★★★★★'.slice(0, starCount) + '☆☆☆☆☆'
                                .slice(
                                    starCount);
                        });
                        stars.addEventListener('mouseleave', function() {
                            this.textContent = '★★★★★'.slice(0, current) + '☆☆☆☆☆'
                                .slice(current);
                        });
                        stars.addEventListener('click', function(e) {
                            const rect = this.getBoundingClientRect();
                            const x = e.clientX - rect.left;
                            current = Math.min(5, Math.max(1, Math.floor((x / this
                                    .offsetWidth) *
                                5) + 1));
                            this.textContent = '★★★★★'.slice(0, current) + '☆☆☆☆☆'
                                .slice(current);
                        });
                    }
                }
            ];


            const allBlocks = [
                ...layoutBlocks,
                ...contentBlocks,
                ...chromeBlocks,
                ...sidebarBlocks,
                ...sectionBlocks,
                ...mediaBlocks,
                ...ecommerceBlocks,
                ...backgroundBlocks,
                ...formBlocks
            ];

            allBlocks.forEach(b => {
                bm.add(b.id, {
                    label: b.label,
                    category: b.category,
                    content: b.content,
                    attributes: {
                        class: 'fa fa-square'
                    },
                    ...(b.script ? {
                        script: b.script
                    } : {})
                });
            });

            @if (!empty($page->gjs_json))
                editor.loadProjectData(@json($page->gjs_json));
            @endif

            @if (!empty($page->css))
                editor.setStyle(`{!! $page->css !!}`);
            @endif

            // Ensure any element with 'editable-text' is recognized as editable by GrapesJS natively
            editor.on('component:create', component => {
                if (component.getAttributes().class?.includes('editable-text')) {
                    component.set({
                        editable: true,
                        stylable: true
                    });
                }
            });

        });

        function fixFileInputs() {
            const fileInputs = editor.Canvas.getBody().querySelectorAll('input[type="file"]');
            fileInputs.forEach(input => {
                input.addEventListener('click', e => {
                    e.preventDefault();
                    const tempInput = document.createElement('input');
                    tempInput.type = 'file';
                    tempInput.style.display = 'none';
                    document.body.appendChild(tempInput);
                    tempInput.addEventListener('change', function(ev) {
                        input.files = ev.target.files;
                        document.body.removeChild(tempInput);
                    });
                    tempInput.click();
                });
            });
        }

        function runBlockScripts(components) {
            components.forEach(comp => {
                const script = comp.get('script');
                if (script && typeof script === 'function') script.call(comp);
                const children = comp.get('components');
                if (children && children.length) runBlockScripts(children.models);
            });
        }

        document.getElementById('saveForm').addEventListener('submit', function(e) {
            document.getElementById('html').value = editor.getHtml();
            document.getElementById('css').value = editor.getCss();
            document.getElementById('js').value = editor.getJs();
            document.getElementById('gjs_json').value = JSON.stringify(editor.getProjectData());
        });
    </script>
</body>

</html>
