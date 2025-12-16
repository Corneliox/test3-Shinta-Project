<?php

namespace App\Http\Controllers;

use App\Models\News;
use Illuminate\Http\Request;

class NewsController extends Controller
{
    public function index()
    {
        // Show latest published news
        $news = News::where('is_published', true)->latest()->paginate(9);
        return view('news.index', compact('news'));
    }

    public function show(News $news)
    {
        if (!$news->is_published && !auth()->check()) {
            abort(404);
        }
        return view('news.show', compact('news'));
    }
}