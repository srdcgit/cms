<!doctype html>
<html>

<head>
    <title>Editor - {{ $page->title }}</title>

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

        #gjs {
            height: 100vh !important;
        }

        .editable-text {
            cursor: text !important;
        }
    </style>

    <!-- GrapesJS -->
    <script src="https://cdn.jsdelivr.net/npm/grapesjs/dist/grapes.js"></script>

    <!-- Plugins JS -->
    <script src="https://cdn.jsdelivr.net/npm/grapesjs-preset-webpage/dist/grapesjs-preset-webpage.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/grapesjs-blocks-basic/dist/grapesjs-blocks-basic.min.js"></script>
</head>

<body>
    @if (session('success'))
        <div id="saveMessage"
            style="position:fixed; top:10px; right:10px; background:green; color:white; padding:10px; border-radius:5px; z-index:9999;">
            {{ session('success') }}
        </div>
        <script>
            setTimeout(() => {
                const msg = document.getElementById('saveMessage');
                if (msg) msg.remove();
            }, 3000); // hide after 3 seconds
        </script>
    @endif


    <!-- Save Form -->
    <form id="saveForm" action="{{ route('builder.update', $page->id) }}" method="POST">
        @csrf
        <input type="hidden" name="html" id="html">
        <input type="hidden" name="css" id="css">
        <input type="hidden" name="gjs_json" id="gjs_json">

        <!-- Back & Save buttons -->
        <div class="d-flex justify-content-between top-buttons">
            <a href="{{ route('pages.index') }}" class="btn btn-secondary">←</a>
            <button type="submit" class="btn btn-primary">Save Page</button>
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
                'gjs-preset-webpage': {},
                'gjs-blocks-basic': {
                    flexGrid: true
                }
            },
            canvas: {
                styles: [
                    'https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css',
                    'https://cdn.jsdelivr.net/npm/grapesjs/dist/css/grapes.min.css',
                    'https://cdn.jsdelivr.net/npm/grapesjs-preset-webpage/dist/grapesjs-preset-webpage.min.css'
                ]
            }
        });

        editor.on('load', () => {
            const bm = editor.BlockManager;
            const dc = editor.DomComponents;

            // ----------- REMOVE DUPLICATE FORMS BLOCKS -----------
            const existingForms = bm.getAll().filter(block => block.attributes.category?.id === 'forms');
            existingForms.forEach(block => bm.remove(block.id));

            // ----------- BASIC BLOCKS -----------
            const basicBlocks = [{
                    id: '1col',
                    label: '1 Column',
                    content: `<div class="row"><div class="col">Column</div></div>`
                },
                {
                    id: '2col',
                    label: '2 Columns',
                    content: `<div class="row"><div class="col">Col 1</div><div class="col">Col 2</div></div>`
                },
                {
                    id: '3col',
                    label: '3 Columns',
                    content: `<div class="row"><div class="col">Col 1</div><div class="col">Col 2</div><div class="col">Col 3</div></div>`
                },
                {
                    id: '2-3col',
                    label: '2/3 Columns',
                    content: `<div class="row"><div class="col-8">Col 1</div><div class="col-4">Col 2</div></div>`
                },
                {
                    id: 'text',
                    label: 'Text',
                    content: `<p class="p-2 editable-text" contenteditable="true">Editable text here</p>`
                },
                {
                    id: 'link',
                    label: 'Link',
                    content: `<a href="#" class="text-decoration-underline editable-text" contenteditable="true">Click here</a>`
                },
                {
                    id: 'image',
                    label: 'Image',
                    content: `<img src="https://via.placeholder.com/400x200" class="img-fluid">`
                },
                {
                    id: 'video',
                    label: 'Video',
                    content: `
        <div class="ratio ratio-16x9 video-wrapper">
            <iframe src="https://www.youtube.com/embed/dQw4w9WgXcQ"
                frameborder="0" allowfullscreen
                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture">
            </iframe>
        </div>
    `,
                    script: function() {
                        // Prevent GrapesJS from selecting parent while interacting
                        const iframe = this.querySelector('iframe');
                        iframe.addEventListener('mousedown', e => e.stopPropagation());
                    }
                },

                {
                    id: 'quote',
                    label: 'Quote',
                    content: `<blockquote class="blockquote p-3 border-start border-primary"><p class="mb-0 editable-text" contenteditable="true">"Quote here"</p></blockquote>`
                },
                {
                    id: 'header',
                    label: 'Header',
                    content: `<header class="p-4 bg-primary text-white text-center"><h1 class="editable-text" contenteditable="true">Website Header</h1></header>`
                },
                {
                    id: 'footer-basic',
                    label: 'Footer',
                    content: `<footer class="p-4 bg-dark text-white text-center editable-text" contenteditable="true">© 2025 My Website</footer>`
                }
            ];
            // ----------- Add basic blocks to GrapesJS -----------
            basicBlocks.forEach(block => {
                bm.add(block.id, {
                    label: block.label,
                    content: block.content,
                    category: 'Basic',
                    attributes: {
                        class: block.id === 'video' ? 'fa fa-video' : 'fa fa-square'
                    },
                    script: block.script
                });
            });


            // ----------- EXTRA BLOCKS -----------
            const extraBlocks = [{
                    id: 'navbar',
                    label: 'Navbar',
                    content: `<nav class="navbar navbar-expand-lg navbar-dark bg-dark"><a class="navbar-brand" href="#">Brand</a></nav>`
                },
                {
                    id: 'card',
                    label: 'Card',
                    content: `<div class="card m-2 shadow" style="width:18rem;"><img src="https://via.placeholder.com/300x150" class="card-img-top"><div class="card-body"><h5 class="card-title editable-text" contenteditable="true">Card Title</h5><p class="card-text editable-text" contenteditable="true">Write anything you want here.</p><a href="#" class="btn btn-primary">Read More</a></div></div>`
                },
                {
                    id: 'banner',
                    label: 'Banner',
                    content: `<section style="position:relative; background-image:url('https://via.placeholder.com/1200x400'); background-size:cover; padding:100px 20px;"><h2 class="editable-text text-white text-center fw-bold" contenteditable="true">Your Banner Title</h2><p class="editable-text text-white text-center" contenteditable="true">Click here to edit the banner text.</p></section>`
                },
                {
                    id: 'footer-extra',
                    label: 'Footer',
                    content: `<footer class="p-4 bg-dark text-white text-center editable-text">© 2025 My Website</footer>`
                }
            ];
            extraBlocks.forEach(block => bm.add(block.id, {
                ...block,
                category: 'Extra',
                attributes: {
                    class: 'fa fa-clone'
                }
            }));

            // ----------- MEDIA BLOCK -----------
            bm.add('media-gallery', {
                label: 'Image Gallery',
                category: 'Media',
                attributes: {
                    class: 'fa fa-images'
                },
                content: `<div class="row g-3">
                    <div class="col-md-4"><img src="https://via.placeholder.com/300" class="img-fluid rounded shadow"></div>
                    <div class="col-md-4"><img src="https://via.placeholder.com/300" class="img-fluid rounded shadow"></div>
                    <div class="col-md-4"><img src="https://via.placeholder.com/300" class="img-fluid rounded shadow"></div>
                  </div>`
            });

            // ----- PRODUCT BLOCK -----
            bm.add('product-card', {
                label: 'Product Card',
                category: 'E-commerce',
                attributes: {
                    class: 'fa fa-shopping-cart'
                },
                content: `<div class="card m-2 shadow" style="width:18rem;">
                    <img src="https://via.placeholder.com/300x150" class="card-img-top">
                    <div class="card-body">
                        <h5 class="card-title editable-text" contenteditable="true">Product Title</h5>
                        <p class="card-text editable-text" contenteditable="true">Product description goes here.</p>
                        <button class="btn btn-success w-100">Buy Now</button>
                    </div>
                  </div>`
            });

            // ----------- BACKGROUND BLOCK -----------
            bm.add('bg-section', {
                label: 'Background Section',
                category: 'Background Sections',
                attributes: {
                    class: 'fa fa-image'
                },
                content: `<section style="position:relative; background-image:url('https://via.placeholder.com/1200x500'); background-size:cover; background-position:center; padding:100px 20px;">
                    <h2 class="editable-text text-white fw-bold" style="font-size:42px; text-align:center;">Click to Edit Title</h2>
                    <p class="editable-text text-white" style="font-size:20px; text-align:center; max-width:900px; margin:0 auto;">Click here to edit this text and write anything you want over the image.</p>
                  </section>`,
                script: function() {
                    const editableItems = this.querySelectorAll('.editable-text');
                    editableItems.forEach(el => el.addEventListener('click', e => e.stopPropagation()));
                }
            });




            // ----------- FORMS BLOCKS -----------
            const formBlocks = [{
                    id: 'form-wrapper',
                    label: 'Form',
                    content: {
                        type: 'form-wrapper',
                        tagName: 'form',
                        classes: ['p-3', 'border', 'rounded', 'bg-light'],
                    }
                },

                {
                    id: 'form-input',
                    label: 'Input',
                    content: `<div class="mb-3">
                        <label class="form-label">Label</label>
                        <input type="text" class="form-control" placeholder="Enter text">
                      </div>`
                },
                {
                    id: 'form-textarea',
                    label: 'Textarea',
                    content: `<div class="mb-3">
                        <label class="form-label">Label</label>
                        <textarea class="form-control" rows="4" placeholder="Enter text"></textarea>
                      </div>`
                },
                {
                    id: 'form-select',
                    label: 'Select',
                    content: {
                        type: 'default',
                        content: `<div class="mb-3">
                <label class="form-label">Select</label>
                <select class="form-select">
                    <option disabled selected>Select...</option>
                    <option>A</option>
                    <option>B</option>
                </select>
            </div>`,
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

            // Register form blocks to block manager
            formBlocks.forEach(b => {
                const attrs = {
                    class: `fa ${b.icon ?? 'fa-wpforms'}`
                };
                const opts = {
                    id: b.id,
                    label: b.label,
                    content: b.content,
                    category: b.category ?? 'Forms',
                    attributes: attrs
                };
                if (b.script) opts.script = b.script;
                bm.add(b.id, opts);
            });

            // ----------------- Generic helpers for editing experience -----------------
            // Make .editable-text double click editable globally inside editor
            const makeGlobalEditable = () => {
                const rootEl = editor.getEl();
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

            // Observe canvas for newly added editable-text elements
            const canvasEl = editor.Canvas.getBody();
            const gObserver = new MutationObserver(makeGlobalEditable);
            gObserver.observe(canvasEl, {
                childList: true,
                subtree: true
            });
            makeGlobalEditable();

            // Optional: small safety - prevent forms inside canvas from submitting inside editor area
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
        @if (!empty($page->gjs_json))
            editor.loadProjectData(@json($page->gjs_json));
            fixFileInputs(); // ← call here
        @elseif (!empty($page->html))
            editor.setComponents(@json($page->html));
            editor.setStyle(@json($page->css));
            fixFileInputs(); // ← call here
        @endif

        // BEFORE SUBMIT: put GrapesJS data into hidden inputs
        document.getElementById('saveForm').addEventListener('submit', function(e) {
            document.getElementById('html').value = editor.getHtml();
            document.getElementById('css').value = editor.getCss();
            document.getElementById('gjs_json').value = JSON.stringify(editor.getProjectData());
            // let the form submit to PageBuilderController@update
        });
    </script>

</body>
