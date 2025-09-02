<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

/**
 * 인증 관련 비즈니스 로직을 담당하는 서비스 클래스
 * 
 * 면접 포인트:
 * 1. Service Layer Pattern의 핵심 구현체
 * 2. JWT 토큰 기반 인증 시스템 구현
 * 3. Laravel의 Hash Facade를 활용한 안전한 패스워드 처리
 * 4. 명시적인 에러 처리와 사용자 친화적 메시지
 * 5. 단일 책임 원칙 (SRP) 준수
 */
class AuthService
{
    /**
     * 새 사용자 회원가입 처리
     * 
     * 면접 포인트:
     * 1. 패스워드 해싱으로 보안 강화 (bcrypt 알고리즘 사용)
     * 2. Laravel의 Hash Facade 활용
     * 3. Eloquent ORM의 Mass Assignment 보호 기능 활용
     * 4. 데이터베이스 제약 조건과 연계한 안전한 사용자 생성
     * 
     * @param array $data 검증된 회원가입 데이터 (username, name, email, password)
     * @return User 생성된 사용자 모델 인스턴스
     * @throws \Illuminate\Database\QueryException 데이터베이스 제약 조건 위반 시
     */
    public function register(array $data): User
    {
        // Laravel Hash::make()를 사용한 안전한 패스워드 해싱
        // bcrypt 알고리즘 사용, cost factor는 config/hashing.php에서 설정 가능
        $data['password'] = Hash::make($data['password']);
        
        // Eloquent의 create() 메서드로 사용자 생성
        // $fillable 속성에 정의된 필드만 Mass Assignment 허용
        return User::create($data);
    }

    /**
     * 사용자 로그인 및 JWT 토큰 발급
     * 
     * 면접 포인트:
     * 1. JWT Guard를 통한 stateless 인증 구현
     * 2. attempt() 메서드로 자동 패스워드 검증
     * 3. 실패 시 명확한 에러 메시지와 적절한 HTTP 상태코드
     * 4. 토큰 기반 인증으로 확장성 확보
     * 
     * @param array $credentials 로그인 인증 정보 (username, password)
     * @return string JWT 액세스 토큰
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException 인증 실패 시 422 에러
     */
    public function login(array $credentials): string
    {
        // JWT Guard를 통한 인증 시도
        // attempt() 메서드는 자동으로 패스워드를 해싱하여 비교
        if (! $token = Auth::guard('api')->attempt($credentials)) {
            // 인증 실패 시 422 Unprocessable Entity 상태코드와 함께 에러 발생
            // 보안상 구체적인 실패 이유(사용자명 없음 vs 패스워드 틀림)는 노출하지 않음
            abort(422, '아이디 또는 비밀번호가 일치하지 않습니다.');
        }

        // 인증 성공 시 JWT 토큰 반환
        return $token;
    }

    /**
     * 사용자 로그아웃 및 JWT 토큰 무효화
     * 
     * 면접 포인트:
     * 1. 서버 사이드에서 토큰 무효화 처리
     * 2. JWT 블랙리스트 기능 활용
     * 3. 보안을 위한 명시적 로그아웃 처리
     * 4. stateless 인증에서도 서버 측 토큰 관리의 중요성
     * 
     * @return void
     */
    public function logout(): void
    {
        // 현재 인증된 사용자의 토큰을 무효화
        // tymon/jwt-auth 라이브러리의 블랙리스트 기능 활용
        Auth::guard('api')->logout();
    }

    /**
     * 현재 인증된 사용자 정보 반환
     * 
     * 면접 포인트:
     * 1. JWT 토큰에서 사용자 정보 추출
     * 2. 미들웨어를 통해 이미 인증된 상태에서 호출
     * 3. 사용자 프로필 조회 등에 활용
     * 
     * @return User 현재 인증된 사용자 모델
     */
    public function me(): User
    {
        // JWT Guard에서 현재 인증된 사용자 정보 반환
        // 토큰이 유효하지 않거나 만료된 경우 null 반환
        return Auth::guard('api')->user();
    }
}
