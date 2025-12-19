@extends('layouts.dashboard')

@section('title', 'All Pages')

@section('content')
    <div class="container mt-4">

        <!-- Create New Page Button -->
        <a href="{{ route('builder.create') }}" class="btn btn-primary mb-3">Create New Page</a>

        <!-- Success Message -->
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <!-- Pages Table -->
        <table class="table table-bordered table-hover">
            <thead class="table-dark">
                <tr>
                    <th>Sl No.</th>
                    <th>Title</th>
                    <th>Slug</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pages as $key => $page)
                    <tr>
                        <td>{{ $key + 1 }}</td>
                        <td>{{ $page->title }}</td>
                        <td>
                            <!-- View Page Link -->
                            <a href="{{ route('builder.page', $page->slug) }}" target="_blank">
                                {{ $page->slug }}
                            </a>
                        </td>
                        <td>
                            <!-- Edit Page -->
                            <a href="{{ route('builder.edit', $page->id) }}" class="btn btn-sm btn-info">Edit</a>

                            <!-- Delete Page -->
                            <form action="{{ route('builder.destroy', $page->id) }}" method="POST"
                                style="display:inline-block;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger"
                                    onclick="return confirm('Are you sure you want to delete this page?')">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center">No pages found. Create a new page!</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
