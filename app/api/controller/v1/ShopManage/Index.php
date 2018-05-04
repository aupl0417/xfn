<?php
/**
 * @title 销售员管理
 * @author aupl
 * */
namespace app\api\controller\v1\ShopManage;

use app\api\controller\v1\Home;
use think\Controller;
use think\Db;
use think\Exception;

class Index extends Home {

    /**
     * 门店管理
     * @return json
     * */
    public function index(){
        $orderModel = model('Shop');
        $data  = $orderModel->getShopData();
        !$data && $this->apiReturn(201);
        $this->apiReturn(200, $data);
    }

    /**
     * 添加门店
     * */
    public function add(){
        unset($this->data['appId'], $this->data['deviceId']);
        $result = $this->validate($this->data, 'AddShop');
        if(true !== $result){
            $this->apiReturn(201, '', $result);
        }

        Db::startTrans();
        try{
            $shopData = [
                's_name'        => $this->data['shopname'],
                's_address'     => $this->data['shopname'],
                's_province'    => $this->data['province'],
                's_city'        => $this->data['city'],
                's_county'      => $this->data['county'],
                's_certificate' => $this->data['certificate'],
                's_county'      => $this->data['county'],
            ];

            $result = Db::name('shop')->insert($shopData);
            if(!$result){
                throw new Exception('添加门店失败');
            }

            $shopId = Db::name('shop')->getLastInsID();

            $sellerData = [
                's_name' => $this->data['username'],
                's_phone' => $this->data['phone'],
                's_createTime' => time(),
                's_shopId' => $shopId,
                's_createId' => $this->data['userId'],
                's_updateTime' => time()
            ];

            $result = Db::name('seller')->insert($sellerData);
            if(!$result){
                throw new Exception('添加店长失败');
            }

            $sellerId = Db::name('seller')->getLastInsID();
            $result = Db::name('shop')->where(['s_id' => $shopId])->update(['s_shopkeeperId' => $sellerId]);
            if($result === false){
                throw new Exception('店长关联门店失败');
            }

            Db::commit();
            $this->apiReturn(200, '', '添加门店成功');
        }catch (Exception $e){
            Db::rollback();
            $this->apiReturn(201, '', '添加门店失败');
        }
    }

    /**
     * 删除门店（同时删除门店下的店长和分销员）
     * @param userId 后台用户ID
     * @param id int 门店ID
     * @return json
     * */
    public function remove(){
        (!isset($this->data['id']) || empty($this->data['id'])) && $this->apiReturn(201, '', 'ID非法');
        $id = $this->data['id'] + 0;

        Db::startTrans();
        try{
            $result = Db::name('shop')->where(['s_id' => $id])->update(['s_isDelete' => 1]);
            if($result === false){
                throw new Exception('删除门店失败');
            }

            $result = Db::name('seller')->where(['s_shopId' => $id])->update(['s_isDelete' => 1]);
            if($result === false){
                throw new Exception('删除门店内的店长及分销员成功');
            }
            Db::commit();
            $this->apiReturn(200, '', '删除成功');
        }catch (Exception $e){
            $this->apiReturn(201, '', '删除失败');
        }
    }


}