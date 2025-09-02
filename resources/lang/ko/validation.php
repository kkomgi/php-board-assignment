<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    */

    // 기본 규칙들
    'required'             => ':attribute 필드는 필수입니다.',
    'string'               => ':attribute은(는) 문자열이어야 합니다.',
    'email'                => ':attribute은(는) 유효한 이메일 주소여야 합니다.',
    'unique'               => ':attribute은(는) 이미 사용 중입니다.',
    'confirmed'            => ':attribute 확인이 일치하지 않습니다.',
    'exists'               => '선택된 :attribute은(는) 유효하지 않습니다.',

    // 길이 관련
    'max'                  => [
        'string'  => ':attribute은(는) :max 문자를 초과할 수 없습니다.',
    ],
    'min'                  => [
        'string'  => ':attribute은(는) 최소 :min 문자여야 합니다.',
    ],

    // 조건부 필수
    'sometimes'            => ':attribute은(는) 선택사항입니다.',
    'nullable'             => ':attribute은(는) 비어있을 수 있습니다.',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    */

    'attributes' => [
        'username' => '아이디',
        'name'     => '이름',
        'email'    => '이메일',
        'password' => '비밀번호',
        'password_confirmation' => '비밀번호 확인',
        'title'    => '제목',
        'body'     => '내용',
        'parent_id' => '상위 댓글',
    ],
];