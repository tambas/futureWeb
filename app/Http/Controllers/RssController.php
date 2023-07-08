<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use \Cache;

use App\Post;

class RssController extends Controller
{
    public function news()
    {
        $posts = Cache::remember('posts_rss', 10, function () {
            return Post::orderBy('created_at', 'desc')->get();
        });

        return view('rss/news', ['posts' => $posts]);
    }
}
