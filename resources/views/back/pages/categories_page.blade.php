@extends('back.layout.pages-layout')
@section('pageTitle', isset($pageTitle) ? $pageTitle : 'Page Title Here')
@section('content')

    @livewire('admin.categories')

@endsection
@push('scripts')
    <script>
        // Parent Category Modal
        window.addEventListener('showParentCategoryModalForm', function () {
            $('#pcategory_modal').modal('show');
        });
        window.addEventListener('hideParentCategoryModalForm', function () {
            $('#pcategory_modal').modal('hide');
        });

        // Category Modal
        window.addEventListener('showCategoryModalForm', function () {
            $('#category_modal').modal('show');
        });

        window.addEventListener('hideCategoryModalForm', function () {
            $('#category_modal').modal('hide');
        });

        // Sort Parent Category Table
        $('table tbody#sortable_parent_categories').sortable({
            cursor: "move",
            update: function (event, ui) {
                $(this).children().each(function (index) {
                    if ($(this).attr('data-ordering') != (index + 1)) {
                        $(this).attr('data-ordering', (index + 1)).addClass('updated');
                    }
                });
                var positions = [];
                $('.updated').each(function () {
                    positions.push([$(this).attr('data-index'), $(this).attr('data-ordering')]);
                    $(this).removeClass('updated');
                });

                Livewire.dispatch('updateParentCategoryOrdering', {positions: positions});
            }
        });

        //Delete item from parent Category table
        window.addEventListener('showDeleteConfirmationModal', function(event) {
            $('#delete_confirmation_modal').modal('show');
        });
        window.addEventListener('hideDeleteConfirmationModal', function(event) {
            $('#delete_confirmation_modal').modal('hide');
        });
    </script>
@endpush