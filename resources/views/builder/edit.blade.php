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
            /* ====== ADMIN LAYOUT SYSTEM ====== */
            .admin-layout { 
                display: flex; 
                min-height: 100vh; 
                background-color: #f8f9fa; 
                transition: background-color 0.3s ease;
            }
            .admin-sidebar { 
                width: 250px; 
                background-color: #2d3436; 
                color: #ffffff; 
                transition: background-color 0.3s ease;
            }
            .admin-content { flex: 1; padding: 0; transition: background-color 0.3s ease; }
            
            /* Navbar Default */
            .vimeo-navbar {
                background-color: #ffffff;
                transition: background-color 0.3s ease;
            }
            
            /* Footer Default */
            .pro-footer {
                background-color: #000000;
                color: #ffffff;
                transition: background-color 0.3s ease;
            }

            /* Sections Default (Unlocked) */
            .hero-section { background-color: #f8f9fa; transition: all 0.3s ease; }
            .features-section { background-color: #ffffff; transition: all 0.3s ease; }
            .cta-section { background-color: #007bff; color: #ffffff; transition: all 0.3s ease; }
            .testimonials-section { background-color: #f8f9fa; transition: all 0.3s ease; }
            .faq-section { background-color: #f8fafc; transition: all 0.3s ease; }
            .banner-section { background-color: #007bff; color: #ffffff; transition: all 0.3s ease; }
            .stats-section { background: linear-gradient(135deg, #0f2027, #203a43, #2c5364); transition: all 0.3s ease; }
            .team-section { background-color: #f8f9fa; transition: all 0.3s ease; }

            /* Footer Newsletter Unlock */
            .v-footer-input {
                background: transparent;
                border: 1px solid rgba(255,255,255,0.3);
                color: #ffffff;
                padding: 10px 15px;
                transition: all 0.3s ease;
            }
            .v-footer-btn {
                background-color: #ffffff;
                color: #000000;
                border: 1px solid #ffffff;
                padding: 10px 40px;
                font-weight: 600;
                cursor: pointer;
                transition: all 0.3s ease;
            }
            .v-footer-btn:hover {
                opacity: 0.9;
            }

            .navbar-icons {
                display: flex !important;
                flex-direction: row !important;
                align-items: center !important;
                gap: 12px !important;
                padding: 0 10px;
            }
            .action-icon {
                color: #2d3748 !important;
                text-decoration: none !important;
                width: 34px;
                height: 34px;
                display: inline-flex !important;
                align-items: center;
                justify-content: center;
                transition: all 0.2s;
                border-radius: 8px;
                background: rgba(0,0,0,0.03);
            }
            .action-icon:hover {
                background: rgba(0, 173, 239, 0.1) !important;
                color: #00adef !important;
                transform: translateY(-1px);
            }
            .action-icon i { font-size: 16px; }

            /* Wix Fashion 1905 Style Unlock */
            .luxury-hero { background-color: #ffffff; padding: 120px 20px; transition: all 0.3s ease; }
            .luxury-title { font-size: 5rem; font-weight: 800; text-transform: uppercase; letter-spacing: -2px; line-height: 0.9; }
            .luxury-subtitle { font-size: 1.2rem; letter-spacing: 4px; text-transform: uppercase; opacity: 0.7; }
            .luxury-button { 
                background: #000; color: #fff; border: 1px solid #000; 
                padding: 15px 40px; text-transform: uppercase; font-weight: 700; 
                letter-spacing: 2px; text-decoration: none; transition: 0.3s;
                display: inline-block;
            }
            .luxury-button:hover { background: transparent; color: #000; }
            .collection-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 40px; }
            .collection-item { position: relative; overflow: hidden; height: 500px; background: #eee; }
            .collection-image { width: 100%; height: 100%; object-fit: cover; transition: 0.5s; }
            .collection-item:hover .collection-image { transform: scale(1.05); }

            /* Search Overlay */
            .search-overlay {
                position: fixed !important;
                top: 0; left: 0; width: 100%; height: 100%;
                background: rgba(255,255,255,0.98);
                backdrop-filter: blur(10px);
                z-index: 10000;
                display: none;
                align-items: center;
                justify-content: center;
                flex-direction: column;
            }
            .search-input {
                width: 50%;
                border: none;
                border-bottom: 2px solid #00adef;
                font-size: 32px;
                outline: none;
                padding: 10px;
                text-align: center;
                background: transparent;
            }
            .close-search {
                position: absolute;
                top: 30px; right: 40px;
                font-size: 40px; color: #666; cursor: pointer;
            }

            .modern-gradient-text {
                background: linear-gradient(to right, #6366f1, #a855f7);
                -webkit-background-clip: text;
                -webkit-text-fill-color: transparent;
                font-weight: 800;
            }

            /* Infinite Cloud Tech Styles */
            .cloud-hero { background: #0f172a; color: #fff; padding: 120px 0; position: relative; overflow: hidden; }
            .cloud-hero::before { 
                content: ''; position: absolute; top: 0; left: 0; width: 100%; height: 100%; 
                background: radial-gradient(circle at 80% 20%, rgba(99, 102, 241, 0.15), transparent);
            }
            .cloud-card { 
                background: #1e293b; border: 1px solid rgba(255,255,255,0.1); 
                border-radius: 24px; padding: 40px; transition: 0.3s;
            }
            .cloud-card:hover { border-color: #6366f1; transform: translateY(-5px); }
            .cloud-feature-icon { 
                width: 60px; height: 60px; background: rgba(99, 102, 241, 0.1); 
                color: #818cf8; border-radius: 16px; display: flex; 
                align-items: center; justify-content: center; font-size: 24px; margin-bottom: 24px;
            }
            .cloud-btn-primary { 
                background: #6366f1; color: #fff; border: none; padding: 14px 32px; 
                border-radius: 12px; font-weight: 600; transition: 0.3s;
                text-decoration: none; display: inline-block;
            }
            .cloud-btn-primary:hover { background: #4f46e5; box-shadow: 0 0 20px rgba(99, 102, 241, 0.4); color: #fff; }

            /* Master Layout Styles */
            .pro-master-shell { display: flex; flex-direction: column; min-height: 100vh; background: #f8fafc; }
            .pro-content-stack { display: flex; flex: 1; height: calc(100vh - 70px); overflow: hidden; }
            .master-sidebar { 
                width: 260px; background: #0f172a; color: #fff; 
                display: flex; flex-direction: column; border-right: 1px solid rgba(255,255,255,0.05);
            }
            .master-main { flex: 1; overflow-y: auto; display: flex; flex-direction: column; background: #ffffff; }
            .dashboard-nav-link { 
                display: flex; align-items: center; gap: 12px; padding: 12px 20px; 
                color: rgba(255,255,255,0.7); text-decoration: none; transition: 0.2s;
            }
            .dashboard-nav-link:hover { background: rgba(255,255,255,0.05); color: #fff; }
            .dashboard-nav-link.active { background: #6366f1; color: #fff; }

            /* Personal Life Coach Elite Styles */
            .coach-hero { 
                background: linear-gradient(rgba(0,0,0,0.3), rgba(0,0,0,0.3)), url('https://images.unsplash.com/photo-1464822759023-fed622ff2c3b?auto=format&fit=crop&w=1920');
                background-size: cover; background-position: center; min-height: 80vh; 
                display: flex; align-items: center; justify-content: center; color: #fff; text-align: center;
            }
            .coach-title { font-size: 6rem; font-weight: 900; letter-spacing: 2px; text-transform: uppercase; line-height: 1; }
            .coach-btn { 
                background: #000; color: #fff; border: 1px solid #000; padding: 12px 40px; 
                text-transform: uppercase; font-weight: 700; letter-spacing: 1px; transition: 0.3s;
            }
            .coach-btn:hover { background: transparent; color: #000; }
            .coach-service-card { 
                border: 2px solid #000; padding: 40px 20px; text-align: center; 
                height: 100%; transition: transform 0.3s ease;
            }
            .coach-service-card:hover { transform: translateY(-10px); }
            .coach-contact-section { background: #56c2c1; padding: 100px 0; color: #000; }
            .coach-input { 
                background: transparent; border: 1.5px solid #000; padding: 12px; 
                width: 100%; margin-bottom: 20px; outline: none;
            }
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
            // Explicitly define link component to ensure traits like Target and Title work correctly
            dc.addType('link', {
                model: {
                    defaults: {
                        traits: [
                            {
                                type: 'select',
                                label: 'Link Type',
                                name: 'link_type',
                                options: [
                                    { value: 'url', name: 'Custom URL' },
                                    { value: 'page', name: 'Internal Page' },
                                ],
                            },
                            {
                                type: 'text',
                                label: 'Href',
                                name: 'href',
                            },
                            {
                                type: 'select',
                                label: 'Select Page',
                                name: 'internal_page',
                                options: [], // Will be populated dynamically
                            },
                            {
                                type: 'text',
                                label: 'Title',
                                name: 'title',
                            },
                            {
                                type: 'select',
                                label: 'Target',
                                name: 'target',
                                options: [
                                    { value: '_self', name: 'This window' },
                                    { value: '_blank', name: 'New window' },
                                    { value: '_top', name: 'Top window' },
                                ]
                            }
                        ],
                    },
                    init() {
                        this.on('change:link_type', this.handleLinkTypeChange);
                        this.on('change:internal_page', this.handlePageChange);
                        this.handleLinkTypeChange();
                        this.updatePageOptions();
                    },
                    handleLinkTypeChange() {
                        const type = this.get('link_type') || 'url';
                        const traits = this.get('traits');
                        const hrefTrait = traits.get('href');
                        const pageTrait = traits.get('internal_page');

                        if (type === 'page') {
                            hrefTrait && hrefTrait.set('attributes', { style: 'display:none' });
                            pageTrait && pageTrait.set('attributes', { style: 'display:block' });
                        } else {
                            hrefTrait && hrefTrait.set('attributes', { style: 'display:block' });
                            pageTrait && pageTrait.set('attributes', { style: 'display:none' });
                        }
                    },
                    handlePageChange() {
                        const slug = this.get('internal_page');
                        if (slug) {
                            this.set('href', `/page/${slug}`);
                        }
                    },
                    async updatePageOptions() {
                        try {
                            const res = await fetch('/api/pages');
                            const pages = await res.json();
                            const options = pages.map(p => ({ value: p.slug, name: p.title }));
                            const traits = this.get('traits');
                            const pageTrait = traits.get('internal_page');
                            if (pageTrait) {
                                pageTrait.set('options', [{ value: '', name: 'Select a page...' }, ...options]);
                                // Refresh traits UI if needed (GrapesJS handle this usually)
                            }
                        } catch (e) {
                            console.error('Failed to fetch pages', e);
                        }
                    }
                },
            });

            editor.Canvas.getBody().addEventListener('click', e => {
                // Allow clicks inside RTE toolbar
                if (e.target.closest('.gjs-rte-actionbar')) {
                    return;
                }

                // Catch any editor link (sidebar, navbar, buttons)
                const link = e.target.closest('a, [data-href]');
                if (!link) return;

                // Allow when Rich Text Editor is active (link editing)
                if (editor.RichTextEditor.isActive()) return;

                // Get the URL and target
                const href = link.getAttribute('href') || link.getAttribute('data-href');
                const target = link.getAttribute('target') || '_self';

                if (href && href !== '#' && href !== '') {
                    e.preventDefault();
                    e.stopPropagation();
                    
                    // Always open in new window if it's _blank, otherwise handle accordingly
                    if (target === '_blank') {
                        window.open(href, '_blank');
                    } else if (target === '_top') {
                        window.top.location.href = href;
                    } else {
                        // For 'This window' (_self), we usually don't want to navigate the editor itself
                        // but if the user insists, we could open in a new tab anyway as a safety measure
                        // or just do nothing. Given the user's request, they want it to "work".
                        window.open(href, '_blank'); 
                    }
                } else {
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
  <aside class="admin-sidebar p-3">
    <h5 class="mb-4">CMS Panel</h5>
    <ul class="nav flex-column gap-2">
      @foreach ($pages as $p)
      <li>
        <a class="nav-link text-white {{ request()->is('page/' . $p->slug) ? 'active' : '' }}"
           href="{{ url('/page/' . $p->slug) }}"
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
<section class="py-5 hero-section">
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
<section class="py-5 features-section">
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
<section class="py-5 cta-section text-center">
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
<section class="py-5 testimonials-section">
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
<section class="stats-section py-5">
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
<section class="py-5 team-section">
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
<section class="py-5 faq-section">
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
<section class="banner-section position-relative text-center" style="padding:80px 20px;">
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


            const iconBlocks = [
                {
                    id: 'icon-user',
                    label: 'Profile Icon',
                    category: 'Icons',
                    content: `
                    <a href="#" class="action-icon" title="Profile">
                        <i class="fa fa-user fs-5"></i>
                    </a>`
                },
                {
                    id: 'icon-bell',
                    label: 'Bell Icon',
                    category: 'Icons',
                    content: `
                    <a href="#" class="action-icon position-relative" title="Notifications">
                        <i class="fa fa-bell fs-5"></i>
                        <span class="position-absolute top-0 start-100 translate-middle p-1 bg-danger border border-light rounded-circle" style="width:8px;height:8px;"></span>
                    </a>`
                },
                {
                    id: 'icon-settings',
                    label: 'Settings Icon',
                    category: 'Icons',
                    content: `
                    <a href="#" class="action-icon" title="Settings">
                        <i class="fa fa-cog fs-5"></i>
                    </a>`
                },
                {
                    id: 'icon-search',
                    label: 'Search Icon',
                    category: 'Icons',
                    content: `
                    <a href="#" class="action-icon" title="Search">
                        <i class="fa fa-search fs-5"></i>
                    </a>`
                },
                {
                    id: 'navbar-action-group',
                    label: 'Navbar Icons Group',
                    category: 'Icons',
                    content: `
                    <div class="navbar-icons ms-auto position-relative">
                        <!-- Search -->
                        <div class="action-wrapper">
                            <a href="javascript:void(0)" class="action-icon search-trigger"><i class="fa fa-search fs-5"></i></a>
                        </div>
                        
                        <!-- Notifications -->
                        <div class="action-wrapper position-relative">
                            <a href="javascript:void(0)" class="action-icon notification-trigger">
                                <i class="fa fa-bell fs-5"></i>
                                <span class="position-absolute top-0 start-100 translate-middle p-1 bg-danger border border-light rounded-circle"></span>
                            </a>
                            <div class="icon-dropdown notification-menu">
                                <h6 class="fw-bold mb-3">Notifications</h6>
                                <div class="dropdown-item-custom">🚀 Welcome to your new CMS!</div>
                                <div class="dropdown-item-custom">🔔 New page "Services" created.</div>
                                <hr>
                                <div class="text-center small"><a href="#">View All</a></div>
                            </div>
                        </div>

                        <!-- Profile -->
                        <div class="action-wrapper position-relative">
                            <a href="javascript:void(0)" class="action-icon profile-trigger"><i class="fa fa-user fs-5"></i></a>
                            <div class="icon-dropdown profile-menu">
                                <h6 class="fw-bold mb-3">User Account</h6>
                                <a href="#" class="dropdown-item-custom"><i class="fa fa-user-circle"></i> My Profile</a>
                                <a href="#" class="dropdown-item-custom"><i class="fa fa-cog"></i> Settings</a>
                                <hr>
                                <a href="#" class="dropdown-item-custom text-danger"><i class="fa fa-sign-out"></i> Logout</a>
                            </div>
                        </div>

                        <!-- Global Search Overlay -->
                        <div class="search-overlay">
                            <span class="close-search">&times;</span>
                            <input type="text" class="search-input" placeholder="Type to search website...">
                            <p class="mt-3 text-muted">Press ESC to close</p>
                        </div>
                    </div>`,
                    script: function() {
                        const root = this;
                        
                        // Toggle Dropdowns
                        const setupDropdown = (triggerSelector, menuSelector) => {
                            const trigger = root.querySelector(triggerSelector);
                            const menu = root.querySelector(menuSelector);
                            if (!trigger || !menu) return;

                            trigger.addEventListener('click', (e) => {
                                e.stopPropagation();
                                const isOpen = menu.classList.contains('show');
                                root.querySelectorAll('.icon-dropdown').forEach(d => d.classList.remove('show'));
                                if (!isOpen) menu.classList.add('show');
                            });
                        };

                        setupDropdown('.notification-trigger', '.notification-menu');
                        setupDropdown('.profile-trigger', '.profile-menu');

                        // Search Overlay
                        const searchTrigger = root.querySelector('.search-trigger');
                        const searchOverlay = root.querySelector('.search-overlay');
                        const closeSearch = root.querySelector('.close-search');
                        const searchInput = root.querySelector('.search-input');

                        if (searchTrigger && searchOverlay) {
                            searchTrigger.addEventListener('click', () => {
                                searchOverlay.classList.add('show');
                                setTimeout(() => searchInput.focus(), 100);
                            });
                            closeSearch.addEventListener('click', () => searchOverlay.classList.remove('show'));
                        }

                        // Close everything when clicking outside
                        document.addEventListener('click', () => {
                            root.querySelectorAll('.icon-dropdown').forEach(d => d.classList.remove('show'));
                        });
                        
                        document.addEventListener('keydown', (e) => {
                            if (e.key === 'Escape' && searchOverlay) searchOverlay.classList.remove('show');
                        });
                    }
                }
            ];

            const professionalBlocks = [
                {
                    id: 'navbar-pro-dynamic',
                    label: 'Vimeo Style Pro Navbar',
                    category: 'Layout',
                    content: `
                    <nav class="vimeo-navbar" style="display: flex !important; flex-direction: row !important; align-items: center !important; justify-content: space-between !important; padding: 15px 40px; width: 100% !important; min-height: 70px !important; box-sizing: border-box !important;">
                        <!-- Logo -->
                        <div style="display: flex !important; align-items: center !important;">
                            <a href="#" style="font-size: 26px; font-weight: 900; text-decoration: none; letter-spacing: -1px; color: inherit;">vimeo</a>
                        </div>

                        <!-- Links Container (Strictly Horizontal) -->
                        <ul class="navbar-nav" style="display: flex !important; flex-direction: row !important; align-items: center !important; gap: 30px !important; list-style: none !important; margin: 0 !important; padding: 0 !important; color: inherit;">
                            <li class="nav-item"><a href="#" style="text-decoration: none; font-size: 15px; font-weight: 500; color: inherit;">Products</a></li>
                            <li class="nav-item"><a href="#" style="text-decoration: none; font-size: 15px; font-weight: 500; color: inherit;">Solutions</a></li>
                            <li class="nav-item"><a href="#" style="text-decoration: none; font-size: 15px; font-weight: 500; color: inherit;">Pricing</a></li>
                        </ul>

                        <!-- Right Actions -->
                        <div style="display: flex !important; flex-direction: row !important; align-items: center !important; gap: 20px !important; color: inherit;">
                            <a href="#" style="text-decoration: none; font-size: 14px; font-weight: 600; color: inherit;">Contact sales</a>
                            <a href="#" style="background: #00adef; color: #fff; padding: 10px 24px; border-radius: 30px; text-decoration: none; font-weight: 700; font-size: 14px; display: inline-flex !important; align-items: center !important; gap: 8px !important;">Join <i class="fa fa-arrow-right"></i></a>
                            
                            <!-- Static Professional Icons -->
                            <div class="navbar-icons" style="display: flex !important; flex-direction: row !important; align-items: center !important; gap: 10px !important; border-left: 1px solid rgba(0,0,0,0.1) !important; padding-left: 15px !important; margin-left: 5px !important; color: inherit;">
                                <div class="action-wrapper"><a href="javascript:void(0)" class="action-icon search-trigger" style="color: inherit;"><i class="fa fa-search"></i></a></div>
                                <div class="action-wrapper"><a href="javascript:void(0)" class="action-icon notification-trigger" style="color: inherit;"><i class="fa fa-bell"></i></a></div>
                                <div class="action-wrapper"><a href="javascript:void(0)" class="action-icon profile-trigger" style="color: inherit;"><i class="fa fa-user-circle"></i></a></div>
                            </div>
                        </div>

                        <!-- Search Box -->
                        <div class="search-overlay" style="display:none; position: fixed !important; top: 0; left: 0; width: 100%; height: 100%; background: rgba(255,255,255,0.98); z-index: 99999; display:none; align-items: center; justify-content: center; flex-direction: column;">
                            <span class="close-search" style="position: absolute; top: 30px; right: 40px; font-size: 40px; cursor: pointer; color: #333;">&times;</span>
                            <div style="width: 100%; text-align: center;">
                                <input type="text" class="search-input" placeholder="Search content..." style="width: 60%; border: none; border-bottom: 3px solid #00adef; font-size: 3rem; outline: none; padding: 15px; text-align: center; background: transparent;">
                            </div>
                        </div>
                    </nav>`,
                    script: function() {
                        const root = this;
                        const sT = root.querySelector('.search-trigger');
                        const sO = root.querySelector('.search-overlay');
                        const sC = root.querySelector('.close-search');
                        if(sT && sO) {
                            sT.addEventListener('click', (e) => { e.preventDefault(); sO.style.display = 'flex'; });
                            sC && sC.addEventListener('click', () => sO.style.display = 'none');
                        }
                    }
                },
                {
                    id: 'footer-pro-multi',
                    label: 'Premium Professional Footer',
                    category: 'Layout',
                    content: `
                    <footer class="pro-footer" style="padding: 80px 40px 40px; font-family: 'Helvetica', 'Arial', sans-serif;">
                        <!-- Top Newsletter Section -->
                        <div style="text-align: center; margin-bottom: 80px; color: inherit;">
                            <h2 style="font-size: 48px; font-weight: 300; margin-bottom: 10px; letter-spacing: -1px; color: inherit;">Are you on <i style="font-family: serif; color: inherit;">the list?</i></h2>
                            <p style="font-size: 14px; margin-bottom: 30px; color: inherit; opacity: 0.7;">Join to get exclusive offers & discounts</p>
                            <div style="display: flex; justify-content: center; gap: 10px; max-width: 500px; margin: 0 auto;">
                                <input type="email" placeholder="Email*" class="v-footer-input" style="flex: 1; outline: none;">
                                <button class="v-footer-btn">Join</button>
                            </div>
                        </div>

                        <!-- Middle Columns Section -->
                        <div style="display: flex; justify-content: space-between; gap: 40px; flex-wrap: wrap; border-top: 1px solid rgba(255,255,255,0.1); padding-top: 60px; margin-bottom: 60px; color: inherit;">
                            <!-- Column 1 -->
                            <div style="flex: 1; min-width: 150px; color: inherit;">
                                <h4 style="font-size: 14px; font-weight: 600; margin-bottom: 25px; text-transform: uppercase; color: inherit;">Shop</h4>
                                <ul style="list-style: none; padding: 0; margin: 0; color: inherit;">
                                    <li style="margin-bottom: 12px;"><a href="#" style="text-decoration: none; font-size: 13px; color: inherit; opacity: 0.7;">All Products</a></li>
                                    <li style="margin-bottom: 12px;"><a href="#" style="text-decoration: none; font-size: 13px; color: inherit; opacity: 0.7;">Best Sellers</a></li>
                                    <li style="margin-bottom: 12px;"><a href="#" style="text-decoration: none; font-size: 13px; color: inherit; opacity: 0.7;">New Arrivals</a></li>
                                </ul>
                            </div>
                            <!-- Column 2 -->
                            <div style="flex: 1; min-width: 150px; color: inherit;">
                                <h4 style="font-size: 14px; font-weight: 600; margin-bottom: 25px; text-transform: uppercase; color: inherit;">Our Store</h4>
                                <p style="font-size: 13px; line-height: 1.6; color: inherit; opacity: 0.7;">500 Terry Francois St.<br>San Francisco, CA 94158</p>
                                <p style="font-size: 13px; margin-top: 15px; color: inherit; opacity: 0.7;">Mon - Fri: 9am - 9pm<br>Sat - Sun: 10am - 8pm</p>
                            </div>
                            <!-- Column 3 -->
                            <div style="flex: 1; min-width: 150px; color: inherit;">
                                <h4 style="font-size: 14px; font-weight: 600; margin-bottom: 25px; text-transform: uppercase; color: inherit;">Policy</h4>
                                <ul style="list-style: none; padding: 0; margin: 0; color: inherit;">
                                    <li style="margin-bottom: 12px;"><a href="#" style="text-decoration: none; font-size: 13px; color: inherit; opacity: 0.7;">Shipping & Returns</a></li>
                                    <li style="margin-bottom: 12px;"><a href="#" style="text-decoration: none; font-size: 13px; color: inherit; opacity: 0.7;">Store Policy</a></li>
                                    <li style="margin-bottom: 12px;"><a href="#" style="text-decoration: none; font-size: 13px; color: inherit; opacity: 0.7;">FAQ</a></li>
                                </ul>
                            </div>
                            <!-- Column 4 -->
                            <div style="flex: 1; min-width: 150px; color: inherit;">
                                <h4 style="font-size: 14px; font-weight: 600; margin-bottom: 25px; text-transform: uppercase; color: inherit;">Customer Service</h4>
                                <p style="font-size: 13px; color: inherit; opacity: 0.7;">Tel: 123-456-7890</p>
                                <p style="font-size: 13px; color: inherit; opacity: 0.7;">Email: info@mysite.com</p>
                                <div style="display: flex; gap: 15px; margin-top: 20px; color: inherit;">
                                    <a href="#" style="color: inherit;"><i class="fab fa-instagram"></i></a>
                                    <a href="#" style="color: inherit;"><i class="fab fa-facebook"></i></a>
                                    <a href="#" style="color: inherit;"><i class="fab fa-twitter"></i></a>
                                </div>
                            </div>
                        </div>

                        <!-- Bottom Section -->
                        <div style="text-align: center; font-size: 12px; border-top: 1px solid rgba(255,255,255,0.1); padding-top: 30px; color: inherit; opacity: 0.5;">
                            &copy; 2026 by Wix. Proudly created with Agentic CRM
                        </div>
                    </footer>`
                }
            ];

            const templateBlocks = [
                {
                    id: 'tpl-wix-fashion-home',
                    label: 'Premium Fashion Store 1905',
                    category: 'Templates',
                    content: `
                    <div class="luxury-home-wrapper" style="background: #fff; color: #000; font-family: 'Inter', sans-serif; scroll-behavior: smooth;">
                        <!-- Minimalist Navbar -->
                        <header style="padding: 25px 50px; display: flex; justify-content: space-between; align-items: center; position: sticky; top: 0; background: rgba(255,255,255,0.98); z-index: 1000; border-bottom: 1px solid #f2f2f2; backdrop-filter: blur(10px);">
                            <div id="nav-logo">
                                <h2 class="editable-text" style="font-weight: 900; letter-spacing: 5px; margin: 0; font-size: 22px; text-transform: uppercase;">M O D E R N E</h2>
                            </div>
                            <nav style="display: flex; gap: 40px; align-items: center;">
                                <a href="#hero" style="text-decoration: none; color: #000; font-size: 13px; font-weight: 600; text-transform: uppercase; letter-spacing: 2px;">Home</a>
                                <a href="#collections" style="text-decoration: none; color: #000; font-size: 13px; font-weight: 600; text-transform: uppercase; letter-spacing: 2px;">Collections</a>
                                <a href="#featured" style="text-decoration: none; color: #000; font-size: 13px; font-weight: 600; text-transform: uppercase; letter-spacing: 2px;">New Arrivals</a>
                                <a href="#story" style="text-decoration: none; color: #000; font-size: 13px; font-weight: 600; text-transform: uppercase; letter-spacing: 2px;">Our Story</a>
                                <div style="display: flex; gap: 20px; margin-left: 20px; border-left: 1px solid #ddd; padding-left: 20px;">
                                    <a href="#" style="color: #000;"><i class="fa fa-search"></i></a>
                                    <a href="#" style="color: #000; position: relative;"><i class="fa fa-shopping-bag"></i><span style="position: absolute; top: -8px; right: -10px; background: #000; color: #fff; font-size: 9px; padding: 2px 5px; border-radius: 50%;">0</span></a>
                                </div>
                            </nav>
                        </header>

                        <!-- Cinematic Hero Section -->
                        <section id="hero" class="luxury-hero" style="height: 90vh; background: #f9f9f9; display: flex; align-items: center; justify-content: center; position: relative; overflow: hidden;">
                            <img src="https://images.unsplash.com/photo-1490481651871-ab68de25d43d?auto=format&fit=crop&w=1920" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; object-fit: cover; opacity: 0.9; z-index: 1;">
                            <div class="container" style="max-width: 1200px; margin: 0 auto; text-align: center; position: relative; z-index: 2;">
                                <p class="luxury-subtitle editable-text" style="margin-bottom: 25px; letter-spacing: 8px; font-weight: 400; font-size: 14px; text-transform: uppercase;">Spring Summer 2026</p>
                                <h1 class="luxury-title editable-text" style="margin-bottom: 50px; font-size: 6rem; font-weight: 900; line-height: 0.8; letter-spacing: -3px;">ELEVATION<br>OF FORM</h1>
                                <a href="#collections" class="luxury-button editable-text" style="padding: 20px 60px; font-size: 14px; border: 2px solid #000;">View Lookbook</a>
                            </div>
                        </section>

                        <!-- Large Collection Grid -->
                        <section id="collections" style="padding: 120px 50px; background: #fff;">
                            <div class="row g-5">
                                <div class="col-md-6">
                                    <div class="collection-item" style="height: 700px; position: relative; overflow: hidden;">
                                        <img src="https://images.unsplash.com/photo-1483985988355-763728e1935b?auto=format&fit=crop&w=1000" class="collection-image" style="width: 100%; height: 100%; object-fit: cover; transition: 0.8s transform ease;">
                                        <div style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.15);"></div>
                                        <div style="position: absolute; bottom: 50px; left: 50px;">
                                            <h3 class="editable-text" style="color: #fff; font-size: 3rem; font-weight: 800; text-transform: uppercase; margin-bottom: 15px;">Outerwear</h3>
                                            <a href="#" class="editable-text" style="color: #fff; text-transform: uppercase; letter-spacing: 3px; font-weight: 700; text-decoration: none; border-bottom: 2px solid #fff; padding-bottom: 5px;">Discover</a>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="collection-item" style="height: 700px; position: relative; overflow: hidden; margin-top: 100px;">
                                        <img src="https://images.unsplash.com/photo-1445205170230-053b830c6050?auto=format&fit=crop&w=1000" class="collection-image" style="width: 100%; height: 100%; object-fit: cover; transition: 0.8s transform ease;">
                                        <div style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.15);"></div>
                                        <div style="position: absolute; bottom: 50px; left: 50px;">
                                            <h3 class="editable-text" style="color: #fff; font-size: 3rem; font-weight: 800; text-transform: uppercase; margin-bottom: 15px;">Essentials</h3>
                                            <a href="#" class="editable-text" style="color: #fff; text-transform: uppercase; letter-spacing: 3px; font-weight: 700; text-decoration: none; border-bottom: 2px solid #fff; padding-bottom: 5px;">Discover</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </section>

                        <!-- Editorial Story Section -->
                        <section id="story" style="padding: 140px 50px; background: #000; color: #fff;">
                            <div class="container" style="max-width: 900px; margin: 0 auto; text-align: center;">
                                <p class="editable-text" style="text-transform: uppercase; letter-spacing: 5px; font-size: 13px; margin-bottom: 40px; opacity: 0.6;">The Philosophy</p>
                                <h1 class="editable-text" style="font-size: 3.5rem; font-weight: 300; line-height: 1.3; margin-bottom: 50px;">"We believe in the power of minimalism to create timeless silhouettes that speak louder than excess."</h1>
                                <div style="width: 80px; height: 1px; background: #fff; margin: 0 auto 50px;"></div>
                                <p class="editable-text" style="font-size: 1.1rem; opacity: 0.5; text-transform: uppercase; letter-spacing: 4px;">Designed in Paris • Crafted Globally</p>
                            </div>
                        </section>

                        <!-- Featured Products -->
                        <section id="featured" style="padding: 120px 50px; background: #fff;">
                            <div class="container" style="max-width: 1400px; margin: 0 auto;">
                                <div style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 80px; border-bottom: 1px solid #eee; padding-bottom: 30px;">
                                    <div>
                                        <p class="editable-text" style="text-transform: uppercase; letter-spacing: 3px; font-size: 12px; margin-bottom: 15px; color: #999;">Curated Selections</p>
                                        <h2 class="editable-text" style="font-size: 2.5rem; font-weight: 900; text-transform: uppercase; margin: 0; letter-spacing: -1px;">New Arrivals</h2>
                                    </div>
                                    <a href="#" class="editable-text" style="color: #000; text-transform: uppercase; font-weight: 700; font-size: 13px; letter-spacing: 2px; text-decoration: none; border: 1px solid #000; padding: 12px 30px;">Shop Full Collection</a>
                                </div>
                                <div class="row g-4">
                                    <div class="col-md-3">
                                        <div style="text-align: left; group" class="product-card">
                                            <div style="height: 480px; background: #f5f5f5; margin-bottom: 25px; overflow: hidden; position: relative;">
                                                <img src="https://images.unsplash.com/photo-1515886657613-9f3515b0c78f?auto=format&fit=crop&w=600" style="width: 100%; height: 100%; object-fit: cover;">
                                                <div style="position: absolute; bottom: 0; width: 100%; background: #000; color: #fff; text-align: center; padding: 15px; transform: translateY(100%); transition: 0.3s; opacity: 0;" class="quick-add">Quick Add +</div>
                                            </div>
                                            <h4 class="editable-text" style="font-size: 14px; font-weight: 700; text-transform: uppercase; margin-bottom: 8px; letter-spacing: 1px;">Structured Wool Coat</h4>
                                            <p class="editable-text" style="opacity: 0.5; font-size: 14px; font-weight: 600;">$295.00</p>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div style="text-align: left;" class="product-card">
                                            <div style="height: 480px; background: #f5f5f5; margin-bottom: 25px; overflow: hidden;">
                                                <img src="https://images.unsplash.com/photo-1539109132382-3bf1551875e6?auto=format&fit=crop&w=600" style="width: 100%; height: 100%; object-fit: cover;">
                                            </div>
                                            <h4 class="editable-text" style="font-size: 14px; font-weight: 700; text-transform: uppercase; margin-bottom: 8px; letter-spacing: 1px;">Satin Slip Dress</h4>
                                            <p class="editable-text" style="opacity: 0.5; font-size: 14px; font-weight: 600;">$180.00</p>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div style="text-align: left;" class="product-card">
                                            <div style="height: 480px; background: #f5f5f5; margin-bottom: 25px; overflow: hidden;">
                                                <img src="https://images.unsplash.com/photo-1550630982-bd6197ce53ce?auto=format&fit=crop&w=600" style="width: 100%; height: 100%; object-fit: cover;">
                                            </div>
                                            <h4 class="editable-text" style="font-size: 14px; font-weight: 700; text-transform: uppercase; margin-bottom: 8px; letter-spacing: 1px;">Draped Knit Top</h4>
                                            <p class="editable-text" style="opacity: 0.5; font-size: 14px; font-weight: 600;">$125.00</p>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div style="text-align: left;" class="product-card">
                                            <div style="height: 480px; background: #f5f5f5; margin-bottom: 25px; overflow: hidden;">
                                                <img src="https://images.unsplash.com/photo-1594633312681-425c7b97ccd1?auto=format&fit=crop&w=600" style="width: 100%; height: 100%; object-fit: cover;">
                                            </div>
                                            <h4 class="editable-text" style="font-size: 14px; font-weight: 700; text-transform: uppercase; margin-bottom: 8px; letter-spacing: 1px;">Classic Tailored Trousers</h4>
                                            <p class="editable-text" style="opacity: 0.5; font-size: 14px; font-weight: 600;">$210.00</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </section>

                        <!-- Minimal Footer -->
                        <footer style="padding: 100px 50px; background: #f9f9f9; border-top: 1px solid #eee;">
                            <div class="row">
                                <div class="col-md-4">
                                    <h3 style="font-weight: 900; letter-spacing: 5px; text-transform: uppercase; font-size: 18px; margin-bottom: 30px;">MODERNE</h3>
                                    <p style="font-size: 13px; line-height: 2; opacity: 0.6; max-width: 300px;">Redefining the modern wardrobe with a focus on quality, comfort, and sustainable minimalism.</p>
                                </div>
                                <div class="col-md-2">
                                    <h4 style="font-size: 12px; font-weight: 800; text-transform: uppercase; letter-spacing: 2px; margin-bottom: 25px;">Support</h4>
                                    <ul style="list-style: none; padding: 0; font-size: 13px; line-height: 2.5; opacity: 0.7;">
                                        <li><a href="#" style="color: #000; text-decoration: none;">Shipping</a></li>
                                        <li><a href="#" style="color: #000; text-decoration: none;">Returns</a></li>
                                        <li><a href="#" style="color: #000; text-decoration: none;">FAQ</a></li>
                                    </ul>
                                </div>
                                <div class="col-md-2">
                                    <h4 style="font-size: 12px; font-weight: 800; text-transform: uppercase; letter-spacing: 2px; margin-bottom: 25px;">Company</h4>
                                    <ul style="list-style: none; padding: 0; font-size: 13px; line-height: 2.5; opacity: 0.7;">
                                        <li><a href="#" style="color: #000; text-decoration: none;">About</a></li>
                                        <li><a href="#" style="color: #000; text-decoration: none;">Sustainability</a></li>
                                        <li><a href="#" style="color: #000; text-decoration: none;">Careers</a></li>
                                    </ul>
                                </div>
                                <div class="col-md-4">
                                    <h4 style="font-size: 12px; font-weight: 800; text-transform: uppercase; letter-spacing: 2px; margin-bottom: 25px;">Newsletter</h4>
                                    <div style="display: flex; border-bottom: 1px solid #000; padding: 5px 0;">
                                        <input type="email" placeholder="Email Address" style="border: none; background: transparent; flex: 1; outline: none; font-size: 13px;">
                                        <button style="border: none; background: transparent; font-weight: 800; font-size: 11px; text-transform: uppercase; letter-spacing: 1px; cursor: pointer;">Join</button>
                                    </div>
                                    <div style="display: flex; gap: 20px; margin-top: 40px;">
                                        <a href="#" style="color: #000;"><i class="fa fa-instagram"></i></a>
                                        <a href="#" style="color: #000;"><i class="fa fa-facebook"></i></a>
                                        <a href="#" style="color: #000;"><i class="fa fa-pinterest"></i></a>
                                    </div>
                                </div>
                            </div>
                            <div style="margin-top: 100px; padding-top: 30px; border-top: 1px solid #eee; text-align: center; font-size: 11px; opacity: 0.4; letter-spacing: 2px; text-transform: uppercase;">
                                &copy; 2026 MODERNE STUDIO. ALL RIGHTS RESERVED.
                            </div>
                        </footer>
                    </div>`
                },
                {
                    id: 'tpl-infinite-cloud',
                    label: 'Infinite Cloud Tech',
                    category: 'Templates',
                    content: `
                    <div class="infinite-cloud-wrapper" style="background: #020617; color: #94a3b8; font-family: 'Inter', sans-serif;">
                        <!-- Hero -->
                        <section class="cloud-hero text-center">
                            <div class="container">
                                <span style="background: rgba(99,102,241,0.1); color: #818cf8; padding: 8px 16px; border-radius: 30px; font-size: 14px; font-weight: 600; margin-bottom: 24px; display: inline-block;">Infrastructure for the future</span>
                                <h1 class="display-3 fw-bold text-white mb-4 editable-text" style="letter-spacing: -1px;">Scale to infinity with Cloud Tech</h1>
                                <p class="lead mb-5 mx-auto editable-text" style="max-width: 650px; opacity: 0.8;">The unified platform for compute, storage, and networking. Deployed at the edge for millisecond latency worldwide.</p>
                                <div class="d-flex justify-content-center gap-3">
                                    <a href="#" class="cloud-btn-primary editable-text">Deploy Now</a>
                                    <a href="#" style="color: #fff; text-decoration: none; padding: 14px 32px; font-weight: 600;">Contact Sales</a>
                                </div>
                            </div>
                        </section>

                        <!-- Bento Grid -->
                        <section style="padding: 100px 0;">
                            <div class="container">
                                <div class="row g-4">
                                    <div class="col-lg-7">
                                        <div class="cloud-card h-100">
                                            <div class="cloud-feature-icon"><i class="fa fa-bolt"></i></div>
                                            <h3 class="text-white fw-bold mb-3 editable-text">Edge Computing</h3>
                                            <p class="editable-text">Run your code closer to your users. Reduce latency by 90% compared to traditional cloud providers.</p>
                                            <div class="mt-4 p-4 rounded-4" style="background: rgba(0,0,0,0.2); border: 1px solid rgba(255,255,255,0.05);">
                                                <div style="font-family: monospace; font-size: 14px; color: #818cf8;">$ deploy --region global</div>
                                                <div style="font-family: monospace; font-size: 14px; color: #4ade80;">✔ Deployment successful in 1.2s</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-5">
                                        <div class="cloud-card h-100 shadow-sm">
                                            <div class="cloud-feature-icon"><i class="fa fa-hdd-o"></i></div>
                                            <h3 class="text-white fw-bold mb-3 editable-text">Global Storage</h3>
                                            <p class="editable-text">S3 compatible storage with automatic replication across 50+ data centers.</p>
                                        </div>
                                    </div>
                                    <div class="col-lg-4">
                                        <div class="cloud-card">
                                            <div class="cloud-feature-icon"><i class="fa fa-shield"></i></div>
                                            <h4 class="text-white fw-bold mb-3 editable-text">Advanced Security</h4>
                                            <p class="small editable-text">DDoS protection and automated SSL certificate management included.</p>
                                        </div>
                                    </div>
                                    <div class="col-lg-8">
                                        <div class="cloud-card h-100 d-flex align-items-center justify-content-between">
                                            <div>
                                                <h4 class="text-white fw-bold mb-3 editable-text">Ready to experience the speed?</h4>
                                                <p class="mb-0 editable-text">Join 10,000+ companies scaling with Cloud Tech.</p>
                                            </div>
                                            <a href="#" class="cloud-btn-primary">Get Started</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </section>

                        <!-- Footer -->
                        <footer style="padding: 60px 0; border-top: 1px solid rgba(255,255,255,0.05);">
                            <div class="container">
                                <div class="row align-items-center">
                                    <div class="col-md-6">
                                        <p class="mb-0">&copy; 2026 Cloud Tech Infrastructure. Built for Performance.</p>
                                    </div>
                                    <div class="col-md-6 text-md-end">
                                        <div class="d-flex gap-4 justify-content-md-end">
                                            <a href="#" style="color: inherit; text-decoration: none;">Status</a>
                                            <a href="#" style="color: inherit; text-decoration: none;">Docs</a>
                                            <a href="#" style="color: inherit; text-decoration: none;">Terms</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </footer>
                    </div>`
                },
                {
                    id: 'tpl-wix-luxury-hero',
                    label: 'Wix Luxury Hero (Modular)',
                    category: 'Templates',
                    content: `
                    <section class="luxury-hero">
                        <div class="container" style="max-width: 1200px; margin: 0 auto; text-align: center;">
                            <p class="luxury-subtitle editable-text" style="margin-bottom: 20px;">Premium Selection</p>
                            <h1 class="luxury-title editable-text" style="margin-bottom: 40px;">ELEVATE YOUR<br>STYLE</h1>
                            <a href="#" class="luxury-button editable-text">Explore Collection</a>
                        </div>
                    </section>`
                },
                {
                    id: 'tpl-wix-collection-grid',
                    label: 'Wix Collection Grid (Modular)',
                    category: 'Templates',
                    content: `
                    <section style="padding: 80px 40px; background: #fff;">
                        <div class="collection-grid">
                            <div class="collection-item">
                                <img src="https://images.unsplash.com/photo-1549298916-b41d501d3772?auto=format&fit=crop&w=800" class="collection-image">
                                <div style="position: absolute; bottom: 30px; left: 30px;">
                                    <h3 class="editable-text" style="color: #fff; font-size: 2rem; font-weight: 800; text-transform: uppercase;">SHOES</h3>
                                    <a href="#" class="editable-text" style="color: #fff; text-transform: uppercase; letter-spacing: 2px; font-weight: 700;">Shop Now</a>
                                </div>
                            </div>
                            <div class="collection-item">
                                <img src="https://images.unsplash.com/photo-1491553895911-0055eca6402d?auto=format&fit=crop&w=800" class="collection-image">
                                <div style="position: absolute; bottom: 30px; left: 30px;">
                                    <h3 class="editable-text" style="color: #fff; font-size: 2rem; font-weight: 800; text-transform: uppercase;">SNEAKERS</h3>
                                    <a href="#" class="editable-text" style="color: #fff; text-transform: uppercase; letter-spacing: 2px; font-weight: 700;">Shop Now</a>
                                </div>
                            </div>
                        </div>
                    </section>`
                },
                {
                    id: 'tpl-pro-master-dashboard',
                    label: 'Pro Dashboard Master',
                    category: 'Templates',
                    content: `
                    <div class="pro-master-shell">
                        <!-- Navbar -->
                        <nav style="height: 70px; background: #fff; border-bottom: 1px solid #e2e8f0; display: flex; align-items: center; justify-content: space-between; padding: 0 30px;">
                            <div style="display: flex; align-items: center; gap: 10px;">
                                <div style="width: 32px; height: 32px; background: #6366f1; border-radius: 8px;"></div>
                                <span style="font-weight: 800; font-size: 20px; letter-spacing: -0.5px; color: #0f172a;">ELITE CRM</span>
                            </div>
                            <div style="display: flex; align-items: center; gap: 20px;">
                                <div style="position: relative;">
                                    <i class="fa fa-bell-o" style="font-size: 18px; color: #64748b;"></i>
                                    <span style="position: absolute; top: -5px; right: -5px; width: 8px; height: 8px; background: #ef4444; border-radius: 50%; border: 2px solid #fff;"></span>
                                </div>
                                <img src="https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?auto=format&fit=crop&w=32&h=32" style="border-radius: 50%;" alt="User">
                            </div>
                        </nav>

                        <div class="pro-content-stack">
                            <!-- Sidebar -->
                            <aside class="master-sidebar">
                                <div style="padding: 30px 20px;">
                                    <nav style="display: flex; flex-direction: column; gap: 5px;">
                                        <a href="#" class="dashboard-nav-link active"><i class="fa fa-th-large"></i> Dashboard</a>
                                        <a href="#" class="dashboard-nav-link"><i class="fa fa-users"></i> Customers</a>
                                        <a href="#" class="dashboard-nav-link"><i class="fa fa-rocket"></i> Projects</a>
                                        <a href="#" class="dashboard-nav-link"><i class="fa fa-bar-chart"></i> Analytics</a>
                                        <div style="margin: 20px 0; height: 1px; background: rgba(255,255,255,0.05);"></div>
                                        <a href="#" class="dashboard-nav-link"><i class="fa fa-cog"></i> Settings</a>
                                        <a href="#" class="dashboard-nav-link"><i class="fa fa-question-circle-o"></i> Help Center</a>
                                    </nav>
                                </div>
                                <div style="margin-top: auto; padding: 20px;">
                                    <div style="background: rgba(99, 102, 241, 0.1); border-radius: 12px; padding: 15px; border: 1px solid rgba(99, 102, 241, 0.2);">
                                        <p style="font-size: 12px; color: #818cf8; font-weight: 600; margin-bottom: 5px;">PRO PLAN</p>
                                        <p style="font-size: 11px; color: rgba(255,255,255,0.6); margin-bottom: 12px;">Get unlimited access to all elite features.</p>
                                        <a href="#" style="font-size: 11px; color: #fff; text-decoration: none; font-weight: 600;">Upgrade Now →</a>
                                    </div>
                                </div>
                            </aside>

                            <!-- Main Content -->
                            <main class="master-main">
                                <div style="flex: 1; padding: 40px;">
                                    <div style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 40px;">
                                        <div>
                                            <h1 class="editable-text" style="font-size: 28px; font-weight: 800; color: #0f172a; margin-bottom: 5px;">Welcome back, Alex</h1>
                                            <p class="editable-text" style="color: #64748b;">Here is what's happening with your business today.</p>
                                        </div>
                                        <button class="btn btn-primary" style="background: #6366f1; border: none; padding: 10px 20px; border-radius: 8px; font-weight: 600;">Download Report</button>
                                    </div>

                                    <!-- Stats Grid -->
                                    <div class="row g-4 mb-5">
                                        <div class="col-md-4">
                                            <div style="background: #fff; border: 1px solid #e2e8f0; border-radius: 16px; padding: 25px;">
                                                <p style="font-size: 14px; color: #64748b; margin-bottom: 10px;">Total Revenue</p>
                                                <h3 style="font-size: 24px; font-weight: 800; color: #0f172a;">$124,592.00</h3>
                                                <p style="font-size: 12px; color: #22c55e; margin-top: 5px;">+14.5% from last month</p>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div style="background: #fff; border: 1px solid #e2e8f0; border-radius: 16px; padding: 25px;">
                                                <p style="font-size: 14px; color: #64748b; margin-bottom: 10px;">Active Clients</p>
                                                <h3 style="font-size: 24px; font-weight: 800; color: #0f172a;">1,284</h3>
                                                <p style="font-size: 12px; color: #22c55e; margin-top: 5px;">+5.2% from last week</p>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div style="background: #fff; border: 1px solid #e2e8f0; border-radius: 16px; padding: 25px;">
                                                <p style="font-size: 14px; color: #64748b; margin-bottom: 10px;">Retention Rate</p>
                                                <h3 style="font-size: 24px; font-weight: 800; color: #0f172a;">98.2%</h3>
                                                <p style="font-size: 12px; color: #f59e0b; margin-top: 5px;">-0.4% from yesterday</p>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Placeholder for deeper content -->
                                    <div style="background: #f1f5f9; border: 2px dashed #cbd5e1; border-radius: 20px; height: 300px; display: flex; align-items: center; justify-content: center; color: #94a3b8;">
                                        [ Dashboard Content Area ]
                                    </div>
                                </div>

                                <!-- Integrated Footer -->
                                <footer style="background: #f8fafc; border-top: 1px solid #e2e8f0; padding: 40px 60px;">
                                    <div style="display: flex; justify-content: space-between; align-items: center;">
                                        <div style="display: flex; align-items: center; gap: 10px;">
                                            <div style="width: 24px; height: 24px; background: #6366f1; border-radius: 6px;"></div>
                                            <span style="font-weight: 800; font-size: 14px; color: #0f172a;">ELITE</span>
                                        </div>
                                        <div style="display: flex; gap: 30px;">
                                            <a href="#" style="font-size: 13px; color: #64748b; text-decoration: none;">Privacy Policy</a>
                                            <a href="#" style="font-size: 13px; color: #64748b; text-decoration: none;">Terms of Service</a>
                                            <a href="#" style="font-size: 13px; color: #64748b; text-decoration: none;">Support</a>
                                        </div>
                                        <p style="font-size: 13px; color: #94a3b8; margin: 0;">&copy; 2026 Elite Infrastructure.</p>
                                    </div>
                                </footer>
                            </main>
                        </div>
                    </div>`
                },
                {
                    id: 'tpl-life-coach-johnson',
                    label: 'Personal Life Coach (Allan)',
                    category: 'Templates',
                    content: `
                    <div id="life-coach-root" class="coach-full-wrapper" style="background: #fff; font-family: 'Montserrat', sans-serif; scroll-behavior: smooth;">
                        <!-- Header -->
                        <header style="position: sticky; top: 0; background: #fff; z-index: 1000; border-bottom: 1px solid #f0f0f0;">
                            <div class="container" style="max-width: 1400px; padding: 30px 40px; display: flex; justify-content: space-between; align-items: center;">
                                <div id="home">
                                    <h2 class="editable-text" style="font-weight: 900; letter-spacing: 2px; margin: 0; font-size: 24px;">ALLAN JOHNSON</h2>
                                    <p class="editable-text" style="font-size: 11px; letter-spacing: 4px; color: #666; margin: 0; text-transform: uppercase; font-weight: 600;">Personal Life Coach</p>
                                </div>
                                <nav style="display: flex; gap: 40px; align-items: center;">
                                    <a href="#home" style="text-decoration: none; color: #000; font-size: 14px; font-weight: 500;">Home</a>
                                    <a href="#about" style="text-decoration: none; color: #000; font-size: 14px; font-weight: 500;">About</a>
                                    <a href="#services" style="text-decoration: none; color: #000; font-size: 14px; font-weight: 500;">Services</a>
                                    <a href="#contact" style="text-decoration: none; color: #000; font-size: 14px; font-weight: 500;">Contact</a>
                                    <a href="#" style="text-decoration: none; color: #000; font-size: 14px; display: flex; align-items: center; gap: 8px; font-weight: 600;">
                                        <i class="fa fa-user-circle-o" style="font-size: 20px; color: #56c2c1;"></i> Log In
                                    </a>
                                </nav>
                            </div>
                        </header>

                        <!-- Hero Section -->
                        <section class="coach-hero" style="min-height: 90vh;">
                            <div class="container" style="max-width: 1200px;">
                                <p class="editable-text" style="text-transform: uppercase; letter-spacing: 6px; font-weight: 700; margin-bottom: 25px; font-size: 14px;">Ambition is the first step towards</p>
                                <h1 class="coach-title editable-text" style="margin-bottom: 35px; font-size: 8rem;">SUCCESS</h1>
                                <p class="editable-text" style="font-size: 1.8rem; margin-bottom: 50px; font-weight: 300; opacity: 0.9;">Now Available for Online Coaching</p>
                                <a href="#services" class="btn btn-dark" style="background: #000; padding: 18px 50px; border-radius: 0; font-weight: 800; text-transform: uppercase; letter-spacing: 3px; border: none; font-size: 14px;">Book Now</a>
                            </div>
                        </section>

                        <!-- About Section -->
                        <section id="about" style="padding: 140px 0;">
                            <div class="container" style="max-width: 1200px;">
                                <div class="row g-0 align-items-center">
                                    <div class="col-md-6" style="padding-right: 100px;">
                                        <h2 class="editable-text" style="font-size: 4.5rem; font-weight: 950; margin-bottom: 50px; line-height: 0.9;">ABOUT<br>ME</h2>
                                        <p class="editable-text" style="font-size: 1.15rem; line-height: 1.9; color: #333; margin-bottom: 50px; font-weight: 400;">
                                            I'm a personal life coach dedicated to helping individuals unlock their potential and achieve their greatest goals. With years of experience in psychology and motivational speaking, I provide a roadmap for your personal and professional evolution. 
                                        </p>
                                        <div class="d-flex gap-4">
                                            <a href="#contact" class="coach-btn editable-text" style="padding: 15px 45px;">Read More</a>
                                            <a href="#contact" class="btn btn-outline-dark rounded-0 px-5 py-3 editable-text" style="font-weight: 700; text-transform: uppercase; letter-spacing: 1px;">Free Consultation</a>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div style="position: relative;">
                                            <div style="position: absolute; top: -30px; left: -30px; width: 100%; height: 100%; border: 2px solid #000; z-index: 0;"></div>
                                            <img src="https://images.unsplash.com/photo-1560250097-0b93528c311a?auto=format&fit=crop&w=1000" alt="Professional Coach" class="img-fluid" style="position: relative; z-index: 1;">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </section>

                        <!-- Services Section (Asymmetric Grid) -->
                        <section id="services" style="padding: 120px 0; background: #eaf6f6;">
                            <div class="container" style="max-width: 1200px;">
                                <h2 class="text-center editable-text" style="font-size: 4rem; font-weight: 950; margin-bottom: 100px; text-transform: uppercase; letter-spacing: -2px;">How I can help you</h2>
                                <div class="row g-5">
                                    <!-- Box 1 (4 columns) -->
                                    <div class="col-lg-4">
                                        <div class="coach-service-card" style="background: #fff; border-width: 3px;">
                                            <h3 class="editable-text" style="font-weight: 900; margin-bottom: 25px; text-transform: uppercase; font-size: 1.8rem;">Free<br>Consultation</h3>
                                            <div style="height: 3px; background: #000; width: 80px; margin: 35px auto;"></div>
                                            <p class="editable-text" style="margin-bottom: 35px; opacity: 0.8; font-size: 0.95rem;">Kickstart your journey with a zero-obligations discovery call.</p>
                                            <p class="editable-text" style="font-weight: 700; margin-bottom: 45px; font-size: 1.1rem;">45 min</p>
                                            <a href="#contact" class="btn btn-dark rounded-0 px-5 py-2 fw-bold" style="letter-spacing: 1px;">Book It</a>
                                        </div>
                                    </div>
                                    <!-- Box 2 (Prominent - 5 columns) -->
                                    <div class="col-lg-5">
                                        <div class="coach-service-card" style="background: #fff; border-width: 3px; padding: 60px 40px;">
                                            <span style="background: #000; color: #fff; padding: 5px 15px; font-size: 10px; font-weight: 800; text-transform: uppercase; margin-bottom: 20px; display: inline-block;">Most Popular</span>
                                            <h3 class="editable-text" style="font-weight: 900; margin-bottom: 25px; text-transform: uppercase; font-size: 2.5rem;">Online<br>Coaching</h3>
                                            <div style="height: 3px; background: #56c2c1; width: 100px; margin: 35px auto;"></div>
                                            <p class="editable-text" style="margin-bottom: 35px; opacity: 0.8; font-size: 1rem; line-height: 1.6;">Personalized one-on-one sessions tailored specifically to your needs and current life challenges.</p>
                                            <div style="margin-bottom: 45px;">
                                                <p class="editable-text" style="font-weight: 600; margin-bottom: 0px;">1 hr Sessions</p>
                                                <p class="editable-text" style="font-weight: 900; font-size: 2rem; color: #56c2c1;">$70</p>
                                            </div>
                                            <a href="#contact" class="btn btn-dark rounded-0 w-100 py-3 fw-bold" style="font-size: 1.1rem; letter-spacing: 2px;">SECURE YOUR SPOT</a>
                                        </div>
                                    </div>
                                    <!-- Box 3 (3 columns) -->
                                    <div class="col-lg-3">
                                        <div class="coach-service-card" style="background: #fff; border-width: 3px;">
                                            <h3 class="editable-text" style="font-weight: 900; margin-bottom: 25px; text-transform: uppercase; font-size: 1.5rem;">Group<br>Workshop</h3>
                                            <div style="height: 3px; background: #000; width: 60px; margin: 35px auto;"></div>
                                            <p class="editable-text" style="margin-bottom: 35px; opacity: 0.8; font-size: 0.9rem;">Interactive group dynamics to foster communal growth.</p>
                                            <p class="editable-text" style="font-weight: 700; margin-bottom: 10px; font-size: 1rem;">Every Tue, Wed</p>
                                            <p class="editable-text" style="font-weight: 900; font-size: 1.5rem; color: #000;">$90</p>
                                            <a href="#contact" class="btn btn-dark rounded-0 px-4 py-2 fw-bold">Join Group</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </section>

                        <!-- Contact Section (Modern Form) -->
                        <section id="contact" class="coach-contact-section" style="padding: 140px 0;">
                            <div class="container" style="max-width: 1200px;">
                                <div class="row g-5">
                                    <div class="col-md-5">
                                        <div style="padding-right: 40px;">
                                            <h2 class="editable-text" style="font-weight: 950; text-transform: uppercase; letter-spacing: -2px; margin-bottom: 50px; font-size: 4rem; line-height: 1;">LETS<br>TALK.</h2>
                                            <div style="margin-bottom: 50px;">
                                                <p class="editable-text" style="font-weight: 700; margin-bottom: 10px; font-size: 1.2rem;">500 Terry Francine Street,</p>
                                                <p class="editable-text" style="font-weight: 700; margin-bottom: 40px; font-size: 1.2rem;">San Francisco, CA 94158</p>
                                                <p class="editable-text" style="margin-bottom: 8px; font-weight: 500;">Tel: 123-456-7890</p>
                                                <p class="editable-text" style="margin-bottom: 40px; font-weight: 500;">Email: info@mysite.com</p>
                                            </div>
                                            <div class="d-flex gap-4 mb-5" style="font-size: 20px;">
                                                <a href="#" style="color: #000;"><i class="fa fa-facebook"></i></a>
                                                <a href="#" style="color: #000;"><i class="fa fa-instagram"></i></a>
                                                <a href="#" style="color: #000;"><i class="fa fa-twitter"></i></a>
                                                <a href="#" style="color: #000;"><i class="fa fa-linkedin"></i></a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-7">
                                        <div style="background: rgba(0,0,0,0.03); padding: 60px; border: 1px solid #000;">
                                            <form>
                                                <div class="row g-4">
                                                    <div class="col-md-6">
                                                        <label style="font-size: 11px; font-weight: 800; text-transform: uppercase; margin-bottom: 10px; display: block;">First Name *</label>
                                                        <input type="text" class="coach-input" style="border-width: 0 0 2px 0; border-radius: 0; padding: 10px 0; background: transparent;">
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label style="font-size: 11px; font-weight: 800; text-transform: uppercase; margin-bottom: 10px; display: block;">Last Name *</label>
                                                        <input type="text" class="coach-input" style="border-width: 0 0 2px 0; border-radius: 0; padding: 10px 0; background: transparent;">
                                                    </div>
                                                    <div class="col-12">
                                                        <label style="font-size: 11px; font-weight: 800; text-transform: uppercase; margin-bottom: 10px; display: block;">Email Address *</label>
                                                        <input type="email" class="coach-input" style="border-width: 0 0 2px 0; border-radius: 0; padding: 10px 0; background: transparent;">
                                                    </div>
                                                    <div class="col-12">
                                                        <label style="font-size: 11px; font-weight: 800; text-transform: uppercase; margin-bottom: 10px; display: block;">Your Message *</label>
                                                        <textarea class="coach-input" style="height: 120px; border-width: 0 0 2px 0; border-radius: 0; padding: 10px 0; background: transparent;"></textarea>
                                                    </div>
                                                </div>
                                                <div class="mt-5">
                                                    <button type="submit" class="btn btn-dark w-100 py-3 fw-bold" style="border-radius: 0; text-transform: uppercase; letter-spacing: 4px; font-size: 16px;">Send Message</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </section>

                        <!-- Footer Links -->
                        <footer style="padding: 40px 0; background: #000; color: #fff; text-align: center;">
                            <div class="container">
                                <p style="font-size: 11px; letter-spacing: 3px; opacity: 0.6; margin: 0;">&copy; 2026 BY ALLAN JOHNSON. PROUDLY CREATED WITH AGENTIC CRM</p>
                            </div>
                        </footer>
                    </div>`
                },
                {
                    id: 'tpl-life-coach-zen',
                    label: 'Zen Transformation Coach (Home Page)',
                    category: 'Templates',
                    content: `
                    <div id="zen-root" class="coach-full-wrapper" style="background: #fdfaf7; font-family: 'Outfit', sans-serif; color: #4a4a4a; scroll-behavior: smooth;">
                        <!-- Organic Nav -->
                        <header style="padding: 30px 60px; display: flex; justify-content: space-between; align-items: center; position: sticky; top: 0; background: rgba(253, 250, 247, 0.95); backdrop-filter: blur(10px); z-index: 1000;">
                            <div id="zen-home">
                                <h2 style="font-weight: 300; letter-spacing: 5px; margin: 0; color: #7c9473;">SAGE WELLNESS</h2>
                            </div>
                            <nav style="display: flex; gap: 40px;">
                                <a href="#zen-home" style="text-decoration: none; color: inherit; font-size: 13px; letter-spacing: 2px;">GROUNDING</a>
                                <a href="#zen-journey" style="text-decoration: none; color: inherit; font-size: 13px; letter-spacing: 2px;">JOURNEY</a>
                                <a href="#zen-rituals" style="text-decoration: none; color: inherit; font-size: 13px; letter-spacing: 2px;">RITUALS</a>
                                <a href="#zen-connect" style="text-decoration: none; color: inherit; font-size: 13px; letter-spacing: 2px; border: 1px solid #7c9473; padding: 8px 20px; border-radius: 30px;">CONNECT</a>
                            </nav>
                        </header>

                        <!-- Zen Hero (Grounding) -->
                        <section style="height: 85vh; display: flex; align-items: center; justify-content: center; text-align: center;">
                            <div class="container" style="max-width: 800px;">
                                <h1 style="font-size: 5rem; font-weight: 200; line-height: 1.1; margin-bottom: 30px; color: #7c9473;">Find your inner <br> stillness.</h1>
                                <p style="font-size: 1.2rem; margin-bottom: 50px; opacity: 0.8; line-height: 1.8;">Experience the profound shift from chaos to clarity through organic transformation coaching.</p>
                                <a href="#zen-rituals" style="background: #7c9473; color: #fff; text-decoration: none; padding: 20px 50px; border-radius: 50px; font-weight: 500; font-size: 1.1rem;">Begin the Journey</a>
                            </div>
                        </section>

                        <!-- Journey Section (Bento Grid) -->
                        <section id="zen-journey" style="padding: 100px 0;">
                            <div class="container">
                                <div class="row g-4">
                                    <div class="col-md-8">
                                        <div style="background: #f4efea; border-radius: 40px; padding: 60px; height: 100%;">
                                            <h2 style="font-size: 3rem; font-weight: 300; margin-bottom: 30px;">Deep Soul Discovery</h2>
                                            <p style="font-size: 1.1rem; line-height: 1.8;">Our flagship journey designed to strip away the layers of societal conditioning and reveal the vibrant, authentic core of your true self.</p>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div style="background: #e7ebee; border-radius: 40px; padding: 40px; height: 100%; display: flex; align-items: flex-end;">
                                            <div>
                                                <h3 style="font-size: 1.5rem; margin-bottom: 15px;">Mindful Habits</h3>
                                                <p>Daily rituals for the modern spirit.</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </section>

                        <!-- Rituals Section (Asymmetric) -->
                        <section id="zen-rituals" style="padding: 100px 0; background: #fff;">
                            <div class="container">
                                <div class="row align-items-center">
                                    <div class="col-md-5">
                                        <img src="https://images.unsplash.com/photo-1506126613408-eca07ce68773?auto=format&fit=crop&w=800" class="img-fluid" style="border-radius: 200px 200px 0 0;">
                                    </div>
                                    <div class="col-md-7" style="padding-left: 80px;">
                                        <h2 style="font-size: 3rem; font-weight: 300; margin-bottom: 40px;">Guided Rituals</h2>
                                        <div style="margin-bottom: 30px;">
                                            <h4 style="color: #7c9473;">Vibrant Health (1hr) — $120</h4>
                                            <p>Holistic nutrition and breathwork integration.</p>
                                        </div>
                                        <div style="margin-bottom: 30px;">
                                            <h4 style="color: #7c9473;">Clear Vision (2hr) — $200</h4>
                                            <p>Strategic alignment with your life's purpose.</p>
                                        </div>
                                        <a href="#zen-connect" style="color: #4a4a4a; font-weight: 700;">REQUEST RITUAL →</a>
                                    </div>
                                </div>
                            </div>
                        </section>

                        <!-- Connect (Modern Minimized Form) -->
                        <section id="zen-connect" style="padding: 120px 0; background: #fdfaf7;">
                            <div class="container" style="max-width: 600px; text-align: center;">
                                <h2 style="font-size: 3rem; font-weight: 200; margin-bottom: 60px;">Speak from the heart.</h2>
                                <form>
                                    <input type="text" placeholder="Your Name" style="width: 100%; padding: 20px; border: none; border-bottom: 1px solid #7c9473; background: transparent; margin-bottom: 30px; outline: none;">
                                    <input type="email" placeholder="Email Address" style="width: 100%; padding: 20px; border: none; border-bottom: 1px solid #7c9473; background: transparent; margin-bottom: 30px; outline: none;">
                                    <textarea placeholder="Tell us your intention..." style="width: 100%; padding: 20px; border: none; border-bottom: 1px solid #7c9473; background: transparent; margin-bottom: 60px; outline: none; height: 100px;"></textarea>
                                    <button type="submit" style="background: #4a4a4a; color: #fff; border: none; padding: 20px 60px; border-radius: 50px; font-weight: 600; letter-spacing: 2px;">SEND INTENTION</button>
                                </form>
                            </div>
                        </section>
                    </div>`
                },
                {
                    id: 'zen-section-grounding',
                    label: 'Zen - Grounding (Hero)',
                    category: 'Templates',
                    content: `
                    <section class="zen-grounding" style="background: #fdfaf7; font-family: 'Outfit', sans-serif; padding: 100px 20px; text-align: center;">
                        <div class="container" style="max-width: 800px; margin: 0 auto;">
                            <h1 style="font-size: 5rem; font-weight: 200; line-height: 1.1; margin-bottom: 30px; color: #7c9473;">Find your inner <br> stillness.</h1>
                            <p style="font-size: 1.2rem; margin-bottom: 50px; opacity: 0.8; line-height: 1.8;">Experience the profound shift from chaos to clarity through organic transformation coaching.</p>
                            <a href="#zen-rituals" style="background: #7c9473; color: #fff; text-decoration: none; padding: 20px 50px; border-radius: 50px; font-weight: 500; font-size: 1.1rem; display: inline-block;">Begin the Journey</a>
                        </div>
                    </section>`
                },
                {
                    id: 'zen-section-journey',
                    label: 'Zen - Journey (Bento Grid)',
                    category: 'Templates',
                    content: `
                    <section id="zen-journey" style="background: #fdfaf7; font-family: 'Outfit', sans-serif; padding: 100px 20px;">
                        <div class="container" style="max-width: 1200px; margin: 0 auto;">
                            <div class="row g-4">
                                <div class="col-md-8">
                                    <div style="background: #f4efea; border-radius: 40px; padding: 60px; height: 100%;">
                                        <h2 style="font-size: 3rem; font-weight: 300; margin-bottom: 30px;">Deep Soul Discovery</h2>
                                        <p style="font-size: 1.1rem; line-height: 1.8;">Our flagship journey designed to strip away the layers of societal conditioning and reveal the vibrant, authentic core of your true self.</p>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div style="background: #e7ebee; border-radius: 40px; padding: 40px; height: 100%; display: flex; align-items: flex-end;">
                                        <div>
                                            <h3 style="font-size: 1.5rem; margin-bottom: 15px;">Mindful Habits</h3>
                                            <p>Daily rituals for the modern spirit.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>`
                },
                {
                    id: 'zen-section-rituals',
                    label: 'Zen - Rituals (Services)',
                    category: 'Templates',
                    content: `
                    <section id="zen-rituals" style="background: #fff; font-family: 'Outfit', sans-serif; padding: 100px 20px;">
                        <div class="container" style="max-width: 1200px; margin: 0 auto;">
                            <div class="row align-items-center">
                                <div class="col-md-5">
                                    <img src="https://images.unsplash.com/photo-1506126613408-eca07ce68773?auto=format&fit=crop&w=800" class="img-fluid" style="border-radius: 200px 200px 0 0; width: 100%;">
                                </div>
                                <div class="col-md-7" style="padding-left: 80px;">
                                    <h2 style="font-size: 3rem; font-weight: 300; margin-bottom: 40px; color: #4a4a4a;">Guided Rituals</h2>
                                    <div style="margin-bottom: 30px;">
                                        <h4 style="color: #7c9473; margin-bottom: 10px;">Vibrant Health (1hr) — $120</h4>
                                        <p style="opacity: 0.8;">Holistic nutrition and breathwork integration for the physical vessel.</p>
                                    </div>
                                    <div style="margin-bottom: 30px;">
                                        <h4 style="color: #7c9473; margin-bottom: 10px;">Clear Vision (2hr) — $200</h4>
                                        <p style="opacity: 0.8;">Strategic alignment with your life's purpose and soul mission.</p>
                                    </div>
                                    <a href="#zen-connect" style="color: #4a4a4a; font-weight: 700; text-decoration: none; border-bottom: 2px solid #7c9473;">REQUEST RITUAL →</a>
                                </div>
                            </div>
                        </div>
                    </section>`
                },
                {
                    id: 'zen-section-connect',
                    label: 'Zen - Connect (Contact)',
                    category: 'Templates',
                    content: `
                    <section id="zen-connect" style="background: #fdfaf7; font-family: 'Outfit', sans-serif; padding: 120px 20px;">
                        <div class="container" style="max-width: 600px; margin: 0 auto; text-align: center;">
                            <h2 style="font-size: 3rem; font-weight: 200; margin-bottom: 60px; color: #4a4a4a;">Speak from the heart.</h2>
                            <form>
                                <input type="text" placeholder="Your Name" style="width: 100%; padding: 20px; border: none; border-bottom: 1px solid #7c9473; background: transparent; margin-bottom: 30px; outline: none; transition: 0.3s; color: #4a4a4a;">
                                <input type="email" placeholder="Email Address" style="width: 100%; padding: 20px; border: none; border-bottom: 1px solid #7c9473; background: transparent; margin-bottom: 30px; outline: none; transition: 0.3s; color: #4a4a4a;">
                                <textarea placeholder="Tell us your intention..." style="width: 100%; padding: 20px; border: none; border-bottom: 1px solid #7c9473; background: transparent; margin-bottom: 60px; outline: none; height: 100px; transition: 0.3s; color: #4a4a4a;"></textarea>
                                <button type="submit" style="background: #4a4a4a; color: #fff; border: none; padding: 20px 60px; border-radius: 50px; font-weight: 600; letter-spacing: 2px; cursor: pointer; transition: 0.3s;">SEND INTENTION</button>
                            </form>
                        </div>
                    </section>`
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
                ...formBlocks,
                ...iconBlocks,
                ...professionalBlocks,
                ...templateBlocks
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
