<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Sessions extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->common->set_timezone();
        $login_type = $this->session->userdata('userType');
        if ($login_type != 'user') {
            redirect('login');
        }
        $get_user_token_details = $this->common->get_user_details($this->session->userdata('cid'));
        if($this->session->userdata('token') != $get_user_token_details->token){
           redirect('login');
        }
        $this->load->model('user/m_sessions', 'objsessions');
        $this->load->model('madmin/m_settings', 'm_settings');

    }

   public function index() {
        $data["all_sessions_week"] = $this->objsessions->getSessionsWeekData();

        if (!empty($data["all_sessions_week"])) {
            $today_date = "";
            foreach ($data["all_sessions_week"] as $val) {
                if (date("Y-m-d") == $val->sessions_date) {
                  $today_date = $val->sessions_date;  
                }
            }
            if($today_date == ""){
               $today_date = $data["all_sessions_week"][0]->sessions_date;
            }
            $data["all_sessions"] = $this->objsessions->getsessions_data($today_date);
        }
        $data['selected_date'] = $today_date;
        $data['sessions_tracks'] = $this->objsessions->get_sessions_tracks();

       $data["roundtable"]=  $this->db->query("select * from roundtable_setting where roundtable_setting_id='1'")->row();


        $iframe=$this->m_settings->getSessionIframe();
        $data['iframe']=$iframe;


        $this->load->view('header');
        $this->load->view('sessions', $data);
        $this->load->view('footer');
    }

    public function session_end(){
        $this->load->view('header');
        $this->load->view('end_session');
        $this->load->view('footer');
    }

    public function getsessions_data($date) {

        $data["all_sessions_week"] = $this->objsessions->getSessionsWeekData();
        $data["all_sessions"] = $this->objsessions->getsessions_data($date);
        $data["roundtable"]=  $this->db->query("select * from roundtable_setting where roundtable_setting_id='1'")->row();

        $data['sessions_tracks'] = $this->objsessions->get_sessions_tracks();
        $this->load->view('header');
        $this->load->view('sessions', $data);
        $this->load->view('footer');
    }

    public function filter_search() {
        $post = $this->input->post();
        $data["all_sessions_week"] = $this->objsessions->getSessionsWeekData();
        $data["all_sessions"] = $this->objsessions->getsessions_data_filter_search($post);
        $data['sessions_tracks'] = $this->objsessions->get_sessions_tracks();
        $this->load->view('header');
        $this->load->view('sessions', $data);
        $this->load->view('footer');
    }

    public function sunday() {
        $data["all_sessions"] = $this->objsessions->getSessionsSundayData();
        $this->load->view('header');
        $this->load->view('sessions', $data);
        $this->load->view('footer');
    }

    public function monday() {
        $data["all_sessions"] = $this->objsessions->getSessionsMondayData();

        $this->load->view('header');
        $this->load->view('sessions', $data);
        $this->load->view('footer');
    }

    public function view($sessions_id) {
        $sessions = $this->objsessions->viewSessionsData($sessions_id);

        if ($sessions->status == 0) {
            header("location:" . base_url() . "sessions/session_end");
            die();
        }


        $data["sessions"] = $sessions;
        $data["session_resource"] = $this->objsessions->get_session_resource($sessions_id);
        $data["roundtable"]=  $this->db->query("select * from roundtable_setting where roundtable_setting_id='1'")->row();


        $this->load->view('header');
        $this->load->view('view_sessions', $data);
        $this->load->view('footer');
    }

    public function get_poll_vot_section() {
        $result_data = $this->objsessions->get_poll_vot_section();
        if (!empty($result_data)) {
            $result_array = array("status" => "success", "result" => $result_data);
        } else {
            $result_array = array("status" => "error");
        }
        echo json_encode($result_array);
    }

    public function pollVoting() {
        $result_data = $this->objsessions->pollVoting();
        if (!empty($result_data)) {
            $result_array = array("status" => "success");
        } else {
            $result_array = array("status" => "error");
        }
        echo json_encode($result_array);
    }

    public function addQuestions() {
        $result_data = $this->objsessions->addQuestions();
        if (!empty($result_data)) {
            $result_array = array("status" => "success");
        } else {
            $result_array = array("status" => "error");
        }
        echo json_encode($result_array);
    }

    public function addBriefcase() {
        $result_data = $this->objsessions->addBriefcase();
        if (!empty($result_data)) {
            $result_array = array("status" => "success");
        } else {
            $result_array = array("status" => "error");
        }
        echo json_encode($result_array);
    }

    public function addresource() {
        $result_data = $this->objsessions->addresource();
        if (!empty($result_data)) {
            $result_array = array("status" => "success");
        } else {
            $result_array = array("status" => "error");
        }
        echo json_encode($result_array);
    }

    public function get_question_list() {
        $result_data = $this->objsessions->get_question_list();
        if (!empty($result_data)) {
            $result_array = array("status" => "success", "question_list" => $result_data);
        } else {
            $result_array = array("status" => "error");
        }
        echo json_encode($result_array);
    }

    public function get_poll_vot_section_close_poll() {
        $result_data = $this->objsessions->get_poll_vot_section_close_poll();
        if (!empty($result_data)) {
            $result_array = array("status" => "success", "result" => $result_data);
        } else {
            $result_array = array("status" => "error");
        }
        echo json_encode($result_array);
    }

    public function get_group_chat_section_status() {
        $result_data = $this->objsessions->get_group_chat_section_status();
        if (!empty($result_data)) {
            $result_array = array("status" => "success", "result" => $result_data);
        } else {
            $result_array = array("status" => "error");
        }
        echo json_encode($result_array);
    }

    public function send_message() {
        $post = $this->input->post();
        $result = $this->objsessions->send_message($post);
        if ($result) {
            $result = array("status" => "success");
        } else {
            $result = array("status" => "error");
        }
        echo json_encode($result);
    }

    public function message() {
        $post = $this->input->post();
        $data['messages'] = $this->objsessions->get_chat_details($post['sessions_id'], $post['sessions_group_chat_id']);
        $output = $this->load->view('message', $data, true);
        echo $output;
    }

    public function attend($sessions_id) {
        $data["sessions"] = $this->objsessions->viewSessionsData($sessions_id);
        $data["roundtable"]=  $this->db->query("select * from roundtable_setting where roundtable_setting_id='1'")->row();

        $this->load->view('header');
        $this->load->view('view_attend', $data);
        $this->load->view('footer');
    }

    public function gettimenow() {
        echo json_encode(array("current_time" => date("H:i:s")));
    }

    public function add_viewsessions_history_open() {
        $post = $this->input->post();
        $this->load->library('user_agent');
        $user_agent = $this->input->ip_address();
        $session_his_arr = array(
            'sessions_id' => $post['sessions_id'],
            'cust_id' => $this->session->userdata("cid"),
            'operating_system' => $this->agent->platform(),
            'computer_type' => $this->agent->browser(),
            'ip_address' => $this->input->ip_address(),
            'resolution' => $post['resolution'],
            'start_date_time' => date("Y-m-d h:i:s"),
            'status' => 0
        );
        $this->db->insert('view_sessions_history', $session_his_arr);
        $insert_id = $this->db->insert_id();
        echo json_encode(array("status" => "success", "view_sessions_history_id" => $insert_id));
    }

    public function update_viewsessions_history_open() {
        $post = $this->input->post();
        $session_his_arr = array(
            'end_date_time' => date("Y-m-d h:i:s")
        );
        $this->db->update('view_sessions_history', $session_his_arr, array("view_sessions_history_id" => $post['view_sessions_history_id']));
        echo json_encode(array("status" => "success"));
    }

    public function sign_up_sessions() {
        $post = $this->input->post();
        $sessions_result = $this->db->get_where("sessions", array('sessions_id' => $post['sessions_id']))->row();
        $this->db->select("s.sessions_id");
        $this->db->from("sign_up_sessions su");
        $this->db->join("sessions s", "su.sessions_id=s.sessions_id");
        $this->db->where('DATE_FORMAT(s.sessions_date, "%Y-%m-%d") =', date('Y-m-d', strtotime($sessions_result->sessions_date)));
        $this->db->where('s.time_slot <=', $sessions_result->time_slot);
        $this->db->where('s.end_time >=', $sessions_result->time_slot);
        $this->db->where("cust_id", $this->session->userdata("cid"));
        $join_sessions_result = $this->db->get();
        if ($join_sessions_result->num_rows() > 0) {
            $result_array = array("status" => "exist");
        } else {
            if ($post['sessions_id'] != "") {
                $session_his_arr = array(
                    'sessions_id' => $post['sessions_id'],
                    'cust_id' => $this->session->userdata("cid")
                );
                $result = $this->db->get_where("sign_up_sessions", $session_his_arr)->row();
                if (empty($result)) {
                    $this->db->insert('sign_up_sessions', $session_his_arr);
                }
                $result_array = array("status" => "success");
            } else {
                $result_array = array("status" => "error");
            }
        }
        echo json_encode($result_array);
    }

    public function save_to_swag_bag() {
        $result_data = $this->objsessions->save_to_swag_bag();
        if ($result_data == "save") {
            $result_array = array("status" => "save");
        } else if ($result_data == "remove") {
            $result_array = array("status" => "remove");
        } else {
            $result_array = array("status" => "error");
        }
        echo json_encode($result_array);
    }

    public function unregister_sessions() {
        $post = $this->input->post();
        $session_his_arr = array(
            'sessions_id' => $post['sessions_id'],
            'cust_id' => $this->session->userdata("cid")
        );
        $this->db->delete("sign_up_sessions", $session_his_arr);
        $result_array = array("status" => "success");
        echo json_encode($result_array);
    }

    public function waitHallControl(){
        $post = $this->input->post();

        $users=$post["users"];
        $result=  $this->db->query("select * from roundtable_setting where roundtable_setting_id='1'")->row();
        $roundTable=$result->roundtable;

        if($roundTable>$users)echo "success";
        else echo "failed";
    }
    public function waitHallButtonControl(){
        $post = $this->input->post();
        $datetime=$post["date"];
        $datetime = date("Y-m-d H:i", strtotime($datetime));
        $datetime = new DateTime($datetime);
        $datetime1 = new DateTime();
        $diff = $datetime->getTimestamp() - $datetime1->getTimestamp();
        if ($diff >= 60) {
            $diff = $diff - 60;
        } else {
            $diff = 0;
        }
        echo $diff;
    }

}
