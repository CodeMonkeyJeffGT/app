<?php
namespace Api\Controller;
use Api\Common\ApiController;
class UsersController extends ApiController {

    private $users;

    public function __construct()
    {
        parent::__construct();

        if( ! $this->checkToken())
        {
            $this->goLogin();
        }
        $this->users = D('user');
    }

    public function index()
    {

    	switch ($this->_method)
        {
            case 'get':
                if(empty($this->id))
                    $this->search($this->data);
                else
                    $this->getUser($this->id);
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

    private function search($data)
    {
        if(empty($data['query']))
            $this->restReturn(array(
                'code'    => 1,
                'message' => '搜索不能为空',
                'data'    => null
            ));
        $users = $this->users->search($data['query']);
        $this->restReturn(array(
            'code'    => 0,
            'message' => '',
            'data'    => $users
        ));
    }

    private function getUser($id)
    {
        $user = $this->users
            ->field('`id`, `head_img_url` `head_img_url`, `nickname` `nickname`')
            ->find($id);
        if(is_null($user))
        {
            $this->restReturn(array(
                'code'    => 1,
                'message' => '用户不存在',
                'data'    => null
            ));
        }
        $user = line_to_up($user);
        $this->restReturn(array(
            'code'    => 0,
            'message' => '',
            'data'    => $user
        ));
    }
}