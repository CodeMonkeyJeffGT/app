<?php
namespace Api\Controller;
use Api\Common\ApiController;
class DynamicLikeController extends ApiController {

    private $dynamicLike;

    public function index()
    {
        if(empty($this->id))
            $this->restReturn(array(
                'code'    => 1,
                'message' => '未指定动态',
                'data'    => null
            ));
        $dnmc = M('dynamic')->where('u_id <> 0')->find($this->id);
        if(is_null($dnmc))
            $this->restReturn(array(
                'code'    => 1,
                'message' => '动态不存在或已被删除',
                'data'    => false
            ));

        $this->dynamicLike = M('dynamic_like');
        $u_id = $this->payload['user']['id'];

    	switch ($this->_method)
    	{
            case 'get':
                $this->list($this->id);
                break;

    		case 'post':
                if( ! $this->checkToken())
                    $this->goLogin();
                $this->like($this->id, $u_id);
    			break;

            case 'delete':
                if( ! $this->checkToken())
                    $this->goLogin();
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

    private function list($id)
    {
        $likes = $this->dynamicLike->query('
            SELECT `user`.`head_img_url` `head_img_url`, `user`.`nickname` `nickname`, `user`.`id` `id`
            FROM `dynamic_like`, `user`
            WHERE `dynamic_like`.`d_id` = %d AND `dynamic_like`.`u_id` = `user`.`id`
        ', $id);
        $likes = line_to_up($likes);
        $this->restReturn(array(
            'code'    => 0,
            'message' => '',
            'data'    => $likes
        ));
    }

    private function like($id, $u_id)
    {
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

    private function unlike($id, $u_id)
    {
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