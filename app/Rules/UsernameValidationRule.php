<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class UsernameValidationRule implements Rule
{
    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        // 길이 체크 (12자 이상 20자 이하)
        if (strlen($value) < 12 || strlen($value) > 20) {
            return false;
        }

        // 영문 대문자 포함 체크
        if (!preg_match('/[A-Z]/', $value)) {
            return false;
        }

        // 영문 소문자 포함 체크
        if (!preg_match('/[a-z]/', $value)) {
            return false;
        }

        // 특수문자 포함 체크
        if (!preg_match('/[!@#$%^&*()_+\-=\[\]{};\':"\\|,.<>\/?]/', $value)) {
            return false;
        }

        // 허용된 문자만 사용했는지 체크 (영문, 숫자, 특수문자만 허용)
        if (!preg_match('/^[a-zA-Z0-9!@#$%^&*()_+\-=\[\]{};\':"\\|,.<>\/?]+$/', $value)) {
            return false;
        }

        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return '아이디는 12~20자로, 영문 대문자, 소문자, 특수문자를 모두 포함해야 합니다.';
    }
}