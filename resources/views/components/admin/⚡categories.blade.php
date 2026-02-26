<?php

use Livewire\Component;
use App\Models\ParentCategory;
use App\Models\Category;
use Livewire\Attributes\Computed;

new class extends Component {

    //Parent Category Modal
    public $isUpateParentCategoryMode = false;
    public $pcategory_id, $pcategory_name;


    //Parent Category
    public function addParentCategory()
    {
        $this->pcategory_id = null;
        $this->pcategory_name = null;
        $this->isUpateParentCategoryMode = false;
        $this->showParentCategoryModalForm();
    }

    public function createParentCategory()
    {
        $this->validate([
            'pcategory_name' => 'required|unique:parent_categories,name'
        ], [
            'pcategory_name.required' => 'Parent category field is required.',
            'pcategory_name.unique' => 'Parent category already exists.'
        ]);

        $pcategory = new ParentCategory();
        $pcategory->name = $this->pcategory_name;
        $saved = $pcategory->save();

        if ($saved) {
            $this->hideParentCategoryModalForm();
            $this->dispatch('showAlert', ['type' => 'success', 'message' => 'New parent category has been created successfully.']);

        } else {
            $this->dispatch('showAlert', ['type' => 'error', 'message' => 'Something went wrong. Try again!']);
        }
    }

    public function editParentCategory($id)
    {
        $pcategory = ParentCategory::findOrFail($id);
        $this->pcategory_id = $pcategory->id;
        $this->pcategory_name = $pcategory->name;
        $this->isUpateParentCategoryMode = true;
        $this->showParentCategoryModalForm();
    }

    public function updateParentCategory()
    {
        $pcategory = ParentCategory::findOrFail($this->pcategory_id);

        $this->validate([
            'pcategory_name' => 'required|unique:parent_categories,name,'.$pcategory->id
        ], [
            'pcategory_name.required' => 'Parent category field is required.',
            'pcategory_name.unique' => 'Parent category already exists.'
        ]);

        $updated = $pcategory->update([
            'name' => $this->pcategory_name,
            'slug' => null
        ]);

        if ( $updated) {
            $this->hideParentCategoryModalForm();
            $this->dispatch('showAlert', ['type' => 'success', 'message' => 'Parent Category updated successfully!']);
        }else{
            $this->dispatch('showAlert', ['type' => 'error', 'message' => 'Something went wrong.']);
        }
    }

    public function showParentCategoryModalForm()
    {
        $this->resetErrorBag();
        $this->dispatch('showParentCategoryModalForm');
    }

    public function hideParentCategoryModalForm()
    {
        $this->dispatch('hideParentCategoryModalForm');
        $this->isUpdateParentCategoryMode = false;
        $this->pcategory_id = $this->pcategory_name = null;
    }


    public function parentCategories()
    {
        $parent_categories = ParentCategory::orderBy('ordering', 'asc')->get();
        return $parent_categories;
    }


};
?>

<div>


    <div class="row">
        <div class="col-12">
            <div class="pd-20 card-box mb-30">
                <div class="clearfix">
                    <div class="pull-left">
                        <h4 class="h4 text-blue">
                            Parent Categories
                        </h4>
                    </div>
                    <div class="pull-right">
                        <a href="javascript:;" wire:click="addParentCategory()" class="btn btn-primary btn-sm">Add P.
                            Category</a>
                    </div>
                </div>
                <div class="table-responsive mt-4">
                    <table class="table table-borderless table-striped table-sm">
                        <thead class="bg-secondary text-white">
                            <th>#</th>
                            <th>Name</th>
                            <th>N. of categories</th>
                            <th>Actions</th>
                        </thead>
                        <tbody>
                            @forelse($this->parentCategories() as $item)
                                <tr>
                                    <td>{{ $item->id }}</td>
                                    <td>{{ $item->name }}</td>
                                    <td> - </td>
                                    <td>
                                        <div class="table-actions">
                                            <a href="javascript:;" wire:click="editParentCategory({{ $item->id }})"
                                                class="text-primary mx-2">
                                                <i class="dw dw-edit2"></i>
                                            </a>
                                            <a href="" class="text-danger mx-2">
                                                <i class="dw dw-delete-3"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4"><span class="text-blue">No item found!</span></td>
                                </tr>
                            @endforelse

                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="pd-20 card-box mb-30">
                <div class="clearfix">
                    <div class="pull-left">
                        <h4 class="h4 text-blue">
                            Categories
                        </h4>
                    </div>
                    <div class="pull-right">
                        <a href="" class="btn btn-primary btn-sm">Add Category</a>
                    </div>
                </div>
                <div class="table-responsive mt-4">
                    <table class="table table-borderless table-striped table-sm">
                        <thead class="bg-secondary text-white">
                            <th>#</th>
                            <th>Name</th>
                            <th>Parent Category</th>
                            <th>N. of posts</th>
                            <th>Actions</th>
                        </thead>
                        <tbody>
                            <tr>
                                <td>1</td>
                                <td>P. Cat 1</td>
                                <td>Any</td>
                                <td>4</td>
                                <td>
                                    <div class="table-actions">
                                        <a href="" class="text-primary mx-2">
                                            <i class="dw dw-edit2"></i>
                                        </a>
                                        <a href="" class="text-danger mx-2">
                                            <i class="dw dw-delete-3"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- MODALS --}}
    <div wire:ignore.self class="modal fade" id="pcategory_modal" tabindex="-1" role="dialog"
        aria-labelledby="myLargeModalLabel" aria-modal="true" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-dialog-centered">
            <form class="modal-content"
                wire:submit="{{ $isUpateParentCategoryMode ? 'updateParentCategory()' : 'createParentCategory()' }}">
                <div class="modal-header">
                    <h4 class="modal-title" id="myLargeModalLabel">
                        {{ $isUpateParentCategoryMode ? 'Update Parent Category' : 'Add Parent Category' }}
                    </h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                        ×
                    </button>
                </div>
                <div class="modal-body">
                    @if ($isUpateParentCategoryMode)
                        <input type="hidden" wire:modal="pcategory_id">
                    @endif
                    <div class="form-group">
                        <label for=""><b>Parent category name</b></label>
                        <input type="text" wire:model="pcategory_name" class="form-control"
                            placeholder="Enter parent category name here...">
                        @error('pcategory_name')
                            <span class="text-danger ml-1">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        Close
                    </button>
                    <button type="submit" class="btn btn-primary">
                        {{ $isUpateParentCategoryMode ? 'Save changes' : 'Create' }}
                    </button>
                </div>
            </form>
        </div>
    </div>



</div>