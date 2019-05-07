<?php
namespace Api\Controller;
use Api\Common\ApiController;

/**
 * 用户管理
 * @author 谷田 11.23
 */
class UserControlController extends ApiController {
    public function __construct()
    {
        parent::__construct();
        if($this->_method !== 'post')
        {
            $this->restReturn(array(
                'code'    => 1,
                'message' => '请求方式错误',
                'data'    => null
            ));
        }
    }

    /**
     * 登陆
     */
    public function login()
    {
        $data = $this->data;
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

        //存入payload以自动生成token
        $this->payload['user'] = array(
            'id'         => $user['id'],
            'nickname'   => $user['nickname'],
            'headImgUrl' => $user['head_img_url']
        );
        $this->restReturn(array(
            'code'    => 0,
            'message' => '登录成功',
            'data'    => $this->payload['user']
        ));
    }

    /**
     * 注册
     */
    public function signup()
    {
        $data = $this->data;
        if(empty($data['username']))
        {
            $this->restReturn(array(
                'code'    => 1,
                'message' => '账号不能为空',
                'data'    => null
            ));
        }
        if(empty($data['password']))
        {
            $this->restReturn(array(
                'code'    => 1,
                'message' => '密码不能为空',
                'data'    => null
            ));
        }
        if(empty($data['nickname']))
        {
            $data['nickname'] = $data['username'];
        }
        if(empty($data['headImgUrl']))
        {
            $avatars = array(
                'https://s3.amazonaws.com/uifaces/faces/twitter/mhesslow/128.jpg',
                'https://s3.amazonaws.com/uifaces/faces/twitter/91bilal/128.jpg',
                'https://s3.amazonaws.com/uifaces/faces/twitter/saulihirvi/128.jpg',
                'https://s3.amazonaws.com/uifaces/faces/twitter/zforrester/128.jpg',
                'https://s3.amazonaws.com/uifaces/faces/twitter/markjenkins/128.jpg',
                'https://s3.amazonaws.com/uifaces/faces/twitter/souperphly/128.jpg',
                'https://s3.amazonaws.com/uifaces/faces/twitter/ateneupopular/128.jpg',
            );

            $data['headImgUrl'] = $avatars[rand(0, count($avatars) - 1)];
        }
        $user = D('user')->getUser($data['username']);
        if( ! empty($user))
        {
            $this->restReturn(array(
                'code'    => 1,
                'message' => '账号已存在',
                'data'    => null
            ));
        }
        $user = M('user')->add(array(
            'username'   => $data['username'],
            'password'   => md5($data['password']),
            'nickname'   => $data['nickname'],
            'head_img_url' => $data['headImgUrl'],
        ));

        //存入payload以自动生成token
        $this->payload['user'] = array(
            'id'         => $user,
            'nickname'   => $data['nickname'],
            'headImgUrl' => $data['headImgUrl']
        );
        $this->restReturn(array(
            'code'    => 0,
            'message' => '注册成功',
            'data'    => $this->payload['user']
        ));
    }

    /**
     * 解释token
     */
    public function transToken()
    {
        if( ! $this->checkToken())
            $this->goLogin();
        $this->restReturn(array(
            'code'    => 0,
            'message' => '',
            'data'    => $this->payload['user']
        ));
    }
}
