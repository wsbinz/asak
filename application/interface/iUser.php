<?php
/**
 * Created by PhpStorm.
 * User: s.manczak
 * Date: 04.10.2017
 * Time: 12:27
 */

interface iUser
{
    public function index();
    public function create_user();
    public function edit_user($id);
    public function delete($id);
}