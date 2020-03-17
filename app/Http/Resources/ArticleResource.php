<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\Resource;
use App\ArticleModel as Articles;
use App\Http\Controllers\MetaTagsController;
class ArticleResource extends Resource {
    private static $response;
    /**
     * @param $post
     * @return array
     */
    public static function SaveNewArticle($post) {
        $newBlog = new Articles;
        $newBlog->featured_title = $post['featured_title'];
        $newBlog->article_slug = $post['article_slug'];
        $newBlog->featured_caption = $post['featured_caption'];
        $newBlog->featured_thumbnail = $post['featured_thumbnail'];
        $newBlog->featured_background_image = $post['featured_background_image'];
        $newBlog->featured_story = $post['featured_story'];
        $newBlog->external_link = $post['external_link'];
        $newBlog->author = $post['author'];
        $newBlog->updated_at = $post['updated_at'];
        $newBlog->created_at = $post['created_at'];
        $newBlog->save();

        $metaTags = new MetaTagsController;
        $metaTags->saveMetaTags($data = [
            'url' => $post['article_slug'],
            'type' => 'website',
            'title' => $post['featured_title'],
            'description' => $post['featured_caption'],
            'image' => $post['featured_background_image'],
            'image_alt' => $post['image_alt'],
            'image_type' => 'image/jpg',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);

        return self::$response = [
            'successCode' => 200,
            'successMessage' => 'Blog post successful',
            // 'allPosts' => self::allPosts()
        ];
    }

    public static function updateArticleByID($data) {
        return Articles::where('id', '=', $data['id'])->update($data);
    }
}
