<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;

/**
 * 🎯 API 성공 응답 전용 Trait
 * 
 * 🔥 면접 핵심 포인트: "성공 응답만 담당하는 전문가"
 * 
 * 📋 응답 처리 분업 체계에서의 역할:
 * 
 * ✅ 이 Trait의 책임 (성공 응답):
 *    - 개발자가 명시적으로 "성공했다"고 알릴 때만 사용
 *    - 비즈니스 로직에 따라 다양한 성공 상황 처리
 *    - 유연한 데이터/메시지/상태코드 조합 지원
 * 
 * ❌ 이 Trait이 하지 않는 것 (실패 응답):
 *    - 에러나 예외 상황은 전혀 처리하지 않음
 *    - 모든 실패는 bootstrap/app.php가 자동 처리
 *    - 개발자는 예외만 던지면 됨
 * 
 * 💡 왜 성공만 따로 분리했는가?
 * 
 * 1️⃣ 의도적 vs 자동적:
 *    - 성공: 개발자의 의도적인 선택 (다양한 성공 상황)
 *    - 실패: 시스템의 자동적인 처리 (일관된 에러 응답)
 * 
 * 2️⃣ 유연성 vs 일관성:
 *    - 성공: 상황별 맞춤 응답 (유연성 중시)
 *    - 실패: 항상 동일한 형식 (일관성 중시)
 * 
 * 3️⃣ 명시적 vs 암묵적:
 *    - 성공: $this->success() 명시적 호출
 *    - 실패: 예외 발생 시 자동 감지
 * 
 * 🎯 실제 사용 패턴:
 * 
 * 📝 게시글 작성 성공:
 * return $this->success($post, '게시글이 작성되었습니다.', 201);
 * 
 * 📋 목록 조회 성공:
 * return $this->success($posts);  // 메시지 없이 데이터만
 * 
 * 🗑️ 삭제 완료:
 * return $this->success(null, '게시글이 삭제되었습니다.');  // 데이터 없이 메시지만
 * 
 * ❌ 에러 상황 (이 Trait가 처리하지 않음):
 * abort(404, '게시글을 찾을 수 없습니다.');  // → bootstrap/app.php가 자동 처리
 * throw new ValidationException(...);        // → bootstrap/app.php가 자동 처리
 */
trait ApiResponseTrait
{
    /**
     * 🎯 통일된 성공 응답 생성 - 오직 성공 상황만 담당
     * 
     * 🔥 면접 설명 포인트:
     * "이 메서드는 오직 성공했을 때만 호출됩니다.
     *  모든 에러나 예외는 bootstrap/app.php에서 자동으로 처리되므로
     *  개발자는 성공 상황에만 집중하면 됩니다."
     * 
     * @param mixed $data 클라이언트에게 전달할 데이터 (배열, 객체, null 등)
     * @param string|null $message 사용자에게 보여줄 성공 메시지 (선택사항)
     * @param int $statusCode HTTP 성공 상태 코드 (기본: 200, 생성: 201 등)
     * @return JsonResponse 표준화된 성공 응답
     * 
     * 📋 응답 형식 예시:
     * 
     * 1️⃣ 전체 정보 포함:
     * {
     *   "success": true,
     *   "data": { "id": 1, "name": "홍길동" },
     *   "message": "회원가입이 완료되었습니다."
     * }
     * 
     * 2️⃣ 데이터만:
     * {
     *   "success": true,
     *   "data": [...] 
     * }
     * 
     * 3️⃣ 메시지만:
     * {
     *   "success": true,
     *   "message": "삭제가 완료되었습니다."
     * }
     */
    protected function success($data = null, string $message = null, int $statusCode = 200): JsonResponse
    {
        // 🏗️ 기본 성공 응답 구조 - success: true는 항상 포함
        $response = ['success' => true];
        
        // 📦 데이터가 있을 때만 data 필드 추가
        // null 체크로 불필요한 null 값 응답 방지
        if ($data !== null) {
            $response['data'] = $data;
        }
        
        // 💬 메시지가 있을 때만 message 필드 추가
        // 선택적 메시지로 유연한 응답 가능
        if ($message !== null) {
            $response['message'] = $message;
        }
        
        // 📤 JSON 응답 반환
        // Laravel의 response() 헬퍼로 HTTP 응답 생성
        return response()->json($response, $statusCode);
    }
    
    /**
     * 💡 참고: 에러 응답은 여기서 처리하지 않음
     * 
     * 이 Trait에는 error() 메서드가 없는 이유:
     * - 모든 에러는 bootstrap/app.php에서 일관되게 처리
     * - 예외 발생 시 자동으로 표준 에러 응답 생성
     * - 개발자는 abort()나 예외만 던지면 됨
     * 
     * 예시:
     * abort(404, '리소스 없음');           // → 자동으로 404 에러 응답
     * throw new ValidationException(...); // → 자동으로 422 에러 응답
     */
}