<?php
namespace Api\Controller;
use Api\Common\ApiController;
class CommentLikeController extends ApiController {

    private $commentLike;

    public function index()
    {
        if( ! $this->checkToken())
            $this->goLogin();
        if(empty($this->id))
            $this->restReturn(array(
                'code'    => 1,
                'message' => '未指定评论',
                'data'    => null
            ));
        $comment = M('comment')->where('u_id <> 0')->find($this->id);
        if(is_null($comment))
            $this->restReturn(array(
                'code'    => 1,
                'message' => '评论不存在或已被删除',
                'data'    => false
            ));

        $this->commentLike = M('comment_like');
        $u_id = $this->payload['user']['id'];

        switch ($this->_method)
        {
            case 'post':
                $this->like($this->id, $u_id);
                break;

            case 'delete':
                $this->unlike($this->id, $u_id);
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

    private function like($id, $u_id)
    {
        $isset = $this->commentLike->where('c_id = %d AND u_id = %d', $id, $u_id)->count();
        if($isset)
        {
            $this->restReturn(array(
                'code'    => 1,
                'message' => '请勿重复点赞',
                'data'    => null
            ));
        }

        $this->commentLike->add(array(
            'c_id' => $id,
            'u_id' => $u_id
        ));
        $this->restReturn(array(
            'code'    => 0,
            'message' => '点赞成功',
            'data'    => true
        ));
    }

    private function unlike($id, $u_id)
    {
        $isset = $this->commentLike->where('c_id = %d AND u_id = %d', $id, $u_id)->count();
        if( ! $isset)
        {
            $this->restReturn(array(
                'code'    => 1,
                'message' => '您还未点赞',
                'data'    => null
            ));
        }
        
        $this->commentLike->where(array(
            'c_id' => $id,
            'u_id' => $u_id
        ))->delete();
        $this->restReturn(array(
            'code'    => 0,
            'message' => '取消成功',
            'data'    => true
        ));
    }
}