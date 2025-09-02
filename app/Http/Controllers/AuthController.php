<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Services\AuthService;
use App\Rules\UsernameValidationRule;
use Symfony\Component\HttpFoundation\Response;

/**
 * 인증 관련 API 컨트롤러
 * 
 * 면접 포인트:
 * 1. Service Layer Pattern 적용 - 비즈니스 로직을 AuthService로 분리
 * 2. JWT 토큰 기반 stateless 인증 구현
 * 3. Laravel Validation을 활용한 입력 검증
 * 4. 커스텀 Validation Rule 적용 (UsernameValidationRule)
 * 5. 일관된 API 응답 형식 (ApiResponseTrait 사용)
 */
class AuthController extends Controller
{
    private AuthService $authService;

    /**
     * 의존성 주입을 통한 AuthService 주입
     * 
     * 면접 설명: Laravel의 Service Container를 활용한 의존성 주입으로
     * 코드의 결합도를 낮추고 테스트 용이성을 높임
     */
    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    /**
     * 회원가입 API
     * 
     * 면접 포인트:
     * 1. Laravel Validation Rules를 사용한 철저한 입력 검증
     * 2. 커스텀 Rule (UsernameValidationRule)로 비즈니스 로직 구현
     * 3. 회원가입 후 즉시 로그인 토큰 발급으로 UX 향상
     * 4. 패스워드 확인(confirmed) 규칙으로 보안 강화
     * 
     * @param Request $request 회원가입 요청 데이터
     * @return JsonResponse JWT 토큰과 사용자 정보를 포함한 응답
     */
    public function register(Request $request): JsonResponse
    {
        // Laravel의 강력한 Validation 기능 활용
        // 각 필드별로 세밀한 검증 규칙 적용
        $data = $request->validate([
            'username' => [
                'required',                    // 필수값 검증
                'string',                      // 문자열 타입 검증
                'unique:users,username',       // 데이터베이스 중복 검증
                new UsernameValidationRule()   // 커스텀 비즈니스 로직 검증
            ],
            'name'     => 'required|string|max:255',           // 이름 길이 제한
            'email'    => 'required|string|email|max:255|unique:users,email', // 이메일 형식 및 중복 검증
            'password' => 'required|string|min:8|confirmed',   // 최소 길이 및 확인 검증
        ]);

        // Service Layer에 비즈니스 로직 위임
        $user  = $this->authService->register($data);
        
        // 회원가입 후 즉시 로그인 처리 (UX 개선)
        // 사용자가 별도로 로그인할 필요 없이 바로 서비스 이용 가능
        $token = $this->authService->login([
            'username' => $user->username,
            'password' => $request->password,  // 평문 패스워드 사용 (해싱 전)
        ]);

        // 표준화된 API 응답 형식으로 토큰 정보 반환
        return $this->success([
            'user'         => $user,
            'access_token' => $token,
            'token_type'   => 'Bearer',  // OAuth 2.0 표준 토큰 타입
            'expires_in'   => auth('api')->factory()->getTTL() * 60, // 토큰 만료 시간 (초)
        ], '회원가입이 완료되었습니다.', 201); // HTTP 201 Created 상태코드
    }

    /**
     * 로그인 API
     * 
     * 면접 포인트:
     * 1. Username 기반 로그인 (email 대신 username 사용)
     * 2. JWT 토큰 발급으로 stateless 인증 구현
     * 3. 토큰 만료시간 정보 제공으로 클라이언트 관리 용이성
     * 
     * @param Request $request 로그인 인증 정보
     * @return JsonResponse JWT 토큰 정보
     */
    public function login(Request $request): JsonResponse
    {
        // 로그인에 필요한 최소한의 정보만 검증
        $data = $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        // AuthService에서 실제 인증 처리
        // 인증 실패 시 AuthService에서 예외 발생
        $token = $this->authService->login($data);

        // 클라이언트가 토큰 관리에 필요한 모든 정보 제공
        return $this->success([
            'access_token' => $token,
            'token_type'   => 'Bearer',
            'expires_in'   => auth('api')->factory()->getTTL() * 60, // 토큰 유효시간
        ], '로그인이 완료되었습니다.');
    }

    /**
     * 로그아웃 API
     * 
     * 면접 포인트:
     * 1. JWT 토큰 무효화 처리
     * 2. 서버 사이드에서 토큰 블랙리스트 관리
     * 3. 보안을 위한 명시적 로그아웃 처리
     * 
     * @param Request $request 인증된 요청
     * @return JsonResponse 로그아웃 완료 응답
     */
    public function logout(Request $request): JsonResponse
    {
        // 현재 토큰을 무효화하여 더 이상 사용할 수 없도록 처리
        $this->authService->logout();

        return $this->success(null, '로그아웃 되었습니다.');
    }
}