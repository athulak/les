<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Profile extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $login_type = $this->session->userdata('userType');
        if ($login_type != 'sponsor') {
            redirect('sponsor-admin/login');
        }

        $this->load->model('madmin/m_sponsors', 'sponsor');
    }

    public function updateName()
    {
        $this->sponsor->updateSponsorName();
        return;
    }

    public function updateAbout()
    {
        $this->sponsor->updateSponsorAbout();
        return;
    }

    public function updateWebsite()
    {
        $this->sponsor->updateSponsorWebsite();
        return;
    }

    public function updateTwitter()
    {
        $this->sponsor->updateSponsorTwitter();
        return;
    }

    public function updateFacebook()
    {
        $this->sponsor->updateSponsorFacebook();
        return;
    }

    public function updateLinkedin()
    {
        $this->sponsor->updateSponsorLinkedin();
        return;
    }

    public function updateLogo($id)
    {
        echo $this->sponsor->updateSponsorLogo($id);
        return;
    }

    public function updateCover($id)
    {
        echo $this->sponsor->updateCover($id);
        return;
    }

    public function getSponsorLogo($id)
    {
        echo $this->sponsor->getSponsorLogo($id);
        return;
    }

    public function newResource($sponsor)
    {
        echo $this->sponsor->newResource($sponsor);
        return;
    }

    public function getAllResources($sponsor)
    {
        echo json_encode($this->sponsor->getAllResources($sponsor));
        return;
    }

    public function deleteResource($resourceId)
    {
        echo $this->sponsor->deleteResource($resourceId);
        return;
    }


}
