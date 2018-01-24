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
    $string = 'ABCDEFGHIJKLMNOPRSTWVXYZŁĆŻĄĘ1234567890!@#$%^&*()';
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
//sfdsda
//Funkcja zwraca przekonwertowany text np: Word is big > word-is-big
function alias($alias)
{
    $alias = convert_accented_characters($alias);
    $alias = url_title($alias,'_');
    return $alias;
}

function check_group($alias_group)
{
        $CI =& get_instance();
        foreach ($alias_group as $group) {
            $where = array('alias' => $group);
            $all_groups = $CI->Admin_model->get_single('GROUPS', $where);

            if (!empty($all_groups)) {
                $where = array(
                    'id_users' => $_SESSION['id'],
                    'id_groups' => $all_groups->id,
                );
            }

            $user_in_group = $CI->Admin_model->get_single("GROUPS_USERS", $where);

            if (!empty($user_in_group)) {
                return true;
            }
        }
        return false;



}

function fileLog($status='',$wiadomosc='TESTOWA WIADOMOSC')
{
    if(write_file(BASEPATH.'../asset/log/log1.txt',$wiadomosc))
    {

    }

    print_r(BASEPATH.'../asset/log/log1.txt');
}