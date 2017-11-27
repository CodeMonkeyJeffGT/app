<?php
namespace Api\Controller;
use Api\Common\ApiController;
class DynamicsController extends ApiController {

    private $dynamics;

    public function index($id = 0)
    {
        $this->dynamics = D('dynamic');

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

        $u_id     = 0;
        if($type == 'like')
        {
            if( ! $this->checkToken())
                $this->goLogin();
            $u_id = $this->payload['user']['id'];
        }
        else
        {
            if($this->checkToken())
                $u_id = $this->payload['user']['id'];
        }

        $dynamics = $this->dynamics->listDynamics($offset, $limit, $u_id);
        $this->restReturn(array(
            'code'    => 0,
            'message' => '',
            'data'    => $dynamics
        ));
    }

    private function getDs($id)
    {
        $u_id     = 0;
        if($type == 'like')
        {
            if( ! $this->checkToken())
                $this->goLogin();
            $u_id = $this->payload['user']['id'];
        }
        else
        {
            if($this->checkToken())
                $u_id = $this->payload['user']['id'];
        }

        $dynamic = $this->dynamics->getDynamic($id, $u_id);
        if(empty($dynamic))
        {
            $this->restReturn(array(
                'code'    => 1,
                'message' => '该动态不存在',
                'data'    => false
            ));
        }
        if(empty($dynamic['headImgUrl']))
        {
            $this->restReturn(array(
                'code'    => 1,
                'message' => '该动态已被删除',
                'data'    => false
            ));
        }
        $this->restReturn(array(
            'code'    => 0,
            'message' => '',
            'data'    => $dynamic
        ));
    }

    private function publish($data)
    {
        $u_id = $this->payload['user']['id'];
        if(empty($data['content']))
            $this->restReturn(array(
                'code'    => 1,
                'message' => '内容不能为空',
                'data'    => false
            ));
        $content = $data['content'];
        $content = base64_encode($content);
        $pubTime = time();

        if( ! isset($data['img']))
            $data['img'] = array();

        $img = $data['img'];
        if( ! empty($img))
        {
            $img = implode(', ', $img);
            $repeat = M('img')->where('id IN (%s) AND d_id != 0', $img)->count();
            if($repeat != 0)
            {
                $this->restReturn(array(
                    'code'    => 1,
                    'message' => '图片已被使用，请重新上传',
                    'data'    => false
                ));
            }
        }

        $id = $this->dynamics->add(array(
            'content'  => $content,
            'u_id'     => $u_id,
            'pub_time' => $pubTime
        ));

        if( ! empty($img))
        {
            $sql = 'UPDATE `img` SET `d_id` = %d WHERE `id` IN (%s)';
            M()->query($sql, $id, $img);
        }
        $this->restReturn(array(
            'code'    => 0,
            'message' => '发布成功',
            'data'    => array(
                'id' => $id
            )
        ));
    }

    // private function edit($id, $data)
    // {
    // }


    private function delete($id)
    {
        if($id == 0)
        {
            $this->restReturn(array(
                'code'    => 1,
                'message' => '底层数据无法删除',
                'data'    => false
            ));
        }

        $u_id = $this->payload['user']['id'];
        $dynamic = $this->dynamics->field('u_id')->find($id);
        if(empty($dynamic))
            $this->restReturn(array(
                'code'    => 1,
                'message' => '该动态不存在',
                'data'    => false
            ));
        if($dynamic['u_id'] == 0)
            $this->restReturn(array(
                'code'    => 1,
                'message' => '该动态已被删除',
                'data'    => false
            ));
        if($dynamic['u_id'] != $u_id)
            $this->restReturn(array(
                'code'    => 1,
                'message' => '您不是该动态的发布者',
                'data'    => false
            ));

        $this->dynamics->where('`id` = %d', $id)->save(array(
            'u_id'    => 0,
            'content' => ''
        ));
        M('img')->where('d_id = %d', $id)->delete();

        $this->restReturn(array(
            'code'    => 0,
            'message' => '删除成功',
            'data'    => true
        ));
    }
}