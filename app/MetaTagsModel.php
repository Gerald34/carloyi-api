<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @property $id
 * @property $url
 * @property $type
 * @property $title
 * @property $description
 * @property $image
 * @property $image_alt
 * @property $image_type
 * @property $created_at
 * @property $updated_at
 */
class MetaTagsModel extends Model
{
    public $table = "meta_tags";
    public $fillable = [
        'id',
        'url',
        'type',
        'title',
        'description',
        'image',
        'image_alt',
        'image_type',
        'created_at',
        'updated_at'
    ];
}
