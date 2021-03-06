<?php

class Meetings_Modal extends CI_Model {

    function __construct()
    {
        parent::__construct();
    }

    public function newMeeting()
    {
        $meetings_time_limit = 30; //in minutes

        //echo($this->input->post('from')); exit;

        $meeting_to_time = new DateTime($this->input->post('from'));
        $meeting_to_time->add(new DateInterval('PT' . $meetings_time_limit . 'M'));
        $meeting_to_time_string = $meeting_to_time->format('Y-m-d H:i');

        $meeting_from_time = new DateTime($this->input->post('from'));
        $meeting_from_time_string = $meeting_from_time->format('Y-m-d H:i');

        //echo($meeting_from_time_string); exit;
        $host = $this->session->userdata('cid');
        $topic = $this->input->post('topic');
        $from = $meeting_from_time_string;
        $to = $meeting_to_time_string;
        $attendees = $this->input->post('attendees');

        $data = array(
            'host' => $host,
            'topic' => $topic,
            'meeting_from' => $from,
            'meeting_to' => $to
        );

        if($this->db->insert('lounge_meetings', $data))
        {
            $meeting_id = $this->db->insert_id();

            foreach ( $attendees as $attendee){
                $attendees_list[] = array(
                    'meeting_id' => $meeting_id,
                    'attendee_id'=> $attendee
                );
            }
            if ($this->db->insert_batch('lounge_meeting_attendees', $attendees_list))
                return true;
        }

        return false;
    }

    public function deleteMeeting()
    {
        $meetingId = $this->input->post('meetingId');
        $this->db->delete('lounge_meeting_attendees', array('meeting_id' => $meetingId));
        if ($this->db->delete('lounge_meetings', array('id' => $meetingId)))
            return true;
        return false;
    }

    public function getMeetings($user)
    {
        $meetings = $query = $this->db->query("
                                        SELECT DISTINCT lm.*, CONCAT(cm.first_name, ' ', cm.last_name) AS host_name
                                        FROM lounge_meetings lm
                                        LEFT JOIN lounge_meeting_attendees lma ON lm.id = lma.meeting_id
                                        LEFT JOIN customer_master cm ON lm.host = cm.cust_id
                                        WHERE lm.host = '{$user}' OR lma.attendee_id = '{$user}'
                                        ");
        if ($meetings->num_rows() > 0) {

            foreach ($meetings->result() as $meeting)
            {
                $meeting->attendees = $this->getAttendeesPerMeet($meeting->id);
                $meeting->meeting_from = (new DateTime($meeting->meeting_from))->format('M-d h:ia');
                $meeting->meeting_to = (new DateTime($meeting->meeting_to))->format('M-d h:ia');
            }
            return $meetings->result();
        } else {
            return false;
        }
    }

    public function getFutureMeetingsNumber($user)
    {
        $now = date('Y-m-d H:i:s');
        $meetings = $query = $this->db->query("
                                        SELECT DISTINCT lm.*
                                        FROM lounge_meetings lm
                                        LEFT JOIN lounge_meeting_attendees lma ON lm.id = lma.meeting_id
                                        WHERE (lm.host = '{$user}' OR lma.attendee_id = '{$user}') AND lm.meeting_to > '{$now}'
                                        ");
        if ($meetings->num_rows() > 0)
        {
            return $meetings->result_array();
        } else {
            return false;
        }
    }

    public function getAttendeesPerMeet($meeting_id)
    {
        $users = $query = $this->db->query("
                                        SELECT lma.*, CONCAT(cm.first_name, ' ', cm.last_name) AS name, cm.profile
                                        FROM lounge_meeting_attendees lma
                                        JOIN customer_master cm ON cm.cust_id = lma.attendee_id
                                        WHERE lma.meeting_id = '{$meeting_id}'
                                        ");
        if ($users->num_rows() > 0) {
            return $users->result_array();
        } else {
            return '';
        }
    }

    public function identityValidation($meeting_id, $attendee_id)
    {
        $users = $query = $this->db->query("
                                        SELECT lm.id 
                                        FROM lounge_meetings lm
                                        LEFT JOIN lounge_meeting_attendees lma ON lm.id = lma.meeting_id
                                        WHERE (lm.host = '{$attendee_id}' OR lma.attendee_id = '{$attendee_id})') 
                                              AND lm.id = '{$meeting_id}'
                                        ");
        if ($users->num_rows() > 0) {
            return true;
        } else {
            return false;
        }
    }

    public function getMeetingDetails($meeting_id)
    {
        $meetings = $query = $this->db->query("
                                        SELECT DISTINCT lm.*, CONCAT(cm.first_name, ' ', cm.last_name) AS host_name
                                        FROM lounge_meetings lm
                                        LEFT JOIN lounge_meeting_attendees lma ON lm.id = lma.meeting_id
                                        LEFT JOIN customer_master cm ON lm.host = cm.cust_id
                                        WHERE lm.id = '{$meeting_id}'
                                        ");
        if ($meetings->num_rows() > 0) {

            foreach ($meetings->result() as $meeting)
            {
                $meeting->attendees = $this->getAttendeesPerMeet($meeting->id);
            }
            return $meetings->result()[0];
        } else {
            return false;
        }
    }

}
