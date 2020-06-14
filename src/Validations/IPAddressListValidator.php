<?php


namespace Larapress\Profiles\Validations;


use Illuminate\Support\Facades\Validator;

class IPAddressListValidator
{
	public static function register() {
		Validator::extend('ip_list', function ($attributes, $values, $parameters, $validator) {
			$ips = explode(",", $values);
			foreach ($ips as $ip) {
				if (! filter_var($ip, FILTER_VALIDATE_IP)) {
					return false;
				}
			}

			return true;
		});
	}
}