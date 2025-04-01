<?php

namespace App\Http\Controllers;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{
    //

    public function index()
    {

        //get post
        $posts = Post::latest()->paginate(5);

        return view('posts.index', compact('posts'));

    }

    public function create(){
        return view('posts.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,webp|max:2048',
            'title' => 'required|min:5',
            'content' => 'required|min:10'
        ]);

        $image = $request->file('image');
        $image->storeAs('public/posts', $image->hashName());

        Post::create([
            'image' => $image->hashName(),
            'title' => $request->title,
            'content' => $request->content
        ]);

        return redirect()->route('post.index')
                        ->with('success', 'Post berhasil ditambahkan');
    }


    public function show($id){

        //get post by ID
        $post = Post::find($id);

        //return view
        return view('posts.show',compact('post'));

    }


    public function edit(Post $post)
    {
        return view('posts.edit', compact('post'));
    }

    public function update(Request $request, Post $post)
    {
        $request->validate([
            'image'     => 'image|mimes:jpeg,png,jpg,webp|max:2048',
            'title'     => 'required|min:5',
            'content'   => 'required|min:10'
        ]);

        	//check image upload
            if ($request->hasFile('image')) {
                // Upload new image
                $image = $request->file('image');
                $image->storeAs('public/posts', $image->hashName());

                // Delete old image if exists
                if ($post->image) {
                    Storage::delete('public/posts/'.$post->image);
                }

                // Update with new image
                $post->update([
                    'image' => $image->hashName(),
                    'title' => $request->title,
                    'content' => $request->content,
                ]);
            } else {
                // Update without image
                $post->update([
                    'title' => $request->title,
                    'content' => $request->content,
                ]);
            }

        //return to post index
        return redirect()->route('post.index')->with('success','Post updated successfully');
    }

    public function destroy(Post $post)
    {
        //delete image
        Storage::delete('public/posts'.$post->image);

        //delete post
        $post->delete();

        //return
        return redirect()->route('post.index')->with('success','Post deleted successfully');
    }



}
