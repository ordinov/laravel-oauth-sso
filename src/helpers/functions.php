<?php

if (!function_exists('user')) {
    function user() {
        if (!auth()->check()) { return false; }
        return auth()->user();
    }
}
