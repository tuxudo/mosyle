<?php

	/*
	|===============================================
	| Mosyle
	|===============================================
	|
	| A working Mosyle instance is required for use of this module.
	|
	| To use the Mosyle module's API aspect, set 'mosyle_enable' to TRUE
	| and enter the a dedicated Mosyle username (email address), password
	| and API key for accessing your Mosyle instance.
	| 
	| If using Mosyle School, you may need to change the Mosyle Address.
	|
	| This module pulls data about Macs that are in Mosyle.
	|
	*/

return [
  'mosyle_enable' => env('MOSYLE_ENABLE', false),
  'mosyle_username' => env('MOSYLE_USERNAME', ""),
  'mosyle_password' => env('MOSYLE_PASSWORD', ""),
  'mosyle_api_key' => env('MOSYLE_API_KEY', ""),
  'mosyle_address' => env('MOSYLE_ADDRESS', "https://businessapi.mosyle.com/"),
];
