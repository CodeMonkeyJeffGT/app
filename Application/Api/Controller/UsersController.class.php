<?php
namespace Api\Controller;
use Api\Common\ApiController;
class UsersController extends ApiController {

    public function index()
    {
        if( ! $this->checkToken())
        {
            $this->goLogin();
        }

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

    }

    private function getUser($id)
    {

    }
}