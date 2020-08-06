<?php

require(dirname(__FILE__) . 'integration_wrappers/ShippingEasy/lib/ShippingEasy.php');

/**
 * NOTE: This is necessary because Lumen does not have the
 *       config_path or app_path helpers
 */

if ( ! function_exists('config_path'))
{
    /**
     * Get the configuration path.
     *
     * @param  string $path
     * @return string
     */
    function config_path($path = '')
    {
        $p =  app()->basePath() . '/config' . ($path ? '/' . $path : $path);
        echo "\n\nConfig path is:  $p\n";

        return $p;
    }
}

if ( ! function_exists('app_path'))
{
    /**
     * Get the app path.
     *
     * @param  string $path
     * @return string
     */
    function app_path($path = '')
    {
        $p = app()->basePath() . '/app' . ($path ? '/' . $path : $path);
        echo "\n\nApp path is:  $p\n";

        return $p;
    }
}
