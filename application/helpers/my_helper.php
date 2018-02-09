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

function fileLog($wiadomosc='',$status='')
{
    $template = 'Użytkownik o nazwie: '.$_SESSION['username'] .'oraz id: '.$_SESSION['id'].' Wykonał akcję: '."\n".$wiadomosc;
    $array = array();
    $t =
         array(
        "Status" => $status,
        "Log" => $template,
        "Data" => date('d.m.Y H:i:s')

    );

    if(file_exists(BASEPATH.'../asset/log/log1.json'))
    {
       $json = json_decode(file_get_contents(BASEPATH.'../asset/log/log1.json'),true);
       //unlink(BASEPATH.'../asset/log/log1.json');
       array_push($json,$t);
       $result = json_encode($json,JSON_UNESCAPED_UNICODE);
    }
    else {
        array_push($array,$t);
        $result = json_encode($array,JSON_UNESCAPED_UNICODE);
    }



    if(!file_exists(BASEPATH.'../asset/log'))
    {
        mkdir("asset/log",755);
    }

    $file = fopen(BASEPATH.'../asset/log/log1.json','w');
    fwrite($file,$result);
    fclose($file);

/*    if(!fopen(BASEPATH.'../asset/log/log1.txt',$result,'a'))
    {
        if(fopen(BASEPATH.'../asset/log/log1.txt',date('d.m.Y H:i:s')." => Nie udało się zapisać logu",'a+'));
    };*/




}