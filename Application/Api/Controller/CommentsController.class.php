<?php
namespace Api\Controller;
use Api\Common\ApiController;
class CommentsController extends ApiController {

    public function index($id = 0){

        $data = I('param.');
        if(empty($data['id']))
            $this->restReturn(array(
                'code'    => 1,
                'message' => '请指定操作id',
                'data'    => null
            ));
    	switch ($this->_method)
    	{
            case 'get':
                $this->listComment($data);
                break;
            
            case 'post':
                $this->pubComment($data);
                break;
            
            case 'delete':
                $this->delComment($data);
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

    private function listComment($data){
        $comments = D('comment')->listComment($data['id']);
        $this->restReturn(array(
            'code'    => 0,
            'message' => '评论列表',
            'data'    => $comments
        ));
    }

    private function pubComment($data){
        if(is_null(D('dynamic')->getDynamic($data['id'])))
        {
            $this->restReturn(array(
                'code'    => 1,
                'message' => '该条动态不存在',
                'data'    => false
            ));
        }
        if(empty($data['content']))
        {
            $this->restReturn(array(
                'code'    => 1,
                'message' => '评论内容不能为空',
                'data'    => false
            ));
        }
        if(empty($data['p_comment']) || is_null(D('comment')->getComment($data['p_comment'])))
        {
            $this->restReturn(array(
                'code'    => 1,
                'message' => '该条评论不存在',
                'data'    => false
            ));
        }
        $comment = M('comment')->add(array(
            'd_id'    => $data['id'],
            'u_id'    => $this->data['user']['id'],
            'content' => $data['content'],
            'p_id'    => $data['p_content']
        ));
        $this->restReturn(array(
            'code'    => 0,
            'message' => '评论成功',
            'data'    => $comment
        ));
    }

    private function delComment($data){
        $comment = D('comment')->getComment($data['id']);
        if(is_null($comment) || $comment['u_id'] != $this->data['user']['id'])
        {
            $this->restReturn(array(
                'code'    => 1,
                'message' => '该条评论不存在',
                'data'    => false
            ));
        }
        M('comment')->where('id = %d', $data['id'])
            ->save(array(
                'content' => '--此评论已被删除--',
                'u_id'    => 0
            ));
        $this->restReturn(array(
            'code'    => 0,
            'message' => '删除成功',
            'data'    => true
        ));
    }
}