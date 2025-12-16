<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\News;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class NewsController extends Controller
{
    public function index()
    {
        $news = News::latest()->paginate(10);
        return view('admin.news.index', compact('news'));
    }

    public function create()
    {
        return view('admin.news.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'thumbnail' => 'required|image|max:2048', // Main card image
        ]);

        $path = $request->file('thumbnail')->store('news_thumbnails', 'public');

        News::create([
            'title' => $validated['title'],
            'content' => $validated['content'],
            'thumbnail' => $path,
            'is_published' => $request->has('is_published'),
        ]);

        return redirect()->route('admin.news.index')->with('status', 'Article published!');
    }

    public function edit(News $news)
    {
        return view('admin.news.edit', compact('news'));
    }

    public function update(Request $request, News $news)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'thumbnail' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('thumbnail')) {
            if ($news->thumbnail) Storage::disk('public')->delete($news->thumbnail);
            $news->thumbnail = $request->file('thumbnail')->store('news_thumbnails', 'public');
        }

        $news->update([
            'title' => $validated['title'],
            'content' => $validated['content'],
            'is_published' => $request->has('is_published'),
        ]);

        return redirect()->route('admin.news.index')->with('status', 'Article updated!');
    }

    public function destroy(News $news)
    {
        if ($news->thumbnail) Storage::disk('public')->delete($news->thumbnail);
        $news->delete();
        return back()->with('status', 'Article deleted.');
    }

    /**
     * SPECIAL: Handle images pasted directly into the editor
     */
    public function uploadEditorImage(Request $request)
    {
        if ($request->hasFile('file')) {
            $path = $request->file('file')->store('news_content_images', 'public');
            return response()->json(['location' => Storage::url($path)]);
        }
        return response()->json(['error' => 'Upload failed'], 500);
    }
}