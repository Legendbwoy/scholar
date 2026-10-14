<?php

use App\Models\Setting;

if (!function_exists('setting')) {
    /**
     * Helper to get or set a setting value
     *
     * @param string|array|null $key
     * @param mixed $default
     * @return mixed
     */
    function setting($key = null, $default = null)
    {
        if (is_null($key)) {
            return app(Setting::class);
        }

        if (is_array($key)) {
            foreach ($key as $k => $v) {
                Setting::set($k, $v);
            }
            return true;
        }

        return Setting::get($key, $default);
    }
}
