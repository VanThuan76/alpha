<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Application Name
    |--------------------------------------------------------------------------
    |
    | This value is the name of your application. This value is used when the
    | framework needs to place the application's name in a notification or
    | any other location as required by the application or its packages.
    |
    */

    
    'token' => env('FACEBOOK_TOKEN'),
    'app_secret' => env('FACEBOOK_APP_SECRET'),
    'verification'=> env('FACEBOOK_VERIFICATION'),
    'greeting_text' => "abc",
    'start_button_payload' => 'YOUR_PAYLOAD_TEXT'
];