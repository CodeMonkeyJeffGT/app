<?php
namespace Api\Controller;
use Api\Common\ApiController;
class CommentsController extends ApiController {

    private $comment;

    public function index()
    {
        if(empty($this->id))
        {
            $this->restReturn(array(
                'code'    => 1,
                'message' => '请指定操作id',
                'data'    => null
            ));
        }

        $this->comment = D('comment');

        switch ($this->_method)
        {
            case 'get':
                $this->listComment($this->id);
                break;
            
            case 'post':
                $u_id = $this->payload['user']['id'];
                $this->pubComment($this->id, $u_id, $this->data);
                break;
            
            case 'delete':
                $u_id = $this->payload['user']['id'];
                $this->delComment($this->id, $u_id);
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

    private function listComment($d_id){
        if(empty(D('dynamic')->getDynamic($d_id)))
        {
            $this->restReturn(array(
                'code'    => 1,
                'message' => '该条动态不存在',
                'data'    => false
            ));
        }
        $u_id = $this->checkToken() ? $this->payload['user']['id'] : 0;
        $comments = $this->comment->listComment($d_id, $u_id);
        foreach ($comments as $key => $value) {
            $comments[$key]['content'] = base64_decode($value['content']);
        }
        $this->restReturn(array(
            'code'    => 0,
            'message' => '评论列表',
            'data'    => $comments
        ));
    }

    private function pubComment($d_id, $u_id, $data){
        if(is_null(D('dynamic')->getDynamic($d_id)))
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
        if( ! empty($data['p_comment']) && is_null($this->comment->getComment($data['p_comment'])))
        {
            $this->restReturn(array(
                'code'    => 1,
                'message' => '该条评论不存在',
                'data'    => false
            ));
        }
        if(empty($data['p_comment']))
            $data['p_comment'] = 0;
        $comment = $this->comment->add(array(
            'd_id'     => $d_id,
            'u_id'     => $u_id,
            'content'  => base64_encode($data['content']),
            'p_id'     => $data['p_comment'],
            'pub_time' => time()
        ));
        $this->restReturn(array(
            'code'    => 0,
            'message' => '评论成功',
            'data'    => $comment
        ));
    }

    private function delComment($id, $u_id){
        $comment = $this->comment->getComment($id);
        if(is_null($comment))
        {
            $this->restReturn(array(
                'code'    => 1,
                'message' => '该条评论不存在',
                'data'    => false
            ));
        }
        if($comment['u_id'] == 0)
        {
            $this->restReturn(array(
                'code'    => 1,
                'message' => '该评论已被删除',
                'data'    => false
            ));
        }
        if($comment['u_id'] != $this->payload['user']['id'])
        {
            $this->restReturn(array(
                'code'    => 1,
                'message' => '您不是该评论的发布者',
                'data'    => false
            ));
        }
        $this->comment->where('id = %d', $id)
            ->save(array(
                'content' => base64_encode('--此评论已被删除--'),
                'u_id'    => 0
            ));
        $this->restReturn(array(
            'code'    => 0,
            'message' => '删除成功',
            'data'    => true
        ));
    }
}