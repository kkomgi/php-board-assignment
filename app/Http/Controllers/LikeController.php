<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Services\LikeService;

class LikeController extends Controller
{
    public function __construct(private LikeService $likeService)
    {
    }

    public function store(Post $post)
    {
        $this->likeService->like($post);

        return $this->success(null, '좋아요 완료');
    }

    public function destroy(Post $post)
    {
        $this->likeService->unlike($post);

        return $this->success(null, '좋아요 취소');
    }

    public function count(Post $post)
    {
        return $this->success([
            'post_id'     => $post->id,
            'likes_count' => $this->likeService->countLikes($post),
        ]);
    }
}
