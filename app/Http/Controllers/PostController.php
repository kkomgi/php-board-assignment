<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use App\Services\PostService;

/**
 * 게시글 관련 API 컨트롤러
 * 
 * 면접 포인트:
 * 1. RESTful API 설계 원칙 준수 (apiResource 라우트 활용)
 * 2. Route Model Binding으로 자동 모델 주입
 * 3. Service Layer Pattern으로 비즈니스 로직 분리
 * 4. Query Parameter를 활용한 검색 및 필터링 기능
 * 5. Laravel의 Validation 기능 활용한 입력 검증
 */
class PostController extends Controller
{
    /**
     * PostService 의존성 주입
     * 
     * 면접 설명: PHP 8.0의 Constructor Property Promotion 문법 활용
     * private PostService $postService와 동일한 효과
     */
    public function __construct(private PostService $postService)
    {
    }

    /**
     * 게시글 목록 조회 API
     * 
     * 면접 포인트:
     * 1. Query Parameter를 통한 유연한 검색 및 필터링
     * 2. 페이지네이션 지원으로 대용량 데이터 처리
     * 3. 기본값 설정으로 안정적인 API 제공
     * 4. Service Layer에서 복잡한 쿼리 로직 처리
     * 
     * Query Parameters:
     * - title: 제목 부분 검색
     * - from: 시작 날짜 필터
     * - to: 종료 날짜 필터
     * - per_page: 페이지당 항목 수 (기본값: 10)
     * 
     * @param Request $request HTTP 요청 객체
     * @return \Illuminate\Http\JsonResponse 페이지네이션된 게시글 목록
     */
    public function index(Request $request)
    {
        // Query Parameter 추출 및 기본값 설정
        $posts = $this->postService->index(
            $request->query('title'),           // 제목 검색어 (선택사항)
            $request->query('from'),            // 시작 날짜 (선택사항)
            $request->query('to'),              // 종료 날짜 (선택사항)
            $request->query('per_page', 10)     // 페이지 크기 (기본 10개)
        );

        return $this->success($posts);
    }

    /**
     * 새 게시글 작성 API
     * 
     * 면접 포인트:
     * 1. Laravel Validation으로 입력 데이터 검증
     * 2. 인증된 사용자 정보 자동 연결 (미들웨어를 통해)
     * 3. HTTP 201 Created 상태코드로 생성 성공 표시
     * 4. Service Layer에서 실제 생성 로직 처리
     * 
     * @param Request $request 게시글 작성 데이터
     * @return \Illuminate\Http\JsonResponse 생성된 게시글 정보
     */
    public function store(Request $request)
    {
        // 게시글 작성에 필요한 필수 데이터 검증
        $data = $request->validate([
            'title' => 'required|string|max:255',  // 제목: 필수, 문자열, 최대 255자
            'body'  => 'required|string',          // 내용: 필수, 문자열 (길이 제한 없음)
        ]);

        // Service Layer에서 게시글 생성 처리
        // 현재 인증된 사용자 정보는 Service에서 자동 연결
        $post = $this->postService->store($data);
        
        // HTTP 201 Created로 성공적인 리소스 생성 표시
        return $this->success($post, '게시글이 작성되었습니다.', 201);
    }

    /**
     * 특정 게시글 상세 조회 API
     * 
     * 면접 포인트:
     * 1. Route Model Binding으로 자동 모델 주입 및 404 처리
     * 2. 관련 데이터 (작성자, 댓글 수, 좋아요 수) 함께 조회
     * 3. Service Layer에서 추가 비즈니스 로직 처리 가능
     * 
     * @param Post $post Route Model Binding으로 자동 주입된 Post 모델
     * @return \Illuminate\Http\JsonResponse 게시글 상세 정보
     */
    public function show(Post $post)
    {
        // Service Layer에서 게시글 상세 정보 가공 후 반환
        // 작성자 정보, 댓글 수, 좋아요 수 등 추가 정보 포함 가능
        return $this->success($this->postService->show($post));
    }

    /**
     * 게시글 수정 API
     * 
     * 면접 포인트:
     * 1. 'sometimes' 규칙으로 부분 업데이트 지원 (PATCH 방식)
     * 2. 권한 검증은 Service Layer에서 처리
     * 3. Route Model Binding으로 존재하지 않는 게시글 자동 404 처리
     * 
     * @param Request $request 수정할 데이터
     * @param Post $post 수정할 게시글 모델
     * @return \Illuminate\Http\JsonResponse 수정된 게시글 정보
     */
    public function update(Request $request, Post $post)
    {
        // 부분 업데이트를 위한 검증 규칙
        // 'sometimes': 해당 필드가 전송된 경우에만 검증 수행
        $data = $request->validate([
            'title' => 'sometimes|required|string|max:255', // 제목이 있으면 필수 검증
            'body'  => 'sometimes|required|string',         // 내용이 있으면 필수 검증
        ]);

        // Service Layer에서 권한 검증 및 업데이트 처리
        $updated = $this->postService->update($post, $data);
        return $this->success($updated, '게시글이 수정되었습니다.');
    }

    /**
     * 게시글 삭제 API
     * 
     * 면접 포인트:
     * 1. Soft Delete vs Hard Delete 고려사항
     * 2. 관련 데이터(댓글, 좋아요) 연쇄 삭제 처리
     * 3. 권한 검증 (작성자만 삭제 가능)
     * 4. HTTP 200으로 삭제 완료 표시 (204 No Content도 가능)
     * 
     * @param Post $post 삭제할 게시글 모델
     * @return \Illuminate\Http\JsonResponse 삭제 완료 응답
     */
    public function destroy(Post $post)
    {
        // Service Layer에서 권한 검증 및 삭제 처리
        // 관련 댓글과 좋아요도 CASCADE DELETE로 자동 삭제
        $this->postService->destroy($post);
        
        // 삭제 성공 응답 (데이터는 null)
        return $this->success(null, '게시글이 삭제되었습니다.');
    }
}
