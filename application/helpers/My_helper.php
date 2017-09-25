<?php
/**
 * Created by PhpStorm.
 * User: s.manczak
 * Date: 25.09.2017
 * Time: 13:28
 */

function refresh()
{
    $CI =& get_instance();
    return redirect($CI->uri->uri_string(),'refresh');
}