<?php if (! defined('BASEPATH')) exit('No direct script access allowed');

if (! function_exists('get_client_ip')) {
    function get_client_ip()
    {
        $ipaddress = '';
        if (isset($_SERVER['HTTP_CLIENT_IP']))
            $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
        else if (isset($_SERVER['HTTP_X_FORWARDED_FOR']))
            $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
        else if (isset($_SERVER['HTTP_X_FORWARDED']))
            $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
        else if (isset($_SERVER['HTTP_FORWARDED_FOR']))
            $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
        else if (isset($_SERVER['HTTP_FORWARDED']))
            $ipaddress = $_SERVER['HTTP_FORWARDED'];
        else if (isset($_SERVER['REMOTE_ADDR']))
            $ipaddress = $_SERVER['REMOTE_ADDR'];
        else
            $ipaddress = 'UNKNOWN';
        return $ipaddress;
    }
}

function checkEditDelete($userType, $dateTime = null)
{
    if ($userType == 'a' || $userType == 'm') {
        return true;
    }
    $userTypesAllowed = ['e', 'u'];
    if (in_array($userType, $userTypesAllowed)) {
        if ($dateTime) {
            $now = new DateTime();
            $record = new DateTime($dateTime);
            $diffSeconds = $now->getTimestamp() - $record->getTimestamp();
            if ($diffSeconds >= 0 && $diffSeconds <= 86400) {
                return true;
            }
            return false;
        }
    }

    return false;
}
