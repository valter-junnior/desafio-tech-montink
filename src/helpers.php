<?php

if (!function_exists('dd')) {
    /**
     * Dump the passed variables and end the script.
     *
     * @param mixed ...$args
     * @return void
     */
    function dd(...$args)
    {
        ini_set('html_errors', 'On');
        echo '<pre style="background-color: #f8f9fa; border: 1px solid #dee2e6; padding: 15px; border-radius: 5px; margin: 10px; font-family: monospace; font-size: 14px; line-height: 1.5; color: #333; overflow-x: auto;">';
        foreach ($args as $x) {
            var_dump($x);
            echo "\n";
        }
        echo '</pre>';
        die();
    }
}