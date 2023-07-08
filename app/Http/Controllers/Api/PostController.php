<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Post;
use \Cache;
class PostController extends Controller
{
    public function get(){
        $posts = Cache::remember('posts_api', 30, function () {
            $postsDB =  Post::latest('published_at')->orderBy('id', 'desc')->published()->take(2)->get();
            $postsarray = [];
            foreach($postsDB as $post)
            {
                $news = [
                    'id' => $post->id,
                    'title' => $post->title,
                    'slug' => $post->slug,
                    'type' => $post->type,
                    'preview' => $post->preview,
                    'image' => $post->image,
                    'published_at' => ucwords(utf8_encode($post->published_at->formatLocalized('%e %B %Y')))
                ];
                array_push($postsarray, $news);
            }
                return $postsarray;
        });
        return response()->json($posts);
    }
}
