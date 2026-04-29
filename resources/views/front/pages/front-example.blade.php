@extends('front.layout.pages-layout')
@section('pageTitle', isset($pageTitle) ? $pageTitle : 'BlogVerse')
@section('content')

---page content here -----

@endsection

{{-- use Illuminate\Support\Facades\Cache;
use App\Models\Post;

public function readPost($slug)
{
    $post = Post::where('slug', $slug)->firstOrFail();

    // Build a unique viewer signature
    $ip = request()->ip();
    $agent = request()->userAgent();

    // Optional: shorten agent to avoid extremely long cache keys
    $agent = substr($agent, 0, 100);

    $key = 'post_viewed_' . $post->id . '_' . md5($ip . '|' . $agent);

    if (!Cache::has($key)) {

        $post->increment('views');

        // store for 24 hours
        Cache::put($key, true, now()->addHours(24));
    }

    return view('blog.read-post', compact('post'));
} --}}