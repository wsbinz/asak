<?php
/**
 * Created by PhpStorm.
 * User: s.manczak
 * Date: 26.09.2017
 * Time: 07:59
 */


$config = array(
    'site_login' => array(
        array(
            'field' => 'email',
            'label' => 'Adres Email',
            'rules' => 'trim|required|valid_email'
        ),
        array(
            'field' => 'password',
            'label' => 'Hasło',
            'rules' => 'trim|required|min_length[5]'
        )
    ),

    'admin_user_create'=> array(
        array(
            'field'=>'username',
            'label' => 'Login',
            'rules' => 'trim|required'
        ),
        array(
            'field'=>'email',
            'label' => 'Email',
            'rules' => 'trim|required|valid_email|is_unique[users.email]'
        ),
        array(
            'field'=>'password',
            'label' => 'Hasło',
            'rules' => 'trim|required|min_length[5]'
        ),
        array(
            'field'=>'password_retry',
            'label' => 'Powtórz hasło',
            'rules' => 'trim|required|matches[password]'
        ),
    ),

    'admin_group_create'=> array(

        array(
            'field'=>'group_name',
            'label' => 'Nazwa_grupy',
            'rules' => 'trim|required'
        ),
        array(
            'field'=>'alias',
            'label' => 'Alias',
            'rules' => 'trim|is_unique[GROUPS.alias]'
        ),
    ),

);