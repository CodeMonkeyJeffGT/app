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
                $this->upload($this->payload['user']['id']);
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

    private function upload($id)
    {
        $config = array(
            'maxSize'    =>    3145728,
            'rootPath'   =>    './img/',
            'savePath'   =>    '',
            'saveName'   =>    array('uniqid',''),
            'exts'       =>    array('jpg', 'gif', 'png', 'jpeg'),
            'autoSub'    =>    true,
            'subName'    =>    array('date','Ymd'),
        );
        $upload = new \Think\Upload($config);// 实例化上传类
        // 上传文件 
        $info = $upload->upload();
        if($upload->getError()) {// 上传错误提示错误信息
            unlink('./img/' . current($info)['savepath'] . current($info)['savename']);
            $this->restReturn(array(
                'code'    => 1,
                'message' => $upload->getError(),
                'data'    => null
            ));
        }else{// 上传成功 获取上传文件信息
            $pic = '/img/' . current($info)['savepath'] . current($info)['savename'];
            $picInfo = $this->pics->insPic($id, $pic);
            $this->restReturn(array(
                'code'    => 0,
                'message' => '上传成功',
                'data'    => $picInfo
            ));
        }
    }

    private function unlinkPic($id, $u_id)
    {
        if(empty($id))
        {
            $this->restReturn(array(
                'code'    => 1,
                'message' => '请选择图片',
                'data'    => null
            ));
        }
        $pic = $this->pics->find($id);
        if(is_null($pic))
        {
            $this->restReturn(array(
                'code'    => 1,
                'message' => '图片不存在',
                'data'    => null
            ));
        }
        if($pic['u_id'] != $u_id)
        {
            $this->restReturn(array(
                'code'    => 1,
                'message' => '请勿删除他人的图片',
                'data'    => null
            ));
        }
        unlink('.' . $pic['url']);
        $this->pics->delete($id);
        $this->restReturn(array(
            'code'    => 0,
            'message' => '删除成功',
            'data'    => null
        ));
    }
}