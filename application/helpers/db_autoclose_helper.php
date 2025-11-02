<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Auto close database connection after request.
 * This prevents "Too many connections" issue.
 */

if (!function_exists('auto_close_db')) {
    function auto_close_db()
    {
        $CI =& get_instance();
        if (isset($CI->db)) {
            $CI->db->close();
        }
    }
}
