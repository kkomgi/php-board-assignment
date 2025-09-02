<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Notifications\Notifiable;

/**
 * 사용자 모델 클래스
 * 
 * 면접 포인트:
 * 1. Laravel의 Authenticatable 클래스 상속으로 인증 기능 확장
 * 2. JWTSubject 인터페이스 구현으로 JWT 토큰 지원
 * 3. Mass Assignment 보호를 위한 $fillable 속성 활용
 * 4. 민감 정보 숨김을 위한 $hidden 속성 활용
 * 5. Eloquent ORM의 관계 설정으로 다른 모델과의 연관관계 정의
 */
class User extends Authenticatable implements JWTSubject
{
    use Notifiable;

    /**
     * Mass Assignment가 허용된 속성들
     * 
     * 면접 설명: 보안상 중요한 Mass Assignment 보호 기능
     * create()나 update() 메서드 사용 시 이 배열에 정의된 필드만 허용
     * 악의적인 사용자가 임의의 필드를 조작하는 것을 방지
     */
    protected $fillable = [
        'username',  // 로그인용 고유 식별자
        'name',      // 사용자 실명
        'email',     // 이메일 주소 (알림 발송용)
        'password',  // 암호화된 패스워드
    ];

    /**
     * JSON 변환 시 숨겨질 속성들
     * 
     * 면접 설명: API 응답에서 민감한 정보 노출 방지
     * toArray()나 toJson() 호출 시 이 배열의 필드들은 제외됨
     * 특히 password는 절대 클라이언트에 노출되어서는 안됨
     */
    protected $hidden = [
        'password',       // 해싱된 패스워드도 노출 금지
        'remember_token', // 세션 기반 인증용 토큰 (JWT 사용으로 불필요하지만 안전장치)
    ];

    /**
     * JWTSubject 인터페이스 구현: JWT 토큰의 주체 식별자 반환
     * 
     * 면접 포인트:
     * 1. JWT 토큰 생성 시 사용자를 식별하는 고유값 제공
     * 2. 일반적으로 Primary Key(id)를 사용
     * 3. 토큰 검증 시 이 값으로 사용자를 찾음
     * 
     * @return mixed 사용자의 Primary Key (일반적으로 id)
     */
    public function getJWTIdentifier()
    {
        // Laravel의 getKey() 메서드는 모델의 Primary Key 반환
        // 기본적으로 'id' 컬럼값을 반환
        return $this->getKey();
    }

    /**
     * JWTSubject 인터페이스 구현: JWT 토큰에 포함할 커스텀 클레임 반환
     * 
     * 면접 포인트:
     * 1. JWT 페이로드에 추가 정보를 포함할 수 있는 기능
     * 2. 사용자 역할(role), 권한(permissions) 등을 토큰에 포함 가능
     * 3. 빈 배열 반환 시 기본 클레임만 사용 (sub, iat, exp 등)
     * 4. 토큰 크기와 보안을 고려하여 필요한 정보만 포함해야 함
     * 
     * @return array JWT 토큰에 포함할 커스텀 클레임 배열
     */
    public function getJWTCustomClaims()
    {
        // 현재는 커스텀 클레임 없음
        // 필요시 ['role' => $this->role, 'permissions' => $this->permissions] 등 추가 가능
        return [];
    }

    /**
     * 이 사용자가 작성한 게시글들과의 관계
     * 
     * 면접 포인트:
     * 1. Eloquent의 One-to-Many 관계 정의
     * 2. hasMany() 메서드로 1:N 관계 표현
     * 3. 외래키는 기본적으로 {model}_id 규칙 따름 (user_id)
     * 4. 지연 로딩(Lazy Loading)과 즉시 로딩(Eager Loading) 최적화 가능
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    /**
     * 이 사용자가 작성한 댓글들과의 관계
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    /**
     * 이 사용자가 누른 좋아요들과의 관계
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function likes()
    {
        return $this->hasMany(Like::class);
    }
}
