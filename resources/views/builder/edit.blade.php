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
        body {
            margin: 0;
        }

        /* GrapesJS Canvas */
        #gjs {
            height: calc(100vh - 50px) !important;
            /* Subtract top bar height */
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

        `
        /* ================= MOBILE FIXES ================= */

        /* Force columns to stack on mobile */
        @media (max-width: 576px) {
            .row>[class*="col-"] {
                width: 100% !important;
                max-width: 100% !important;
                flex: 0 0 100% !important;
            }

            /* Text center on mobile */
            h1,
            h2,
            h3,
            h4,
            h5,
            h6,
            p {
                text-align: center;
            }

            /* Buttons full width */
            .btn {
                width: 100%;
            }

            /* Navbar fixes */
            .navbar-nav {
                text-align: center;
            }

            /* Sidebar layouts stack */
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

            /* Image spacing */
            img {
                margin-bottom: 1rem;
            }

            /* Reduce padding */
            section {
                padding-top: 2rem !important;
                padding-bottom: 2rem !important;
            }
        }

        `
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
                    `
            /* ===== ADMIN LAYOUT FIX ===== */
            .admin-layout { display: flex; min-height: 100vh; overflow: hidden; background: #f5f6fa; }
            .admin-sidebar { width: 240px; min-height: 100vh; background: #212529; color: #fff; flex-shrink: 0; }
            .admin-content { flex: 1; min-height: 100vh; overflow-y: auto; background: #f5f6fa; }
            .admin-navbar { position: sticky; top: 0; z-index: 10; background: #fff; }
            .admin-layout .container { max-width: 100%; }
            `
                ]
            },
            deviceManager: {
                devices: [{
                        name: 'Desktop',
                        width: ''
                    },
                    {
                        name: 'Tablet',
                        width: '768px'
                    },
                    {
                        name: 'Mobile',
                        width: '395px'
                    }
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


        editor.on('load', () => {
            const bm = editor.BlockManager;
            const dc = editor.DomComponents;

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
                    id: 'layout-sidebar-left',
                    label: 'Page Layout (Sidebar Left)',
                    category: 'Layout',
                    content: `
<div class="admin-layout d-flex">

  <!-- SIDEBAR LEFT -->
  <aside class="admin-sidebar bg-dark text-white p-3">
    <h5 class="mb-4 editable-text">CMS Panel</h5>
    <ul class="nav flex-column gap-2">
      <li><a class="nav-link text-white editable-text" href="#">Dashboard</a></li>
      <li><a class="nav-link text-white editable-text" href="#">Banner</a></li>
      <li><a class="nav-link text-white editable-text" href="#">Product</a></li>
      <li><a class="nav-link text-white editable-text" href="#">Category</a></li>
    </ul>
  </aside>

  <!-- MAIN CONTENT -->
  <div class="admin-content flex-grow-1 bg-light">
    <header class="admin-navbar shadow-sm p-3 bg-white">
      <span class="editable-text">Admin Dashboard</span>
    </header>

    <main class="p-4">
      <h2 class="editable-text">Main Content Area</h2>
      <p class="editable-text">
        Drag tables, forms, sections, or content blocks here.
      </p>
    </main>
  </div>

</div>
`
                },

                {
                    id: 'layout-sidebar-right',
                    label: 'Page Layout (Sidebar Right)',
                    category: 'Layout',
                    content: `
<div class="admin-layout d-flex">

  <!-- MAIN CONTENT -->
  <div class="admin-content flex-grow-1 bg-light">
    <header class="admin-navbar shadow-sm p-3 bg-white">
      <span class="editable-text">Admin Dashboard</span>
    </header>

    <main class="p-4">
      <h2 class="editable-text">Main Content Area</h2>
      <p class="editable-text">
        Drag tables, forms, sections, or content blocks here.
      </p>
    </main>
  </div>

  <!-- SIDEBAR RIGHT -->
  <aside class="admin-sidebar bg-dark text-white p-3">
    <h5 class="mb-4 editable-text">CMS Panel</h5>
    <ul class="nav flex-column gap-2">
      <li><a class="nav-link text-white editable-text" href="#">Dashboard</a></li>
      <li><a class="nav-link text-white editable-text" href="#">Banner</a></li>
      <li><a class="nav-link text-white editable-text" href="#">Product</a></li>
      <li><a class="nav-link text-white editable-text" href="#">Category</a></li>
    </ul>
  </aside>

</div>
`
                }
            ];




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
                                  <div class="col-6 col-md-3">
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
                                  <div class="col-12 col-md-4">
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
                }
            ];


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
                        iframe && iframe.addEventListener('mousedown', e => e.stopPropagation());
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
<div class="card m-2 shadow" style="width:18rem;">
  <img src="https://via.placeholder.com/300x150" class="card-img-top" alt="">
  <div class="card-body">
    <h5 class="card-title editable-text">Product Title</h5>
    <p class="card-text editable-text">Product description goes here.</p>
    <div class="d-flex justify-content-between align-items-center mb-2">
      <span class="fw-bold editable-text">$49</span>
      <span class="text-muted small editable-text">In stock</span>
    </div>
    <button class="btn btn-success w-100">Buy Now</button>
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
                                                                                                  <div class="col-sm-6 col-md-3">
                                                                                                    <div class="card h-100 shadow-sm border-0">
                                                                                                      <img src="https://via.placeholder.com/300x180" class="card-img-top" alt="">
                                                                                                      <div class="card-body">
                                                                                                        <h6 class="card-title editable-text">Product name</h6>
                                                                                                        <p class="card-text small text-muted editable-text">Short description.</p>
                                                                                                        <div class="d-flex justify-content-between align-items-center">
                                                                                                          <span class="fw-bold editable-text">$39</span>
                                                                                                          <button class="btn btn-sm btn-outline-primary">Add to cart</button>
                                                                                                        </div>
                                                                                                      </div>
                                                                                                    </div>
                                                                                                  </div>`).join('')}
    </div>
  </div>
</section>`
                }
            ];

            // ---------------- BACKGROUND / HERO IMAGE SECTIONS ----------------
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
                            select.addEventListener('change', e => this.dataset.selected = e.target.value);
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
                            this.dataset.selected = e.target.value; // store the selected value
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
                            ['click', 'mousedown', 'mouseup', 'focus'].forEach(evt => radio
                                .addEventListener(evt, e => e.stopPropagation()));
                            if (this.dataset.selected) radio.checked = this.dataset.selected ===
                                radio.value;
                            radio.addEventListener('change', e => this.dataset.selected = radio
                                .value);
                        });
                    }
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
                                classes: ['p-4', 'shadow-sm', 'bg-light', 'rounded', 'mx-auto'],
                                attributes: {
                                    attributes: {
                                        style: "max-width:700px; width:100%; height:auto;"
                                    }

                                },
                                components: [{
                                        tagName: 'h3',
                                        classes: ['mb-4', 'fw-bold', 'text-center',
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
                                        classes: ['btn', 'btn-primary', 'w-100',
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
                                        ['click', 'mousedown', 'mouseup', 'focus']
                                        .forEach(evt =>
                                            el.addEventListener(evt, e => e
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
                                        el.addEventListener('dblclick', e => {
                                            e.stopPropagation();
                                            el.setAttribute('contenteditable',
                                                'true');
                                            el.focus();
                                        });
                                        el.addEventListener('blur', () =>
                                            el.removeAttribute('contenteditable')
                                        );
                                    };

                                    this.querySelectorAll('.editable-text').forEach(
                                        makeEditable);

                                    // Observe dynamic changes
                                    const observer = new MutationObserver(() => {
                                        this.querySelectorAll(
                                            'input, textarea, select, option, button'
                                        ).forEach(protect);
                                        this.querySelectorAll('.editable-text')
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
                            const starCount = Math.min(5, Math.max(1, Math.floor((x / this
                                .offsetWidth) * 5) + 1));
                            this.textContent = '★★★★★'.slice(0, starCount) + '☆☆☆☆☆'.slice(
                                starCount);
                        });
                        stars.addEventListener('mouseleave', function() {
                            this.textContent = '★★★★★'.slice(0, current) + '☆☆☆☆☆'.slice(current);
                        });
                        stars.addEventListener('click', function(e) {
                            const rect = this.getBoundingClientRect();
                            const x = e.clientX - rect.left;
                            current = Math.min(5, Math.max(1, Math.floor((x / this.offsetWidth) *
                                5) + 1));
                            this.textContent = '★★★★★'.slice(0, current) + '☆☆☆☆☆'.slice(current);
                        });
                    }
                }
            ];



            // ---------------- REGISTER ALL BLOCKS ----------------
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

            // Add device switch commands
            editor.Commands.add('set-device-desktop', {
                run: function(editor) {
                    editor.setDevice('Desktop');
                }
            });
            editor.Commands.add('set-device-tablet', {
                run: function(editor) {
                    editor.setDevice('Tablet');
                }
            });
            editor.Commands.add('set-device-mobile', {
                run: function(editor) {
                    editor.setDevice('Mobile');
                }
            });

            // Load saved data
            @if (!empty($page->gjs_json))
                // Load saved GrapesJS project
                editor.loadProjectData(@json($page->gjs_json));
            @endif

            // Always apply the saved CSS
            @if (!empty($page->css))
                editor.setStyle(`{!! $page->css !!}`);
            @endif

            // ---------------- GLOBAL EDITABLE TEXT HELPER ----------------
            const makeGlobalEditable = () => {
                const rootEl = editor.Canvas.getBody();
                rootEl.querySelectorAll('.editable-text').forEach(el => {
                    if (el.getAttribute('data-edit-listener')) return;
                    el.setAttribute('data-edit-listener', '1');
                    el.addEventListener('dblclick', e => {
                        e.stopPropagation();
                        el.setAttribute('contenteditable', 'true');
                        el.focus();
                    });
                    el.addEventListener('blur', () => el.removeAttribute('contenteditable'));
                });
            };

            const canvasEl = editor.Canvas.getBody();
            const observer = new MutationObserver(makeGlobalEditable);
            observer.observe(canvasEl, {
                childList: true,
                subtree: true
            });
            makeGlobalEditable();

            // Prevent form submit inside editor
            canvasEl.addEventListener('submit', e => e.preventDefault(), true);
        });
        // ---------------- FIX FILE INPUTS FOR EDITING EXISTING PAGES ----------------
        function fixFileInputs() {
            const fileInputs = editor.Canvas.getBody().querySelectorAll('input[type="file"]');

            fileInputs.forEach(input => {
                input.addEventListener('click', e => {
                    e.preventDefault(); // prevent GrapesJS hijack

                    // Create a temporary input outside canvas
                    const tempInput = document.createElement('input');
                    tempInput.type = 'file';
                    tempInput.style.display = 'none';
                    document.body.appendChild(tempInput);

                    tempInput.addEventListener('change', function(ev) {
                        // Copy the file(s) back to original input
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
                if (script && typeof script === 'function') {
                    script.call(comp);
                }
                // Recursively run scripts for nested components
                const children = comp.get('components');
                if (children && children.length) runBlockScripts(children.models);
            });
        }



        // BEFORE SUBMIT: put GrapesJS data into hidden inputs
        document.getElementById('saveForm').addEventListener('submit', function(e) {
            document.getElementById('html').value = editor.getHtml();
            document.getElementById('css').value = editor.getCss();
            document.getElementById('gjs_json').value = JSON.stringify(editor.getProjectData());
            // let the form submit to PageBuilderController@update
        });
    </script>

</body>
