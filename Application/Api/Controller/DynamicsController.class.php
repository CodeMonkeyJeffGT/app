<?php
namespace Api\Controller;
use Api\Common\ApiController;
class DynamicsController extends ApiController {

    private $dynamics;

    public function __construct()
    {
        parent::__construct();
        $this->dynamics = D('dynamic');
    }

    public function index($id = 0)
    {
        switch ($this->_method)
        {
            case 'get':
                if(empty($this->id))
                    $this->list($this->data);
                else
                    $this->getDs($this->id);
                break;
            
            case 'post':
                if( ! $this->checkToken())
                    $this->goLogin();
                $this->publish($this->data);
                break;

            case 'PUT':
                if( ! $this->checkToken())
                    $this->goLogin();
                if(empty($this->id))
                    $this->restReturn(array(
                        'code'    => 1,
                        'message' => '请选择操作id',
                        'data'    => null
                    ));
                $this->edit($this->id, $this->data);
                break;
            
            case 'delete':
                if( ! $this->checkToken())
                    $this->goLogin();
                if(empty($this->id))
                    $this->restReturn(array(
                        'code'    => 1,
                        'message' => '请选择操作id',
                        'data'    => null
                    ));
                $this->delete($this->id);
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

    private function list($data)
    {
        $type   = isset($data['type']) ? $data['type'] : 'hot';
        $offset = isset($data['offset']) ? $data['offset'] : 0;
        $limit  = isset($data['limit']) ? $data['limit'] : 20;

        $id     = 0;
        if($type == 'like')
        {
            if( ! $this->checkToken())
                $this->goLogin();
            $id = $payload['user']['id'];
        }
        else
        {
            if($this->checkToken())
                $id = $payload['user']['id'];
        }

        $dynamics = $this->dynamics->listDynamics($id, $offset, $limit);
        $this->restReturn(array(
            'code'    => 0,
            'message' => '',
            'data'    => $dynamics
        ));
    }

    private function getDs($id)
    {

    }

    private function publish($data)
    {

    }

    private function edit($id, $data)
    {

    }

    private function delete($id)
    {

    }
}