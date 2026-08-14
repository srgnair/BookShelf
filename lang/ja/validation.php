<?php

return [
    'required' => ':attributeを入力してください。',
    'email' => ':attributeはメール形式で入力してください。',
    'confirmed' => ':attributeと一致しません。',
    'unique' => 'その:attributeは既に使用されています。',
    'string' => ':attributeは文字列で入力してください。',

    'min' => [
        'string' => ':attributeは:min文字以上で入力してください。',
    ],

    'max' => [
        'string' => ':attributeは:max文字以内で入力してください。',
    ],

    'attributes' => [
        'name' => 'お名前',
        'email' => 'メールアドレス',
        'password' => 'パスワード',
    ],
];
