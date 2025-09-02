<?php

namespace App\Services;

use App\Models\Comment;
use App\Models\Post;
use Illuminate\Support\Facades\Auth;

class CommentService
{
    public function list(Post $post, int $perPage = 10)
    {
        return $post->comments()
            ->with('user')
            ->with(['replies' => function ($q) {
                $q->with('user');
            }])
            ->whereNull('parent_id')  // 최상위 댓글만
            ->orderBy('created_at', 'asc')
            ->paginate($perPage);
    }

    public function store(Post $post, array $data): Comment
    {
        $data['user_id'] = Auth::id();
        $data['post_id'] = $post->id;

        // 대댓글인 경우 부모 댓글이 같은 게시글에 속하는지 확인
        if (!empty($data['parent_id'])) {
            $parent = Comment::findOrFail($data['parent_id']);
            if ($parent->post_id !== $post->id) {
                abort(400, '잘못된 대댓글 요청입니다.');
            }
        }

        return Comment::create($data);
    }

    public function update(Post $post, Comment $comment, array $data): Comment
    {
        $this->checkPostMatch($post, $comment);
        $this->authorizeOwner($comment);

        $comment->update($data);
        return $comment;
    }

    public function destroy(Post $post, Comment $comment): void
    {
        $this->checkPostMatch($post, $comment);
        $this->authorizeOwner($comment);

        $comment->delete();
    }

    private function authorizeOwner(Comment $comment): void
    {
        if ($comment->user_id !== Auth::id()) {
            abort(403, '작성자만 수정/삭제할 수 있습니다.');
        }
    }

    private function checkPostMatch(Post $post, Comment $comment): void
    {
        if ($comment->post_id !== $post->id) {
            abort(400, '잘못된 접근입니다.');
        }
    }
}
