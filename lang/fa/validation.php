<?php

/*
| Persian validation messages for the rules this app (and Filament's forms) use.
| Rules not listed here fall back to English.
*/

return [
    'accepted' => ':attribute باید پذیرفته شود.',
    'after' => ':attribute باید تاریخی بعد از :date باشد.',
    'after_or_equal' => ':attribute باید تاریخی بعد از :date یا برابر با آن باشد.',
    'array' => ':attribute باید یک آرایه باشد.',
    'before' => ':attribute باید تاریخی قبل از :date باشد.',
    'before_or_equal' => ':attribute باید تاریخی قبل از :date یا برابر با آن باشد.',
    'between' => [
        'array' => ':attribute باید بین :min و :max آیتم داشته باشد.',
        'file' => 'حجم :attribute باید بین :min و :max کیلوبایت باشد.',
        'numeric' => ':attribute باید بین :min و :max باشد.',
        'string' => ':attribute باید بین :min و :max کاراکتر باشد.',
    ],
    'boolean' => ':attribute باید درست یا نادرست باشد.',
    'confirmed' => 'تکرار :attribute مطابقت ندارد.',
    'current_password' => 'رمز عبور فعلی صحیح نیست.',
    'date' => ':attribute یک تاریخ معتبر نیست.',
    'date_format' => ':attribute با قالب :format مطابقت ندارد.',
    'different' => ':attribute و :other باید متفاوت باشند.',
    'email' => ':attribute باید یک ایمیل معتبر باشد.',
    'exists' => ':attribute انتخاب‌شده معتبر نیست.',
    'in' => ':attribute انتخاب‌شده معتبر نیست.',
    'integer' => ':attribute باید عدد صحیح باشد.',
    'max' => [
        'array' => ':attribute نباید بیشتر از :max آیتم داشته باشد.',
        'file' => 'حجم :attribute نباید بیشتر از :max کیلوبایت باشد.',
        'numeric' => ':attribute نباید بزرگ‌تر از :max باشد.',
        'string' => ':attribute نباید بیشتر از :max کاراکتر باشد.',
    ],
    'min' => [
        'array' => ':attribute باید حداقل :min آیتم داشته باشد.',
        'file' => 'حجم :attribute باید حداقل :min کیلوبایت باشد.',
        'numeric' => ':attribute باید حداقل :min باشد.',
        'string' => ':attribute باید حداقل :min کاراکتر باشد.',
    ],
    'numeric' => ':attribute باید عدد باشد.',
    'password' => [
        'letters' => ':attribute باید حداقل یک حرف داشته باشد.',
        'mixed' => ':attribute باید حداقل یک حرف بزرگ و یک حرف کوچک داشته باشد.',
        'numbers' => ':attribute باید حداقل یک عدد داشته باشد.',
        'symbols' => ':attribute باید حداقل یک نماد داشته باشد.',
        'uncompromised' => ':attribute در نشت‌های اطلاعاتی دیده شده است. لطفاً :attribute دیگری انتخاب کنید.',
    ],
    'regex' => 'قالب :attribute معتبر نیست.',
    'required' => 'وارد کردن :attribute الزامی است.',
    'same' => ':attribute و :other باید یکسان باشند.',
    'size' => [
        'array' => ':attribute باید :size آیتم داشته باشد.',
        'file' => 'حجم :attribute باید :size کیلوبایت باشد.',
        'numeric' => ':attribute باید :size باشد.',
        'string' => ':attribute باید :size کاراکتر باشد.',
    ],
    'string' => ':attribute باید متن باشد.',
    'unique' => 'این :attribute قبلاً ثبت شده است.',
    'url' => ':attribute باید یک آدرس معتبر باشد.',

    'custom' => [],

    'attributes' => [
        'name' => 'نام',
        'email' => 'ایمیل',
        'password' => 'رمز عبور',
        'date' => 'تاریخ',
        'mood_score' => 'امتیاز حال',
        'notes' => 'یادداشت',
    ],
];
