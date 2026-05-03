<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Post;
use App\Models\User;
use Artesaos\SEOTools\Facades\SEOMeta;
use Artesaos\SEOTools\Facades\SEOTools;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BlogController extends Controller
{
    public function getTags($limit = null, $slug = null, $category = null, $author = null, $Posttags = null)
    {
        if ($slug) {
            $tags = Post::where('visibility', 1)
                ->where('slug', $slug)
                ->whereNotNull('tags')
                ->pluck('tags');
        } elseif ($category) {
            $tags = Post::where('visibility', 1)
                ->where('category', $category)
                ->whereNotNull('tags')
                ->pluck('tags');
        } elseif ($author) {
            $tags = Post::where('visibility', 1)
                ->where('author_id', $author)
                ->whereNotNull('tags')
                ->pluck('tags');
        } elseif ($Posttags) {
            $tags = $Posttags;
        } else {
            $tags = Post::where('visibility', 1)
                ->whereNotNull('tags')
                ->pluck('tags');
        }

        $unique_tags = $tags->flatMap(function ($tagsString) {
            return explode(',', $tagsString);
        })->map(fn ($tag) => trim($tag)) // Trim any extra white spaces
            ->unique()
            ->sort()
            ->values();

        if ($limit) {
            $unique_tags = $unique_tags->take($limit);
        }

        return $unique_tags->all();
    }

    public function getPostCategories($limit = null)
    {
        $post_categories = Category::withCount('posts')
            ->having('posts_count', '>', 0)
            ->limit($limit)
            ->orderBy('posts_count', 'desc')
            ->get();

        return $post_categories;
    }

    public function index()
    {
        $title = isset(settings()->site_title) ? settings()->site_title : '';
        $description = isset(settings()->site_meta_description) ? settings()->site_meta_description : '';
        $imgURL = isset(settings()->site_logo) ? asset('/images/site/'.settings()->site_logo) : '';
        $keywords = isset(settings()->site_meta_keywords) ? settings()->site_meta_keywords : '';
        $currentUrl = isset(settings()->site_meta_keywords) ? settings()->site_meta_keywords : '';

        /** Meta SEO */
        SEOTools::setTitle($title, false);
        SEOTools::setDescription($description);
        SEOMeta::setKeywords($keywords);

        /** Open Graph */
        SEOTools::opengraph()->setUrl($currentUrl);
        SEOTools::opengraph()->addImage($imgURL);
        SEOTools::opengraph()->addProperty('type', 'articles');

        /** Twitter */
        SEOTools::twitter()->addImage($imgURL);
        SEOTools::twitter()->setUrl($currentUrl);
        SEOTools::twitter()->setSite('@ScribbleDiary');

        // Get the most recent post
        $featured_post = Post::where('is_featured', true)
            ->where('visibility', 1)
            ->orderByDesc('created_at')
            ->first();

        // Get the latest post
        $latest_post = Post::where('is_featured', false)
            ->where('visibility', 1)
            ->limit(6)
            ->orderByDesc('created_at')
            ->get();

        // Get Post Categories
        $post_categories = $this->getPostCategories(6);

        $data = [
            'pageTitle' => $title,
            'featuredPost' => $featured_post,
            'latestPost' => $latest_post,
            'postCategories' => $post_categories,
        ];

        return view('front.pages.index', $data);
    }

    public function allPost()
    {
        $title = isset(settings()->site_title) ? settings()->site_title : '';
        $description = isset(settings()->site_meta_description) ? settings()->site_meta_description : '';
        $imgURL = isset(settings()->site_logo) ? asset('/images/site/'.settings()->site_logo) : '';
        $keywords = isset(settings()->site_meta_keywords) ? settings()->site_meta_keywords : '';
        $currentUrl = isset(settings()->site_meta_keywords) ? settings()->site_meta_keywords : '';

        /** Meta SEO */
        SEOTools::setTitle($title, false);
        SEOTools::setDescription($description);
        SEOMeta::setKeywords($keywords);

        /** Open Graph */
        SEOTools::opengraph()->setUrl($currentUrl);
        SEOTools::opengraph()->addImage($imgURL);
        SEOTools::opengraph()->addProperty('type', 'articles');

        /** Twitter */
        SEOTools::twitter()->addImage($imgURL);
        SEOTools::twitter()->setUrl($currentUrl);
        SEOTools::twitter()->setSite('@ScribbleDiary');

        // Get Post Categories
        $post_categories = $this->getPostCategories(10);

        // Get Popular Posts
        $popular_posts = Post::where('visibility', 1)
            ->limit(5)
            ->get();

        // Get all posts with pagination
        $all_posts = Post::where('visibility', 1)
            ->orderByDesc('created_at')
            ->paginate(12);

        // Get Unique Tags
        $unique_tags = $this->getTags(15);

        $data = [
            'pageTitle' => $title,
            'postCategories' => $post_categories,
            'popularPosts' => $popular_posts,
            'allPosts' => $all_posts,
            'allTags' => $unique_tags,
            'bannerInfo' => null,
        ];

        return view('front.pages.allPost', $data);
    }

    public function categoryPosts($slug = null)
    {
        // Find Category by slug
        $category = Category::where('slug', $slug)->firstOrFail();

        // Retriving post related to category
        $post = Post::where('category', $category->id)
            ->where('visibility', 1)
            ->orderBy('created_at')
            ->paginate(12);

        // Get Post Categories
        $post_categories = $this->getPostCategories(10);

        // Get Popular Posts
        $popular_posts = Post::where('visibility', 1)
            ->where('category', $category->id)
            ->limit(5)
            ->get();

        // Get category related Tags
        $tags = $this->getTags(15, null, $category->id);

        $title = 'Post in Category '.$category->name;
        $description = 'Browse the lastest posts in the '.$category->name.' category. Stay updated with articles, insights and tutorials.';

        /** Set SEO Meta Tags */
        SEOTools::setTitle($title, false);
        SEOTools::setDescription($description);
        SEOTools::opengraph()->setUrl(Url()->current());

        $data = [
            'pageTitle' => $title,
            'allPosts' => $post,
            'postCategories' => $post_categories,
            'popularPosts' => $popular_posts,
            'allTags' => $tags,
            'categoryName' => $category->name,
            'bannerInfo' => $category->name,
        ];

        return view('front.pages.allPost', $data);
    }

    public function tagPosts($tagName = null)
    {
        // Find all post related to the Tag
        $post = Post::where('tags', 'LIKE', "%{$tagName}%")
            ->where('visibility', 1)
            ->paginate(12);

        // Get Post Categories
        $post_categories = $this->getPostCategories(10);

        // Get Popular Posts
        $popular_posts = Post::where('tags', 'LIKE', "%{$tagName}%")
            ->where('visibility', 1)
            ->limit(5)
            ->get();

        // Get Tags
        $tags = $this->getTags(15);

        /** For Meta Tags */
        $title = 'Post tagged with '.$tagName;
        $description = "Explore our collection of posts tagged with {$tagName}.";

        /** Set SEO Meta Tags */
        SEOTools::setTitle($title, false);
        SEOTools::setDescription($description);
        SEOTools::setCanonical(Url()->current());

        SEOTools::opengraph()->setUrl(Url()->current());
        SEOTools::opengraph()->addProperty('type', 'articles');

        $data = [
            'pageTitle' => $title,
            'allPosts' => $post,
            'allTags' => $tags,
            'postCategories' => $post_categories,
            'popularPosts' => $popular_posts,
            'bannerInfo' => $tagName,
        ];

        return view('front.pages.allPost', $data);

    }

    public function authorPosts($username = null)
    {
        $author = User::where('username', $username)->firstOrFail();
        // Find all post related to the Author
        $post = Post::where('author_id', $author->id)
            ->where('visibility', 1)
            ->paginate(12);

        // Get Post Categories
        $categories = Category::withCount('posts')
            ->having('posts_count', '>', 0)
            ->get();

        // Get Post Categories by the author
        $post_categories = [];

        foreach ($post as $item) {
            foreach ($categories as $category) {
                if ($item->category == $category->id) {
                    $post_categories[] = $category;
                }
            }
        }

        // Get Popular Posts
        $popular_posts = Post::where('author_id', $author->id)
            ->where('visibility', 1)
            ->orderByDesc('number_of_views')
            ->limit(5)
            ->get();

        // Get Tags
        $tags = $this->getTags(15, null, null, $author->id);

        /** For Meta Tags */
        $title = 'Post by '.$author->name;
        $description = "Explore our collection of posts by {$author->name} on various topics.";

        /** Set SEO Meta Tags */
        SEOTools::setTitle($title, false);
        SEOTools::setDescription($description);
        SEOTools::setCanonical(route('blog.author_posts', ['username' => $author->username]));

        SEOTools::opengraph()->setUrl(route('blog.author_posts', ['username' => $author->username]));
        SEOTools::opengraph()->addProperty('type', 'profile');
        SEOTools::opengraph()->setProfile([
            'first_name' => $author->name,
            'username' => $author->username,
        ]);

        $data = [
            'pageTitle' => $title,
            'author' => $author,
            'allPosts' => $post,
            'allTags' => $tags,
            'postCategories' => $post_categories,
            'popularPosts' => $popular_posts,
            'bannerInfo' => $author->name,
        ];

        return view('front.pages.allPost', $data);

    }

    public function searchPosts(Request $request)
    {
        // Search Validation
        $request->validate([
            'articleSearch' => 'required|string|max:200|regex:/^[\pL\pN\s]+$/u',
        ], [
            'articleSearch.required' => 'Enter at least 2 characters.',
            'articleSearch.max' => 'Characters are too long.',
            'articleSearch.regex' => 'Search must only contain letters and numbers.',
        ]);

        $query = $request->input('articleSearch');

        $keywords = explode(' ', $query);
        $postsQuery = Post::query();

        foreach ($keywords as $keyword) {
            $postsQuery->orWhere('title', 'LIKE', '%'.$keyword.'%')
                ->orwhere('tags', 'LIKE', '%'.$keyword.'%');
        }
        $posts = $postsQuery->where('visibility', 1)
            ->orderByDesc('created_at')
            ->paginate(12);

        //Getting tags related to the search
        $Posttags = $posts->pluck('tags');
        $tags = $this->getTags(15, null, null, null, $Posttags);

        //Getting most view post among the searches
        $popular_posts = $postsQuery->where('visibility' , 1)
                                    ->orderByDesc('number_of_views')
                                    ->limit(5)
                                    ->get();


        //Getting Post Categories related to the search
        $categories = Category::withCount('posts')
            ->having('posts_count', '>', 0)
            ->get();

        $post_categories = [];

        foreach ($posts as $item) {
            foreach ($categories as $category) {
                if ($item->category == $category->id) {
                    $post_categories[] = $category;
                }
            }
        }

        /** Meta Tags */
        $title = "Search results for {$query}";
        $description = "Browse search results for {$query} on our blog,";

        SEOTools::setTitle($title, false);
        SEOTools::setDescription($description);

        $data = [
            'pageTitle' => $title,
            'bannerInfo' => $query,
            'allPosts' => $posts,
            'allTags' => $tags,
            'postCategories' => $post_categories,
            'popularPosts' => $popular_posts,
        ];

        return view('front.pages.allPost', $data);
    }

    public function readPost($slug = null)
    {
        //Fetch single post by slug
        $post = POST::where('slug', $slug)->firstOrFail();

        //Get related post
        $relatedPosts = Post::where('category', $post->category)
                            ->where('id', '!=', $post->id)
                            ->where('visibility', 1)
                            ->take(3)
                            ->get();

        //Get the next post
        $nextPost = Post::where('id', '>' , $post->id)
                        ->where('visibility', 1)
                        ->orderBy('id', 'asc')
                        ->first();

        //Get the previous post
        $prevPost = Post::where('id', '<' , $post->id)
                        ->where('visibility', 1)
                        ->orderBy('id', 'desc')
                        ->first();

        //Get more articles you may like
        $moreArticles = Post::where('id', '!=', $post->id)
                            ->inRandomOrder()
                            ->limit(3)
                            ->get();

        //Get the post tags
        $postTags = POST::where('slug', $slug)->value('tags');
        $tags = explode(',', $postTags);

        //Set SEO Meta Tags
        $title = $post->title;
        $description = ($post->meta_description != '') ? $post->meta_description : str::words($post->content, 35);

        SEOTools::setTitle($title, false);
        SEOTools::setDescription($description);
        SEOTools::opengraph()->setUrl(route('blog.read_post', ['slug' => $post->slug]));
        SEOTools::opengraph()->addProperty('type', 'article');
        SEOTools::opengraph()->addImage(asset('images/posts/'. $post->featured_image));
        SEOTools::twitter()->setImage(asset('images/posts/'.$post->fetured_image));

        $data = [
            'pageTitle' => $title,
            'post' => $post,
            'relatedPosts' => $relatedPosts,
            'nextPost' => $nextPost,
            'previousPost' => $prevPost,
            'tags' => $tags,
            'moreArticles' => $moreArticles
        ];

        return view('front.pages.single_post', $data);


    }
}
