@extends('back.layout.pages-layout')
@section('pageTitle', isset($pageTitle) ? $pageTitle : 'Page Title Here')
@section('content')

    <div class="page-header">
        <div class="row">
            <div class="col-md-12 col-sm-12">
                <div class="title">
                    <h4>Profile</h4>
                </div>
                <nav aria-label="breadcrumb" role="navigation">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.dashboard') }}">Home</a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">
                            Profile
                        </li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    @livewire('admin.profile')

@endsection
@push('scripts')
    <script>

        const cropper = new Kropify('#profilePictureFile', {
            aspectRatio: 1,
            preview: 'image#profilePicturePreview',
            processURL: '{{ route('admin.update_profile_picture') }}',
            allowedExtensions: ['jpg', 'jpeg', 'png'],
            showLoader: true,
            animationClass: 'pulse',
            fileName: 'profilePictureFile',
            cancelButtonText: 'Cancel',
            maxWoH: 255,

            onError: function (msg) {
                console.log(msg);
            },

            onDone: function (response) {

                if (response.status === 1) {
                    Livewire.dispatch('refreshUserInfo',[]);
                    Livewire.dispatch('showAlert', [{
                        type: 'success',
                        message: response.message
                    }]);
                     
                } else {
                    Livewire.dispatch('showAlert', [{
                        type: 'error',
                        message: response.message
                    }]);
                }
            }
        });

    </script>
@endpush