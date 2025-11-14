<?php

namespace App\Http\Controllers;

use App\Models\News;
use App\Models\NewsCategory;
use Illuminate\Http\Request;

class NewsController extends Controller
{
    public function index()
    {
        $news = News::with('category')->latest()->paginate(6);
        $categories = NewsCategory::all();
        $featuredNews = News::with('category')->latest()->take(3)->get();
        
        return view('pages.news.index', compact('news', 'categories', 'featuredNews'));
    }

   public function show($slug)
{
    $news = News::where('slug', $slug)->firstOrFail();

    // Related news
    $relatedNews = News::where('category_id', $news->category_id)
                       ->where('id', '!=', $news->id)
                       ->take(4)->get();

    // Popular news: berdasarkan views
    $popularNews = News::orderBy('views', 'desc')->take(5)->get();

    // Recent news: berdasarkan created_at
    $recentNews = News::orderBy('created_at', 'desc')->take(5)->get();

    // Categories
    $categories = NewsCategory::withCount('news')->get();

    // Previous & Next news
    $previousNews = News::where('id', '<', $news->id)->orderBy('id', 'desc')->first();
    $nextNews = News::where('id', '>', $news->id)->orderBy('id', 'asc')->first();

    return view('pages.news.show', compact(
        'news', 'relatedNews', 'popularNews', 'recentNews', 'categories', 'previousNews', 'nextNews'
    ));
}



}
