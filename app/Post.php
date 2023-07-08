<?php

namespace App;

use Carbon\Carbon;
use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use Sluggable;

    /**
     * Return the sluggable configuration array for this model.
     *
     * @return array
     */
    public function sluggable()
    {
        return [
            'slug' => [
                'source' => 'title'
            ]
        ];
    }

    protected $fillable = [
        'title',
        'type',
        'preview',
        'content',
        'published',
        'published_at',
        'image'
    ];

    public static $rules = [
        'store' => [
            'title' => 'required|min:3|max:40',
            'type'  => 'required',
            'preview'  => 'required',
            'content'  => 'required',
            'url_main_image' => 'required|url',
        ]
    ];

    protected $dates = ['published_at'];

    public function scopePublished($query)
    {
        $query->where('published_at', '<=', Carbon::now())->where('published', '=', '1');
    }

    public function scopeUnpublished($query)
    {
        $query->where('published_at', '>', Carbon::now());
    }

    public function author()
    {
        return $this->hasOne(User::class, 'id', 'author_id');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class, 'post_id', 'id')->orderBy('created_at', 'desc');
    }

    public function isDraft()
    {
        return($this->published == 0);
    }

    public function isProgrammed()
    {
        return ($this->published == 1 and $this->published_at > Carbon::now());
    }

    public function isPublished()
    {
        return($this->published == 1 and $this->published_at <= Carbon::now());
    }
}
