<?php

function user() {
    if (!auth()->check()) { return false; }
    return auth()->user();
}