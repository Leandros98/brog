<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use Illuminate\Support\Facades\Auth;

class PostController extends Controller
{
    public function index()
    {
        $posts = Post::where('is_shared', true)
                     ->orWhere('user_id', Auth::id())
                     ->with('user')
                     ->get();

        return response()->json($posts);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'is_shared' => 'boolean',
        ]);

        $post = Post::create([
            'user_id' => Auth::id(),
            'title' => $request->title,
            'content' => $request->content,
            'is_shared' => $request->is_shared,
        ]);

        return response()->json($post, 201);
    }

    public function show($id)
    {
        $post = Post::with('user')->findOrFail($id);
        if (!$post->is_shared && $post->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        return response()->json($post);
    }
}
