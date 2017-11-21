<?php
namespace Api\Model;
use Think\Model;
class DynamicModel extends Model {

    public function getDynamic($id)
    {
        return $this->where('id = %d', $id)
        	->find();
    }

    public function listDynamics($last_id, $limit = 20)
    {
    	return $this->where('id < %d', $last_id)
    		->order('id DESC')
    		->limit($limit)
    		->select();
    }

}