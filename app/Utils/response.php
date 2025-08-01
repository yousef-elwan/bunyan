<?php


if (!function_exists('successResponse')) {
    function successResponse(string $message = "", $data = [], $stateCode = 200, $with = [], $pagination = null)
    {
        return _response($message, $data, [], $stateCode, true, $with, $pagination);
    }
}

if (!function_exists('errorResponse')) {
    function errorResponse(string $message = "", int $state = 500, array $errors = [])
    {
        return _response($message, [], $errors, $state, false);
    }
}

if (!function_exists('_response')) {

    function _response($message, $data, $errors, $state, $success, $with = [], $pagination = null)
    {
        return response()->json([
            "message" => $message,
            "data" => $data,
            "errors" => $errors,
            "state" => $state,
            "success" => $success,
            "pagination" => $pagination,
            ...$with
        ], $state);
    }
}
