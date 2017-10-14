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
    return redirect($CI->uri->uri_string(), 'refresh');
}

//Funkcja generuje nam unikatowy cig znaków
function random_string()
{
    $string = 'ABCDEFGHIJKLMNOPRSTWVXYZŁĆŻĄĘ1234567890';
    $random = '';

    for($i=0; $i<20; $i++)
    {
        $random .= $string[rand(0,strlen($string)-1)];
    }
    $random .= time();

    return md5($random);
}

//Funkcja sprawdza czy jesteśmy zalogowani jezeli tak zwraca true
function logged_in()
{
    if(isset($_SESSION['logged_in']) && $_SESSION['logged_in']==1)
    {
        return true;
    }
    return false;

}

//Funkcja zwraca przekonwertowany text np: Word is big > word-is-big
function alias($alias)
{
    $alias = convert_accented_characters($alias);
    $alias = url_title($alias,'_');
    return $alias;
}