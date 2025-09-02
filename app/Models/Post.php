<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * 게시글 모델 클래스
 * 
 * 면접 포인트:
 * 1. Eloquent ORM의 Model 클래스 상속으로 데이터베이스 추상화
 * 2. HasFactory 트레이트로 테스트 데이터 생성 지원
 * 3. Mass Assignment 보호를 위한 $fillable 속성
 * 4. Eloquent 관계 설정으로 연관 모델들과의 관계 정의
 * 5. timestamps 자동 관리 (created_at, updated_at)
 */
class Post extends Model
{
    use HasFactory;

    /**
     * Mass Assignment가 허용된 속성들
     * 
     * 면접 설명: create()나 update() 시 대량 할당을 허용할 필드 정의
     * 보안상 중요한 필드들(id, created_at, updated_at 등)은 제외
     */
    protected $fillable = [
        'user_id',  // 작성자 ID (외래키)
        'title',    // 게시글 제목
        'body',     // 게시글 내용
    ];

    /**
     * 이 게시글의 작성자와의 관계 (Many-to-One)
     * 
     * 면접 포인트:
     * 1. belongsTo() 관계로 N:1 관계 정의
     * 2. 각 게시글은 하나의 사용자에 속함
     * 3. 외래키는 기본적으로 {relation}_id 규칙 (user_id)
     * 4. 지연 로딩으로 필요시에만 사용자 정보 조회
     * 
     * 사용 예시:
     * $post = Post::find(1);
     * $author = $post->user; // 작성자 정보 조회
     * echo $post->user->name; // 작성자 이름
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * 이 게시글에 달린 좋아요들과의 관계 (One-to-Many)
     * 
     * 면접 포인트:
     * 1. hasMany() 관계로 1:N 관계 정의
     * 2. 한 게시글에 여러 좋아요가 가능
     * 3. 좋아요 수 계산, 특정 사용자의 좋아요 여부 확인 등에 활용
     * 
     * 사용 예시:
     * $post = Post::find(1);
     * $likeCount = $post->likes()->count(); // 좋아요 수
     * $userLiked = $post->likes()->where('user_id', $userId)->exists(); // 사용자 좋아요 여부
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function likes()
    {
        return $this->hasMany(Like::class);
    }

    /**
     * 이 게시글에 달린 댓글들과의 관계 (One-to-Many)
     * 
     * 면접 포인트:
     * 1. hasMany() 관계로 1:N 관계 정의
     * 2. 댓글 목록 조회, 댓글 수 계산 등에 활용
     * 3. 계층형 댓글 구조에서 최상위 댓글들만 가져오는 것도 가능
     * 
     * 사용 예시:
     * $post = Post::find(1);
     * $comments = $post->comments; // 모든 댓글
     * $topLevelComments = $post->comments()->whereNull('parent_id')->get(); // 최상위 댓글만
     * $commentCount = $post->comments()->count(); // 댓글 수
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    /**
     * 게시글 목록에서 작성자 정보를 함께 가져오는 스코프
     * 
     * 면접 포인트:
     * 1. Query Scope를 활용한 재사용 가능한 쿼리 로직
     * 2. N+1 문제 해결을 위한 Eager Loading 적용
     * 3. with() 메서드로 관련 모델을 한 번에 조회하여 성능 최적화
     * 
     * 사용 예시:
     * $posts = Post::withAuthor()->get(); // 작성자 정보 포함한 게시글 목록
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithAuthor($query)
    {
        return $query->with('user:id,username,name');
    }

    /**
     * 게시글과 관련 통계 정보를 함께 가져오는 스코프
     * 
     * 면접 포인트:
     * 1. withCount()를 활용한 관련 모델의 개수 조회
     * 2. 서브쿼리를 통해 효율적인 집계 데이터 조회
     * 3. API 응답에서 자주 필요한 정보들을 미리 계산
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithStats($query)
    {
        return $query->withCount(['comments', 'likes']);
    }
}
