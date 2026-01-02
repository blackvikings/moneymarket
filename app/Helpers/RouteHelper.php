<?php

if (!function_exists('get_route_prefix')) {
    function get_route_prefix()
    {
        if (auth()->check() && auth()->user()->hasRole('admin')) {
            return 'admin.';
        }
        return 'employee.';
    }
}
