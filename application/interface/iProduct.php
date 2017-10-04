<?php
/**
 * Created by PhpStorm.
 * User: s.manczak
 * Date: 04.10.2017
 * Time: 12:23
 */

interface iProduct
{
    public function index();
    public function add_product();
    public function show($id,$alias);
    public function edit_product($id);
    public function change_product($id);
    public function delete_product($id);

}

