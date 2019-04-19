<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BlogPostsModel extends Model
{
    protected $table ="blog_posts";

    protected $fillable = [
        'blog_title',
        'blog_description',
        'blog_caption',
        'blog_thumbnail',
        'blog_background',
        'external_link',
        'updated_at',
        'created_at'
    ];
}
