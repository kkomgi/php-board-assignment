<?php

namespace App\Services;

use App\Models\Post;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Carbon;

class PostService
{
    /**
     * 게시글 목록
     */
    public function index(?string $title, ?string $from, ?string $to, int $perPage = 10): LengthAwarePaginator
    {
        $query = Post::query()->withCount('likes')->with('user');

        // 제목 검색
        if ($title) {
            $query->where('title', 'like', "%{$title}%");
        }

        // 작성일 범위 검색
        if ($from) {
            $query->whereDate('created_at', '>=', Carbon::parse($from));
        }
        if ($to) {
            $query->whereDate('created_at', '<=', Carbon::parse($to));
        }

        return $query->orderByDesc('created_at')->paginate($perPage);
    }

    /**
     * 게시글 생성
     */
    public function store(array $data): Post
    {
        $data['user_id'] = Auth::id();
        return Post::create($data);
    }

    /**
     * 단건 조회
     */
    public function show(Post $post): Post
    {
        return $post->loadCount('likes')->load('user');
    }

    /**
     * 수정
     */
    public function update(Post $post, array $data): Post
    {
        $this->authorizeOwner($post);
        $post->update($data);
        return $post;
    }

    /**
     * 삭제
     */
    public function destroy(Post $post): void
    {
        $this->authorizeOwner($post);
        $post->delete();
    }

    /**
     * 작성자 본인인지 체크
     */
    private function authorizeOwner(Post $post): void
    {
        if ($post->user_id !== Auth::id()) {
            abort(403, '작성자만 수정/삭제할 수 있습니다.');
        }
    }
}
