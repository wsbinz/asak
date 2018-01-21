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
    public function order_products()
    {
        if(!empty($_POST)) {

            $docum_value = $this->input->post('docum_value',true);

            if (is_numeric($docum_value))
            {
                if ($docum_value >= 0)
                {
                    $data = array(
                        'nr_docum' => $this->input->post('nr_docum', true),
                        'nr_mat' => $this->input->post('nr_mat', true),
                        'docum_value' => $docum_value,
                        //'value_sign' => $this->input->post('value_sign', true),
                    );
                    $this->Admin_model->create("DOCUM_ITEM", $data);

                    $data = array(
                        'nr_docum' => $this->input->post('nr_docum', true),
                        'docum_type' => "Zm",
                        'vend_name' => $this->input->post('vend_name', true),
                        'docum_desc' => $this->input->post('docum_desc', true),
                    );
                    $this->Admin_model->create("DOCUM_HEAD", $data);

                    $this->session->set_flashdata('alert', "Zamówienie zostało wysłane");
                }
                else
                {
                    $this->session->set_flashdata('alert', "Liczba zamawianego towaru musi być równa lub większa od zera!");
                }
            }
            else
            {
                $this->session->set_flashdata('alert', "Liczba zamawianego towaru musi być wartością numeryczną!");
            }
        }

        //$data['doctype'] = $this->Admin_model->get("DOCUM_HEAD");
        $data['zam'] = $this->Admin_model->get("VIEW_ORDER");
        $data['dost'] = $this->Admin_model->get('VEND');
        $data['validation'] = $this->session->flashdata('alert');
        $this->twig->display('admin/product/order_products',$data);
    }
    public function zm()
    {
        $total_rows = $this->Admin_model->num_rows("DOCUM_HEAD");
        $start_index = ($this->uri->segment(4)) ? $this->uri->segment(4) : 0 ;
        $config['total_rows'] = $total_rows;
        $config['per_page'] = 4;
        $config['uri_segment'] = 4;
        $config['base_url'] = base_url('admin/order/zm');
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
        $data['views'] = $this->Admin_model->get("DOCUM_HEAD",$config['per_page'],$start_index);
        $this->pagination->initialize($config);
        $data['links'] = $this->pagination->create_links();
        $data['validation'] = $this->session->flashdata('alert');
        $this->twig->display('site/docs/list_order',$data);
    }
    public function show($id)
    {
        if(is_numeric($id)) {
            $where = array('nr_docum' => $id);
            $data['docum_item'] = $this->Admin_model->get_single("DOCUM_ITEM", $where);
            $data['validation'] = $this->session->flashdata('alert');
            $this->twig->display('admin/order/show', $data);
        }
        else
        {
            $this->session->set_flashdata('alert', "Podany dokument nie istnieje !");
            redirect(base_url("admin/order/view_order"));
        }
    }
    public function pdf()
    {
    }
}