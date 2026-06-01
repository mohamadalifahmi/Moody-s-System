<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Business Types
    |--------------------------------------------------------------------------
    |
    | Define supported business types and their display names.
    | The 'general' type works for any business. Additional types
    | can adapt the UI labels, order types, and workflows.
    |
    */
    'types' => [
        'general'   => 'عام',
        'retail'    => 'متجر تجزئة',
        'wholesale' => 'تجارة جملة',
        'services'  => 'خدمات',
        'restaurant' => 'مطعم',
        'cafe'      => 'مقهى',
        'workshop'  => 'ورشة عمل',
    ],

    /*
    |--------------------------------------------------------------------------
    | Default Order Types per Business Type
    |--------------------------------------------------------------------------
    |
    | Each business type can have its own set of order types.
    |
    */
    'order_types' => [
        'general' => ['on_site' => 'مباشر', 'delivery' => 'توصيل', 'online' => 'أونلاين'],
        'retail' => ['in_store' => 'في المتجر', 'online' => 'أونلاين', 'delivery' => 'توصيل'],
        'restaurant' => ['dine_in' => 'داخلي', 'takeaway' => 'سفري', 'delivery' => 'توصيل'],
        'cafe' => ['dine_in' => 'داخلي', 'takeaway' => 'سفري'],
        'services' => ['on_site' => 'في الموقع', 'off_site' => 'خارجي', 'online' => 'عن بعد'],
    ],
];
