<?php
namespace Api\Model;
use Think\Model;
class UserModel extends Model {

    public function getUser($username)
    {
        return $this->field('`id`, `password`, `nickname`, `head_img_url`')
            ->where('username = "%s"', $username)
            ->find();
    }

    public function search($query)
    {
		$strnum = mb_strlen($query,'UTF8');
		$array = array();
		while($strnum)
		{
			$array[] = mb_substr($query, 0, 1, 'utf8');
			$query   = mb_substr($query, 1, $strnum, 'utf8');
			$strnum  = mb_strlen($query, 'UTF8');
		}
		$query = '%' . implode('%', $array) . '%';
    	return line_to_up($this->where('nickname LIKE "%s"', $query)->select());
    }

}