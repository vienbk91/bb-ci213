<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Slider extends CI_Controller {
    
    public function __construct() {
        parent::__construct();
        $this->load->library('libslider');
    }

        public function index()
	{
        $this->load->view('backbone/views/slider');
	}

    public function addupdate()
	{
        $slider_form_action = $this->input->post('slider_form_action');
        if($slider_form_action=='add' || $slider_form_action=='update'){
            $data = $this->libslider->addSlide($slider_form_action);
        }else{
            $data = json_encode(array(
                'status'=> 400,
                'messae' => 'Unauthorised/bad request!'
                ));
        }
	}

    
}

/* End of file backbone.php */
/* Location: ./application/controllers/backbone.php */