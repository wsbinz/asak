<?php

/**
 * Created by PhpStorm.
 * User: s.manczak
 * Date: 27.09.2017
 * Time: 09:08
 */
class Site_model extends CI_Model
{

    public function create($table,$data)
    {
        $this->db->insert($table ,$data);
    }

    public function update($table,$data,$where)
    {
        $this->db->where($where);
        $this->db->update($table,$data);
    }

    public function delete($table,$where)
    {
        $this->db->where($where);
        $this->db->delete($table);
    }

    public function get($table)
    {
        $query = $this->db->get($table);
        return $query->result();
    }

    public function get_single($table,$where)
    {
        $query = $this->db->get_where($table,$where);
        $this->db->get($table);
        return $query->row();
    }

}