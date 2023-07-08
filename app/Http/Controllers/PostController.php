<?php

namespace App\Http\Controllers;

use App\Exceptions\GenericException;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Services\Pagination;
use \Cache;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Validator;
use App\Post;
use App\Comment;
use ChrisKonnertz\OpenGraph\OpenGraph;

class PostController extends Controller
{
    const CACHE_EXPIRE_MINUTES = 10;
    const POSTS_PER_PAGE = 8;
    const POSTS_IN_INDEX = 6;
    const COMMENTS_PER_PAGE = 10;

    public function index(Request $request)
    {
        $posts = Cache::remember('posts_index', self::CACHE_EXPIRE_MINUTES, function () {
            return Post::latest('published_at')->orderBy('id', 'desc')->published()->paginate(self::POSTS_IN_INDEX);
        });

        return view('posts.index', compact('posts'));
    }

    public function news(Request $request)
    {
        $page = $request->has('page') && is_numeric($request->input('page')) ? $request->input('page') : 1;
        if (!is_numeric($page)) {
            abort(404);
        }
        
        $posts = Cache::remember('posts_page_' . $page, self::CACHE_EXPIRE_MINUTES, function () {
            return Post::latest('published_at')->orderBy('id', 'desc')->published()->paginate(self::POSTS_PER_PAGE);
        });

        return view('posts.news', compact('posts'));
    }

    public function newsType(Request $request, $type)
    {
        if (config('dofus.news_type.'.$type)) {
            $typeConfig = config('dofus.news_type.'.$type);
            $page = $request->has('page') && is_numeric($request->input('page')) ? $request->input('page') : 1;

            $posts = Cache::remember('posts_'.$type.'_page_' . $page, self::CACHE_EXPIRE_MINUTES, function () use ($type) {
                return Post::latest('published_at')->orderBy('id', 'desc')->published()->where('type', $type)->paginate(self::POSTS_PER_PAGE);
            });

            return view('posts.type', compact('posts', 'type', 'typeConfig'));
        } else {
            throw new GenericException('invalid_news_type');
        }
    }

    public function show(Request $request, $id, $slug = "")
    {
        $result = false;
        $page = $request->has('page') && is_numeric($request->input('page')) ? $request->input('page') : 1;
        $errors;
        if (!is_numeric($page)) {
            abort(404);
        }
        
        $post = Cache::remember('posts_' . $id, self::CACHE_EXPIRE_MINUTES, function () use ($id) {
            return Post::findOrFail($id);
        });

        if ($post->isDraft() && (Auth::guest() || !Auth::user()->can('view-drafts'))) {
            abort(404);
        }

        if ($slug == "" || $slug != $post->slug) {
            return redirect()->route('posts.show', ['id' => $id, 'slug' => $post->slug]);
        }

        $og = new OpenGraph();

        $og->title($post->title)
           ->type('article')
           ->image(URL::asset($post->image))
           ->description(html_entity_decode(strip_tags($post->preview)))
           ->url($request->url())
           ->siteName(config('dofus.title') . ' - ' . config('dofus.subtitle'));

        if ($request->pjax() && $request->isMethod('post')) 
        {
            $validator = Validator::make($request->all(), Comment::$rules['store']);
            if ($validator->fails()) 
            {
                $errors = $validator->messages();
            }
            else
            {
                $comment = new Comment;
                $comment->post_id = $id;
                $comment->author_id = Auth::user()->id;
                $comment->text = $request->comment;
                $comment->save();
                
                $totalResults = Comment::where('post_id', $id)->count();
                $this->clearCommentsCache($totalResults, $id, self::COMMENTS_PER_PAGE);
                $page = $this->getLastPage($totalResults, self::COMMENTS_PER_PAGE);
                $result = true;
            }
        }

        $comments = Cache::remember('posts_' . $id . '_comments_' . $page, self::CACHE_EXPIRE_MINUTES, function () use ($id, $page) {
            return Comment::where('post_id', $id)->orderBy('created_at', 'asc')->paginate(self::COMMENTS_PER_PAGE, ['*'], 'page', $page);
        });

        return view('posts.show', compact('post', 'comments', 'og', 'errors', 'result'));
    }

    public function redirect(Request $request, $id, $slug = "")
    {
        return redirect()->route('posts.show', ['id' => $id, 'slug' => $slug]);
    }

    public function commentDestroy(Request $request, $id, $slug = "", $commentid)
    {
        $comment = Comment::findOrFail($commentid);
        $this->authorize('destroy', $comment); // Edit later (return true)
        $comment->delete();
        $totalResults = Comment::where('post_id', $id)->count();
        $this->clearCommentsCache($totalResults, $id, self::COMMENTS_PER_PAGE);
            
        return redirect()->back();
    }

    public function clearCommentsCache($totalResults, $postId, $perPage)
    {
        $totalPages = (int)ceil($totalResults/$perPage);
        for($x = 1; $x <= $totalPages; $x++)
        {
            Cache::forget('posts_' . $postId . '_comments_'. $x);
        }
    }
    public function getLastPage($totalResults, $perPage)
    {
        return (int)ceil($totalResults/$perPage);
    }
}
