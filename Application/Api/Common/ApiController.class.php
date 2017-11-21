<?php
namespace Api\Common;
use Think\Controller\RestController;
class ApiController extends RestController {

	private $secret = 'jo0iUPHOJDFPJ90u9F9jpojFEUJ3';  //全局secret，部署状态下勿修改

    protected $check_token = true;      //是否自动验证token有效性
    protected $send_token  = true;      //是否自动发送token
    protected $expire_dor  = 7200;      //token有效时长，默认为2h/7200s

    protected $header      = '';        //token的header
    protected $payload     = array();   //token的payload

    public function __construct()
    {
    	parent::__construct();
        date_default_timezone_set('PRC');
        //如果需要验证并且token无效
        if($this->check_token && ! $this->checkToken())
    	   $this->goLogin();
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
        $this->send_token = false;
		header('Authorization:');
		$this->restReturn(array(
            'code'    => '2',
            'message' => '请登录',
            'data'    => null
		));
    }

    protected function restReturn($data)
    {
    	$this->response($data, $this->_type);
    }

    public function __destruct()
    {
        if($this->send_token)
        {
            //生成token并放入header
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
            header('Authorization:' . $prev . '.' . $signature);
        }
        parent::__destruct();
    }

}