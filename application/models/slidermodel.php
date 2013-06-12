<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class SliderModel extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->table = 'slider';
    }

    public function getAllSlides($slider_status=TRUE){
        $query = $this->db->get_where( $this->table, array('slider_status' => $slider_status));
        return $query->result_array();
    }

    public function getSlideByKey($key, $value){
		$this->db->select($this->table_natableme.'.* ');
		$this->db->where($key, $value);
        $query = $this->db->get($this->table);
		if ($query->num_rows() == 1) return $query->row_array();
		return NULL;
    }
    public function getSlideByKeys($keyValuePairs=array()){
        $this->db->select($this->table.'.* ');
        if(is_array($keyValuePairs) && ($keyValuePairs)>0){
            foreach ($keyValuePairs as $key => $value){
                $this->db->where($key, $value);
            }
        }elseif($keyValuePairs!=''){
            $this->db->where($keyValuePairs, NULL, false);
        }
        $this->db->order_by('slider_order', 'ASC');
		$query = $this->db->get($this->table);
		if ($query->num_rows() > 0) return $query->result_array();
		return NULL;
    }
    public function getSlides($orderBy='', $orderByDirection='asc', $offset=0, $limit=20, $where='', $and=true ){
        $orderBy = ($orderBy!='')?$orderBy : $this->table.'.slider_order';
        (isset ($and) && ($and == TRUE ? ($and=' AND ') : ($and=' OR ') )) || ($and = '');
        $query = $this->db->select($this->table.'.*')
            ->from($this->table)
            ->order_by($orderBy, $orderByDirection)
            ->limit($limit, $offset)
            ->get();
		if ($query->num_rows() > 0) return $query->result_array();
		return NULL;
    }
    public function getCountOfSlides($orderBy='', $orderByDirection='asc', $offset=0, $limit=20, $where='', $and=true ){
        $orderBy = ($orderBy!='')?$orderBy : $this->table.'.slider_order';
        (isset ($and) && ($and == TRUE ? ($and=' AND ') : ($and=' OR ') )) || ($and = '');
        return( $this->db->select($this->table.'.*')
            ->from($this->table)
            ->order_by($orderBy, $orderByDirection)
            ->limit($limit, $offset)
            ->count_all_results());
    }
    public function updateSlideByKeyValueCondition($keyvalue, $where=array()){
        if(!is_array($keyvalue)){
            return false;
        }
        $this->db->set($keyvalue);
        if(isset ($where)){
            $this->db->where($where['key'], $where['value']);
        }
        $this->db->update($this->table);
        return true;
    }
	function addSlide($data)
	{
       if(!is_array($data)){
            return false;
        }
        $slider_order = $this->getSliderOrderNumber();
        $data['slider_order'] = $slider_order['slider_order']+1;
        $this->db->insert($this->table, $data);
        return true;
	}
	function deleteSlide($slideId)
	{
		$this->db->where($this->table.'.slider_id', $slideId);
		$this->db->delete($this->table);
		if ($this->db->affected_rows() > 0) {
			return TRUE;
		}
		return FALSE;
	}

    function setSlideOrder($arrayId){
        $query = "UPDATE ".$this->table." SET slider_order = (CASE slider_id ";
        foreach($arrayId as $sort => $id) {
            $query .= " WHEN ".$id." THEN ".$sort;
        }
        $query .= " END) WHERE slider_id IN (" . implode(",", $arrayId) . ")";
        $this->db->query($query);
    }

    public function getSliderOrderNumber(){
        $sqlQuery = $this->db->select($this->table.'.slider_order as slider_order')
            ->from($this->table)
            ->order_by($this->table.'.slider_order', 'desc')
            ->limit(1);
        $query = $sqlQuery->get();
        if ($query->num_rows() > 0){
            return $query->row_array();
        }else{
            return 1;
        }
    }
}