<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Post;
use App\Category;
use App\Tag;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $posts = Post::all();
        $tags = Tag::all();

        return view('admin.posts.index', compact('posts', 'tags'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        $categories = Category::all();
        $tags = Tag::all();
        return view('admin.posts.create', compact('categories', 'tags'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
        $request->validate([
            'title'=> 'required|max:255',
            'content'=> 'required|min:5',
            'category_id'=> 'required|exists:categories,id',
            'tags[]'=> 'exists:tags,id',
        ]);

        $postData = $request->all();
        $newPost = new Post();
        $newPost->fill($postData);
        $newPost->slug = Post::createSlug($newPost->title);
        $newPost->save();

        if(array_key_exists('tags', $postData)){
            $newPost->tags()->sync($postData['tags']);
        }
        $newPost->save();

        return redirect()->route('admin.posts.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Post $post)
    {
        //
        if (!$post) {
            abort(404);
        }

        // $category = Category::find($post->category_id);
        return view('admin.posts.show', compact('post'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Post $post)
    {
        //
        if (!$post) {
            abort(404);
        }
        $categories = Category::all();
        $tags = Tag::all();
        return view('admin.posts.edit', compact('post', 'categories', 'tags'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Post $post)
    {
        //
        $request->validate([
            'title'=> 'required|max:255',
            'content'=> 'required|min:5',
            'category_id'=> 'required|exists:categories,id',
            'tags[]'=>'exists:tags,id',
        ]);

        $postData = $request->all();
        $post->fill($postData);
        $post->slug = Post::createSlug($post->title);
        $post->save();

        if(array_key_exists('tags', $postData)){
            $post->tags()->sync($postData['tags']);
        }
        $post->save();
        return redirect()->route('admin.posts.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
        $post = Post::findOrFail($id);
        $post->tags()->sync([]);
        $post->delete();
        return redirect()->route('admin.posts.index');
    }
}
