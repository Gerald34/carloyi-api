<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @property  $id
 * @property $featured_title
 * @property $article_slug
 * @property $featured_caption
 * @property $featured_thumbnail
 * @property $featured_background_image
 * @property $featured_story
 * @property $author
 * @property $external_link
 * @property $created_at
 * @property $updated_at
 */
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
