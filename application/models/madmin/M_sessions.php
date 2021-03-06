<?php

class M_sessions extends CI_Model {

    function __construct() {
        parent::__construct();
    }

    function addBriefcase() {
        $post = $this->input->post();
        $resource_type = (isset($post['type'])) ? $post['type'] : 'note';
        $insert_array = array(
            'cust_id' => $this->session->userdata("aid"),
            'sessions_id' => $post['sessions_id'],
            'note' => $post['briefcase'],
            'resource_type' => $resource_type,
            'reg_briefcase_date' => date("Y-m-d")
        );
        $result_data = $this->db->get_where("sessions_cust_briefcase", array("cust_id" => $this->session->userdata("aid"), 'sessions_id' => $post['sessions_id']))->row();
        if (empty($result_data)) {
            $this->db->insert("sessions_cust_briefcase", $insert_array);
        } else {
            $this->db->update("sessions_cust_briefcase", $insert_array, array("sessions_cust_briefcase_id" => $result_data->sessions_cust_briefcase_id));
        }
        return TRUE;
    }
    function getSessionsAll() {
        $this->db->select('*');
        $this->db->from('sessions s');
		 ($this->session->userdata('start_date') != "") ? $where['DATE(s.sessions_date) >='] = date('Y-m-d', strtotime($this->session->userdata('start_date'))) : '';
        ($this->session->userdata('end_date') != "") ? $where['DATE(s.sessions_date) <='] = date('Y-m-d', strtotime($this->session->userdata('end_date'))) : '';
        if (!empty($where)) {
            $this->db->where($where);
        }
        $this->db->order_by("s.sessions_date", "asc");
        $this->db->order_by("s.time_slot", "asc");
        $sessions = $this->db->get();
        if ($sessions->num_rows() > 0) {
            $return_array = array();
            foreach ($sessions->result() as $val) {
                $val->presenter = $this->common->get_presenter($val->presenter_id, $val->sessions_id);
                $val->session_type_details = $this->common->get_session_type($val->sessions_type_id);
                $val->total_sign_up_sessions = $this->common->get_total_sign_up_sessions($val->sessions_id);
                $val->sissions_limit = $this->common->get_roundtable_setting()->roundtable;
                $return_array[] = $val;
            }
            return $return_array;
        } else {
            return '';
        }
    }

    function getSessionsFilter() {
        $this->db->select('*');
        $this->db->from('sessions s');

        $post = $this->input->post();
        $session_filter = array(
            'start_date' => date('Y-m-d', strtotime($post['start_date'])),
            'end_date' => date('Y-m-d', strtotime($post['end_date']))
        );
        $this->session->set_userdata($session_filter);
		
        ($post['session_type'] != "") ? $where['s.sessions_type_id ='] = trim($post['session_type']) : '';

        ($post['start_date'] != "") ? $where['DATE(s.sessions_date) >='] = date('Y-m-d', strtotime($post['start_date'])) : '';

        ($post['end_date'] != "") ? $where['DATE(s.sessions_date) <='] = date('Y-m-d', strtotime($post['end_date'])) : '';

        if (!empty($where)) {
            $this->db->where($where);
        }

        $this->db->order_by("s.sessions_date", "asc");
        $this->db->order_by("s.time_slot", "asc");
        $sessions = $this->db->get();
        if ($sessions->num_rows() > 0) {
            $return_array = array();
            foreach ($sessions->result() as $val) {
                $val->presenter = $this->common->get_presenter($val->presenter_id, $val->sessions_id);
                $val->session_type_details = $this->common->get_session_type($val->sessions_type_id);
                $val->total_sign_up_sessions = $this->common->get_total_sign_up_sessions($val->sessions_id);
                $val->sissions_limit = $this->common->get_roundtable_setting()->roundtable;
                $return_array[] = $val;
            }
            return $return_array;
        } else {
            return '';
        }
    }

    function getPresenterDetails() {
        $this->db->select('*');
        $this->db->from('presenter');
		$this->db->order_by("last_name", "asc");
        $presenter = $this->db->get();
        if ($presenter->num_rows() > 0) {
            return $presenter->result();
        } else {
            return '';
        }
    }

    function edit_sessions($sessions_id) {
        $this->db->select('*');
        $this->db->from('sessions');
        $this->db->where("sessions_id", $sessions_id);
        $sessions = $this->db->get();
        if ($sessions->num_rows() > 0) {
            $result_sessions = $sessions->row();
            $result_sessions->sessions_presenter = $this->common->get_session_presenter($sessions_id);
            return $result_sessions;
        } else {
            return '';
        }
    }
    function endSession() {
        $post = $this->input->post();
        $sessionId=$post["sesionId"];
        $this->db->update('sessions', array('status' => 0), array('sessions_id' => $sessionId));

        return "success";
    }

    function openSession() {
        $post = $this->input->post();
        $sessionId=$post["sesionId"];
        $this->db->update('sessions', array('status' => 1), array('sessions_id' => $sessionId));

        return "success";
    }
	
	function undoSession() {
        $post = $this->input->post();
        $sessionId=$post["sesionId"];
        $this->db->update('sessions', array('status' => 1), array('sessions_id' => $sessionId));

        return "success";
    }


