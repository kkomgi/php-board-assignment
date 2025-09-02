<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;

class UserController extends Controller
{
    /**
     * 내 정보 조회
     */
    public function show(): JsonResponse
    {
        return $this->success(auth('api')->user());
    }

    /**
     * 내 정보 수정
     */
    public function update(Request $request): JsonResponse
    {
        $user = auth('api')->user();

        $data = $request->validate([
            'name'     => 'sometimes|required|string|max:255',
            'email'    => 'sometimes|required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8',
        ]);

        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        $user->update($data);

        return $this->success($user, '정보가 수정되었습니다.');
    }

    /**
     * 내 계정 삭제
     */
    public function destroy(): JsonResponse
    {
        $user = auth('api')->user();
        $user->delete();

        return $this->success(null, '계정이 삭제되었습니다.');
    }
}
