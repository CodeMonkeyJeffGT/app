<?php
namespace Api\Model;
use Think\Model;
class CommentModel extends Model {

    public function listComment($d_id)
    {
        return $this->where('d_id = "%d"', $d_id)
            ->select();
    }

    public function getComment($id)
    {
    	return $this->where('id = %d', $id)
    		->find();
    }

}