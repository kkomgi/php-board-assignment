<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use App\Models\Comment;
use App\Services\CommentService;

class CommentController extends Controller
{
    public function __construct(private CommentService $commentService)
    {
    }

    public function index(Request $request, Post $post)
    {
        $comments = $this->commentService->list(
            $post,
            $request->query('per_page', 10)
        );

        return $this->success($comments);
    }

    public function store(Request $request, Post $post)
    {
        $data = $request->validate([
            'body' => 'required|string',
            'parent_id' => 'nullable|exists:comments,id',
        ]);

        $comment = $this->commentService->store($post, $data);

        return $this->success($comment, '댓글이 작성되었습니다.', 201);
    }

    public function update(Request $request, Post $post, Comment $comment)
    {
        $data = $request->validate([
            'body' => 'required|string',
        ]);

        $updated = $this->commentService->update($post, $comment, $data);

        return $this->success($updated, '댓글이 수정되었습니다.');
    }

    public function destroy(Post $post, Comment $comment)
    {
        $this->commentService->destroy($post, $comment);

        return $this->success(null, '댓글이 삭제되었습니다.');
    }
}
