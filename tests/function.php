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


