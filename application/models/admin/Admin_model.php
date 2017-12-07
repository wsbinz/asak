<?php
/**
 * Created by PhpStorm.
 * User: s.manczak
 * Date: 26.09.2017
 * Time: 08:23
 */
class Admin_model extends CI_Model
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

    public function get($table,$limit='',$offset='')
    {
        if($limit == '') {
            $query = $this->db->get($table);
            return $query->result();
        }
        else
        {
            $query = $this->db->get($table,$limit,$offset);
            return $query->result();
        }
    }

    public function get_where($table,$where)
    {
        $this->db->where($where);
        $query = $this->db->get($table);
        return $query->result();
    }

    public function get_single($table,$where)
    {
        $query = $this->db->get_where($table,$where);
        $this->db->get($table);
        return $query->row();
    }

    public function get_max($table,$max)
    {
        $this->db->select_max($max);
        $query = $this->db->get($table);
        return $query->result();
    }

    public function search($table,$col,$or)
    {
        $this->db->like($col);
        $this->db->or_like($or);
        $query = $this->db->get($table);
        return $query->result();
    }

    public function num_rows($table)
    {
        $query = $this->db->get($table);
        return $query->num_rows();
    }

}