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
    // 
    private function uploadByBase64($img, $path, $name)
    {
        $base64_img = trim($img);
 
        if( ! is_dir($path)){
            mkdir($path, 0777);
        }
         
        if(preg_match('/^(data:\s*image\/(\w+);base64,)/', $base64_img, $result)){
            $type = $result[2];
            if(in_array($type, array('pjpeg', 'jpeg', 'jpg', 'gif', 'bmp', 'png'))){
                $new_file = $path . $name . '.' . $type;
                if(file_put_contents($new_file, base64_decode(str_replace($result[1], '', $base64_img)))){
                    return true;
                }else{
                    return '图片上传失败';
                }
            }else{
                return '图片上传类型错误';
            }
        }else{
          return '文件错误';
        }
    }

    private function uploadByFile($img, $path, $name)
    {

    }
}