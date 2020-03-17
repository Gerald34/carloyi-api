<?php

namespace App\Http\Resources;

use App\MetaTagsModel;
use Illuminate\Http\Resources\Json\Resource;

class MetaTagsResource extends Resource
{

    /**
     * @param $data
     * @return array
     */
    public static function createMetaTags($data) {
        return self::_saveMetaData($data);
    }

    /**
     * @param $data
     * @return array
     */
    private static function _saveMetaData($data): array {
        $metaData = new  MetaTagsModel;
        $metaData->url = $data['url'];
        $metaData->type = $data['type'];
        $metaData->title = $data['title'];
        $metaData->description = $data['description'];
        $metaData->image = $data['image'];
        $metaData->image_alt = $data['image_alt'];
        $metaData->image_type = $data['image_type'];
        $metaData->created_at = $data['created_at'];
        $metaData->updated_at = $data['updated_at'];
        $metaData->save();

        return ($metaData === 1) ? ['response' => 'Metadata inserted'] : ['response' => 'Meatadata not saved'];
    }

    /**
     * @param $slug
     * @return mixed
     */
    public static function getMetaData($slug) {
        return MetaTagsModel::where('url', $slug)->first();
    }
}
