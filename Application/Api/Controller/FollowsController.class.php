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
                ));
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
        $user = M('user')->find($u_id);
        if(empty($user))
        {
            $this->restReturn(array(
                'code'    => 1,
                'message' => '用户不存在',
                'data'    => null, 
            ));
        }
        $followed = $this->follow
            ->where('u_id = %d AND f_id = %d', $u_id, $id)
            ->find();
        if( ! empty($followed))
        {
            $this->restReturn(array(
                'code'    => 1,
                'message' => '请勿重复关注',
                'data'    => null, 
            ));
        }
        $this->add(array(
            'u_id' => $u_id,
            'f_id' => $id
        ));
        $this->restReturn(array(
            'code'    => 0,
            'message' => '关注成功',
            'data'    => true, 
        ));
    }
 
    private function unfollow($u_id, $id)
    {
        $user = M('user')->find($u_id);
        if(empty($user))
        {
            $this->restReturn(array(
                'code'    => 1,
                'message' => '用户不存在',
                'data'    => null, 
            ));
        }
        $followed = $this->follow
            ->where('u_id = %d AND f_id = %d', $u_id, $id)
            ->find();
        if(empty($followed))
        {
            $this->restReturn(array(
                'code'    => 1,
                'message' => '您还未关注',
                'data'    => null, 
            ));
        }
        $this->where(array(
            'u_id' => $u_id,
            'f_id' => $id
        ))->delete();
        $this->restReturn(array(
            'code'    => 0,
            'message' => '取消成功',
            'data'    => true, 
        ));
    }
}