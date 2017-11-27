<?php
namespace Api\Controller;
use Api\Common\ApiController;
class PicsController extends ApiController {

    public function index()
    {
    	switch ($this->_method)
        {
            case 'post':

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

    private function send_error($code)
    {
        switch (variable) {
            case 'value':
                # code...
                break;
            
            default:
                # code...
                break;
        }

        '上传成功',
        '超过服务器大小限制',
        '超过浏览器大小限制',
        '文件残缺',
        '没找到要上传的文件',
        '',
        '服务器临时文件夹丢失',
        '写入临时文件出错 ',
        'PHP错误导致上传中断'
    }
}