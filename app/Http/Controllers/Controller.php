<?php

namespace App\Http\Controllers;

use App\Traits\ApiResponseTrait;

/**
 * 🎯 베이스 컨트롤러 - API 응답 시스템의 핵심
 * 
 * 🔥 면접 핵심 포인트: "성공은 의도적으로, 실패는 자동으로"
 * 
 * 📋 이 프로젝트의 응답 처리 완벽 분업 체계:
 * 
 * ✅ 성공 응답 (Success Response):
 *    - 처리 방식: Trait를 통한 명시적 호출
 *    - 담당자: ApiResponseTrait의 success() 메서드
 *    - 호출 시점: 개발자가 의도적으로 "성공했다"고 알릴 때
 *    - 사용 방법: $this->success($data, $message, $statusCode)
 *    - 예시: return $this->success($user, '회원가입 완료', 201);
 * 
 * ❌ 실패 응답 (Error Response):
 *    - 처리 방식: 글로벌 예외 핸들링
 *    - 담당자: bootstrap/app.php의 Exception Handler
 *    - 발생 시점: 예외가 던져질 때 자동으로
 *    - 사용 방법: abort(422, '에러 메시지') 또는 예외 발생
 *    - 예시: ValidationException → 자동으로 422 응답
 * 
 * 💡 왜 이렇게 분리했는가?
 * 
 * 1️⃣ 관심사 분리 (Separation of Concerns):
 *    - 성공: 개발자가 컨트롤해야 하는 비즈니스 로직
 *    - 실패: 시스템이 자동으로 처리해야 하는 예외 상황
 * 
 * 2️⃣ 코드 일관성:
 *    - 성공: 상황에 따라 다른 데이터/메시지 (유연성)
 *    - 실패: 항상 동일한 형식 (일관성)
 * 
 * 3️⃣ 유지보수성:
 *    - 성공 응답 수정: ApiResponseTrait만 수정
 *    - 실패 응답 수정: bootstrap/app.php만 수정
 * 
 * 4️⃣ 개발 편의성:
 *    - 성공: 명확한 의도 표현 필요
 *    - 실패: 예외만 던지면 시스템이 알아서 처리
 * 
 * 🎯 실무에서의 가치:
 * - 대규모 팀에서 일관된 API 응답 보장
 * - 프론트엔드 개발자에게 예측 가능한 인터페이스 제공
 * - 신입 개발자도 쉽게 일관된 코드 작성 가능
 */
abstract class Controller
{
    /**
     * 🎨 ApiResponseTrait 사용으로 모든 컨트롤러에서
     * $this->success() 메서드 자동 제공
     * 
     * 이제 모든 하위 컨트롤러에서 다음과 같이 사용 가능:
     * - return $this->success($data);                    // 데이터만
     * - return $this->success($data, '성공 메시지');      // 데이터 + 메시지
     * - return $this->success(null, '완료', 201);        // 메시지 + 상태코드
     */
    use ApiResponseTrait;
}
