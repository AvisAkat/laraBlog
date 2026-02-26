@extends('back.layout.pages-layout')
@section('pageTitle', isset($pageTitle) ? $pageTitle : 'Page Title Here')
@section('content')

    <div class="page-header">
        <div class="row">
            <div class="col-md-12 col-sm-12">
                <div class="title">
                    <h4>Settings</h4>
                </div>
                <nav aria-label="breadcrumb" role="navigation">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.dashboard') }}">Home</a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">
                            Settings
                        </li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <div class="pd-20 card-box mb-4">
        @livewire('admin.settings')
    </div>

@endsection
@push('scripts')
    <script>

        //Preview Site logo
        const upload_site_logo = document.querySelector("#site_logo");
        const image_site_logo = document.querySelector("#preview_site_logo");

        upload_site_logo.addEventListener("change", function (event) {
            uploadFileLogo(event);
        });

        function uploadFileLogo(event) {
            const file = event.target.files[0];

            if (!file) return;

            // Allowed types
            const allowedTypes = ["image/jpeg", "image/png"];

            if (!allowedTypes.includes(file.type)) {
                alert("Only JPG and PNG images are allowed.");
                upload_site_logo.value = ""; // reset input
                image_site_logo.src = ""; // clear preview
                return;
            }

            const img = new Image();
            const objectURL = URL.createObjectURL(file);

            img.onload = function () {
                //Reject square imgaes
                if (img.width === img.height) {
                    alert("Only rectangular images are allowed.");
                    upload_site_logo.value = "";
                    image_site_logo.src = "";
                    URL.revokeObjectURL(objectURL);
                    return;
                }

                //Accept image
                image_site_logo.src = objectURL;
                URL.revokeObjectURL(objectURL);
            };

            img.src = objectURL;
        }




        //Preview Site Favicon
        const upload_site_favicon = document.querySelector("#site_favicon");
        const image_site_favicon = document.querySelector("#preview_site_favicon");

        upload_site_favicon.addEventListener("change", function (event) {
            uploadFileFavicon(event);
        });

        function uploadFileFavicon(event) {
            const file = event.target.files[0];

            if (!file) return;

            // Allowed types
            const allowedTypes = ["image/jpeg", "image/png", "image/ico"];

            if (!allowedTypes.includes(file.type)) {
                alert("Only JPG and PNG images are allowed.");
                upload_site_favicon.value = ""; // reset input
                image_site_favicon.src = ""; // clear preview
                return;
            }

            const img_favicon = new Image();
            const objectURL_favicon = URL.createObjectURL(file);

            img_favicon.onload = function () {
                //Reject square imgaes
                if (img_favicon.width !== img_favicon.height) {
                    alert("Only square images are allowed.");
                    upload_site_favicon.value = "";
                    image_site_favicon.src = "";
                    URL.revokeObjectURL(objectURL_favicon);
                    return;
                }

                //Accept image
                image_site_favicon.src = objectURL_favicon;
                URL.revokeObjectURL(objectURL_favicon);
            };


            img_favicon.src = objectURL_favicon;
        }


        // Update site logo form 
        $('#updateLogoForm').submit(function (e) {
            e.preventDefault();
            var form = this;
            var inputVal = $(form).find('input[type="file"]').val();
            var errorElement = $(form).find('span.text-danger');
            errorElement.text('');

            if (inputVal.length > 0) {
                $.ajax({
                    url: $(form).attr('action'),
                    method: $(form).attr('method'),
                    data: new FormData(form),
                    processData: false,
                    dataType: 'json',
                    contentType: false,
                    beforeSend: function () { },
                    success: function (data) {
                        if (data.status == 1) {
                            $(form)[0].reset();
                            var linkElement = document.querySelector('link[rel="icon"]');
                            linkElement.href = data.image_path;
                            Livewire.dispatch('showAlert', [{
                                type: 'success',
                                message: data.message
                            }]);
                            $('img.site_logo').each(function () {
                                $(this).attr('src', '/' + data.image_path)
                            });
                        } else {
                            Livewire.dispatch('showAlert', [{
                                type: 'error',
                                message: data.message
                            }]);
                        }
                    }
                });
            } else {
                errorElement.text('Please, select an image file.')
            }
        });


        // Update site favicon form 
        $('#updateFaviconForm').submit(function (e) {
            e.preventDefault();
            var form = this;
            var inputVal = $(form).find('input[type="file"]').val();
            var errorElement = $(form).find('span.text-danger');
            errorElement.text('');

            if (inputVal.length > 0) {
                $.ajax({
                    url: $(form).attr('action'),
                    method: $(form).attr('method'),
                    data: new FormData(form),
                    processData: false,
                    dataType: 'json',
                    contentType: false,
                    beforeSend: function () { },
                    success: function (data) {
                        if (data.status == 1) {
                            $(form)[0].reset();
                            Livewire.dispatch('showAlert', [{
                                type: 'success',
                                message: data.message
                            }]);
                            $('img.site_favicon').each(function () {
                                $(this).attr('src', '/' + data.image_path)
                            });
                        } else {
                            Livewire.dispatch('showAlert', [{
                                type: 'error',
                                message: data.message
                            }]);
                        }
                    }
                });
            } else {
                errorElement.text('Please, select an image file.')
            }
        });
    </script>
@endpush