@extends('back.layout.pages-layout')
@section('pageTitle', isset($pageTitle) ? $pageTitle : 'Page Title Here')
@section('content')

    <div class="page-header">
        <div class="row">
            <div class="col-md-6 col-sm-12">
                <div class="title">
                    <h4>Add Post</h4>
                </div>
                <nav aria-label="breadcrumb" role="navigation">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.dashboard') }}">Home</a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">
                            Add Post
                        </li>
                    </ol>
                </nav>
            </div>
            <div class="col-md-6 col-sm-12 text-right">
                <a href="{{ route('admin.posts') }}" class="btn btn-primary">View all posts</a>
            </div>
        </div>
    </div>

    <form action="{{ route('admin.create_post') }}" method="POST" autocomplete="off" enctype="multipart/form-data"
        id="addPostForm">
        @csrf

        <div class="row">
            <div class="col-md-9">
                <div class="card card-box mb-2">
                    <div class="card-body">
                        <div class="form-group">
                            <label for=""><b>Title</b>:</label>
                            <input type="text" name="title" id="" placeholder="Enter post title" class="form-control">
                            <span class="text-danger error-text title_error"></span>
                        </div>
                        <div class="form-group">
                            <label for=""><b>Content</b>:</label>
                            <textarea name="post_content" id="" cols="30" rows="10" placeholder="Enter post content here...."
                                class="form-control"></textarea>
                            <span class="text-danger error-text post_content_error"></span>
                        </div>
                    </div>
                </div>
                <div class="card card-box mb-2">
                    <div class="card-header weight-500">SEO</div>
                    <div class="card-body">
                        <div class="form-group">
                            <label for=""><b>Post meta keywords</b>: <small>(separated by comma.)</small></label>
                            <input type="text" name="meta_keywords" id="" placeholder="Enter post meta keywords"
                                class="form-control">
                        </div>
                        <div class="form-group">
                            <label for=""><b>Post meta description</b>:</label>
                            <textarea name="meta_description" id="" cols="30" rows="10"
                                placeholder="Enter post meta description...." class="form-control"></textarea>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card card-box mb-2">
                    <div class="card-body">
                        <div class="form-group">
                            <label for=""><b>Post Category</b>:</label>
                            <select name="category" id="" class="custom-select form-control">
                                <option value="">Choose...</option>
                                {!! $categories_html !!}
                            </select>
                            <span class="text-danger error-text category_error"></span>
                        </div>
                        <div class="form-group">
                            <label for=""><b>Post Featured image</b>:</label>
                            <input type="file" name="featured_image" class="form-control-file form-control height-auto"
                                height="auto" id="featured_image">
                            <span class="text-danger error-text featured_image_error"></span>
                        </div>
                        <div class="d-block mb-3" style="max-width: 250px;">
                            <img src="" alt="" class="img-thumbnail" id="featured_image_preview">
                        </div>
                        <div class="form-group mb-3">
                            <label for=""><b>Tags</b>:</label>
                            <input type="text" name="tags" id="" class="form-control" data-role="tagsinput">
                        </div>
                        <hr>
                        <div class="form-group">
                            <label for=""><b>Visibility</b>:</label>
                            <div class="custom-control custom-radio mb-5">
                                <input type="radio" name="visibility" id="customRadio2" class="custom-control-input"
                                    value="1" checked>
                                <label for="customRadio1" class="custom-control-label">Public</label>
                            </div>
                            <div class="custom-control custom-radio mb-5">
                                <input type="radio" name="visibility" id="customRadio2" class="custom-control-input"
                                    value="0">
                                <label for="customRadio2" class="custom-control-label">Private</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card-box p-2 mb-3 text-center" style="min-width: 8rem; max-width: 9rem;"">
            <button type="submit" class="btn btn-primary">Create post</button>
        </div>

    </form>

@endsection
@push('stylesheets')
    <link rel="stylesheet" href="{{ asset('back/src/plugins/bootstrap-tagsinput/bootstrap-tagsinput.css') }}">
@endpush
@push('scripts')
    <script src="{{ asset('back/src/plugins/bootstrap-tagsinput/bootstrap-tagsinput.js') }}"></script>

    <script>

        //image preview for post thumbnail
        const upload_featured_image = document.querySelector("#featured_image");
        const featured_image_preview = document.querySelector("#featured_image_preview");

        upload_featured_image.addEventListener("change", function (event) {
            previewPostThumbnail(event);
        });

        function previewPostThumbnail(event) {
            const file = event.target.files[0];

            if (!file) return;

            // Allowed types
            const allowedTypes = ["image/jpeg", "image/png", "image/jpg"];

            if (!allowedTypes.includes(file.type)) {
                alert("Only JPG and PNG images are allowed.");
                upload_featured_image.value = ""; // reset input
                featured_image_preview.src = ""; // clear preview
                return;
            }

            const img = new Image();
            const objectURL = URL.createObjectURL(file);

            img.onload = function () {
                //Reject square imgaes
                if (img.width === img.height) {
                    alert("Only rectangular images are allowed.");
                    upload_featured_image.value = "";
                    featured_image_preview.src = "";
                    URL.revokeObjectURL(objectURL);
                    return;
                }

                //Accept image
                featured_image_preview.src = objectURL;
                URL.revokeObjectURL(objectURL);
            };

            img.src = objectURL;
        }

        //CREATE A POST (SUBMITING USING AJAX)
        $('#addPostForm').on('submit', function(e) {
            e.preventDefault();
            var form = this;
            var formdata = new FormData(form);

            $.ajax({
                url:$(form).attr('action'),
                method:$(form).attr('method'),
                data:formdata,
                processData:false,
                dataType:'json',
                contentType:false,
                beforeSend:function(){
                    $(form).find('span.error-text').text('');
                },
                success:function(data){
                    if(data.status == 1){
                        $(form)[0].reset();
                        $('img#featured_image_preview').attr('src', '');
                        $('input[name="tags"]').tagsinput('removeAll');
                        Livewire.dispatch('showAlert', [{
                            type: 'success',
                            message: data.message
                        }]);
                    }else{
                        Livewire.dispatch('showAlert', [{
                            type: 'error',
                            message: data.message
                        }]);
                    }
                },
                error:function(data){
                    $.each(data.responseJSON.errors, function(prefix, val){
                        $(form).find('span.' + prefix + '_error').text(val[0]);
                    });
                }
            });
        });

    </script>


@endpush