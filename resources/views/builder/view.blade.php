<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $page->title }}</title>

    <!-- Optional: Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">

    <!-- FontAwesome for Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Page Custom CSS -->
    <style>
        .pro-footer a:hover { color: #fff !important; opacity: 0.8; }
        .pro-footer button:hover { background: #eee !important; }
        .pro-footer input::placeholder { color: rgba(255,255,255,0.5) !important; }
    </style>
    @if (!empty($page->css))
        <style>
            {!! $page->css !!}
        </style>
    @endif
    <style>
        /* ====== Wix Style Premium Navbar ====== */
        .wix-navbar {
            display: flex !important;
            flex-direction: row !important;
            align-items: center !important;
            justify-content: space-between !important;
            padding: 10px 25px !important;
            background: #fff !important;
            border-bottom: 1px solid #e2e2e2 !important;
            width: 100% !important;
            height: 50px !important;
            box-sizing: border-box !important;
            font-family: 'Helvetica Neue', 'Arial', sans-serif !important;
        }
        .wix-navbar .action-icon:hover { color: #000 !important; transform: scale(1.1); }
        
        /* Search Overlay */
        .search-overlay {
            position: fixed !important;
            top: 0 !important;
            left: 0 !important;
            width: 100% !important;
            height: 100% !important;
            background: rgba(255,255,255,0.98) !important;
            backdrop-filter: blur(8px) !important;
            z-index: 10000 !important;
            display: none;
            align-items: center;
            justify-content: center;
            flex-direction: column;
        }
        .close-search {
            position: absolute !important;
            top: 30px !important;
            right: 40px !important;
            font-size: 40px !important;
            cursor: pointer !important;
            color: #333 !important;
        }
        .search-input {
            width: 60% !important;
            border: none !important;
            border-bottom: 3px solid #000 !important;
            font-size: 3rem !important;
            outline: none !important;
            padding: 10px !important;
            text-align: center !important;
            background: transparent !important;
        }
    </style>
</head>

<body>

    <!-- Page HTML Content -->
    @if (!empty($page->html))
        {!! $page->html !!}
    @else
        <div class="container text-center mt-5">
            <h1>No content available for this page</h1>
        </div>
    @endif

    <!-- Bootstrap JS (needed for navbar, modals, etc.) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Page Custom JavaScript -->
    @if (!empty($page->js))
        <script>
            {!! $page->js !!}
        </script>
    @endif

</body>
</html>