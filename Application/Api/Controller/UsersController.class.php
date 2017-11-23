<?php
namespace Api\Controller;
use Api\Common\ApiController;
class UsersController extends ApiController {

    public function index()
    {
    	switch ($this->_method)
        {
            case 'get':
                $this->search($data);
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
}