    function createSessions() {
        $post = $this->input->post();

        if (isset($post['sessions_type'])) {
            $sessions_type_id = $post['sessions_type'];
        } else {
            $sessions_type_id = "";
        }

        if (!empty($post['sessions_tracks'])) {
            $sessions_tracks_id = implode(",", $post['sessions_tracks']);
        } else {
            $sessions_tracks_id = "";
        }

        if (!empty($post['moderator_id'])) {
            $moderator_id = implode(",", $post['moderator_id']);
        } else {
            $moderator_id = "";
        }


        $set = array(
            'presenter_id' => (isset($post['select_presenter_id'])) ? implode(",", $post['select_presenter_id']) : '',
            'moderator_id' => $moderator_id,
            'session_title' => trim($post['session_title']),
            'sessions_description' => trim($post['sessions_description']),
            'zoom_link' => trim($post['zoom_link']),
            'zoom_password' => trim($post['zoom_password']),
            'sessions_date' => date("Y-m-d", strtotime($post['sessions_date'])),
            'time_slot' => date("H:i", strtotime($post['time_slot'])),
            'end_time' => date("H:i", strtotime($post['end_time'])),
            'embed_html_code' => trim($post['embed_html_code']),
            'embed_html_code_presenter' => trim($post['embed_html_code_presenter']),
            'sessions_type_id' => $sessions_type_id,
            'sessions_tracks_id' => $sessions_tracks_id,
            'sessions_type_status' => trim($post['sessions_type_status']),
			'tool_box_status' => 1,
            'sessions_visibility' => (isset($post['sessions_visibility'])) ? $post['sessions_visibility'] : '',
            "reg_date" => date("Y-m-d h:i")
        );
        $this->db->insert("sessions", $set);
        $sessions_id = $this->db->insert_id();
        if ($sessions_id > 0) {
            if ($_FILES['sessions_photo']['name'] != "") {
                $_FILES['sessions_photo']['name'] = $_FILES['sessions_photo']['name'];
                $_FILES['sessions_photo']['type'] = $_FILES['sessions_photo']['type'];
                $_FILES['sessions_photo']['tmp_name'] = $_FILES['sessions_photo']['tmp_name'];
                $_FILES['sessions_photo']['error'] = $_FILES['sessions_photo']['error'];
                $_FILES['sessions_photo']['size'] = $_FILES['sessions_photo']['size'];
                $this->load->library('upload');
                $this->upload->initialize($this->set_upload_options());
                $this->upload->do_upload('sessions_photo');
                $file_upload_name = $this->upload->data();
                $this->db->update('sessions', array('sessions_photo' => $file_upload_name['file_name']), array('sessions_id' => $sessions_id));
            }

            if ($_FILES['sponsor_log']['name'] != "") {
                $_FILES['sponsor_log']['name'] = $_FILES['sponsor_log']['name'];
                $_FILES['sponsor_log']['type'] = $_FILES['sponsor_log']['type'];
                $_FILES['sponsor_log']['tmp_name'] = $_FILES['sponsor_log']['tmp_name'];
                $_FILES['sponsor_log']['error'] = $_FILES['sponsor_log']['error'];
                $_FILES['sponsor_log']['size'] = $_FILES['sponsor_log']['size'];
                $this->load->library('upload');
                $this->upload->initialize($this->set_upload_options_sponsor());
                $this->upload->do_upload('sponsor_log');
                $file_upload_name = $this->upload->data();
                $this->db->update('sessions', array('sponsor_log' => $file_upload_name['file_name']), array('sessions_id' => $sessions_id));
            }

            if (isset($post['select_presenter_id']) && !empty($post['select_presenter_id'])) {
                $array_size_limit = sizeof($post['select_presenter_id']);
                $file_upload_name = array();
                $files = $_FILES;
                $this->load->library('upload');
                for ($i = 0; $i < $array_size_limit; $i++) {
                    $set_array = array(
                        'sessions_id' => $sessions_id,
                        'order_index_no' => $post['order_no'][$i],
                        'select_presenter_id' => $post['select_presenter_id'][$i],
                        'presenter_title' => $post['presenter_title'][$i],
                        'presenter_time_slot' => $post['presenter_time_slot'][$i],
                        'presenter_resource_link' => $post['presenter_resource_link'][$i],
                        'upload_published_name' => $post['upload_published_name'][$i],
                        'link_published_name' => $post['link_published_name'][$i]
                    );
                    $this->db->insert("sessions_add_presenter", $set_array);
                    $sessions_add_presenter_id = $this->db->insert_id();
                    if ($sessions_add_presenter_id > 0) {
                        if ($_FILES['presenter_resource']['name'][$i] != "") {
                            $_FILES['presenter_resource']['name'] = $files['presenter_resource']['name'][$i];
                            $_FILES['presenter_resource']['type'] = $files['presenter_resource']['type'][$i];
                            $_FILES['presenter_resource']['tmp_name'] = $files['presenter_resource']['tmp_name'][$i];
                            $_FILES['presenter_resource']['error'] = $files['presenter_resource']['error'][$i];
                            $_FILES['presenter_resource']['size'] = $files['presenter_resource']['size'][$i];
                            $this->upload->initialize($this->set_upload_presenter_resource());
                            $this->upload->do_upload('presenter_resource');
                            $file_upload_name = $this->upload->data();
                            $this->db->update('sessions_add_presenter', array('presenter_resource' => $file_upload_name['file_name']), array('sessions_add_presenter_id' => $sessions_add_presenter_id));
                        }
                    }
                }
            }
            return "1";
        } else {
            return "2";
        }
    }

    function set_upload_presenter_resource() {
        $this->load->helper('string');
        $randname = random_string('numeric', '8');
        $config = array();
        $config['upload_path'] = './uploads/presenter_resource/';
        $config['allowed_types'] = '*';
        $config['overwrite'] = FALSE;
        $config['file_name'] = "presenter_resource_" . $randname;
        return $config;
    }

    function set_upload_options() {
        $this->load->helper('string');
        $randname = random_string('numeric', '8');
        $config = array();
        $config['upload_path'] = './uploads/sessions/';
        $config['allowed_types'] = 'jpg|png';
        $config['overwrite'] = FALSE;
        $config['file_name'] = "sessions_" . $randname;
        return $config;
    }

    function set_upload_options_sponsor() {
        $this->load->helper('string');
        $randname = random_string('numeric', '8');
        $config = array();
        $config['upload_path'] = './uploads/sponsor_log/';
        $config['allowed_types'] = 'jpg|png';
        $config['overwrite'] = FALSE;
        $config['file_name'] = "sponsor_log_" . $randname;
        return $config;
    }

