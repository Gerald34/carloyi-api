<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Resources\MetaTagsResource;

class MetaTagsController extends Controller
{
    /**
     * @param $data
     * @return array
     */
    public function saveMetaTags($data) {
        $data = [
            'url' => $data['url'],
            'type' => $data['type'],
            'title' => $data['title'],
            'description' => $data['description'],
            'image' => $data['image'],
            'image_alt' => $data['image_alt'],
            'image_type' => $data['image_type'],
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ];
        return MetaTagsResource::createMetaTags($data);
    }

    /**
     * @param $slug
     * @return mixed
     */
    public function getMetaTags($slug) {
        return MetaTagsResource::getMetaData($slug);
    }
}
