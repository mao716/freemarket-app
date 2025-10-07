<?php
// config/dev.php
return [
	'autologin' => env('DEV_AUTO_LOGIN', false),
	'autologin_user_id' => (int) env('DEV_AUTO_LOGIN_USER_ID', 1),
];
