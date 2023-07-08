<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Post;
use Carbon\Carbon;
use Illuminate\Http\Request;
use \Cache;

use App\Http\Requests;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\Facades\Image;
use Yuansir\Toastr\Facades\Toastr;

class PostController extends Controller
{
    const POSTS_PER_PAGE = 8;

    private function fetchNewsType()
    {
        $typeArray = [];
        if (config('dofus.news_type')) {
            foreach (config('dofus.news_type') as $type) {
                $typeArray[$type['db']] = $type['name'];
            }
        }
        return $typeArray;
    }
    public function index()
    {
        return view('admin.posts.index');
    }

    public function create()
    {
        $typeArray = $this->fetchNewsType();
        return view('admin.posts.create', compact('typeArray'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), Post::$rules['store']);

        if (!array_key_exists($request->type, config('dofus.news_type'))) {
            return redirect(route('admin.post.create'))
                ->withErrors(['type' => 'Le type est invalide'])
                ->withInput();
        }
        if ($validator->fails()) {
            return redirect(route('admin.post.create'))
                ->withErrors($validator)
                ->withInput();
        }
        // DATES //
        $published    = $request->published ? '1' : '0';
        $published_at = $request->published ? $request['published_at'] : Carbon::now();

        // IMAGE (RECEIVE LINK) //
        $explode    = explode(url('/'), $request->url_main_image);
        $image_link = $explode[1];

        // INSERT INTO DB //
        $request->user()->posts()->create([
            'title'        => $request->title,
            'type'         => $request->type,
            'preview'      => $request->preview,
            'content'      => $request->content,
            'image'        => $image_link,
            'published'    => $published,
            'published_at' => $published_at
        ]);

        $this->clearPostsCache(null, $request->type, $this->CountAllPosts(), $this->CountTypePosts($request->type), self::POSTS_PER_PAGE);

        Toastr::success('Post created', $title = null, $options = []);
        return redirect(route('admin.posts'));
    }

    public function destroy(Post $post)
    {
        $this->authorize('destroy', $post); // Edit later (return true)
        $type = $post->type;
        if ($post->id != config('dofus.motd.postid')) {
            $post->delete();
            $this->clearPostsCache($post->id, $type, $this->CountAllPosts(), $this->CountTypePosts($type), self::POSTS_PER_PAGE);

            return response()->json([], 200);
        } else {
            return response()->json(['motd' => ['0' => 'Can\'t delete this post (MOTD)']], 422);
        }
    }

    public function edit(Post $post)
    {
        $typeArray = $this->fetchNewsType();
        $post = Post::findOrFail($post->id);
        return view('admin.posts.edit', compact('post', 'typeArray'));
    }

    public function update(Post $post, Request $request)
    {
        $post = Post::findOrFail($post->id);

        $validator = Validator::make($request->all(), Post::$rules['store']);

        if (!array_key_exists($request->type, config('dofus.news_type'))) {
            return redirect()->back()
                ->withErrors(['type' => 'Le type est invalide'])
                ->withInput();
        }

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        // DATES //
        $published    = $request->published ? '1' : '0';
        $published_at = $request->published ? $request['published_at'] : Carbon::now();

        // IMAGE (RECEIVE LINK) //
        $explode    = explode(url('/'), $request->url_main_image);
        $image_link = $explode[1];

        // UPDATE INTO DB //
        $post->update([
            'title'        => $request->title,
            'type'         => $request->type,
            'preview'      => $request->preview,
            'content'      => $request->content,
            'image'        => $image_link,
            'published'    => $published,
            'published_at' => $published_at
        ]);

        $this->clearPostsCache($post->id, $post->type, $this->CountAllPosts(), $this->CountTypePosts($post->type), self::POSTS_PER_PAGE);

        Toastr::success('Post updated', $title = null, $options = []);
        return redirect(route('admin.posts'));
    }

    private function clearPostsCache($id = null, $type = null, $totalResults, $totalResultsForType = null, $perPage)
    {
        if($id)
            Cache::forget('posts_'.$id);

        $totalPages = (int)ceil($totalResults/$perPage);
            for($x = 1; $x <= $totalPages; $x++)
            {
                Cache::forget('posts_page_' . $x);
                if($type)
                    Cache::forget('posts_'.$type.'_page_' . $x);
            }
        if($type && $totalResultsForType)
        {
            $totalPagesType = (int)ceil($totalResultsForType/$perPage);
            for($y = 1; $y <= $totalPagesType; $y++)
            {
                    Cache::forget('posts_'.$type.'_page_' . $y);
            }
        }
        Cache::forget('posts_index');
    }
    private function CountAllPosts()
    {
        return Post::latest('published_at')->published()->count();
    }
    private function CountTypePosts($type)
    {
        return Post::latest('published_at')->where('type', $type)->published()->count();
    }
}
