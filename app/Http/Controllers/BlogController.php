<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Artesaos\SEOTools\Facades\SEOTools;
use Artesaos\SEOTools\Facades\SEOMeta;

class BlogController extends Controller
{
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

        //Get the most recent post
        $featured_post = Post::where('visibility', 1)
                ->orderByDesc('created_at')
                ->first();
      
        //Get the latest post
        $latest_post = Post::skip(1)
            ->limit(6)
            ->where('visibility', 1)
            ->orderByDesc('created_at')
            ->get();


        $data = [
            'pageTitle' => $title,
            'featuredPost' => $featured_post,
            'latestPost' => $latest_post,
        ];

        return view('front.pages.index', $data);
    }
}
