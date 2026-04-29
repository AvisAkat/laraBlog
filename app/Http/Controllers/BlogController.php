<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Post;
use Artesaos\SEOTools\Facades\SEOMeta;
use Artesaos\SEOTools\Facades\SEOTools;

class BlogController extends Controller
{
    public function getTags($limit = null, $slug = null)
    {

        if ($slug) {
            $tags = Post::where('visibility', 1)
                ->where('slug', $slug)
                ->whereNotNull('tags')
                ->pluck('tags');
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
        $featured_post = Post::where('visibility', 1)
            ->orderByDesc('created_at')
            ->first();

        // Get the latest post
        $latest_post = Post::skip(1)
            ->limit(6)
            ->where('visibility', 1)
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
        ];

        return view('front.pages.allPost', $data);
    }
}
