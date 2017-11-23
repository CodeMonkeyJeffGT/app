<?php
namespace Api\Common;
use Think\Controller\RestController;
class ApiController extends RestController {

    //全局secret，部署状态下勿修改
	private $secret = 'jo0iUPHOJDFPJ90u9F9jpojFEUJ3';

    //token有效时长，默认为7d
    protected $expire_dor  = 7 * 86400;

    //token的header
    protected $header      = '';
    //token的payload
    protected $payload     = array();

    //参数数组
    protected $data        = array();
    //url中指定的id
    protected $id          = 0;

    public function __construct()
    {
    	parent::__construct();
        date_default_timezone_set('PRC');
        header('Access-Control-Allow-Origin:*');
        header('Access-Control-Allow-Headers:X-Requested-With');
        header('Access-Control-Allow-Methods:PUT,POST,GET,DELETE,OPTIONS');
        header('X-Powered-By: 3.2.1');

        $this->id   = (int)I('get.id', 0);
        unset($_GET['id']);
        switch ($this->_method)
        {
            case 'get':
                $this->data = I('get.');
                break;
            
            default:
                $this->data = json_decode(@file_get_contents('php://input'), true);
                break;
        }
        //利用checkToken方法填充header和payload
        $this->checkToken();
    }

    protected function checkToken()
    {
    	//获取token并验证有效性
    	$token = I('server.Authorization');
    	if(empty($token) || count($token = explode('.', $token)) !== 3)
    	{
    		return false;
    	}

        list($this->header, $this->payload, $signature) = $token;
    	if(hash_hmac('sha256', $this->header . '.' . $this->payload, $this->secret) !== $signature)
    	{
    		return false;
    	}

    	$this->header = json_decode(base64_decode($token[0]), true);
    	$this->payload = json_decode(base64_decode($token[1]), true);

    	if($this->payload['expire'] < time())
    	{
    		return false;
    	}

    	if( ! isset($this->payload['user']['id']) || ! is_numeric($this->payload['user']['id']))
    	{
    		return false;
    	}
        return true;
    }

    protected function goLogin()
    {
        $this->payload = array();
		$this->restReturn(array(
            'code'    => '2',
            'message' => '请登录',
            'data'    => null
		));
    }

    protected function restReturn($data)
    {
        $data['Authorization'] = '';
        if( ! empty($this->payload))
        {
            //生成token并放入data
            if(empty($this->header))
            {
                $this->header = array(
                    'typ' => 'JWT',
                    'alg' => 'HS256'
                );
            }
            $this->payload['expire'] = time() + $this->expire_dor;
            $this->header            = base64_encode(json_encode($this->header));
            $this->payload           = base64_encode(json_encode($this->payload));
            $prev                    = $this->header . '.' . $this->payload;
            $signature = hash_hmac('sha256', $prev, $this->secret);
            $data['Authorization'] = $prev . '.' . $signature;
        }
        full_url($data, 'headImgUrl');
        full_url($data, 'url');
        
    	$this->response($data, $this->_type);
    }

    // public function __destruct()
    // {
    //     if($this->send_token && ! empty($this->payload))
    //     {
    //         //生成token并放入header
    //         if(empty($this->header))
    //         {
    //             $this->header = array(
    //                 'typ' => 'JWT',
    //                 'alg' => 'HS256'
    //             );
    //         }
    //         $this->payload['expire'] = time() + $this->expire_dor;
    //         $this->header            = base64_encode(json_encode($this->header));
    //         $this->payload           = base64_encode(json_encode($this->payload));
    //         $prev                    = $this->header . '.' . $this->payload;
    //         $signature = hash_hmac('sha256', $prev, $this->secret);
    //         header('Authorization:' . $prev . '.' . $signature);
    //     }
    //     parent::__destruct();
    // }

}