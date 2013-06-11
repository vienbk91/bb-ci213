<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Gallery extends CI_Controller {
    
	public function index()
	{
        $this->load->view('backbone/views/galleryimage');
	}

	public function image()
	{
        $this->load->view('backbone/views/galleryimage');
	}
    
	public function images()
	{
        $this->load->view('backbone/views/galleryimages');
	}
    
	public function addimage()
	{
        $this->load->view('backbone/views/addimage');
	}

    public function delimage()
	{
        $this->load->view('backbone/views/delimage');
	}

}

/* End of file backbone.php */
/* Location: ./application/controllers/backbone.php */