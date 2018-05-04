<?php
/**
 * Created by PhpStorm.
 * User: liaozijie
 * Date: 2018-04-25
 * Time: 9:28
 */

namespace app\api\controller\v1\Screen;

use app\api\controller\v1\Base;
use app\api\service\Wechat;
use think\Controller;
use think\Db;
use think\Exception;

class Scan extends Base
{

    /**
     * 微信扫码（包括关注）后，通过openid获取用户信息并保存到数据库
     * @param id 商机记录ID
     * @param openid 微信用户的openid
     * @return json
     * */
    public function callback(){
        (!isset($this->data['id']) || empty($this->data['id'])) && $this->apiReturn(201, '', '商机ID不能为空');
        (!isset($this->data['openid']) || empty($this->data['openid'])) && $this->apiReturn(201, '', 'OPENID不能为空');


        $id          = $this->data['id'] + 0;
        $openid      = $this->data['openid'];

        $service     = new Wechat();
        $accessToken = $service->getAccessToken();
        $userInfo    = $service->getUserInfo($accessToken, $openid);
        !$userInfo && $this->apiReturn(201, '', '获取用户信息失败');

        Db::startTrans();
        try{
            $wuser = Db::name('wechat_info')->where(['w_nickname' => $userInfo['nickname'], 'w_openid' => $userInfo['openid']])->field('w_id')->find();
            if(!$wuser){
                $data = [
                    'w_nickname'      => $userInfo['nickname'],
                    'w_sex'           => $userInfo['sex'],
                    'w_openid'        => $userInfo['openid'],
                    'w_city'          => $userInfo['city'],
                    'w_province'      => $userInfo['province'],
                    'w_country'       => $userInfo['country'],
                    'w_headImgurl'    => $userInfo['headimgurl'],
                    'w_subscribe'     => $userInfo['subscribe'],
                    'w_uninid'        => $userInfo['unionid'],
                    'w_subscribeTime' => $userInfo['subscribe_time'],
                ];
                $result    = Db::name('wechat_info')->insert($data);
                if(!$result){
                    throw new \Exception('添加微信用户信息失败');
                }
                $wId = Db::name('wechat_info')->getLastInsID();
            }else{
                $wId = $wuser['w_id'];
            }

            $result = Db::name('business_opportunity')->where(['bo_id' => $id])->update(['bo_wid' => $wId, 'bo_state' => 0]);
            if($result === false){
                throw new \Exception('更新用户到商机表失败');
            }

            Db::commit();
            $this->apiReturn(200);
        }catch (\Exception $e){
            $this->apiReturn(201, '', $e->getMessage());
        }
    }

    /**
     * 扫码后，用于一直请求该商机记录，用户是否扫码成功
     * @param id 商机ID
     * @return json
     * */
    public function state(){
        (!isset($this->data['id']) || empty($this->data['id'])) && $this->apiReturn(201, '', '商机ID不能为空');

        $result = model('BusinessOpportunity')->getDataById($this->data['id'] + 0, 'bo_wid,bo_state');
        !$result && $this->apiReturn(201);

        (!$result['bo_wid'] || $result['bo_state'] == -1) && $this->apiReturn(201);
        $this->apiReturn(200);
    }

}