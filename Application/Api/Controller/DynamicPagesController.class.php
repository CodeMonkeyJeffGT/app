<?php
namespace Api\Controller;
use Api\Common\ApiController;
class DynamicsPagesController extends ApiController {

    public function index($id = 0)
    {
    	switch ($this->_method)
    	{
    		case 'get':
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