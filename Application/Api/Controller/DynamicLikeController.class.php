<?php
namespace Api\Controller;
use Api\Common\ApiController;
class DynamicLikeController extends ApiController {

    private $dynamicLike;

    public function __construct()
    {
        parent::__construct();
        $this->dynamicLike = M('dynamic_like');

    }

    public function index()
    {
        if( ! $this->checkToken())
            $this->goLogin();
        if(empty($this->id))
            $this->restReturn(array(
                'code'    => 1,
                'message' => '未指定动态',
                'data'    => null
            ));
        $id = $this->id;
        $dnmc = M('dynamic')->where('u_id <> 0')->find($id);
        if(is_null($dnmc))
            $this->restReturn(array(
                'code'    => 1,
                'message' => '动态不存在或已被删除',
                'data'    => false
            ));
    	switch ($this->_method)
    	{
    		case 'post':
                $this->like($this->id);
    			break;

            case 'delete':
                $this->unlike($this->id);
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

    private function like($id)
    {
        $u_id = $this->payload['user']['id'];
        $isset = $this->dynamicLike->where('d_id = %d AND u_id = %d', $id, $u_id)->count();
        if($isset)
        {
            $this->restReturn(array(
                'code'    => 1,
                'message' => '请勿重复点赞',
                'data'    => null
            ));
        }
        $this->dynamicLike->add(array(
            'd_id' => $id,
            'u_id' => $u_id
        ));
        $this->restReturn(array(
            'code'    => 0,
            'message' => '点赞成功',
            'data'    => true
        ));
    }

    private function unlike($id)
    {
        $u_id = $this->payload['user']['id'];
        $isset = $this->dynamicLike->where('d_id = %d AND u_id = %d', $id, $u_id)->count();
        if( ! $isset)
        {
            $this->restReturn(array(
                'code'    => 1,
                'message' => '您还未点赞',
                'data'    => null
            ));
        }
        $this->dynamicLike->where(array(
            'd_id' => $id,
            'u_id' => $u_id
        ))->delete();
        $this->restReturn(array(
            'code'    => 0,
            'message' => '取消成功',
            'data'    => true
        ));
    }
}