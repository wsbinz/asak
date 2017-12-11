<?php
/**
 * Created by PhpStorm.
 * User: s.manczak
 * Date: 26.09.2017
 * Time: 08:23
 */
class Admin_model extends CI_Model
{

    public function create($table,$data) //tworzenie nowego czegos w bazie
    {
        $this->db->insert($table ,$data);
    }

    public function update($table,$data,$where) //update w bazie
    {
        $this->db->where($where);
        $this->db->update($table,$data);
    }

    public function delete($table,$where) //usuwanie czegos z bazy
    {
        $this->db->where($where);
        $this->db->delete($table);
    }

    public function get($table,$limit='',$offset='') //zwraca wszystkie wiersze (opcjonalnie od ktorego do ktorego czyli LIMIT i OFFSET z SQL)
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

    public function get_where($table,$where) //zwraca wiele wierszy ale z danym where
    {
        $this->db->where($where);
        $query = $this->db->get($table);
        return $query->result();
    }

    public function get_single($table,$where) //zwraca jeden wiersz z danym where
    {
        $query = $this->db->get_where($table,$where);
        $this->db->get($table);
        return $query->row();

    }

    public function get_max($table,$max) //zwraca maxymalna wartosc no tutaj tez zwroci jedna wartosc bo maxymalna
    {
        $this->db->select_max($max);
        $query = $this->db->get($table);
        return $query->result();
    }

    public function search($table,$col,$or,$where='') //Tuaj zwraca Ci dane wiersze ktra maja zawarty wyraz normalny LIKE w sql
    {
        $this->db->where($where);
        $this->db->like($col);
        $this->db->or_like($or);
        $query = $this->db->get($table);
        return $query->result();
    }

    public function num_rows($table) //Pobiera liczbe wierszy z tabeli
    {
        $query = $this->db->get($table);
        return $query->num_rows();
    }

}

