<?php
return [
    'input_type' => [
      'text' => 'Text',
      'number' => 'Number',
      'color' => 'Color',
      'date' => 'Date',
      'textarea' => 'Textarea',
      // 'select' => 'Select',
      'toggle' => 'Toggle',
      'image' => 'Image',
      'multiple_image' => 'Multiple Image',
      'file' => 'File',
      'multiple_file' => 'Multiple File',
    ],

    'theme' => [
      'primary_color' => '#926e38',
      'primary_rgba' => '163, 118, 78',
      'bg_subtle' => '#926e380c',
      'btn_bg' => '#926e38',
      'btn_border' => '#926e38',
      'btn_hover_bg' => '#a47d42',
      'btn_hover_border' => '#a47d42',
    ],
    
    'company' => [
      'name' => 'Tanur Muthmainnah',
      'code' => 'TNM',
    ],

    'apiwps' => [
      'api_url' => env('TANUR_API_URL', 'https://api.tanurtour.app'),
      'sandbox_api_url' => env('TANUR_SANDBOX_API_URL', 'https://sandbox.tanurtour.app/api'),
      'app_id' => env('TANUR_APP_ID', 'your_app_id'),
      'sandbox_app_id' => env('TANUR_SANDBOX_APP_ID', 'your_sandbox_app_id'),
      'timeout' => env('TANUR_API_TIMEOUT', 30),
    ],
];