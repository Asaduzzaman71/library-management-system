<?php

namespace App\Interfaces;

use App\Http\Requests\PostRequest;

interface PostInterface
{

    public function getAllPosts();

    public function getPostById($id);

    public function getPostsByCategoryId($id);

    public function requestPost(PostRequest $request, $id = null);

    public function deletePost($id);

    public function deletePostImage($id);
}
