<?php

return [
    'required' => ':attributeを入力してください。',
    'email' => ':attributeはメール形式で入力してください。',
    'confirmed' => ':attributeと一致しません。',
    'unique' => 'その:attributeは既に使用されています。',
    'string' => ':attributeは文字列で入力してください。',
    'date' => ':attributeは有効な日付を入力してください。',
    'url' => ':attributeは有効なURLを入力してください。',
    'array' => ':attributeの形式が正しくありません。',
    'integer' => ':attributeは整数で入力してください。',
    'distinct' => ':attributeに重複した値が含まれています。',
    'exists' => '選択された:attributeは存在しません。',
    'before_or_equal' => ':attributeは今日以前の日付を入力してください。',

    'min' => [
        'string' => ':attributeは:min文字以上で入力してください。',
        'array' => ':attributeは:min個以上選択してください。',
        'numeric' => ':attributeは:min以上で入力してください。',
    ],

    'max' => [
        'string' => ':attributeは:max文字以内で入力してください。',
        'array' => ':attributeは:max個以内で選択してください。',
        'numeric' => ':attributeは:max以下で入力してください。',
    ],

    'attributes' => [
        'name' => 'お名前',
        'email' => 'メールアドレス',
        'password' => 'パスワード',

        'title' => 'タイトル',
        'author' => '著者名',
        'isbn' => 'ISBN',
        'published_date' => '出版日',
        'description' => '説明',
        'image_url' => '画像URL',
        'genres' => 'ジャンル',
        'genres.*' => 'ジャンル',

        'rating' => '評価',
        'comment' => 'コメント',
    ],
];
