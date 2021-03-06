<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\ArticleModel as Articles;
use App\Http\Resources\ArticleResource;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Intervention\Image\ImageManager;
use Illuminate\Support\Facades\Storage;

class ArticleController extends Controller
{
        /**
     * @param Request $request
     * @return array
     */
    public function createNewArticle(Request $request) {

        $post = [
            'featured_title' => strip_tags($request->input('featured_title')),
            'article_slug' => strip_tags($request->input('article_slug')),
            'featured_caption' => strip_tags($request->input('featured_caption')),
            'featured_thumbnail' => strip_tags($request->input('article_thumbnail')),
            'featured_background_image' => strip_tags($request->input('article_background')),
            'featured_story' => $request->input('featured_story'),
            'author' => $request->input('author'),
            'external_link' => strip_tags($request->input('external_link')),
            'updated_at' => Carbon::now(),
            'created_at' => Carbon::now()
        ];

        return ArticleResource::SaveNewArticle($post);
    }

    public function getArticle($articleID) {
        return Articles::where('id', $articleID)->first();
    }

    public function editArticle(Request $request) {

        $data = [
        'id' => $request->input('itemID'),
        'featured_title' => $request->input('featured_title'),
        'article_slug' => $request->input('article_slug'),
        'featured_caption' => $request->input('featured_caption'),
        'featured_thumbnail' => $request->input('featured_thumbnail'),
        'featured_background_image' => $request->input('featured_background_image'),
        'featured_story' => $request->input('featured_story'),
        'author' => $request->input('author'),
        'external_link' => $request->input('external_link'),
        'updated_at' => Carbon::now()
        ];

        return ArticleResource::updateArticleByID($data);
    }

    public function uploadFeaturedThumbnail(Request $request) {
        $fileNameToStore = [];
        if ($request->hasFile('featured_thumbnail')) {
            // get filename with extension
            $fileNameWithExtension = $request->file('featured_thumbnail')->getClientOriginalName();
            // get filename without extension
            $filename = pathinfo($fileNameWithExtension, PATHINFO_FILENAME);
            // get file extension
            $extension = $request->file('featured_thumbnail')->getClientOriginalExtension();
            // filename to store
            $fileNameToStore = $filename . '.' . $extension;
            // upload File to external server
            Storage::disk('thumbnail')->put($fileNameToStore, fopen($request->file('featured_thumbnail'), 'r+'));
            // store $fileNameToStore in the database
        }

        return [
            'successCode' => 205,
            'successMessage' => 'Images uploaded',
            'newImagePath' => $fileNameToStore
        ];
    }

    public function uploadFeaturedBackground(Request $request) {
        $fileNameToStore = [];
        if ($request->hasFile('featured_background_image')) {
            // get filename with extension
            $fileNameWithExtension = $request->file('featured_background_image')->getClientOriginalName();
            // get filename without extension
            $filename = pathinfo($fileNameWithExtension, PATHINFO_FILENAME);
            // get file extension
            $extension = $request->file('featured_background_image')->getClientOriginalExtension();
            // filename to store
            $fileNameToStore = $filename . '.' . $extension;
            // upload File to external server
            Storage::disk('background')->put($fileNameToStore, fopen($request->file('featured_background_image'), 'r+'));
            // store $fileNameToStore in the database
        }

        return [
            'successCode' => 205,
            'successMessage' => 'Images uploaded',
            'newImagePath' => $fileNameToStore
        ];
    }

    public function latest() {
        return Articles::orderBy('id', 'desc')->first();
    }

    public function getCollection($current) {
        $collection =  Articles::select('id')->get();
        $ids = [];
        foreach ($collection as $article) {
            $ids[] =  $article->id;
        }
        if (($key = array_search($current, $ids)) !== false) {
            unset($ids[$key]);
        }
        $remains = array_values($ids);

        return Articles::select('id', 'featured_title', 'article_slug', 'featured_thumbnail')->whereIn('id', $remains)->get();

    }

    public function featuredArticle($articleSlug) {
        return Articles::where('article_slug', $articleSlug)->first();
    }

        /**
     * @return FeaturedArticles[]|\Illuminate\Database\Eloquent\Collection
     */
    public function featured() {
        return Articles::all();
    }

}
