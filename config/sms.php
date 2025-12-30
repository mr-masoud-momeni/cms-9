<?php
return [
'sms_ir' => [
'api_key' => env('SMS_IR_API_KEY'),
'line'    => env('SMS_IR_LINE'),
'url'     => 'https://api.sms.ir/v1/send/verify',
],
];
