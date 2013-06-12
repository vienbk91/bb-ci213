<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Js extends CI_Controller {
    
    public function __construct() {
        parent::__construct();
        $this->config->load('jsregistry.php');
        $this->jsregistry = $this->config->item('jsregistry');
        $this->jsmimetypes = $this->config->item('jsmimetypes');
    }

    public function index($jsfile='')
	{
        $this->files($jsfile);
	}

	public function files($jsfile='')
	{
        if(isset($this->jsregistry[$jsfile])){
            $js_data = $this->load->view('backbone/'.$this->jsregistry[$jsfile]['file'], null, true);
            $this
                ->output
                ->set_content_type($this->jsmimetypes[$this->jsregistry[$jsfile]['type']] )
                ->set_output($js_data);
        }else{
            $this->output->set_content_type($this->jsmimetypes['js'] )->set_output('');
        }
	}

	public function models($model='')
	{
        $this->load->model('slidermodel');
        $data = '';
        $mime_type = 'js';
        switch ($model){
            default :
                break;
            case 'slidecollection':
                $data = 'var slides = new SliderImageCollection('.json_encode($this->slidermodel->getAllSlides()).');';
                $mime_type = 'js';
                break;
        }
        $this->output->set_content_type($mime_type)->set_output($data);
	}

}

/* End of file backbone.php */
/* Location: ./application/controllers/backbone.php */