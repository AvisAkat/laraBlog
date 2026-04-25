<?php

use Livewire\Component;
use App\Models\Post;
use Livewire\WithPagination;
use App\Models\ParentCategory;
use App\Models\Category;
use Illuminate\Support\Facades\File;

new class extends Component {

    use WithPagination;

    //Delte Post and Post Modal info
    public $delete_id, $delete_function_name, $delete_name;

    public $postPerPage = 7;

    //filter properties
    public $search = null;
    public $author = null;
    public $category = null;
    public $visibility = null;
    public $sortBy = 'desc';

    //To prevent the selected boxes from refereshing after refreshing the page.
    protected $queryString = [
        'search' => ['except' => ''],
        'author' => ['except' => ''],
        'category' => ['except' => ''],
        'visibility' => ['except' => ''],
        'sortBy' => ['except' => 'desc'],
    ];

    //A hook for reseting the page when the search value is updated. To go back to page 1 when you search using the searchbox.
    public function updatedSearch()
    {
        $this->resetPage();
    }
    public function updatedAuthor()
    {
        $this->resetPage();
    }
    public function updatedCategory()
    {
        $this->resetPage();
    }
    public function updatedVisibility()
    {
        $this->resetPage();
    }
    public function updatedSortBy()
    {
        $this->resetPage();
    }

    public function categories_selection()
    {
        //Prepare categories selection
        $categories_html = '';

        $pcategories = ParentCategory::whereHas('children', function ($q) {
            $q->whereHas('posts');
        })->orderBy('name', 'asc')->get();

        $categories = Category::whereHas('posts')->where('parent', 0)->orderBy('name', 'asc')->get();

        if (count($pcategories) > 0) {
            foreach ($pcategories as $item) {
                $categories_html .= '<optgroup label="' . $item->name . '">';
                foreach ($item->children as $category) {
                    if ($category->posts->count() > 0) {
                        $categories_html .= '<option value="' . $category->id . '">' . $category->name . '</option>';
                    }
                }
                $categories_html .= '</optgroup>';
            }
        }

        if (count($categories) > 0) {
            foreach ($categories as $item) {
                $categories_html .= '<option value="' . $item->id . '">' . $item->name . '</option>';
            }
        }
        return $categories_html;
    }


    //fetch all post
    public function allPosts()
    {

        // //when super admin opens all post only his post should show by default
        // $this->author = auth()->user()->type == "superAdmin" ? auth()->user()->id : '';

        return auth()->user()->type == "superAdmin" ?
            Post::search(trim($this->search))
                ->when($this->author, function ($query) {
                    $query->where('author_id', $this->author);
                })
                ->when($this->category, function ($query) {
                    $query->where('category', $this->category);
                })
                ->when($this->visibility, function ($query) {
                    $query->where('visibility', $this->visibility == 'public' ? 1 : 0);
                })
                ->orderBy('id', $this->sortBy)
                ->paginate($this->postPerPage)

            :

            Post::search(trim($this->search))
                ->when($this->author, function ($query) {
                    $query->where('author_id', $this->author);
                })
                ->when($this->category, function ($query) {
                    $query->where('category', $this->category);
                })
                ->when($this->visibility, function ($query) {
                    $query->where('visibility', $this->visibility == 'public' ? 1 : 0);
                })
                ->where('author_id', auth()->id())
                ->orderBy('id', $this->sortBy)
                ->paginate($this->postPerPage);
    }

    public function showPostDeleteConfirmationModal($id)
    {
        $this->delete_id = $id;
        $this->delete_function_name = 'deletePost';
        $this->delete_name = 'Post';
        $this->dispatch('showDeleteConfirmationModal');
    }

    public function deletePost($id)
    {
        $post = Post::findOrFail($id);
        $path = 'images/posts/';
        $resized_path = $path.'resized/';
        $old_featured_image = $post->featured_image;

        //Delete featured image
        if( $old_featured_image != "" && File::exists(public_path($path.$old_featured_image)) ){
            File::delete(public_path($path.$old_featured_image));
            
            //Delete Resized Image
            if( File::exists(public_path($resized_path.'resized_'.$old_featured_image)) ){
                File::delete(public_path($resized_path.'resized_'.$old_featured_image));
            }
            //Delete Thumbnail Image
            if( File::exists(public_path($resized_path.'thumb_'.$old_featured_image)) ){
                File::delete(public_path($resized_path.'thumb_'.$old_featured_image));
            }
        }

        //Delete Post from DB
        $delete = $post->delete();

        if($delete){
            $this->dispatch(('hideDeleteConfirmationModal'));
            $this->dispatch('showAlert', ['type' => 'success', 'message' => 'Post has been deleted sucessfully']);
        }else{
            $this->dispatch(('hideDeleteConfirmationModal'));
            $this->dispatch('showAlert', ['type' => 'error', 'message' => 'Something went wrong.']);
        }


    }

};
?>
<div>
    <div class="pd-20 card-box mb-30">
        <div class="row mb-20">
            <div class="col-md-4">
                <label for="search"><b class="text-secondary">Search</b>:</label>
                <input type="text" wire:model.live="search" id="search" class="form-control" placeholder="Search posts">
            </div>
            @if (auth()->user()->type == "superAdmin")
                <div class="col-md-2">
                    <label for="author"><b class="text-secondary">Author</b>:</label>
                    <select wire:model.live="author" id="author" class="custom-select form-control">
                        <option value="">No selected</option>
                        @foreach (App\Models\User::whereHas('posts')->get() as $user)
                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                        @endforeach
                    </select>
                </div>
            @endif
            <div class="col-md-2">
                <label for="category"><b class="text-secondary">Category</b>:</label>
                <select wire:model.live="category" id="category" class="custom-select form-control">
                    <option value="">No selected</option>
                    {!! $this->categories_selection() !!}
                </select>
            </div>
            <div class="col-md-2">
                <label for="visibility"><b class="text-secondary">Visibility</b>:</label>
                <select wire:model.live="visibility" id="visibility" class="custom-select form-control">
                    <option value="">No selected</option>
                    <option value="public">Public</option>
                    <option value="private">Private</option>
                </select>
            </div>
            <div class="col-md-2">
                <label for="sort"><b class="text-secondary">Sort By</b>:</label>
                <select wire:model.live="sortBy" id="sort" class="custom-select form-control">
                    <option value="asc">ASC</option>
                    <option value="desc">DESC</option>
                </select>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-striped table-auto table-sm">
                <thead class="bg-secondary text-white">
                    <th scope="col">#ID</th>
                    <th scope="col">Image</th>
                    <th scope="col">Title</th>
                    <th scope="col">Author</th>
                    <th scope="col">Category</th>
                    <th scope="col">Visibility</th>
                    <th scope="col">Action</th>
                </thead>
                <tbody>
                    @forelse ($this->allPosts() as $item)
                        <tr>
                            <td scope="'row">{{ $item->id }}</td>
                            <td>
                                <a href="">
                                    <img src="{{ asset('images/posts/resized/resized_' . $item->featured_image) }}"
                                        width="100" alt="">
                                </a>
                            </td>
                            <td>{{ $item->title }}</td>
                            <td>{{ $item->author->name }}</td>
                            <td>{{ $item->post_category->name }}</td>
                            <td>
                                @if ($item->visibility == 1)
                                    <span class="badge badge-pill badge-success">
                                        <i class="icon-copy ti-world"></i> Public
                                    </span>
                                @else
                                    <span class="badge badge-pill badge-warning">
                                        <i class="icon-copy ti-lock"></i> Private
                                    </span>
                                @endif
                            </td>
                            <td>
                                <div class="table-actions">
                                    <a href="{{ route('admin.edit_post', ['id' => $item->id]) }}" data-color="#265ed7"
                                        style="color: rgb(38, 94, 215)">
                                        <i class="icon-copy dw dw-edit2"></i>
                                    </a>
                                    <a href="javascript:;" wire:click="showPostDeleteConfirmationModal({{ $item->id }})"
                                        data-color="#e95959" style="color: rgb(233, 89, 89)">
                                        <i class="icon-copy dw dw-delete-3"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td class="text-center" colspan="7"><span class="text-blue">No Post found!</span></td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="block mt-1">
            {{ $this->allPosts()->links('livewire::simple-bootstrap') }}
        </div>
    </div>

    {{-- Delete confirmation modal --}}
    @component('components.delete-confirmation-modal', [
        'delete_id' => $delete_id,
        'delete_function_name' => $delete_function_name,
        'delete_name' => $delete_name
    ])
    
    @endcomponent
{{-- Delete Modal End --}}
</div>