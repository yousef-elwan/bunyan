<?php

use App\Models\BusinessSetting;

if (!function_exists('getWebConfig')) {
    function getWebConfig($name): string|object|array|null
    {
        $config = null;
        // $check = ['language', 'company_name', 'company_email'];
        $check = [];

        if (in_array($name, $check) == true && session()->has($name)) {
            $config = session($name);
        } else {
            $data = BusinessSetting::where(['type' => $name])->first();
            if (isset($data)) {
                $config = json_decode($data['value'], true);
                if (is_null($config)) {
                    $config = $data['value'];
                }
            }

            if (in_array($name, $check) == true) {
                session()->put($name, $config);
            }
        }
        return $config;
    }
}
