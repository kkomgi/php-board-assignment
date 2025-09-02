<?php

namespace App\Services;

use App\Models\Post;
use Illuminate\Support\Facades\Auth;

class LikeService
{
    public function like(Post $post): void
    {
        $userId = Auth::id();

        // 이미 좋아요했는지 체크
        $exists = $post->likes()->where('user_id', $userId)->exists();
        if ($exists) {
            abort(400, '이미 좋아요한 게시글입니다.');
        }

        $post->likes()->create([
            'user_id' => $userId,
        ]);
    }

    public function unlike(Post $post): void
    {
        $userId = Auth::id();

        // 해당 유저의 좋아요 삭제 (있으면 삭제, 없으면 영향 없음)
        $post->likes()->where('user_id', $userId)->delete();
    }

    public function countLikes(Post $post): int
    {
        return $post->likes()->count();
    }
}
