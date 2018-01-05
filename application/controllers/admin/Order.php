<?php
/**
 * Created by PhpStorm.
 * User: Boruś
 * Date: 2017-12-14
 * Time: 13:19
 */

defined('BASEPATH') OR exit('No direct script access allowed');

class Order extends Admin_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->library('session');
        $this->load->model('admin/Admin_model');
        $this->load->helper('My');
        $this->load->library('pagination');
    }


    public function index()
    {
        $data['dost'] = $this->Admin_model->get('VEND');
        $data['indk_mwym'] = $this->Admin_model->get("MNAME");


        $zam = $this->input->post('zam',true);

        if (!empty($zam))
        {
            if (is_numeric($zam))
            {
                if (($zam > 0))
                {
                    $data = array(
                        'nr_mat' => $nr_mat,
                        'ilosc' => $zam,
                    );

                    $this->Admin_model->create("DOCUM_ITEM", $data);
                    $this->session->set_flashdata('alert', "Zamówienie zostało wysłane");

                }

                else {
                    $this->session->set_flashdata('alert', "Ilość zamawianego towaru musi być równa lub większa od zera!");
                    $data['ilosc'] = $zam;
                    $this->twig->display('admin/product/order_products', $data);
                }
            } else {
                $this->session->set_flashdata('alert', "Ilość do zamówienia musi być wartością numeryczną!");
                $data['ilosc'] = $zam;
                $this->twig->display('admin/product/order_products', $data);
            }


        }

        $data['validation'] = $this->session->flashdata('alert');
        $this->twig->display('admin/product/order_products', $data);

        /*$where = array('nr_mat'=>$nr_mat);
        $data['sdas'] = $this->Admin_model->get_where('STOR_AMOUNTS',$where);
        $where = array('nr_mat'=>46);
        $data['msize'] = $this->Admin_model->get_where("MSIZE",$where);*/
    }

    public function pz()
    {

        $total_rows = $this->Admin_model->num_rows("DOCUM_HEAD");
        $start_index = ($this->uri->segment(4)) ? $this->uri->segment(4) : 0 ;

        $config['total_rows'] = $total_rows;
        $config['per_page'] = 4;
        $config['uri_segment'] = 4;
        $config['base_url'] = base_url('admin/order/pz');

        /*        $config['num_links'] = 2;
                $config['use_page_numbers'] = TRUE;
                $config['reuse_query_string'] = TRUE;*/

        $config['full_tag_open'] = '<ul class="pagination">';
        $config['full_tag_close'] = '</ul>';

        $config['first_link'] = 'First';
        $config['first_tag_open'] = '<li class="">';
        $config['first_tag_close'] = '</li>';

        $config['last_link'] = 'Last';
        $config['last_tag_open'] = '<li class="">';
        $config['last_tag_close'] = '</li>';

        $config['next_link'] = 'Next';
        $config['next_tag_open'] = '<li class="page-item"><span class="page-link">';
        $config['next_tag_close'] = '</span></li>';

        $config['prev_link'] = 'Previous';
        $config['prev_tag_open'] = '<li class="page-item"><span class="page-link">';
        $config['prev_tag_close'] = '</span></li>';

        $config['cur_tag_open'] = '<li class="page-item active"><a href="">';
        $config['cur_tag_close'] = '</a></li>';

        $config['num_tag_open'] = '<li class="page-item">';
        $config['num_tag_close'] = '</li>';

        $data['indk_doc'] = $this->Admin_model->get("DOCUM_HEAD",$config['per_page'],$start_index);

        $this->pagination->initialize($config);
        $data['links'] = $this->pagination->create_links();

        $data['validation'] = $this->session->flashdata('alert');
        $this->twig->display('site/product/list_pz',$data);

    }
    public function pdf()
    {

    }
}
