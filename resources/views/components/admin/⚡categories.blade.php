<?php

use Livewire\Component;
use App\Models\ParentCategory;
use App\Models\Category;
use Livewire\Attributes\Computed;
use Livewire\WithPagination;

new class extends Component {
    use WithPagination;

    //Parent Category Modal
    public $isUpdateParentCategoryMode = false;
    public $pcategory_id, $pcategory_name;


    //Category Modal
    public $isUpdateCategoryMode = false;
    public $category_id, $category_name, $parent = 0;

    //Delte Parent Category and Category Modal info
    public $delete_id, $delete_function_name, $delete_name;

    //Pagination
    public $pcactegoriesPerPage = 5;
    public $categoriesPerPage = 7;


    protected $listeners = ['updateParentCategoryOrdering', 'updateCategoryOrdering'];

    //Parent Category
    public function addParentCategory()
    {
        $this->pcategory_id = null;
        $this->pcategory_name = null;
        $this->isUpdateParentCategoryMode = false;
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
        $this->isUpdateParentCategoryMode = true;
        $this->showParentCategoryModalForm();
    }

    public function updateParentCategory()
    {
        $pcategory = ParentCategory::findOrFail($this->pcategory_id);

        $this->validate([
            'pcategory_name' => 'required|unique:parent_categories,name,' . $pcategory->id
        ], [
            'pcategory_name.required' => 'Parent category field is required.',
            'pcategory_name.unique' => 'Parent category already exists.'
        ]);

        $updated = $pcategory->update([
            'name' => $this->pcategory_name,
            'slug' => null
        ]);

        if ($updated) {
            $this->hideParentCategoryModalForm();
            $this->dispatch('showAlert', ['type' => 'success', 'message' => 'Parent Category updated successfully!']);
        } else {
            $this->dispatch('showAlert', ['type' => 'error', 'message' => 'Something went wrong.']);
        }
    }

    public function deleteParentCategory($id)
    {
        $pcategory = ParentCategory::findOrFail($id);

        //Check if this parent category has children
        if ($pcategory->children->count() > 0) {
            foreach ($pcategory->children as $category) {
                //Release a category
                Category::where('id', $category->id)->update(['parent' => 0]);
            }
        }

        //Delete parent Category
        $deleted = $pcategory->delete();

        if ($deleted) {
            $this->dispatch('hideDeleteConfirmationModal');
            $this->dispatch('showAlert', ['type' => 'success', 'message' => 'Parent Category has been deleted successfully!']);
        } else {
            $this->dispatch(('hideDeleteConfirmationModal'));
            $this->dispatch('showAlert', ['type' => 'error', 'message' => 'Something went wrong.']);
        }

    }

    public function showParentCategoryDeleteConfirmationModal($id)
    {
        $this->delete_id = $id;
        $this->delete_function_name = 'deleteParentCategory';
        $this->delete_name = 'Parent Category';
        $this->dispatch('showDeleteConfirmationModal');
    }



    public function updateParentCategoryOrdering($positions)
    {
        foreach ($positions as $position) {
            $index = $position[0];
            $new_position = $position[1];
            ParentCategory::where('id', $index)->update([
                'ordering' => $new_position
            ]);
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

    //Displayinf parent categories into the parent category table
    public function parentCategories()
    {
        $parent_categories = ParentCategory::orderBy('ordering', 'asc')->paginate($this->pcactegoriesPerPage, ['*'], 'pcat_page');
        return $parent_categories;
    }

    // Category
    public function addCategory()
    {
        $this->category_id = null;
        $this->category_name = null;
        $this->parent = 0;

        $this->isUpdateCategoryMode = false;
        $this->showCategoryModalForm();
    }

    public function createCategory()
    {
        $this->validate([
            'category_name' => 'required|unique:categories,name',
            'parent' => 'required|exists:parent_categories,id'
        ], [
            'category_name.required' => 'Category field is required.',
            'category_name.unique' => 'Category name already exists.',
            'parent.required' => 'You must select a parent category.',
            'parent.exists' => 'Please select a valid parent category.'
        ]);

        // Store new Category
        $category = new Category();
        $category->parent = $this->parent;
        $category->name = $this->category_name;
        $saved = $category->save();

        if ($saved) {
            $this->hideCategoryModalForm();
            $this->dispatch('showAlert', ['type' => 'success', 'message' => 'New category has been created successfully.']);
        } else {
            $this->dispatch('showAlert', ['type' => 'error', 'message' => 'Something went wrong. Try again!']);
        }
    }

    //For displaying categories into the categories table
    public function categories()
    {
        $categories = Category::orderBy('ordering', 'asc')->paginate($this->categoriesPerPage, ['*'], 'cat_page');
        return $categories;
    }

    public function editCategory($id)
    {
        $category = Category::findOrFail($id);
        $this->category_name = $category->name;
        $this->category_id = $category->id;
        $this->parent = $category->parent;
        $this->isUpdateCategoryMode = true;
        $this->dispatch('showCategoryModalForm');
    }

    public function updateCategory()
    {
        $category = Category::findOrFail($this->category_id);

        $this->validate([
            'category_name' => 'required|unique:categories,name,' . $category->id,
            'parent' => 'required|exists:parent_categories,id'
        ], [
            'category_name.required' => 'Category field is required.',
            'category_name.unique' => 'Category name already exists.',
            'parent.required' => 'You must select a parent category.',
            'parent.exists' => 'Please select a valid parent category.'
        ]);

        // update category 
        $category->name = $this->category_name;
        $category->parent = $this->parent;
        $category->slug = null;
        $updated = $category->save();

        if ($updated) {
            $this->hideCategoryModalForm();
            $this->dispatch('showAlert', ['type' => 'success', 'message' => 'Category updated successfully!']);
        } else {
            $this->dispatch('showAlert', ['type' => 'error', 'message' => 'Something went wrong.']);
        }


    }

    //Odering Category Table
    public function updateCategoryOrdering($positions)
    {
        foreach ($positions as $position) {
            $index = $position[0];
            $new_position = $position[1];
            Category::where('id', $index)->update([
                'ordering' => $new_position
            ]);
        }
    }

    //Show delete modal
    public function showCategoryDeleteConfirmationModal($id)
    {
        $this->delete_id = $id;
        $this->delete_function_name = 'deleteCategory';
        $this->delete_name = 'Category';
        $this->dispatch('showDeleteConfirmationModal');
    }

    public function deleteCategory($id)
    {
        $category = Category::findOrFail($id);

        //Check if this category has related post(S)

        //delete category
        $deleted = $category->delete();

        if ($deleted) {
            $this->dispatch('hideDeleteConfirmationModal');
            $this->dispatch('showAlert', ['type' => 'success', 'message' => 'Category deleted successfully!']);
        } else {
            $this->dispatch(('hideDeleteConfirmationModal'));
            $this->dispatch('showAlert', ['type' => 'success', 'message' => 'Something went wrong.']);
        }
    }


    public function showCategoryModalForm()
    {
        $this->resetErrorBag();
        $this->dispatch('showCategoryModalForm');
    }

    public function hideCategoryModalForm()
    {
        $this->dispatch('hideCategoryModalForm');
        $this->isUpdateCategoryMode = false;
        $this->category_id = $this->category_name = null;
        $this->parent = 0;
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
                        <tbody id="sortable_parent_categories">
                            @forelse($this->parentCategories() as $item)
                                <tr data-index="{{ $item->id }}" data-ordering="{{ $item->ordering }}">
                                    <td>{{ $item->id }}</td>
                                    <td>{{ $item->name }}</td>
                                    <td> {{ $item->children->count() }} </td>
                                    <td>
                                        <div class="table-actions">
                                            <a href="javascript:;" wire:click="editParentCategory({{ $item->id }})"
                                                class="text-primary mx-2">
                                                <i class="dw dw-edit2"></i>
                                            </a>
                                            <a href="javascript:;"
                                                wire:click="showParentCategoryDeleteConfirmationModal({{ $item->id }})"
                                                class="text-danger mx-2">
                                                <i class="dw dw-delete-3"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td class="text-center" colspan="4"><span class="text-blue">No item found!</span></td>
                                </tr>
                            @endforelse

                        </tbody>
                    </table>
                </div>
                <div class="d-block mt-1 text-center">
                    {{ $this->parentCategories()->links('livewire::simple-bootstrap') }}
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
                        <a href="javascript:;" wire:click="addCategory()" class="btn btn-primary btn-sm">Add
                            Category</a>
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
                        <tbody id="sortable_categories">
                            @forelse ($this->categories() as $item)
                                <tr data-index="{{ $item->id }}" data-ordering="{{ $item->ordering }}">
                                    <td>{{ $item->id }}</td>
                                    <td>{{ $item->name }}</td>
                                    <td>
                                        {{ !is_null($item->parent_category) ? $item->parent_category->name : '-'  }}
                                    </td>
                                    <td>-</td>
                                    <td>
                                        <div class="table-actions">
                                            <a href="javascript:;" wire:click="editCategory({{ $item->id }})"
                                                class="text-primary mx-2">
                                                <i class="dw dw-edit2"></i>
                                            </a>
                                            <a href="javascript:;"
                                                wire:click="showCategoryDeleteConfirmationModal({{ $item->id }})"
                                                class="text-danger mx-2">
                                                <i class="dw dw-delete-3"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td class="text-center" colspan="5"><span class="text-blue">No item found!</span></td>
                                </tr>

                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="d-block mt-1 text-center">
                    {{ $this->categories()->links('livewire::simple-bootstrap') }}
                </div>
            </div>
        </div>
    </div>

    {{-- MODALS --}}
    {{-- Add and update parent category modal --}}
    <div wire:ignore.self class="modal fade" id="pcategory_modal" tabindex="-1" role="dialog"
        aria-labelledby="myLargeModalLabel" aria-modal="true" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-dialog-centered">
            <form class="modal-content"
                wire:submit="{{ $isUpdateParentCategoryMode ? 'updateParentCategory()' : 'createParentCategory()' }}">
                <div class="modal-header">
                    <h4 class="modal-title" id="myLargeModalLabel">
                        {{ $isUpdateParentCategoryMode ? 'Update Parent Category' : 'Add Parent Category' }}
                    </h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                        ×
                    </button>
                </div>
                <div class="modal-body">
                    @if ($isUpdateParentCategoryMode)
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
                        {{ $isUpdateParentCategoryMode ? 'Save changes' : 'Create' }}
                    </button>
                </div>
            </form>
        </div>
    </div>


    {{-- Add and update category modal --}}
    <div wire:ignore.self class="modal fade" id="category_modal" tabindex="-1" role="dialog"
        aria-labelledby="myLargeModalLabel" aria-modal="true" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-dialog-centered">
            <form class="modal-content"
                wire:submit="{{ $isUpdateCategoryMode ? 'updateCategory()' : 'createCategory()' }}">
                <div class="modal-header">
                    <h4 class="modal-title" id="myLargeModalLabel">
                        {{ $isUpdateCategoryMode ? 'Update Category' : 'Add Category' }}
                    </h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                        ×
                    </button>
                </div>
                <div class="modal-body">
                    @if ($isUpdateCategoryMode)
                        <input type="hidden" wire:modal="category_id">
                    @endif

                    <div class="form-group">
                        <label for=""><b>Parent category</b></label>
                        <select wire:model="parent" class="custom-select">
                            <option value="0">Uncategorized</option>
                            @foreach ($this->parentCategories() as $pcategory)
                                <option value="{{ $pcategory->id }}">{{ $pcategory->name }}</option>
                            @endforeach
                        </select>
                        @error('parent')
                            <span class="text-danger ml-1">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for=""><b>Category name</b></label>
                        <input type="text" wire:model="category_name" class="form-control"
                            placeholder="Enter category name here...">
                        @error('category_name')
                            <span class="text-danger ml-1">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        Close
                    </button>
                    <button type="submit" class="btn btn-primary">
                        {{ $isUpdateCategoryMode ? 'Save changes' : 'Create' }}
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Delete confirmation modal --}}
    @component('components.delete-confirmation-modal', [
        'delete_id' => $delete_id,
        'delete_function_name' => $delete_function_name,
        'delete_name' => $delete_name
    ])

    @endcomponent

    {{-- MODALS END --}}


</div>