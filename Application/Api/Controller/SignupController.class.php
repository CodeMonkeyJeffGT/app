<?php
namespace Api\Controller;
use Api\Common\ApiController;
class SignupController extends ApiController {

	protected $check_token = false;

    public function index()
    {
        $data = I('param.');
    	switch ($this->_method)
    	{
    		case 'post':
                $this->signup($data);
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

    private function signup($data)
    {
        if(empty($data['username']))
        {
            $this->restReturn(array(
                'code'    => 1,
                'message' => '账号不能为空',
                'data'    => false
            ));
        }
        if(empty($data['password']))
        {
            $this->restReturn(array(
                'code'    => 1,
                'message' => '密码不能为空',
                'data'    => false
            ));
        }
        if(empty($data['nick']))
        {
            $data['nick'] = $data['username'];
        }
        if(empty($data['headImgUrl']))
        {
            $data['headImgUrl'] = '/headimg/default.png';
        }
        $user = D('user')->getUser($data['username']);
        if( ! empty($user))
        {
            $this->restReturn(array(
                'code'    => 1,
                'message' => '账号已存在',
                'data'    => false
            ));
        }
        $user = M('user')->add(array(
            'username'   => $data['username'],
            'password'   => md5($data['password']),
            'nick'       => $data['nick'],
            'headimgurl' => $data['headImgUrl'],
        ));
        
        $this->data['user'] = array(
            'id'         => $user,
            'nick'       => $data['nick'],
            'headImgUrl' => $data['headImgUrl']
        );
        $this->restReturn(array(
            'code'    => 0,
            'message' => '注册成功',
            'data'    => $user
        ));
    }
}