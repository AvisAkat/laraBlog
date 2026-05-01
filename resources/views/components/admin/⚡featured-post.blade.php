<?php

use Livewire\Component;
use App\Models\Post;
use Livewire\WithPagination;
use App\Models\ParentCategory;
use App\Models\Category;
use Illuminate\Support\Facades\File;

new class extends Component {
    use WithPagination;

    public $postPerPage = 13;

    //filter properties
    public $search = null;
    public $author = null;
    public $category = null;
    public $visibility = null;
    public $sortBy = 'desc';

    //Read more modal properties
    public $selected_post = null;

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

    public function showReadMoreModal($post)
    {
        $this->selected_post = $post;
        $this->dispatch('show_read_more_modal');
    }

    public function hideReadMoreModal()
    {
        $this->selected_post = null;
        $this->dispatch('hide_read_more_modal');
    }

    //Make a post featured
    public function makePostFeatured($post_id)
    {
        $post = Post::findOrFail($post_id);

        //Check if the post is already featured
        if ($post->is_featured) {
            //If the post is already featured, then unfeature it.
            $this->dispatch('showAlert', ['type' => 'info', 'message' => 'Post is already featured!']);
        } else {
            //If the post is not featured, then feature it.
            if ($post->visibility == 0) {
                $this->dispatch('showAlert', ['type' => 'warning', 'message' => 'Private post cannot be featured!']);
                return;
            }

            //Unfeature all other posts
            Post::where('is_featured', true)->update(['is_featured' => false]);

            //Feature the selected post
            $post->is_featured = true;
            $post->save();

            $this->dispatch('showAlert', ['type' => 'success', 'message' => 'Post featured successfully!']);
        }
    }

    public function currentFeaturedPost()
    {
        return Post::where('is_featured', true)->first();
    }
};
?>

<div>
    <div class="pd-20 card-box mb-30">
        <h4 class="text-blue h4 mt-10 mb-10">Current Featured Post</h4>
        <div class="container pd-0">
            <div class="blog-list">
                <ul>
                    <li>
                        <div class="row no-gutters">
                            <div class="col-lg-4 col-md-12 col-sm-12">
                                <div class="blog-img"
                                    style="background: url(&quot;{{ asset('images/posts/' . $this->currentFeaturedPost()->featured_image) }}&quot;) center center no-repeat;">
                                    <img src="{{ asset('images/posts/' . $this->currentFeaturedPost()->featured_image) }}"
                                        alt="" class="bg_img" style="display: none">
                                </div>
                            </div>
                            <div class="col-lg-8 col-md-12 col-sm-12">
                                <div class="blog-caption">
                                    <h3 class="mb-20">
                                        {{ $this->currentFeaturedPost()->title }}
                                    </h3>
                                    <div class="blog-by">
                                        <p>
                                            {!! Str::ucfirst(strip_words($this->currentFeaturedPost()->content, 60)) !!}
                                        </p>
                                        <div class="text-secondary">
                                            <span class="">
                                                <i class="icon-copy bi bi-person-fill"></i>
                                                {{ $this->currentFeaturedPost()->author->name }}
                                            </span>
                                            <span class="">
                                                | <i class="icon-copy bi bi-clock"></i>
                                                {{ readingDuration($this->currentFeaturedPost()->title, $this->currentFeaturedPost()->content) }}
                                                @choice('min|mins', readingDuration($this->currentFeaturedPost()->title, $this->currentFeaturedPost()->content))
                                                read
                                            </span>
                                            <span class="">
                                                | <i class="icon-copy bi bi-calendar2-date"></i>
                                                {{ $this->currentFeaturedPost()->created_at->format('M d, Y') }}
                                            </span>
                                        </div>
                                        <div class="pt-10">
                                            <a href="javascript:;"
                                                wire:click="showReadMoreModal({{ $this->currentFeaturedPost() }})"
                                                class="btn btn-outline-primary">Read More</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <div class="pd-20 card-box mb-30">
        <h4 class="text-blue h4 mt-10 mb-10">All Post</h4>
        <div class="row mb-20">
            <div class="col-md-4">
                <label for="search"><b class="text-secondary">Search</b>:</label>
                <input type="text" wire:model.live="search" id="search" class="form-control" placeholder="Search posts">
            </div>
            @if (auth()->user()->type === \App\UserType::SuperAdmin)
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
                                    <a href="javascript:;" wire:click="showReadMoreModal({{ $item }})" data-color=" #265ed7"
                                        data-toggle="tooltip" title="read more" style="color: rgb(38, 94, 215)">
                                        <i class="icon-copy bi bi-file-arrow-up-fill"></i>
                                    </a>
                                    <a href="javascript:;" wire:click="makePostFeatured({{ $item->id }})"
                                        data-color="#099b13" data-toggle="tooltip" title="Make Featured"
                                        style="color: #099b13">
                                        <i class="icon-copy bi bi-check-square-fill"></i>
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

    {{-- Read more modal --}}
    <div wire:ignore.self class="modal fade bs-example-modal-lg" id="read_more_modal" tabindex="-1" role="dialog"
        aria-labelledby="myLargeModalLabel" aria-modal="true" style="padding-right: 15px;">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="myLargeModalLabel">
                        Read More
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                        ×
                    </button>
                </div>
                <div class="modal-body">
                    <div class="blog-detail">
                        <h4 class="mb-10 text-center mb-40">
                            {{ $this->selected_post ? $this->selected_post['title'] : '' }}
                        </h4>
                        <div class="blog-img" style="display: flex;justify-content: center;">
                            <img style="width: 80%"
                                src="{{ asset('images/posts/' . optional($this->selected_post)['featured_image']) }}"
                                alt="">
                        </div>
                        <div class="blog-caption">
                            <p>
                                Lorem ipsum dolor sit amet, consectetur adipisicing
                                elit, sed do eiusmod tempor incididunt ut labore et
                                dolore magna aliqua. Ut enim ad minim veniam, quis
                                nostrud exercitation ullamco laboris nisi ut aliquip
                                ex ea commodo consequat. Duis aute irure dolor in
                                reprehenderit in voluptate velit esse cillum dolore eu
                                fugiat nulla pariatur. Excepteur sint occaecat
                                cupidatat non proident, sunt in culpa qui officia
                                deserunt mollit anim id est laborum.
                            </p>
                            <p>
                                Lorem ipsum dolor sit amet, consectetur adipisicing
                                elit, sed do eiusmod tempor incididunt ut labore et
                                dolore magna aliqua. Ut enim ad minim veniam, quis
                                nostrud exercitation ullamco laboris nisi ut aliquip
                                ex ea commodo consequat. Duis aute irure dolor in
                                reprehenderit in voluptate velit esse cillum dolore eu
                                fugiat nulla pariatur. Excepteur sint occaecat
                                cupidatat non proident, sunt in culpa qui officia
                                deserunt mollit anim id est laborum.
                            </p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        Close
                    </button>
                    <button type="button" class="btn btn-primary"
                        wire:click="makePostFeatured({{ optional($this->selected_post)['id'] }})">
                        Make Featured
                    </button>
                </div>
            </div>
        </div>
    </div>

</div>