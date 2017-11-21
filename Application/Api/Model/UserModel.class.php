<?php
namespace Api\Model;
use Think\Model;
class UserModel extends Model {

    public function getUser($username)
    {
        return $this->field('id, password, nick, headimgurl')
            ->where('username = "%s"', $username)
            ->find();
    }

}