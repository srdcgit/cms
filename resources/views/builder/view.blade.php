<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $page->title }}</title>

    <!-- Optional: Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">

    <!-- Page Custom CSS -->
    @if (!empty($page->css))
        <style>
            {!! $page->css !!}
        </style>
    @endif
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
