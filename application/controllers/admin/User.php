<?php

/**
 * Created by PhpStorm.
 * User: s.manczak
 * Date: 26.09.2017
 * Time: 08:06
 */

defined('BASEPATH') OR exit('No direct script access allowed');

class User extends Admin_Controller {


    public function __construct()
    {
        parent::__construct();
        date_default_timezone_set('UTC');
        $this->load->model('admin/Admin_model');
        $this->load->helper('My');
    }

    public function index()
    {

        $data['users'] = $this->Admin_model->get("USERS"); // Pobieranie wszystkich grup
        $groups = $this->Admin_model->get("GROUPS"); // Pobieranie wszystkich grup
        $data['groups_users'] = $this->Admin_model->get('GROUPS_USERS');

        foreach ($groups as $group)
        {
            $group_id_array[$group->id] = $group->group_name;
        }

        $data['group'] =  $group_id_array;

        $data['validation']= $this->session->flashdata('alert');
        $this->twig->display('admin/user/index',$data);
    }

    public function create_user()
    {

        $activation_code = random_string();
        if(!empty($_POST)){


            if($this->form_validation->run('admin_user_create') == TRUE)
            {
                $data = array(
                    'username' => $this->input->post('username',true),
                    'email' => trim($this->input->post('email',true)),
                    'password' => password_hash($this->input->post('password',true),PASSWORD_DEFAULT),
                    'create_date' => time(),
                    'active' => 0,
                    'activation_code' => $activation_code,
                );
                //Tworzenie uzytkownika w bazie
                $user = $this->Admin_model->create('USERS',$data);

                //Pobieranie id uzytkownika
                $where = array('email' => $data['email']);
                $user_id = $this->Admin_model->get_single("USERS",$where);

                $data_group = array(
                    'id_groups' => $this->input->post('group',true),
                    'id_users' => $user_id->id,
                );
                $this->Admin_model->create('GROUPS_USERS',$data_group); //Dodawanie go do grupy.

                //Wysyłanie meila do uzytkownika
                $do = $_POST['email'];
                $from = "biuro@ts3-tnt.pl <biuro@ts3-tnt.pl>";
                $mailheaders="From: $from\n";
                $mailheaders.="Reply-To: $from\n";
                $mailheaders.="X-Mailer: PHP\n";
                $mailheaders.="MIME-version: 1.0\n";
                $mailheaders.="Content-type: text/html; charset=utf-8";
                $message = '<p>Witaj ' . $data['username'].'. Aby aktytowac konto kliknij w poniższy link:'
                    .base_url('account/activation/'.$activation_code ).'</p>';
                $subject = "Aktywacja konta w serwisie ASAK";
                $do = (string)$data['email'];

                $mail = mail($do,$subject, $message, $mailheaders);

                $this->session->set_flashdata('alert',"Użytkownik został dodany !");
            }
            else
            {
                $this->session->set_flashdata('alert',validation_errors());
            }


        }
        //Pobieranie grup
        $data['group'] = $this->Admin_model->get("GROUPS");
        //$groups_user = $this->Admin_model->get("GROUPS_USERS");


        $data['validation']= $this->session->flashdata('alert');
        $this->twig->display('admin/user/create',$data);
    }

    public function edit_user($id)
    {
        $where = array('id' =>$id);
        $data['user'] = $this->Admin_model->get_single("USERS",$where); //Pobieranie danych uzytkownika

        $data['group'] = $this->Admin_model->get("GROUPS"); // Pobieranie wszystkich grup


        if(!empty($_POST)){
            $old_password = $data['user']->password;

            if($this->form_validation->run('admin_user_edit') == TRUE)
            {
                $data = array(
                    'username' => $this->input->post('username',true),
                    'email' => trim($this->input->post('email',true)),
                    'password' => password_hash($this->input->post('password',true),PASSWORD_DEFAULT),
                    'create_date' => time(),
                );

                if($_POST['password'] == '')
                {
                    $data['password'] = $old_password;
                }

                //edytowanie uzytkownika w bazie
                $where = array('id' => $id);
                $user = $this->Admin_model->update('USERS',$data,$where);

                $data_group = array(
                    'id_groups' => $this->input->post('group',true),
                    'id_users' => $id,
                );


                $where = array('id_users' =>$id);
                $data['user_in_group'] = $this->Admin_model->get_single("GROUPS_USERS",$where);
                //Sprawdza czy uzytkownik nalezy do grupy jezeli tak to robi update jezeli nie to create
                if($data['user_in_group'] == '')
                {
                    $user = $this->Admin_model->create('GROUPS_USERS',$data_group);
                }
                else
                {
                    $where = array('id_users' =>$id);
                    $this->Admin_model->update('GROUPS_USERS',$data_group,$where);
                }
                $this->session->set_flashdata('alert',"Użytkownik został edytowany !");
                refresh();
            }
            else
            {
                $this->session->set_flashdata('alert',validation_errors());
            }


        }


        $where = array('id_users' =>$id);
        $data['user_in_group'] = $this->Admin_model->get_single("GROUPS_USERS",$where);

        if(!empty($data['user_in_group'])) {
            foreach ($data['group'] as $group) {
                if ($group->id == $data['user_in_group']->id_groups) {
                    $data['group_id'] = $data['user_in_group']->id_groups;
                }

            }
        }

        $data['validation']= $this->session->flashdata('alert');
        $this->twig->display('admin/user/edit',$data);
    }


    public function edit_email($email)
    {
        $email_id = $this->uri->segment(4);
        $where = array('email' =>$email);
        $user_id = $this->Admin_model->get_single("USERS",$where);

        if(!empty($user_id) && $email_id == $user_id->id)
        {

            return true;
        }
        else
        {
            $this->form_validation->set_message('edit_email', 'Ktoś posiada już taki e-mail');
            return false;
        }
    }



}