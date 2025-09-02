<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\LikeController;

/**
 * API 라우트 정의
 * 
 * 면접 포인트:
 * 1. RESTful API 설계 원칙 준수
 * 2. JWT 미들웨어를 통한 인증 보호
 * 3. Route Model Binding 활용으로 자동 모델 주입
 * 4. 논리적 그룹핑으로 코드 가독성 향상
 * 5. HTTP 메서드별 적절한 의미 부여 (GET, POST, PUT, DELETE)
 */

// ================================
// 🔓 공개 API (인증 불필요)
// ================================

/**
 * 회원가입 API
 * POST /api/register
 * 
 * 면접 설명: 새로운 사용자 계정 생성
 * 응답: 사용자 정보 + JWT 토큰 (즉시 로그인 처리)
 */
Route::post('register', [AuthController::class, 'register']);

/**
 * 로그인 API
 * POST /api/login
 * 
 * 면접 설명: 기존 사용자 인증 및 JWT 토큰 발급
 * 응답: JWT 토큰 정보 (access_token, token_type, expires_in)
 */
Route::post('login', [AuthController::class, 'login']);

// ================================
// 🔒 보호된 API (JWT 인증 필요)
// ================================

/**
 * JWT 인증이 필요한 API 그룹
 * 
 * 면접 포인트:
 * 1. auth:api 미들웨어로 JWT 토큰 검증
 * 2. 유효하지 않은 토큰 시 401 Unauthorized 자동 응답
 * 3. 만료된 토큰 시 토큰 갱신 또는 재로그인 유도
 * 4. 인증된 사용자 정보는 auth()->user()로 접근 가능
 */
Route::middleware('auth:api')->group(function () {

    // ================================
    // 👤 인증 관련 API
    // ================================
    
    /**
     * 로그아웃 API
     * POST /api/logout
     * 
     * 면접 설명: 현재 JWT 토큰을 블랙리스트에 추가하여 무효화
     */
    Route::post('logout', [AuthController::class, 'logout']);

    // ================================
    // 👤 사용자 관리 API
    // ================================
    
    /**
     * 내 정보 조회 API
     * GET /api/user
     * 
     * 면접 설명: 현재 인증된 사용자의 프로필 정보 반환
     */
    Route::get('user', [UserController::class, 'show']);
    
    /**
     * 내 정보 수정 API
     * PUT /api/user
     * 
     * 면접 설명: 현재 사용자의 프로필 정보 수정 (이름, 이메일 등)
     */
    Route::put('user', [UserController::class, 'update']);
    
    /**
     * 회원탈퇴 API
     * DELETE /api/user
     * 
     * 면접 설명: 현재 사용자 계정 삭제 (관련 게시글, 댓글도 CASCADE DELETE)
     */
    Route::delete('user', [UserController::class, 'destroy']);

    // ================================
    // 📝 게시글 CRUD API
    // ================================
    
    /**
     * RESTful 게시글 리소스 API
     * 
     * 면접 포인트:
     * 1. apiResource()로 표준 REST API 엔드포인트 자동 생성
     * 2. Route Model Binding으로 {post} 파라미터가 Post 모델로 자동 주입
     * 3. 존재하지 않는 게시글 접근 시 자동 404 응답
     * 
     * 생성되는 라우트:
     * GET    /api/posts          - 게시글 목록 (index)
     * POST   /api/posts          - 새 게시글 작성 (store)
     * GET    /api/posts/{post}   - 게시글 상세 (show)
     * PUT    /api/posts/{post}   - 게시글 수정 (update)
     * DELETE /api/posts/{post}   - 게시글 삭제 (destroy)
     */
    Route::apiResource('posts', PostController::class);

    // ================================
    // 💬 댓글 관련 API
    // ================================
    
    /**
     * 특정 게시글의 댓글 목록 조회
     * GET /api/posts/{post}/comments
     * 
     * 면접 설명: 게시글에 달린 모든 댓글과 대댓글을 계층구조로 반환
     */
    Route::get('/posts/{post}/comments', [CommentController::class, 'index']);

    /**
     * 새 댓글/대댓글 작성
     * POST /api/posts/{post}/comments
     * 
     * 면접 설명: 
     * - parent_id 없으면 일반 댓글
     * - parent_id 있으면 해당 댓글의 대댓글로 작성
     */
    Route::post('/posts/{post}/comments', [CommentController::class, 'store']);
    
    /**
     * 댓글/대댓글 수정
     * PUT /api/posts/{post}/comments/{comment}
     * 
     * 면접 설명: 작성자만 수정 가능, 권한 검증은 Controller에서 처리
     */
    Route::put('/posts/{post}/comments/{comment}', [CommentController::class, 'update']);
    
    /**
     * 댓글/대댓글 삭제
     * DELETE /api/posts/{post}/comments/{comment}
     * 
     * 면접 설명: 대댓글이 있는 댓글 삭제 시 하위 대댓글들도 CASCADE DELETE
     */
    Route::delete('/posts/{post}/comments/{comment}', [CommentController::class, 'destroy']);

    // ================================
    // ❤️ 좋아요 관련 API
    // ================================
    
    /**
     * 게시글 좋아요 추가
     * POST /api/posts/{post}/likes
     * 
     * 면접 설명: 
     * - 이미 좋아요한 경우 중복 방지 (DB 유니크 제약)
     * - 토글 방식이 아닌 명시적 추가 API
     */
    Route::post('/posts/{post}/likes', [LikeController::class, 'store']);
    
    /**
     * 게시글 좋아요 취소
     * DELETE /api/posts/{post}/likes
     * 
     * 면접 설명: 현재 사용자의 해당 게시글 좋아요 제거
     */
    Route::delete('/posts/{post}/likes', [LikeController::class, 'destroy']);
    
    /**
     * 게시글 좋아요 수 조회
     * GET /api/posts/{post}/likes/count
     * 
     * 면접 설명: 해당 게시글의 총 좋아요 수와 현재 사용자의 좋아요 여부 반환
     */
    Route::get('/posts/{post}/likes/count', [LikeController::class, 'count']);
});