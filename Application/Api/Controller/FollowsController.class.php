<?php
namespace Api\Controller;
use Api\Common\ApiController;
class FollowsController extends ApiController {

    private $follow;

    public function index()
    {
        if( ! $this->checkToken())
            $this->goLogin();
        $this->follow = D('follow');
        $u_id = $this->payload['user']['id'];
    	switch ($this->_method)
        {
            case 'get':
                $this->listFollow($u_id);
                break;
            
            case 'post':
                if(empty($this->id))
                    $this->restReturn(array(
                        'code'    => 1,
                        'message' => '未指定用户',
                        'data'    => false
                    ));
                $this->follow($u_id, $this->id);
                break;
            
            case 'delete':
                if(empty($this->id))
                    $this->restReturn(array(
                        'code'    => 1,
                        'message' => '未指定用户',
                        'data'    => false
                    ));
                $this->unfollow($u_id, $this->id);
                break;
            
            default:
                $this->restReturn(array(
                    'code'    => 1,
                    'message' => '请求方式错误',
                    'data'    => null
                ));=
                break;
        }
    }

    private function listFollow($id)
    {
        $follow = $this->follow->listFollow($id);
        $this->restReturn(array(
            'code'    => 0,
            'message' => '',
            'data'    => $follow
        ));
    }

    private function follow($u_id, $id)
    {

    }

    private function unfollow($u_id, $id)
    {

    }
}