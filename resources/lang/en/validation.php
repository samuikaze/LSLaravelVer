<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages here.
    |
    */

    'accepted' => 'The :attribute must be accepted.',
    'active_url' => 'The :attribute is not a valid URL.',
    'after' => 'The :attribute must be a date after :date.',
    'after_or_equal' => 'The :attribute must be a date after or equal to :date.',
    'alpha' => 'The :attribute may only contain letters.',
    'alpha_dash' => 'The :attribute may only contain letters, numbers, dashes and underscores.',
    'alpha_num' => 'The :attribute may only contain letters and numbers.',
    'array' => 'The :attribute must be an array.',
    'before' => 'The :attribute must be a date before :date.',
    'before_or_equal' => 'The :attribute must be a date before or equal to :date.',
    'between' => [
        'numeric' => 'The :attribute must be between :min and :max.',
        'file' => 'The :attribute must be between :min and :max kilobytes.',
        'string' => 'The :attribute must be between :min and :max characters.',
        'array' => 'The :attribute must have between :min and :max items.',
    ],
    'boolean' => 'The :attribute field must be true or false.',
    'confirmed' => ':attribute兩次輸入不一樣',
    'date' => ':attribute的日期值不被接受',
    'date_equals' => 'The :attribute must be a date equal to :date.',
    'date_format' => ':attribute的日期格式需依照:format格式編寫',
    'different' => 'The :attribute and :other must be different.',
    'digits' => ':attribute必須為:digits位數字',
    'digits_between' => 'The :attribute must be between :min and :max digits.',
    'dimensions' => 'The :attribute has invalid image dimensions.',
    'distinct' => 'The :attribute field has a duplicate value.',
    'email' => ':attribute欄位請以正確的格式輸入',
    'ends_with' => 'The :attribute must end with one of the following: :values',
    'exists' => 'The selected :attribute is invalid.',
    'file' => ':attribute必須為檔案',
    'filled' => 'The :attribute field must have a value.',
    'gt' => [
        'numeric' => 'The :attribute must be greater than :value.',
        'file' => 'The :attribute must be greater than :value kilobytes.',
        'string' => 'The :attribute must be greater than :value characters.',
        'array' => 'The :attribute must have more than :value items.',
    ],
    'gte' => [
        'numeric' => 'The :attribute must be greater than or equal :value.',
        'file' => 'The :attribute must be greater than or equal :value kilobytes.',
        'string' => 'The :attribute must be greater than or equal :value characters.',
        'array' => 'The :attribute must have :value items or more.',
    ],
    'image' => ':attribute必須為圖片檔案格式',
    'in' => ':attribute值不被接受',
    'in_array' => 'The :attribute field does not exist in :other.',
    'integer' => ':attribute必須為整數值',
    'ip' => 'The :attribute must be a valid IP address.',
    'ipv4' => 'The :attribute must be a valid IPv4 address.',
    'ipv6' => 'The :attribute must be a valid IPv6 address.',
    'json' => 'The :attribute must be a valid JSON string.',
    'lt' => [
        'numeric' => 'The :attribute must be less than :value.',
        'file' => 'The :attribute must be less than :value kilobytes.',
        'string' => 'The :attribute must be less than :value characters.',
        'array' => 'The :attribute must have less than :value items.',
    ],
    'lte' => [
        'numeric' => 'The :attribute must be less than or equal :value.',
        'file' => 'The :attribute must be less than or equal :value kilobytes.',
        'string' => 'The :attribute must be less than or equal :value characters.',
        'array' => 'The :attribute must not have more than :value items.',
    ],
    'max' => [
        'numeric' => ':attribute不可大於:max',
        'file' => ':attribute 檔案大小不可大於 :max KB',
        'string' => ':attribute不可大於:max字元',
        'array' => 'The :attribute may not have more than :max items.',
    ],
    'mimes' => ':attribute檔案格式必須為： :values',
    'mimetypes' => ':attribute檔案格式必須為： :values.',
    'min' => [
        'numeric' => ':attribute欄位的值需大於 :min',
        'file' => 'The :attribute must be at least :min kilobytes.',
        'string' => ':attribute必須大於:min字',
        'array' => 'The :attribute must have at least :min items.',
    ],
    'not_in' => 'The selected :attribute is invalid.',
    'not_regex' => 'The :attribute format is invalid.',
    'numeric' => ':attribute必須為數字',
    'present' => 'The :attribute field must be present.',
    'regex' => ':attribute格式不正確',
    'required' => ':attribute欄位不能為空！',
    'required_if' => 'The :attribute field is required when :other is :value.',
    'required_unless' => 'The :attribute field is required unless :other is in :values.',
    'required_with' => 'The :attribute field is required when :values is present.',
    'required_with_all' => 'The :attribute field is required when :values are present.',
    'required_without' => 'The :attribute field is required when :values is not present.',
    'required_without_all' => 'The :attribute field is required when none of :values are present.',
    'same' => 'The :attribute and :other must match.',
    'size' => [
        'numeric' => 'The :attribute must be :size.',
        'file' => 'The :attribute must be :size kilobytes.',
        'string' => 'The :attribute must be :size characters.',
        'array' => 'The :attribute must contain :size items.',
    ],
    'starts_with' => 'The :attribute must start with one of the following: :values',
    'string' => ':attribute必須為字串',
    'timezone' => 'The :attribute must be a valid zone.',
    'unique' => ':attribute已經被使用過了',
    'uploaded' => ':attribute檔案上傳失敗',
    'url' => ':attribute格式必須為網址格式',
    'uuid' => 'The :attribute must be a valid UUID.',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "attribute.rule" to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */

    'custom' => [
        'attribute-name' => [
            'rule-name' => 'custom-message',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap our attribute placeholder
    | with something more reader friendly such as "E-Mail Address" instead
    | of "email". This simply helps us make our message more expressive.
    |
    */

    'attributes' => [
        // 前台表單
        'username' => '使用者名稱',
        'password' => '密碼',
        'usernickname' => '暱稱',
        'email' => '電子郵件',
        'userrealname' => '真實姓名',
        'userphone' => '電話號碼',
        'useraddress' => '聯絡地址',
        'avatorimage' => '虛擬形象',
        'delavatorimage' => '刪除虛擬形象',
        'posttitle' => '文章標題',
        'posttype' => '文章分類',
        'postcontent' => '文章內容',
        'replytitle' => '回文標題',
        'replycontent' => '回文內容',
        'qty' => '數量',
        'fPattern' => '結帳方式',
        'clientname' => '訂單姓名',
        'clientphone' => '訂單電話',
        'clientaddress' => '訂單地址或郵局/超商名稱',
        'clientcasher' => '付款方式',
        'removereason' => '申請取消訂單的原因',
        // 後台表單
        'carouselDescript' => '輪播描述',
        'carouselTarget' => '輪播位址',
        'carouselImg' => '輪播圖片',
        'newsType' => '消息類型',
        'newsTitle' => '消息標題',
        'newsContent' => '消息內容',
        'prodname' => '作品名稱',
        'prodtype' => '作品類型',
        'prodplatform' => '作品平台',
        'prodreldate' => '作品發售日期',
        'produrl' => '作品位址',
        'proddescript' => '作品描述',
        'prodimage' => '作品視覺圖',
        'boardname' => '討論板名稱',
        'boarddescript' => '討論版描述',
        'hideboard' => '隱藏討論板',
        'boardimage' => '討論板圖片',
        'privnum' => '權限編號',
        'privname' => '權限名稱',
        'userpriviledge' => '使用者權限',
        'searchuser' => '搜尋使用者的關鍵字',
        'searchtarget' => '搜尋目標',
        'goodname' => '商品名稱',
        'goodstatus' => '商品販售狀態',
        'goodprice' => '商品價格',
        'goodquantity' => '商品在庫量',
        'gooddescript' => '商品描述',
        'goodimage' => '商品圖片',
        'delgoodimage' => '移除商品圖片',
        'reviewResult' => '審核結果',
        'reviewNotify' => '審核通知',
        'numAdminList' => '後台管理顯示資料列數',
        'numNews' => '最新消息單頁顯示行數',
        'numGoodQtyDanger' => '週邊商品庫存紅字閥值',
        'numGoods' => '週邊商品單頁顯示行數',
        'numPosts' => '討論板單頁顯示項目數',
        'numArticles' => '文章頁面單頁顯示個數',
        'adminPriv' => '討論版管理權限授權',
        'backendPriv' => '後台登入權限授權',
        'registerable' => '新帳號註冊',
    ],

];
