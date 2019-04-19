<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\BlogPostsModel as news;
use App\FeaturedArticleModel;

class NewsController extends Controller
{
    public function getNews() {
        return News::all('id', 'blog_title','article_slug', 'blog_caption', 'blog_thumbnail');
    }

    public function featured() {
        return FeaturedArticleModel::get();
    }

    public function featuredArtice($articleSlug) {
        return FeaturedArticleModel::where('article_slug', $articleSlug)->get();
    }

    public function blogPost($articleSlug) {
        return News::where('article_slug', $articleSlug)->get();
    }
}

