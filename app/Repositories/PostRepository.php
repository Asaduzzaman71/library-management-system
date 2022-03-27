<?php

namespace App\Repositories;

use App\Http\Requests\PostRequest;
use App\Interfaces\PostInterface;
use App\Traits\ResponseAPI;
use App\Models\Post;
use App\Models\PostImage;
use \Illuminate\Support\Facades\DB;
use App\Traits\FileUpload;
use Illuminate\Support\Facades\Storage;


class PostRepository implements PostInterface
{
    // Use ResponseAPI Trait in this repository
    use ResponseAPI;
    // Use FileUpload Trait in this repository
    use FileUpload;


    public function getAllPosts()
    {
        try {
            $posts = Post::with('postImages')->get();
            return $this->success("All Posts", $posts);
        } catch(\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode());
        }
    }

    public function getPostById($id)
    {
        try {
            $post = Post::find($id);
            // Check the post
            if(!$post) return $this->error("No post with ID $id", 404);
            return $this->success("Post Detail", $post);
        } catch(\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode());
        }
    }

    public function getPostsByCategoryId($id){

        try {
            $posts = Post::with('postImages')->where('category_id',$id)->latest()->get();
            // Check the post
            if( count($posts) > 0 )   return $this->success("Post list", $posts);
            return $this->error("No post with category ID $id", 404);

        } catch(\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode());
        }
    }

    public function requestPost(PostRequest $request, $id = null)
    {
        DB::beginTransaction();
        try {

            $post = $id ? Post::find($id) : new Post;
            // Check the Post
            if($id && !$post) return $this->error("No post with ID $id", 404);
            $post->category_id = (int)$request->category_id;
            $post->title = $request->title;
            //$post->slug = Str::slug($request->title);
            $post->excerpt = $request->excerpt;
            $post->content = $request->content;
            $post->author_id = auth()->id();
            // Save the Post
            $post->save();

            if($request->hasFile('images')){
                foreach($request->file('images') as $image){
                    $path = $this->FileUpload($image,'blog');
                    $postImage = new PostImage();
                    $postImage->post_id =  $post->id;
                    $postImage->image =  $path;
                    $postImage->save();
                }
            }

            $post = Post::with('postImages')->where('id',$post->id)->first();
            DB::commit();
            return $this->success(
                $id ? "Post updated"
                    : "Post created",
                $post, $id ? 200 : 201);
        } catch(\Exception $e) {
            DB::rollBack();
            return $this->error($e->getMessage(), $e->getCode());
        }
    }

    public function deletePost($id)
    {
        DB::beginTransaction();
        try {
            $post = Post::find($id);

            // Check the Post
            if(!$post) return $this->error("No post with ID $id", 404);

            // Delete the Post
            $post->delete();
            $postImages = PostImage::where('post_id',$id)->get();
            foreach($postImages as $postImage){
                Storage::disk('public')->delete($postImage->image);
                $postImage->delete();
            }

            DB::commit();
            return $this->success("Post deleted", $post);
        } catch(\Exception $e) {
            DB::rollBack();
            return $this->error($e->getMessage(), $e->getCode());
        }
    }

    public function deletePostImage($id)
    {
        DB::beginTransaction();
        try {
            $postImage = PostImage::find($id);
            Storage::disk('public')->delete($postImage->image);
            if(!$postImage) return $this->error("No post image with ID $id", 404);
            $postImage->delete();

            DB::commit();
            return $this->success("Post Image deleted", $postImage);
        } catch(\Exception $e) {
            DB::rollBack();
            return $this->error($e->getMessage(), $e->getCode());
        }
    }
}
