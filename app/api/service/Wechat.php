<?php
namespace app\api\service;

use think\Cache;
use think\Config;
use think\Controller;
use think\Model;

class Wechat extends Model {

    protected $apiUrl  = 'https://api.weixin.qq.com/';//接口统一地址
    protected $url     = 'https://api.weixin.qq.com/cgi-bin/';//授权地址
    protected $openUrl = 'https://open.weixin.qq.com/connect/oauth2/authorize?';//授权
    protected $tokenCodeUrl = 'https://api.weixin.qq.com/sns/oauth2/access_token?';//获取access_token
    protected $getUserUrl   = 'https://api.weixin.qq.com/sns/userinfo?';//授权获取用户信息
    protected $domain;
    protected $appId;
    protected $secret;
    protected $openid = '';

    public function __construct()
    {
        parent::__construct(); // TODO: Change the autogenerated stub
        $this->domain = $_SERVER['HTTP_HOST'];
        $appConfigKey = str_replace('.', '_', $this->domain);
        $this->appId  = Config::get($appConfigKey . '.appID');
        $this->secret = Config::get($appConfigKey . '.appsecret');
    }

    public function index(){
        $url = $this->oauth2();
//        $this->assign('url', $url);
//        return $this->fetch();
    }

    /**
     * 获取access_token 保存7200秒
     * */
    public function getAccessToken(){
        $cacheKey = md5('access_token');
        cache($cacheKey, null);
        if(!$data = cache($cacheKey)){
            $url = $this->url . 'token?';
            $params = [
                'grant_type' => 'client_credential',
                'appid'       => $this->appId,
                'secret'      => $this->secret,
            ];

            $result = curl_get($url . http_build_query($params));
            $result = json_decode($result, true);
            if(!isset($result['errcode'])){
                $data = $result['access_token'];
                cache($cacheKey, $data, $result['expires_in']);
            }
        }

        return $data;
    }

    public function oauth2($redirect_url, $state = ''){
        $params = [
            'appid'          => $this->appId,
            'redirect_uri'   => $redirect_url,//'http://1.aupl.applinzi.com/public/index.php/wx/Home/getCode',
            'response_type'  => 'code',
            'scope'          => 'snsapi_userinfo',//'snsapi_base',
            'state'          => $state ?: $this->getRandString(16),
        ];
//        dump($params);die;
        $url = $this->openUrl . http_build_query($params) . '#wechat_redirect';
        return $url;
    }

    /*public function getCode(){
        $code = input('code', '', 'htmlspecialchars');
        $accessToken = $this->getAccessTokenByCode($code);
        $userInfo    = $this->getUser($accessToken, $this->openid);
        dump($userInfo);
    }*/

    public function getAccessTokenByCode($code = ''){
        $params = [
            'appid'         => $this->appId,
            'secret'        => $this->secret,
            'code'          => $code,
            'grant_type'    => 'authorization_code'
        ];

        $result = curl_get($this->tokenCodeUrl . http_build_query($params));
        $result = json_decode($result, true);

        if(isset($result['access_token'])){
            return $result;
        }

        return false;
    }

    public function getUser($accessToken, $openId){

        $params = [
            'access_token' => $accessToken,
            'openid'       => $openId,
            'lang'         => 'zh_CN'
        ];

        $result = curl_get($this->getUserUrl . http_build_query($params));
        $result = json_decode($result, true);
        if(isset($result['errcode'])){
            return false;
        }

        return $result;
    }

    public function getRandString($length = 32){
        $string = 'abcdefghijklmnpqrstuvwxyzABCDEFGHIJKLMNPQRSTUVWXYZ23456789';
        return substr(str_shuffle($string), 0, $length);
    }

    public function qcode($id){
        $token = $this->getAccessToken();
        $url = 'https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token=' . $token;
        $params = [
            'expire_seconds' => 604800,
            'action_name'    => 'QR_SCENE',
            'action_info'    => ['scene' => ['scene_id' => $id]],
            'access_token' => $token,
        ];
        $result = curl_post($url, json_encode($params));
        $result = json_decode($result, true);
        if(!$result['ticket']){
            return false;
        }

        $ticketUrl = 'https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket='. urlencode($result['ticket']);
        return $ticketUrl;
    }


    public function getUserInfo($accessToken, $openId){
        $url = 'https://api.weixin.qq.com/cgi-bin/user/info?access_token=' . $accessToken . '&openid=' . $openId . '&lang=zh_CN';
        $userInfo = curl_get($url);
        $userInfo = json_decode($userInfo, true);
//        if(!$userInfo || isset($userInfo['errcode'])){
//            return false;
//        }
        return $userInfo;
    }
}