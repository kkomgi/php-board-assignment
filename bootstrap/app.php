<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Throwable;

/**
 * 🎯 Laravel 애플리케이션 부트스트랩 - 실패 응답 전용 처리소
 * 
 * 🔥 면접 핵심 포인트: "모든 실패는 여기서 자동으로 처리"
 * 
 * 📋 응답 처리 분업 체계에서의 역할:
 * 
 * ❌ 이 파일의 책임 (실패 응답 전용):
 *    - 모든 예외와 에러 상황을 자동으로 감지
 *    - 일관된 형식의 에러 응답 자동 생성
 *    - 개발자는 예외만 던지면 시스템이 알아서 처리
 *    - 보안을 고려한 환경별 메시지 처리
 * 
 * ✅ 이 파일이 하지 않는 것 (성공 응답):
 *    - 성공 응답은 전혀 처리하지 않음
 *    - 모든 성공은 ApiResponseTrait에서 명시적 처리
 *    - 개발자가 의도적으로 success() 호출해야 함
 * 
 * 💡 왜 실패만 따로 분리했는가?
 * 
 * 1️⃣ 자동적 vs 의도적:
 *    - 실패: 예외 발생 시 자동으로 감지하여 처리
 *    - 성공: 개발자가 의도적으로 알려줘야 함
 * 
 * 2️⃣ 일관성 vs 유연성:
 *    - 실패: 모든 에러가 동일한 형식 (일관성)
 *    - 성공: 상황별 다른 데이터/메시지 (유연성)
 * 
 * 3️⃣ 중앙집중 vs 분산:
 *    - 실패: 한 곳에서 모든 예외 처리 (유지보수 용이)
 *    - 성공: 각 컨트롤러에서 상황별 처리 (맞춤형 응답)
 * 
 * 🎯 실제 처리 패턴:
 * 
 * 🚫 ValidationException:
 * $request->validate([...]);  // 실패 시 자동으로 422 응답
 * 
 * 🚫 AuthenticationException:
 * JWT 토큰 없거나 만료 시 자동으로 401 응답
 * 
 * 🚫 NotFoundHttpException:  
 * Route Model Binding 실패 시 자동으로 404 응답
 * 
 * 🚫 일반 예외:
 * abort(500, '서버 에러');  // 자동으로 500 응답
 * 
 * ✅ 성공 상황 (이 파일이 처리하지 않음):
 * return $this->success($data, $message);  // ApiResponseTrait에서 처리
 */

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        //
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // 글로벌 에러 핸들링
        $exceptions->render(function (Throwable $e, Request $request) {
            // JSON 요청 (API)에 대해서만 커스텀 응답
            if ($request->expectsJson()) {
                
                // Validation 오류 (422)
                if ($e instanceof \Illuminate\Validation\ValidationException) {
                    return response()->json([
                        'success' => false,
                        'message' => '입력값이 올바르지 않습니다.',
                        'errors' => $e->errors(),
                    ], 422);
                }

                // 인증 오류 (401)
                if ($e instanceof \Illuminate\Auth\AuthenticationException) {
                    return response()->json([
                        'success' => false,
                        'message' => '인증이 필요합니다.',
                    ], 401);
                }

                // 권한 오류 (403)
                if ($e instanceof \Illuminate\Auth\Access\AuthorizationException) {
                    return response()->json([
                        'success' => false,
                        'message' => '권한이 없습니다.',
                    ], 403);
                }

                // 404 오류 (ModelNotFoundException도 NotFoundHttpException으로 변환됨)
                if ($e instanceof \Symfony\Component\HttpKernel\Exception\NotFoundHttpException) {
                    return response()->json([
                        'success' => false,
                        'message' => '요청한 리소스를 찾을 수 없습니다.',
                    ], 404);
                }

                // 405 Method Not Allowed
                if ($e instanceof \Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException) {
                    return response()->json([
                        'success' => false,
                        'message' => '허용되지 않는 HTTP 메서드입니다.',
                    ], 405);
                }

                // 429 Too Many Requests
                if ($e instanceof \Illuminate\Http\Exceptions\ThrottleRequestsException) {
                    return response()->json([
                        'success' => false,
                        'message' => '너무 많은 요청입니다. 잠시 후 다시 시도해주세요.',
                    ], 429);
                }

                // HTTP 예외 (상태 코드가 있는 경우)
                if (method_exists($e, 'getStatusCode')) {
                    $statusCode = $e->getStatusCode();
                    
                    // 개발 환경에서는 상세 메시지, 프로덕션에서는 일반 메시지
                    $message = config('app.debug') 
                        ? $e->getMessage() 
                        : $this->getGenericErrorMessage($statusCode);

                    return response()->json([
                        'success' => false,
                        'message' => $message ?: '요청 처리 중 오류가 발생했습니다.',
                    ], $statusCode);
                }

                // 기타 모든 예외 (500)
                $message = config('app.debug') 
                    ? $e->getMessage()
                    : '서버 내부 오류가 발생했습니다.';

                return response()->json([
                    'success' => false,
                    'message' => $message,
                ], 500);
            }

            // JSON이 아닌 요청은 기본 Laravel 처리로 넘김
            return null;
        });

        // 예외 보고 설정 (로그에 기록하지 않을 예외들)
        $exceptions->dontReport([
            \Illuminate\Auth\AuthenticationException::class,
            \Illuminate\Validation\ValidationException::class,
            \Symfony\Component\HttpKernel\Exception\NotFoundHttpException::class,
        ]);
    })
    ->create();

/**
 * HTTP 상태 코드에 따른 일반적인 에러 메시지 반환
 */
function getGenericErrorMessage(int $statusCode): string
{
    return match($statusCode) {
        400 => '잘못된 요청입니다.',
        401 => '인증이 필요합니다.',
        403 => '권한이 없습니다.',
        404 => '요청한 리소스를 찾을 수 없습니다.',
        405 => '허용되지 않는 HTTP 메서드입니다.',
        422 => '입력값이 올바르지 않습니다.',
        429 => '너무 많은 요청입니다.',
        500 => '서버 내부 오류가 발생했습니다.',
        502 => '게이트웨이 오류입니다.',
        503 => '서비스를 사용할 수 없습니다.',
        default => '요청 처리 중 오류가 발생했습니다.',
    };
}