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
        const upload = document.querySelector("#site_logo");
        const image = document.querySelector("#preview_site_logo");

        upload.addEventListener("change", function (event) {
            uploadFile(event);
        });

        function uploadFile(event) {
            const file = event.target.files[0];

            if (!file) return;

            // Allowed types
            const allowedTypes = ["image/jpeg", "image/png"];

            if (!allowedTypes.includes(file.type)) {
                alert("Only JPG and PNG images are allowed.");
                upload.value = ""; // reset input
                image.src = ""; // clear preview
                return;
            }

            image.src = URL.createObjectURL(file);
        }

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
    </script>
@endpush