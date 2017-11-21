<?php
namespace Api\Controller;
use Api\Common\ApiController;
class LoginController extends ApiController {

	protected $check_token = false;

    public function index()
    {
        $data = I('param.');
    	switch ($this->_method)
        {
            case 'post':
                $this->login($data);
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

    private function login($data)
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
        $user = D('user')->getUser($data['username']);
        if(empty($user))
        {
            $this->restReturn(array(
                'code'    => 1,
                'message' => '账号不存在',
                'data'    => false
            ));
        }
        if(md5($data['password']) != $user['password'])
        {
            $this->restReturn(array(
                'code'    => 1,
                'message' => '密码错误',
                'data'    => false
            ));
        }
        
        $this->data['user'] = array(
            'id'         => $user['id'],
            'nick'       => $user['nick'],
            'headImgUrl' => $user['headimgurl']
        );
        $this->restReturn(array(
            'code'    => 0,
            'message' => '登录成功',
            'data'    => true
        ));
    }
}