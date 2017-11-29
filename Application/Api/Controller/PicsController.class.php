<?php

<?php
namespace Api\Controller;
use Api\Common\ApiController;
class PicsController extends ApiController {

    private $pics;

    public function index()
    {
        if( ! $this->checkToken())
            $this->goLogin();

        $this->pics = D('img');

        switch ($this->_method)
        {
            case 'post':
                $this->upload($this->payload['user']['id'], $this->data);
                break;
            
            case 'delete':
                $this->unlinkPic($this->id, $this->payload['user']['id']);
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

    // private function upload($id, $data)
    // {
    //     $config = array(
    //         'maxSize'    =>    3145728,
    //         'rootPath'   =>    './img/',
    //         'savePath'   =>    '',
    //         'saveName'   =>    array('uniqid',''),
    //         'exts'       =>    array('jpg', 'gif', 'png', 'jpeg'),
    //     }
    //     $error = array(
    //         1 => '',
    //         2 => '',
    //         3 => ''
    //     );
    //     if(empty($_FILES) || empty(current($_FILES)['name']))
    //     {
    //         $this->restReturn(array(
    //             'code'    => 1,
    //             'message' => '未上传文件',
    //             'data'    => false
    //         ));
    //     }
    //     $files = current($_FILES);
    //     $fileinfo = array('succeed' => array(), 'error' => array());
    //     for($i = 0, $len = count($files['name']); $i < $len; $i++)
    //     {
    //         if($files['error'][$i] != 0)
    //         {
    //             $fileinfo['error']
    //         }
    //         else
    //         {
                
    //         }
    //     }
    //     if()
    // }
}