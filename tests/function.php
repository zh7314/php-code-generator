<?php

if (!function_exists('p')) {

    function p($data = null) {
        echo '<pre>';
        print_r($data);
        echo '</pre>';
    }

}
if (!function_exists('pp')) {

    function pp($data = null) {
        echo '<pre>';
        print_r($data);
        echo '</pre>';
        die;
    }

}
if (!function_exists('is_json')) {

    function is_json($string) {
        @json_decode($string);
        return (json_last_error() == JSON_ERROR_NONE);
    }

}

