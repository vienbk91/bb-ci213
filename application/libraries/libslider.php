<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
class LibSlider{
    public function __construct() {
        $this->CI =& get_instance();
        $this->CI->load->model('slidermodel');
        $this->sliderConfig = $this->CI->config->item('sliderConfig');
    }
    public function sliderGrid($returnAsString=false){
//        if($returnAsString==false && !$this->CI->input->is_ajax_request()){
//            echo 'Invalid request!';
//        }
        $this->CI->load->model('admin/madminpages');
        $this->CI->load->model('admin/madminposts');
        $this->CI->load->library('pagination');
        $configPagination['sortURI'] = 'admin/slider/slidergrid/' ;
        $uriSegments = $this->CI->uri->segment_array();
        array_shift($uriSegments);
        array_shift($uriSegments);
        array_shift($uriSegments);
        $data['offset'] = isset($uriSegments[2])?$uriSegments[2]:$this->CI->config->item('paginationDefaultOffset');
        $data['orderByDirection'] = isset($uriSegments[1])?$uriSegments[1]:'';
        $data['orderByColumn'] = isset($uriSegments[0])?str_replace('-', '.', $uriSegments[0]):'';
        $data['per_page'] = $this->CI->config->item('paginationItems');
        $data['slides'] = $this->CI->slidermodel->getSlides(
                $data['orderByColumn'],
                $data['orderByDirection'],
                $data['offset'],
                $data['per_page']
            );
        $slideCount = count($data['slides']);
        for($i=0; $i < $slideCount; $i++){
            if($data['slides'][$i]['sliderlink_type'] != 'static'){
                switch ($data['slides'][$i]['sliderlink_type']){
                    case 'post':
                        $menuitem = $this->CI->madminposts->getPost($data['slides'][$i]['slider_link']);
                        $data['slides'][$i]['slider_link'] = base_url().'news/'.$menuitem['slug'];
                        break;
                    case 'page':
                        $menuitem = $this->CI->madminpages->getPage($data['slides'][$i]['slider_link']);
                        $data['slides'][$i]['slider_link'] = base_url().$menuitem['slug'];
                        break;
                }
            }
        }
        $configPagination['sortURI'] = base_url().$configPagination['sortURI'];
        $configPagination['base_url'] = $configPagination['sortURI'].'slider-slider_id/asc';
        $configPagination['total_rows'] = $this->CI->slidermodel->getCountOfSlides();
        $configPagination['uri_segment'] = $this->CI->uri->total_segments();
        $configPagination['num_links'] = 3;
        $configPagination['first_link'] = 'First';
        $configPagination['last_link'] = 'Last';
        $configPagination['next_link'] = '&raquo;';
        $configPagination['prev_link'] = '&laquo;';
        $configPagination['per_page'] = $data['per_page'];
        $configPagination['anchor_class'] = 'paginater';
        $data['pathToSlides'] = $this->sliderConfig['pathToSlides'];
        $data['configPagination'] = $configPagination;
        $this->CI->pagination->initialize($configPagination);
        $data['paginationLinks'] = $this->CI->pagination->create_links();
        if($returnAsString == false){
            echo $this->CI->load->view('admin/slider/slider-grid', $data);
        }else{
            return $this->CI->load->view('admin/slider/slider-grid', $data,  TRUE);
        }
    }
    public function updateSlideStatus($slideId, $status){
        $keyvalue = array('slider_status'=>$status);
        $whereCondition = array(
            'key'=>'slider_id',
            'value'=>$slideId);
        $data['status'] = $this->CI->slidermodel->updateSlideByKeyValueCondition($keyvalue,$whereCondition);
        $data['message'] = $data['status'] == true?
            ($status == 1 ?
                '<a class="toggle-slide" href="'.  base_url().'/admin/slider/manageslides/status/'.$slideId.'/0" title="Click to disable"><img src="'.base_url().'/assets/images/cms/iconset/status-active.png" border="0" /></a>':
                '<a class="toggle-slide" href="'.  base_url().'/admin/slider/manageslides/status/'.$slideId.'/1" title="Click to enable"><img src="'.base_url().'/assets/images/cms/iconset/status-disabled.png" border="0" /></a>')
            :'Failed!';
        return $data['message'];
    }
    public function deleteSlide($slideId){
        return $this->CI->slidermodel->deleteSlide($slideId);
    }
    public function getSlideByKey($slideId){
        $data = $this->CI->slidermodel->getSlideByKey('slider_id',$slideId);
        $data['status'] = $data != NULL ? 200:500;//http status codes FTW!
        $data['message'] = '';
        $data['pathToSlides'] = base_url().$this->sliderConfig['pathToSlides'];
        return($data);
    }
    public function getAllSlides(){
        $this->CI->load->model('admin/madminpages');
        $this->CI->load->model('admin/madminposts');
        $slides = $this->CI->slidermodel->getSlideByKeys(array('slider_status'=> 1));

        $slideCount = count($slides);
        for($i=0; $i < $slideCount; $i++){
            if($slides[$i]['sliderlink_type'] != 'static'){
                switch ($slides[$i]['sliderlink_type']){
                    case 'post':
                        $menuitem = $this->CI->madminposts->getPost($slides[$i]['slider_link']);
                        $slides[$i]['slider_link'] = base_url().'news/'.$menuitem['slug'];
                        break;
                    case 'page':
                        $menuitem = $this->CI->madminpages->getPage($slides[$i]['slider_link']);
                        $slides[$i]['slider_link'] = base_url().$menuitem['slug'];
                        break;
                }
            }
        }
        return(
            array(
                'slides' => $slides,
                'pathToSlides'=>$this->sliderConfig['pathToSlides']//and path to slides folder
            )
        );
    }
    public function addSlide($slider_form_action = 'add'){
        if ($slider_form_action == 'add' && $this->CI->form_validation->run('slides-upload-form') == FALSE)
        {
            return json_encode(array('status'=> 500, 'message'=>'Please check form. '.validation_errors()));
        }
        $this->CI->load->library('upload');
        $this->CI->load->library('image_lib');
        $slideData = array();
        $slideData['slider_name'] = $this->CI->input->post('slider_name');
        $slideData['slider_subtitle'] = $this->CI->input->post('slider_subtitle');
        $slideData['slider_description'] = $this->CI->input->post('slider_description');
        $slideData['slider_link'] = $this->CI->input->post('slider_link');
        $slideData['sliderlink_type'] = $this->CI->input->post('sliderlink_type');
        $slideData['slider_status'] = $this->CI->input->post('slider_status');
        if(isset($_FILES['slider_file']) ){
            $uploadConfig = $this->sliderConfig;
            //allow unique names for files
            $uploadConfig['file_name'] = 'slide-'.md5(date('YmdHIS').rand()).'-'.$_FILES['slider_file']['name'];
            $this->CI->upload->initialize($uploadConfig);
            if(!$this->CI->upload->do_upload('slider_file')){;
                    return json_encode(array(
                    'status'=> 500,
                    'message'=>$this->CI->upload->display_errors(),
                    ));
            }
            chmod($uploadConfig['upload_path'].$uploadConfig['file_name'], 0777);
            $resizeConfig = array_merge(
                $this->CI->config->item('image_resize'),
                $uploadConfig
            );
            $resizeConfig['source_image'] = $uploadConfig['upload_path'].$uploadConfig['file_name'];
            $this->CI->image_lib->initialize($resizeConfig);
            $this->CI->image_lib->resize();
            $slideData['slider_path'] = $uploadConfig['file_name'];
        }
        if($slider_form_action == 'add'){
            $result = $this->CI->slidermodel->addSlide($slideData);
        }elseif($slider_form_action == 'update'){
            $slider_id = (int)$this->CI->input->post('slider_id');
            if($slider_id == '' || $slider_id<0){
                    return json_encode(array(
                    'status'=> 500,
                    'message'=>'Invalid data!',
                    ));
            }
            $whereCondition = array(
                'key'=>'slider_id',
                'value'=>$slider_id);
            $result = $this->CI->slidermodel->updateSlideByKeyValueCondition($slideData, $whereCondition);
        }
        if($result == TRUE){
            $slideData['status'] = 200;
            $slideData['message'] = 'Slide saved!';
        }else{
            $slideData['status'] = 500;
            $slideData['message'] = 'Slide could not be saved!';
        }
        return json_encode($slideData);
    }

    function setSlideOrder(){
        $arrayId = $this->CI->input->post('arrayid');
        $this->CI->slidermodel->setSlideOrder($arrayId);
    }
}