    function generateRandomString($length = 8) {
        $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, strlen($characters) - 1)];
        }
        return $randomString;
    }

    function updateSessions() {
        $post = $this->input->post();



        if (isset($post['sessions_type'])) {
            $sessions_type_id = $post['sessions_type'];
        } else {
            $sessions_type_id = "";
        }
        if (!empty($post['sessions_tracks'])) {
            $sessions_tracks_id = implode(",", $post['sessions_tracks']);
        } else {
            $sessions_tracks_id = "";
        }

        if (!empty($post['moderator_id'])) {
            $moderator_id = implode(",", $post['moderator_id']);
        } else {
            $moderator_id = "";
        }


        $set = array(
            'presenter_id' => (isset($post['select_presenter_id'])) ? implode(",", $post['select_presenter_id']) : '',
            'moderator_id' => $moderator_id,
            'session_title' => trim($post['session_title']),
            'sessions_description' => trim($post['sessions_description']),
            'zoom_link' => trim($post['zoom_link']),
            'zoom_password' => trim($post['zoom_password']),
            'sessions_date' => date("Y-m-d", strtotime($post['sessions_date'])),
            'time_slot' => date("H:i", strtotime($post['time_slot'])),
            'end_time' => date("H:i", strtotime($post['end_time'])),
            'embed_html_code' => trim($post['embed_html_code']),
            'embed_html_code_presenter' => trim($post['embed_html_code_presenter']),
            'sessions_type_id' => $sessions_type_id,
            'sessions_tracks_id' => $sessions_tracks_id,
            'sessions_type_status' => trim($post['sessions_type_status']),
			'tool_box_status' => (isset($post['tool_box_status'])) ? $post['tool_box_status'] : 1,
            'sessions_visibility' => (isset($post['sessions_visibility'])) ? $post['sessions_visibility'] : '',
        );
        $this->db->update("sessions", $set, array("sessions_id" => $post['sessions_id']));
        $sessions_id = $post['sessions_id'];
        if ($sessions_id > 0) {
            if ($_FILES['sessions_photo']['name'] != "") {
                $_FILES['sessions_photo']['name'] = $_FILES['sessions_photo']['name'];
                $_FILES['sessions_photo']['type'] = $_FILES['sessions_photo']['type'];
                $_FILES['sessions_photo']['tmp_name'] = $_FILES['sessions_photo']['tmp_name'];
                $_FILES['sessions_photo']['error'] = $_FILES['sessions_photo']['error'];
                $_FILES['sessions_photo']['size'] = $_FILES['sessions_photo']['size'];
                $this->load->library('upload');
                $this->upload->initialize($this->set_upload_options());
                $this->upload->do_upload('sessions_photo');
                $file_upload_name = $this->upload->data();
                $this->db->update('sessions', array('sessions_photo' => $file_upload_name['file_name']), array('sessions_id' => $sessions_id));
            }

            if ($_FILES['sponsor_log']['name'] != "") {
                $_FILES['sponsor_log']['name'] = $_FILES['sponsor_log']['name'];
                $_FILES['sponsor_log']['type'] = $_FILES['sponsor_log']['type'];
                $_FILES['sponsor_log']['tmp_name'] = $_FILES['sponsor_log']['tmp_name'];
                $_FILES['sponsor_log']['error'] = $_FILES['sponsor_log']['error'];
                $_FILES['sponsor_log']['size'] = $_FILES['sponsor_log']['size'];
                $this->load->library('upload');
                $this->upload->initialize($this->set_upload_options_sponsor());
                $this->upload->do_upload('sponsor_log');
                $file_upload_name = $this->upload->data();
                $this->db->update('sessions', array('sponsor_log' => $file_upload_name['file_name']), array('sessions_id' => $sessions_id));
            }

            if (isset($post['select_presenter_id']) && !empty($post['select_presenter_id'])) {
                $array_size_limit = sizeof($post['select_presenter_id']);
                $file_upload_name = array();
                $files = $_FILES;
                $this->load->library('upload');
                for ($i = 0; $i < $array_size_limit; $i++) {
                    if ($post['status'][$i] == "update") {
                        $set_array = array(
                            'order_index_no' => $post['order_no'][$i],
                            'select_presenter_id' => $post['select_presenter_id'][$i],
                            'presenter_title' => $post['presenter_title'][$i],
                            'presenter_time_slot' => $post['presenter_time_slot'][$i],
                            'presenter_resource_link' => $post['presenter_resource_link'][$i],
                            'upload_published_name' => $post['upload_published_name'][$i],
                            'link_published_name' => $post['link_published_name'][$i]
                        );
                        $this->db->update("sessions_add_presenter", $set_array, array("sessions_add_presenter_id" => $post['sessions_add_presenter_id'][$i]));
                        $sessions_add_presenter_id = $post['sessions_add_presenter_id'][$i];
                    } else {
                        $set_array = array(
                            'sessions_id' => $sessions_id,
                            'order_index_no' => $post['order_no'][$i],
                            'select_presenter_id' => $post['select_presenter_id'][$i],
                            'presenter_title' => $post['presenter_title'][$i],
                            'presenter_time_slot' => $post['presenter_time_slot'][$i],
                            'presenter_resource_link' => $post['presenter_resource_link'][$i],
                            'upload_published_name' => $post['upload_published_name'][$i],
                            'link_published_name' => $post['link_published_name'][$i]
                        );
                        $this->db->insert("sessions_add_presenter", $set_array);
                        $sessions_add_presenter_id = $this->db->insert_id();
                    }
                    if ($sessions_add_presenter_id > 0) {

                        if ($_FILES['presenter_resource']['name'][$i] != "") {
                            $_FILES['presenter_resource']['name'] = $files['presenter_resource']['name'][$i];
                            $_FILES['presenter_resource']['type'] = $files['presenter_resource']['type'][$i];
                            $_FILES['presenter_resource']['tmp_name'] = $files['presenter_resource']['tmp_name'][$i];
                            $_FILES['presenter_resource']['error'] = $files['presenter_resource']['error'][$i];
                            $_FILES['presenter_resource']['size'] = $files['presenter_resource']['size'][$i];
                            $this->upload->initialize($this->set_upload_presenter_resource());
                            $this->upload->do_upload('presenter_resource');
                            $file_upload_name = $this->upload->data();
                            $this->db->update('sessions_add_presenter', array('presenter_resource' => $file_upload_name['file_name']), array('sessions_add_presenter_id' => $sessions_add_presenter_id));
                        }
                    }
                }
            }
            return "1";
        } else {
            return "2";
        }
    }

    function delete_sessions() {
        $post = $this->input->post();
        $sessions_id = $post["sesionId"];
        $this->db->delete("sessions", array("sessions_id" => $sessions_id));
        $this->db->delete("sessions_poll_question", array("sessions_id" => $sessions_id));
        $this->db->delete("poll_question_option", array("sessions_id" => $sessions_id));
        return "success";
    }

    public function alldelete($ids) {
        foreach ($ids as $id) {
            $did = intval($id) . '<br>';
            $this->db->delete("sessions", array("sessions_id" => $did));
            $this->db->delete("sessions_poll_question", array("sessions_id" => $did));
            $this->db->delete("poll_question_option", array("sessions_id" => $did));
        }
        if ($this->db->affected_rows()) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    function add_poll_data() {
        $post = $this->input->post();
        $set = array(
            'sessions_id' => trim($post['sessions_id']),
            'poll_type_id' => $post['poll_type_id'],
            'question' => trim($post['question']),
			'slide_number' => trim($post['slide_number']),
            'poll_comparisons_id' => 0,
            "create_poll_date" => date("Y-m-d h:i")
        );
        $this->db->insert("sessions_poll_question", $set);
        $insert_id = $this->db->insert_id();
        if ($insert_id > 0) {
            for ($i = 1; $i <= 10; $i++) {
                if ($post['option_' . $i] != "") {
                    $set_array = array(
                        'sessions_poll_question_id' => $insert_id,
                        'sessions_id' => trim($post['sessions_id']),
                        'option' => $post['option_' . $i],
                        "total_vot" => 0
                    );
                    $this->db->insert("poll_question_option", $set_array);
                }
            }
        }
        if ($post['poll_comparisons_with_us'] != "") {
            $this->add_poll_comparisons($post, $insert_id);
        }
        return TRUE;
    }

    function add_poll_comparisons($post, $insert_id) {
        $set = array(
            'sessions_id' => trim($post['sessions_id']),
            'poll_type_id' => $post['poll_comparisons_with_us'],
            'question' => trim($post['question']),
			'slide_number' => trim($post['slide_number']),
            'poll_comparisons_id' => $insert_id,
            "create_poll_date" => date("Y-m-d h:i")
        );
        $this->db->insert("sessions_poll_question", $set);
        $insert_new_id = $this->db->insert_id();
        if ($insert_new_id > 0) {
            $this->db->update("sessions_poll_question", array("poll_comparisons_id" => $insert_new_id), array("sessions_poll_question_id" => $insert_id));
            for ($i = 1; $i <= 10; $i++) {
                if ($post['option_' . $i] != "") {
                    $set_array = array(
                        'sessions_poll_question_id' => $insert_new_id,
                        'sessions_id' => trim($post['sessions_id']),
                        'option' => $post['option_' . $i],
                        "total_vot" => 0
                    );
                    $this->db->insert("poll_question_option", $set_array);
                }
            }
        }
    }

    function get_poll_details($sessions_id) {
        $this->db->select('*');
        $this->db->from('sessions_poll_question s');
        $this->db->join('poll_type p', 's.poll_type_id=p.poll_type_id');
        $this->db->where("s.sessions_id", $sessions_id);
        $poll_question = $this->db->get();
        if ($poll_question->num_rows() > 0) {
            $poll_question_array = array();
            foreach ($poll_question->result() as $val) {
                $val->option = $this->db->get_where("poll_question_option", array("sessions_poll_question_id" => $val->sessions_poll_question_id))->result();
                $poll_question_array[] = $val;
            }
            return $poll_question_array;
        } else {
            return '';
        }
    }

    function deletePollQuestion($sessions_poll_question_id) {
        $this->db->delete("sessions_poll_question", array("sessions_poll_question_id" => $sessions_poll_question_id));
        $this->db->delete("poll_question_option", array("sessions_poll_question_id" => $sessions_poll_question_id));
        return TRUE;
    }

    function editPollQuestion($sessions_poll_question_id) {
        $this->db->select('*');
        $this->db->from('sessions_poll_question');
        $this->db->where("sessions_poll_question_id", $sessions_poll_question_id);
        $poll_question = $this->db->get();
        if ($poll_question->num_rows() > 0) {
            $poll_question_array = $poll_question->row();
            $poll_question_array->option = $this->db->get_where("poll_question_option", array("sessions_poll_question_id" => $poll_question_array->sessions_poll_question_id))->result();
            return $poll_question_array;
        } else {
            return '';
        }
    }

    function update_poll_data() {
        $post = $this->input->post();
        $set = array(
            'question' => trim($post['question']),
			'slide_number' => trim($post['slide_number']),
            'poll_type_id' => $post['poll_type_id']
        );
        $this->db->update("sessions_poll_question", $set, array("sessions_poll_question_id" => $post['sessions_poll_question_id']));
        $option_result = $this->db->get_where("poll_question_option", array("sessions_poll_question_id" => $post['sessions_poll_question_id']))->result();
        if (isset($option_result) && !empty($option_result)) {
            foreach ($option_result as $key => $val) {
                $key++;
                if ($post['option_' . $key] != "") {
                    $set_array = array(
                        'option' => $post['option_' . $key],
                    );
                    $this->db->update("poll_question_option", $set_array, array("poll_question_option_id" => $val->poll_question_option_id));
                } else {
                    $this->db->delete("poll_question_option", array("poll_question_option_id" => $val->poll_question_option_id));
                }
            }

            $total = 10;
            $edit = sizeof($option_result);
            $edit = $edit + 1;
            for ($i = $edit; $i <= $total; $i++) {
                if ($post['option_' . $i] != "") {
                    $set_array = array(
                        'option' => $post['option_' . $i],
                    );
                    $set_array_int = array(
                        'sessions_poll_question_id' => $post['sessions_poll_question_id'],
                        'sessions_id' => trim($post['sessions_id']),
                        'option' => $post['option_' . $i],
                        "total_vot" => 0
                    );
                    $this->db->insert("poll_question_option", $set_array_int);
                }
            }
        }
        return TRUE;
    }

    function get_question_list() {
        $post = $this->input->post();
        $this->db->select('*');
        $this->db->from('sessions_cust_question s');
        $this->db->join('customer_master c', 's.cust_id=c.cust_id');
        $this->db->where(array("s.sessions_id" => $post['sessions_id'], 'sessions_cust_question_id >' => $post['list_last_id'], 'hide_status' => 0));
        //  $this->db->order_by("s.sessions_cust_question_id", "DESC");
        $result = $this->db->get();
        if ($result->num_rows() > 0) {
            return $result->result();
        } else {
            return '';
        }
    }

    function addQuestionAnswer() {
        $post = $this->input->post();
        $set = array(
            'answer' => trim($post['q_answer']),
            'answer_by_id' => $this->session->userdata("aid"),
            "answer_status" => 1
        );
        $this->db->update("sessions_cust_question", $set, array("sessions_cust_question_id" => $post['sessions_cust_question_id']));
        return TRUE;
    }

    function getSessionsReportData($sessions_id) {
        $post = $this->input->post();
        $this->db->select('*');
        $this->db->from('sessions_cust_question s');
        $this->db->join('sessions se', 'se.sessions_id=s.sessions_id');
        $this->db->join('presenter p', 'p.presenter_id=se.presenter_id');
        $this->db->join('customer_master c', 'c.cust_id=s.cust_id');
        $this->db->where(array("s.sessions_id" => $sessions_id));
        $result = $this->db->get();
        if ($result->num_rows() > 0) {
            return $result->result();
        } else {
            return '';
        }
    }

    function get_poll_type() {
        $this->db->select('*');
        $this->db->from('poll_type');
        $poll_type = $this->db->get();
        if ($poll_type->num_rows() > 0) {
            return $poll_type->result();
        } else {
            return '';
        }
    }

    function view_result($sessions_poll_question_id) {
        $this->db->select('*');
        $this->db->from('sessions_poll_question s');
        $this->db->join('poll_type p', 's.poll_type_id=p.poll_type_id');
        $this->db->where(array("s.sessions_poll_question_id" => $sessions_poll_question_id));
        $result = $this->db->get();
        if ($result->num_rows() > 0) {
            $poll_question_array = $result->row();
            if ($poll_question_array->poll_comparisons_id == 0) {
                $poll_question_array->option = $this->db->get_where("poll_question_option", array("sessions_poll_question_id" => $poll_question_array->sessions_poll_question_id))->result();
                $poll_question_array->max_value = $this->get_maxvalue_option($poll_question_array->sessions_poll_question_id);
                return $poll_question_array;
            } else {
                $result_compar_poll = $this->db->get_where("sessions_poll_question", array("sessions_poll_question_id" => $poll_question_array->poll_comparisons_id))->row();
                if ($result_compar_poll->status == 0) {
                    $poll_question_array->option = $this->db->get_where("poll_question_option", array("sessions_poll_question_id" => $poll_question_array->sessions_poll_question_id))->result();
                    $poll_question_array->max_value = $this->get_maxvalue_option($poll_question_array->sessions_poll_question_id);
                    return $poll_question_array;
                } else {
                    $poll_question_array->option = $this->getOption($poll_question_array->sessions_poll_question_id, $poll_question_array->poll_comparisons_id);
                    $poll_question_array->max_value = $this->get_maxvalue_option($poll_question_array->sessions_poll_question_id);
                    if (!empty($result_compar_poll)) {
                        //  $poll_question_array->compere_poll_type = $this->db->get_where("poll_type", array("poll_type_id" => $result_compar_poll->poll_type_id))->row()->poll_type;
                        //  $poll_question_array->compere_option = $this->db->get_where("poll_question_option", array("sessions_poll_question_id" => $result_compar_poll->sessions_poll_question_id))->result();
                        $poll_question_array->compere_max_value = $this->get_maxvalue_option($result_compar_poll->sessions_poll_question_id);
                    }
                    return $poll_question_array;
                }
            }
        } else {
            return '';
        }
    }

    function getOption($sessions_poll_question_id, $poll_comparisons_id) {

        $this->db->select('*');
        $this->db->from('poll_question_option');
        $this->db->where("sessions_poll_question_id", $sessions_poll_question_id);
        $result = $this->db->get();
        if ($result->num_rows() > 0) {
            $result_compar_poll = $this->db->get_where("sessions_poll_question", array("sessions_poll_question_id" => $poll_comparisons_id))->row();
            $result_array = array();
            foreach ($result->result() as $val) {
                $val->compere_option = $this->db->get_where("poll_question_option", array("sessions_poll_question_id" => $result_compar_poll->sessions_poll_question_id, "option" => $val->option))->row()->total_vot;
                $result_array[] = $val;
            }
            return $result_array;
        } else {
            return '';
        }
    }

    function view_session($sessions_id) {
        $this->db->select('*');
        $this->db->from('sessions s');
        $this->db->where("sessions_id", $sessions_id);
        $sessions = $this->db->get();
        if ($sessions->num_rows() > 0) {
            $result_sessions = $sessions->row();
            $result_sessions->presenter = $this->common->get_presenter($result_sessions->presenter_id, $result_sessions->sessions_id);
            return $result_sessions;
        } else {
            return '';
        }
    }

    function get_poll_vot_section() {
        $post = $this->input->post();
        $this->db->select('*');
        $this->db->from('sessions_poll_question');
        $this->db->where(array("sessions_id" => $post['sessions_id']));
        $where = '(status = 1 or status = 2)';
        $this->db->where($where);
        $result = $this->db->get();
        if ($result->num_rows() > 0) {
            $poll_question_array = $result->row();
            if ($poll_question_array->status == 1) {
                $poll_question_array->poll_status = 1;
                $poll_question_array->exist_status = 1;
                $poll_question_array->select_option_id = 0;
                $poll_question_array->option = $this->db->get_where("poll_question_option", array("sessions_poll_question_id" => $poll_question_array->sessions_poll_question_id))->result();
                return $poll_question_array;
            } else if ($poll_question_array->status == 2) {
                if ($poll_question_array->poll_comparisons_id == 0) {
                    $poll_question_array->poll_status = 2;
                    $poll_question_array->option = $this->db->get_where("poll_question_option", array("sessions_poll_question_id" => $poll_question_array->sessions_poll_question_id))->result();
                    $poll_question_array->max_value = $this->get_maxvalue_option($poll_question_array->sessions_poll_question_id);
                    return $poll_question_array;
                } else {
                    $result_compar_poll = $this->db->get_where("sessions_poll_question", array("sessions_poll_question_id" => $poll_question_array->poll_comparisons_id))->row();
                    if ($result_compar_poll->status == 0) {
                        $poll_question_array->poll_status = 2;
                        $poll_question_array->option = $this->db->get_where("poll_question_option", array("sessions_poll_question_id" => $poll_question_array->sessions_poll_question_id))->result();
                        $poll_question_array->max_value = $this->get_maxvalue_option($poll_question_array->sessions_poll_question_id);
                        return $poll_question_array;
                    } else {
                        $poll_question_array->option = $this->getOption($poll_question_array->sessions_poll_question_id, $poll_question_array->poll_comparisons_id);
                        $poll_question_array->poll_status = 2;
                        $poll_question_array->max_value = $this->get_maxvalue_option($poll_question_array->sessions_poll_question_id);
                        if (!empty($result_compar_poll)) {
                            //  $poll_question_array->compere_poll_type = $this->db->get_where("poll_type", array("poll_type_id" => $result_compar_poll->poll_type_id))->row()->poll_type;
                            //  $poll_question_array->compere_option = $this->db->get_where("poll_question_option", array("sessions_poll_question_id" => $result_compar_poll->sessions_poll_question_id))->result();
                            $poll_question_array->compere_max_value = $this->get_maxvalue_option($result_compar_poll->sessions_poll_question_id);
                        }
                        return $poll_question_array;
                    }
                }
            }
        } else {
            return '';
        }
    }

    function get_maxvalue_option($sessions_poll_question_id) {
        $this->db->select('MAX(total_vot) as max_total_vot');
        $this->db->from('poll_question_option');
        $this->db->where("sessions_poll_question_id", $sessions_poll_question_id);
        $sessions = $this->db->get();
        if ($sessions->num_rows() > 0) {
            return $sessions->row()->max_total_vot;
        } else {
            return '';
        }
    }

    function get_poll_vot_section_close_poll() {
        $post = $this->input->post();
        $this->db->select('*');
        $this->db->from('sessions_poll_question');
        $this->db->where(array("sessions_id" => $post['sessions_id']));
        $where = '(status = 4)';
        $this->db->where($where);
        $result = $this->db->get();
        if ($result->num_rows() > 0) {
            $poll_question_array = $result->row();
            if ($poll_question_array->status == 1) {
                $poll_question_array->poll_status = 1;
                $poll_question_array->exist_status = 1;
                $poll_question_array->select_option_id = 0;
                $poll_question_array->option = $this->db->get_where("poll_question_option", array("sessions_poll_question_id" => $poll_question_array->sessions_poll_question_id))->result();
                return $poll_question_array;
            } else if ($poll_question_array->status == 2) {
                $poll_question_array->poll_status = 2;
                $poll_question_array->option = $this->db->get_where("poll_question_option", array("sessions_poll_question_id" => $poll_question_array->sessions_poll_question_id))->result();
                return $poll_question_array;
            } else if ($poll_question_array->status == 4) {
                $poll_question_array->poll_status = 4;
                $poll_question_array->option = $this->db->get_where("poll_question_option", array("sessions_poll_question_id" => $poll_question_array->sessions_poll_question_id))->result();
                return $poll_question_array;
            }
        } else {
            return '';
        }
    }

    function get_favorite_question_list() {
        $post = $this->input->post();
        $this->db->select('*');
        $this->db->from('tbl_favorite_question_admin fq');
        $this->db->join('sessions_cust_question s', 's.sessions_cust_question_id = fq.sessions_cust_question_id');
        $this->db->where(array("fq.sessions_id" => $post['sessions_id'], 'fq.cust_id' => $this->session->userdata("aid"), 'fq.tbl_favorite_question_admin_id >' => $post['list_last_id'], 'fq.hide_status' => 0));
        $result = $this->db->get();
        if ($result->num_rows() > 0) {
            return $result->result();
        } else {
            return '';
        }
    }

    function likeQuestion() {
        $post = $this->input->post();
        $insert_array = array(
            'cust_id' => $this->session->userdata("aid"),
            'sessions_id' => $post['sessions_id'],
            'sessions_cust_question_id' => $post['sessions_cust_question_id']
        );
        $favorite_question_row = $this->db->get_where('tbl_favorite_question_admin', $insert_array)->row();
        if (!empty($favorite_question_row)) {
            $this->db->delete("tbl_favorite_question_admin", $insert_array);
        } else {
            $this->db->insert("tbl_favorite_question_admin", $insert_array);
        }
        return TRUE;
    }

    function get_resource($sessions_id) {
        $this->db->select('*');
        $this->db->from('session_resource');
        $this->db->where('sessions_id', $sessions_id);
        $session_resource = $this->db->get();
        if ($session_resource->num_rows() > 0) {
            return $session_resource->result();
        } else {
            return '';
        }
    }

    function add_resource($post) {
        $data = array(
            'link_published_name' => $post['link_published_name'],
            'resource_link' => $post['resource_link'],
            'upload_published_name' => $post['upload_published_name'],
            'sessions_id' => $post['sessions_id']
        );
        $this->db->insert('session_resource', $data);
        $id = $this->db->insert_id();
        if ($id > 0) {
            if ($_FILES['resource_file']['name'] != "") {
                $_FILES['resource_file']['name'] = $_FILES['resource_file']['name'];
                $_FILES['resource_file']['type'] = $_FILES['resource_file']['type'];
                $_FILES['resource_file']['tmp_name'] = $_FILES['resource_file']['tmp_name'];
                $_FILES['resource_file']['error'] = $_FILES['resource_file']['error'];
                $_FILES['resource_file']['size'] = $_FILES['resource_file']['size'];
                $this->load->library('upload');
                $this->upload->initialize($this->set_upload_options_resource());
                $this->upload->do_upload('resource_file');
                $file_upload_name = $this->upload->data();
                $this->db->update('session_resource', array('resource_file' => $file_upload_name['file_name']), array('session_resource_id' => $id));
            }
            return TRUE;
        } else {
            return FALSE;
        }
    }

    function set_upload_options_resource() {
        $this->load->helper('string');
        $randname = random_string('numeric', '8');
        $config = array();
        $config['upload_path'] = './uploads/resource_sessions/';
        $config['allowed_types'] = '*';
        $config['overwrite'] = FALSE;
        $config['file_name'] = "resource_sessions_" . $randname;
        return $config;
    }

    function get_session_resource($sessions_id) {
        $this->db->select('*');
        $this->db->from('session_resource');
        $this->db->where('sessions_id', $sessions_id);
        $session_resource = $this->db->get();
        if ($session_resource->num_rows() > 0) {
            return $session_resource->result();
        } else {
            return '';
        }
    }

    function importSessionsPoll() {
        $this->load->library('csvimport');
        if ($_FILES['sessions_poll_file']['error'] != 4) {
            $pathMain = FCPATH . "/uploads/csv/";
            $filename = $this->generateRandomString() . '_' . $_FILES['sessions_poll_file']['name'];
            $result = $this->common->do_upload('sessions_poll_file', $pathMain, $filename);
            $file_path = $result['upload_data']['full_path'];
            if ($this->csvimport->get_array($file_path)) {
                $csv_array = $this->csvimport->get_array($file_path);
                if (!empty($csv_array)) {
                    foreach ($csv_array as $val) {
                        if ($val['question'] != "" && $val['poll_type_id'] != "") {
                            $post = $this->input->post();
                            $set = array(
                                'sessions_id' => trim($post['sessions_id']),
                                'poll_type_id' => $val['poll_type_id'],
                                'question' => trim($val['question']),
                                'poll_comparisons_id' => 0,
                                "create_poll_date" => date("Y-m-d h:i")
                            );
                            $this->db->insert("sessions_poll_question", $set);
                            $insert_id = $this->db->insert_id();
                            if ($insert_id > 0) {
                                for ($i = 1; $i <= 10; $i++) {
                                    if ($val['option_' . $i] != "") {
                                        $set_array = array(
                                            'sessions_poll_question_id' => $insert_id,
                                            'sessions_id' => trim($post['sessions_id']),
                                            'option' => $val['option_' . $i],
                                            "total_vot" => 0
                                        );
                                        $this->db->insert("poll_question_option", $set_array);
                                    }
                                }
                            }
                        }
                    }
                }
                return TRUE;
            } else {
                return FALSE;
            }
        } else {
            return FALSE;
        }
    }

    function getSessionTypes() {
        $this->db->select('*');
        $this->db->from('sessions_type');
        $sessions_type = $this->db->get();
        if ($sessions_type->num_rows() > 0) {
            return $sessions_type->result();
        } else {
            return '';
        }
    }

    function getSessionTracks() {
        $this->db->select('*');
        $this->db->from('sessions_tracks');
        $sessions_tracks = $this->db->get();
        if ($sessions_tracks->num_rows() > 0) {
            return $sessions_tracks->result();
        } else {
            return '';
        }
    }

    function get_user_sign_up($sessions_id) {
        $this->db->select('*');
        $this->db->from('sign_up_sessions s');
        $this->db->join('customer_master c', 's.cust_id=c.cust_id');
        $this->db->where("s.sessions_id", $sessions_id);
        $result = $this->db->get();
        if ($result->num_rows() > 0) {
            return $result->result();
        } else {
            return '';
        }
    }

    function import_sessions() {
        $this->load->library('csvimport');
        if ($_FILES['import_file']['error'] != 4) {
            $pathMain = FCPATH . "/uploads/csv/";
            $filename = $this->generateRandomString() . '_' . $_FILES['import_file']['name'];
            $result = $this->common->do_upload('import_file', $pathMain, $filename);
            $file_path = $result['upload_data']['full_path'];
            if ($this->csvimport->get_array($file_path)) {
                $csv_array = $this->csvimport->get_array($file_path);
                if (!empty($csv_array)) {
                    foreach ($csv_array as $val) {

                        if ($val['session_title'] != "") {
                            if ($val['moderator'] != "") {
                                $moderator = explode(",", $val['moderator']);
                                $this->db->select("*");
                                $this->db->from("presenter");
                                $this->db->where_in("email", $moderator);
                                $result_presenter = $this->db->get();
                                if ($result_presenter->num_rows() > 0) {
                                    $moderator_id = array();
                                    foreach ($result_presenter->result() as $value) {
                                        $moderator_id[] = $value->presenter_id;
                                    }
                                    $moderator_id = implode(",", $moderator_id);
                                } else {
                                    $moderator_id = "";
                                }
                            } else {
                                $moderator_id = "";
                            }
                            if ($val['presenter_id'] != "") {
                                $presenter = explode(",", $val['presenter_id']);
                                $this->db->select("*");
                                $this->db->from("presenter");
                                $this->db->where_in("email", $presenter);
                                $result_presenter = $this->db->get();
                                if ($result_presenter->num_rows() > 0) {
                                    $presenter_id = array();
                                    foreach ($result_presenter->result() as $value) {
                                        $presenter_id[] = $value->presenter_id;
                                    }
                                    $presenter_id = implode(",", $presenter_id);
                                } else {
                                    $presenter_id = "";
                                }
                            } else {
                                $presenter_id = "";
                            }

                            if ($val['sessions_type_id'] != "") {
                                $sessions_type = explode(",", $val['sessions_type_id']);
                                $this->db->select("*");
                                $this->db->from("sessions_type");
                                $this->db->where_in("sessions_type", $sessions_type);
                                $sessions_type_result = $this->db->get();
                                if ($sessions_type_result->num_rows() > 0) {
                                    $sessions_type_id = array();
                                    foreach ($sessions_type_result->result() as $value) {
                                        $sessions_type_id[] = $value->sessions_type_id;
                                    }
                                    $sessions_type_id = implode(",", $sessions_type_id);
                                } else {
                                    $sessions_type_id = "";
                                }
                            } else {
                                $sessions_type_id = "";
                            }

                            if ($val['sessions_tracks_id'] != "") {
                                $sessions_tracks = explode(",", $val['sessions_tracks_id']);
                                $this->db->select("*");
                                $this->db->from("sessions_tracks");
                                $this->db->where_in("sessions_tracks", $sessions_tracks);
                                $sessions_tracks_result = $this->db->get();
                                if ($sessions_tracks_result->num_rows() > 0) {
                                    $sessions_tracks_id = array();
                                    foreach ($sessions_tracks_result->result() as $value) {
                                        $sessions_tracks_id[] = $value->sessions_tracks_id;
                                    }
                                    $sessions_tracks_id = implode(",", $sessions_tracks_id);
                                } else {
                                    $sessions_tracks_id = "";
                                }
                            } else {
                                $sessions_tracks_id = "";
                            }

                            if ($val['presenter_title'] != "") {
                                $presenter = explode(",", $val['presenter_title']);
                                $this->db->select("*");
                                $this->db->from("presenter");
                                $this->db->where_in("email", $presenter);
                                $result_presenter = $this->db->get();
                                if ($result_presenter->num_rows() > 0) {
                                    $presenter_title = array();
                                    foreach ($result_presenter->result() as $value) {
                                        $presenter_title[] = $value->presenter_id;
                                    }
                                    $presenter_title = implode(",", $presenter_title);
                                } else {
                                    $presenter_title = "";
                                }
                            } else {
                                $presenter_title = "";
                            }

                            $set = array(
                                'presenter_id' => $presenter_id,
                                'moderator_id' => $moderator_id,
                                'session_title' => trim($val['session_title']),
                                'sessions_description' => trim($val['sessions_description']),
                                'sessions_date' => date("Y-m-d", strtotime($val['sessions_date'])),
                                'time_slot' => date("H:i", strtotime($val['start_time'])),
                                'end_time' => date("H:i", strtotime($val['end_time'])),
                                'embed_html_code' => trim($val['embed_html_code']),
                                'embed_html_code_presenter' => trim($val['embed_html_code_presenter']),
                                'sessions_type_id' => $sessions_type_id,
                                'sessions_tracks_id' => $sessions_tracks_id,
                                'sessions_type_status' => trim($val['status']),
                                'sessions_visibility' => trim($val['sessions_visibility']),
                                "reg_date" => date("Y-m-d h:i")
                            );

                            $this->db->insert("sessions", $set);
                            $sessions_id = $this->db->insert_id();
                            if ($val['sessions_photo'] != "") {
                                $file_name = 'sessions_' . $this->generateRandomString() . '.jpg';
                                $url = $val['sessions_photo'];
                                $img = './uploads/sessions/' . $file_name;
                                file_put_contents($img, file_get_contents($url));
                                $this->db->update('sessions', array('sessions_photo' => $file_name), array('sessions_id' => $sessions_id));
                            }


                            if ($val['sponsor_logo'] != "") {
                                $file_name = 'sponsor_' . $this->generateRandomString() . '.jpg';
                                $url = $val['sponsor_logo'];
                                $img = './uploads/sponsor_log/' . $file_name;
                                file_put_contents($img, file_get_contents($url));
                                $this->db->update('sessions', array('sponsor_log' => $file_name), array('sessions_id' => $sessions_id));
                            }

                            $set_array = array(
                                'sessions_id' => $sessions_id,
                                'order_index_no' => trim($val['order_no']),
                                'select_presenter_id' => $presenter_title,
                                'presenter_title' => trim($val['p_title']),
                                'presenter_time_slot' => date("H:i", strtotime($val['presenter_start_time'])),
                                'presenter_resource_link' => trim($val['resource_links']),
                                'upload_published_name' => trim($val['upload_published_name']),
                                'link_published_name' => trim($val['link_published_name'])
                            );
                            $this->db->insert("sessions_add_presenter", $set_array);
                            $sessions_add_presenter_id = $this->db->insert_id();
                            if ($sessions_add_presenter_id > 0) {
                                $file_name = 'resource_' . $this->generateRandomString() . '.jpg';
                                $url = $val['resource_uploads'];
                                $img = './uploads/presenter_resource/' . $file_name;
                                file_put_contents($img, file_get_contents($url));
                                $this->db->update('sessions_add_presenter', array('presenter_resource' => $file_name), array('sessions_add_presenter_id' => $sessions_add_presenter_id));
                            }
                        } else {
                            $import_fail_record['session_sessions_data'][] = array(
                                'session_title' => trim($val['session_title']),
                                'sessions_description' => trim($val['sessions_description']),
                                'sessions_date' => date("Y-m-d", strtotime($val['sessions_date'])),
                                'status' => "Import Fail"
                            );
                            $this->session->set_userdata($import_fail_record);
                        }
                    }
                    return TRUE;
                } else {
                    return FALSE;
                }
            } else {
                return FALSE;
            }
        } else {
            return FALSE;
        }
    }

}
