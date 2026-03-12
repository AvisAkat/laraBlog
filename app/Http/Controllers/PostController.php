<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\ParentCategory;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

class PostController extends Controller
{
    public function addPost()
    {
        $categories_html = '';
        $pcategories = ParentCategory::whereHas('children')->orderBy('name', 'asc')->get();
        $categories = Category::where('parent', 0)->orderBy('name', 'asc')->get();

        if (count($pcategories) > 0) {
            foreach ($pcategories as $item) {
                $categories_html .= '<optgroup label="'.$item->name.'">';
                foreach ($item->children as $category) {
                    $categories_html .= '<option value="'.$category->id.'">'.$category->name.'</option>';
                }
                $categories_html .= '</optgroup>';
            }
        }

        if (count($categories) > 0) {
            foreach ($categories as $item) {
                $categories_html .= '<option value="'.$item->id.'">'.$item->name.'</option>';
            }
        }

        $data = [
            'pageTitle' => 'Add new post',
            'categories_html' => $categories_html,
        ];

        return view('back.pages.add_post', $data);

    }

    public function createPost(Request $request)
    {
        // form validation
        $request->validate([
            'title' => 'required|unique:posts,title',
            'post_content' => 'required',
            'category' => 'required|exists:categories,id',
            'featured_image' => 'required|mimes:png,jpg,jpeg|max:1024',
        ], [

            'post_content.required' => 'Content fileld is required.',
        ]);

        // create post
        if ($request->hasFile('featured_image')) {
            $path = 'images/posts/';
            $file = $request->file('featured_image');
            $filename = $file->getClientOriginalName();
            $new_filename = time().'_'.$filename;

            // upload featured image
            $upload = $file->move(public_path($path), $new_filename);

            if ($upload) {
                // Generate Resized Image and Thumbnail
                $resized_path = $path.'resized/';
                if (! File::isDirectory($resized_path)) {
                    File::makeDirectory($resized_path, 0777, true, true);
                }

                $manager = new ImageManager(new Driver);

                // Thumbnail (Aspect ratio 1)
                $image1 = $manager->read($path.$new_filename);
                $image1->cover(250, 250)->save($resized_path.'thumb_'.$new_filename);

                // Thumbnail (Aspect ratio 1.6)
                $image2 = $manager->read($path.$new_filename);
                $image2->cover(512, 320)->save($resized_path.'resized_'.$new_filename);

               
                $post = new Post;
                $post->author_id = auth()->id();
                $post->category = $request->category;
                $post->title = $request->title;
                $post->content = $request->post_content;
                $post->featured_image = $new_filename;
                $post->tags = $request->tags;
                $post->meta_keywords = $request->meta_keywords;
                $post->meta_description = $request->meta_description;
                $post->visibility = $request->visibility;
                $saved = $post->save();

                if ($saved) {
                    return response()->json(data: ['status' => 1, 'message' => 'New post has been successfully created.']);
                } else {
                    return response()->json(['status' => 0, 'message' => 'Something went wrong.']);
                }
            } else {
                return response()->json(['status' => 0, 'message' => 'Something went wrong on uploading a featured image.']);
            }
        }
    }
}
