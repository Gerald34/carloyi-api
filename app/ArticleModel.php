<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ArticleModel extends Model
{
    public $table = 'featured_articles';
        public $fillable = [
        'id',
        'featured_title ',
        'article_slug',
        'featured_caption',
        'featured_thumbnail',
        'featured_background_image',
        'featured_story',
        'author',
        'external_link',
        'created_at',
        'updated_at'
    ];
}
