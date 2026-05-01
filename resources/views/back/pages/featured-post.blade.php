@extends('back.layout.pages-layout')
@section('pageTitle', isset($pageTitle) ? $pageTitle : 'Page Title Here')
@section('content')

    <div class="page-header">
        <div class="row">
            <div class="col-md-6 col-sm-12">
                <div class="title">
                    <h4>Featured Posts</h4>
                </div>
                <nav aria-label="breadcrumb" role="navigation">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.dashboard') }}">Home</a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">
                            Featured Post
                        </li>
                    </ol>
                </nav>
            </div>
            <div class="col-md-6 col-sm-12 text-right">
                <a href="{{ route('admin.add_post') }}" class="btn btn-primary"><i class="icon-copy bi bi-plus-circle"></i>
                    Add Post</a>
            </div>
        </div>
    </div>

    @livewire('admin.featured-post')

@endsection
@push('scripts')
    <script>
        //Show or Hide read more modal
        window.addEventListener('show_read_more_modal', function(event) {
            $('#read_more_modal').modal('show');
        });
        window.addEventListener('hide_read_more_modal', function(event) {
            $('#read_more_modal').modal('hide');
        });
    </script>
@endpush