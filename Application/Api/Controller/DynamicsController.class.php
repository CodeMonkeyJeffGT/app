<?php
namespace Api\Controller;
use Api\Common\ApiController;
class DynamicsController extends ApiController {

    public function index($id = 0){

        $data = I('param.');
        switch ($this->_method)
        {
            case 'get':
                break;
            
            case 'post':
                break;

            case 'PUT':
                break;
            
            case 'delete':
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