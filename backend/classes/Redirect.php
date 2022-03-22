<?php

class Redirect
{
    public static function redirect_to($url, $statusCode = 303)
    {
        header('Location: ' . $url, true, $statusCode);
        die();
    }
}
