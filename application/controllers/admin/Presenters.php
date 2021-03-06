<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Presenters extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->common->set_timezone();
        $login_type = $this->session->userdata('aname');
        if ($login_type != 'admin') {
            redirect('admin/alogin');
        }
        $this->load->model('madmin/m_presenters', 'mpresenters');
    }

    public function index() {
        $data['presenters'] = $this->mpresenters->get_presenters();
        $import_presenter_session = $this->session->userdata("session_presenter");
        if (!empty($import_presenter_session)) {
            $data['import_presenter_session'] = $this->session->userdata("session_presenter");
            $this->session->unset_userdata('session_presenter');
        }
        $this->load->view('admin/header');
        $this->load->view('admin/presenters', $data);
        $this->load->view('admin/footer');
    }

    public function add_presenters() {
        $post = $this->input->post();
        if (!empty($post)) {
            $res = $this->mpresenters->add_presenters($post);
            if ($res) {
                header('Location: ' . base_url() . 'admin/presenters?msg=S');
            } else {
                header('Location: ' . base_url() . 'admin/presenters?msg=E');
            }
        }
    }

    public function get_presenter_byid($pid) {
        $q = $this->db->get_where('presenter', array('presenter_id' => $pid));
        if ($q->num_rows() > 0) {
            $plan = $q->row();
            $data['msg'] = 'success';
            $data['data'] = $plan;
        } else {
            $data['msg'] = 'error';
            $data['data'] = 'Record not found please try again later!';
        }
        echo json_encode($data);
    }

    public function deletePresenter($pid) {
        $q = $this->db->get_where('sessions', array('presenter_id' => $pid));
        if ($q->num_rows() > 0) {
            header('Location: ' . base_url() . 'admin/presenters?msg=nd');
        } else {
            $this->db->delete('presenter', array('presenter_id' => $pid));
            header('Location: ' . base_url() . 'admin/presenters?msg=D');
        }
    }

    function import_presenter() {
        $result = $this->mpresenters->import_presenter();
        if ($result) {
            header('location:' . base_url() . 'admin/presenters');
        } else {
            header('location:' . base_url() . 'admin/presenters');
        }
    }

}
