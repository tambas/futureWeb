<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\Post;

use App\User;
use Carbon\Carbon;
use Yajra\Datatables\Datatables;

class PostDatatablesController extends Controller
{
    /**
     * Process datatables ajax request.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function anyData()
    {
        $posts = Post::join('users', 'posts.author_id', '=', 'users.id')
            ->select(['posts.id', 'posts.title', 'users.firstname', 'posts.slug', 'posts.type', 'posts.published', 'posts.published_at']);

        return Datatables::of($posts)
            ->addColumn('action', function ($post) {
                return '
                <a id="preview-'.$post->id.'" class="preview btn btn-xs btn-default" data-toggle="tooltip" title="Preview"><i class="fa fa-eye"></i></a>
                <a href="post/'.$post->id.'/edit" class="edit btn btn-xs btn-default" data-toggle="tooltip" title="Edit"><i class="fa fa-pencil"></i></a>
                <a id="delete-'.$post->id.'" class="delete pull-right btn btn-xs btn-danger" data-toggle="tooltip" title="Delete"><i class="fa fa-trash"></i></a>';
            })
            ->editColumn('published', function ($post) {
                if ($post->isProgrammed()) {
                    $pub = '<span class="label label-info">Programmed</span>';
                }
                if ($post->isDraft()) {
                    $pub = '<span class="label label-danger">Draft</span>';
                }
                if ($post->isPublished()) {
                    $pub = '<span class="label label-success">Published</span>';
                }
                return $pub;
            })
            ->editColumn('title', '{{ str_limit($title, 60) }}')
            ->editColumn('type', '{{ config("dofus.news_type.".$type.".name") }}')
            ->make(true);
    }
}
