<div wire:ignore.self class="modal fade" id="delete_confirmation_modal" tabindex="-1" role="dialog"
        aria-modal="true" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-body text-center font-18">
                    <h4 class="padding-top-30 mb-30 weight-500">
                        Are you sure you want to delete?
                    </h4>
                    <h6 class="weight-500">
                        {{ $delete_name }}
                    </h6>
                    <div class="padding-bottom-30 row mt-5">
                        <div class="col-6">
                            <button type="button" class="btn btn-danger border-radius-100 w-75"
                                data-dismiss="modal">
                                Cancel
                            </button>
                        </div>
                        <div class="col-6">
                            <a href="javascript:;" wire:click="{{ $delete_function_name }}({{ $delete_id }})"
                                class="btn btn-success border-radius-100 w-75">
                                Yes
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>