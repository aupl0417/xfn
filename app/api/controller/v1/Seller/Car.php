<?php
/**
 * Created by PhpStorm.
 * User: liaozijie
 * Date: 2018-04-25
 * Time: 9:28
 */

namespace app\api\controller\v1\Seller;

use app\api\controller\v1\Home;
use think\Controller;
use think\Db;
use think\Exception;

class Car extends Home
{

    /*
     * 车辆信息采集
     * */
    public function collection(){
        unset($this->data['appId'], $this->data['deviceId']);

        $data = array();
        foreach($this->data as $key => $value){
            $data['o_' . $key] = $value;
        }

        $result = $this->validate($data, 'CarInfoCollect');
        if(true !== $result){
            $this->apiReturn(201, '', $result);
        }

        $data['o_orderId']    = makeOrder();
        $data['o_createTime'] = time();
        $data['o_state']      = -1;

        $result = Db::name('car_order')->insert($data);
        !$result && $this->apiReturn(201, '', '车辆信息采集失败');
        $insertId = Db::name('car_order')->getLastInsID();

        $model = model('Car');
        $field = 'c_name as name,c_introPrice as price';
        $carInfo = $model->getCarById($data['o_cid'], $field);
        if($carInfo){
            $data['newCar'] = $carInfo;
        }

        if($data['o_type'] == 1){
            $field = 'c_name as name,c_introPrice as price';
            $data['oldCar'] = $model->getCarById($data['o_changeCarId'], $field);
        }

        $user = model('Buyer')->getBuyerById($data['o_uid'], 'b_username as username,b_phone as phone');
        $data = array_merge($data, $user);
        $trimColor = Db::name('car_trim')->where(['ct_id' => $data['o_trim']])->field('ct_name')->find();
        $data['trimColor'] = $trimColor ? $trimColor['ct_name'] : '';
        $deliveryArray = ['1-7天', '8-15天', '16-30天', '30天以上'];
        $boutiqueArray = ['不带精品', '加精品提车', '加精金额'];
        $data['o_deliveryTime'] = $deliveryArray[$data['o_deliveryTime'] - 1];
        $data['o_boutique']     = $boutiqueArray[$data['o_boutique'] - 1];
        $data = array_merge(['o_id' => $insertId], $data);

        $this->apiReturn(200, $data);
    }


    public function carInfo(){
        unset($this->data['appId'], $this->data['deviceId']);
        if($this->data['type'] != 2){
            $this->apiReturn(201, '', '产品类型ID非法');
        }

        Db::startTrans();
        try{
            $userData = [
                'b_username' => $this->data['username'],
                'b_phone'    => $this->data['phone']
            ];
            unset($this->data['username'], $this->data['phone']);
            $result = $this->validate($userData, 'AddUser.create');
            if(true !== $result){
                throw new Exception($result);
            }

            $userData['b_createTime'] = time();
            $result = Db::name('user')->insert($userData);
            if(!$result){
                throw new Exception('添加用户信息失败');
            }

            $userId = Db::name('user')->getLastInsID();

            $data = array();
            foreach($this->data as $key => $value){
                $data['o_' . $key] = $value;
            }

            $result = $this->validate($data, 'CarInfoCollect.addSeveral');

            if(true !== $result){
                throw new Exception($result);
            }

            $data['o_uid']        = $userId;
            $data['o_orderId']    = makeOrder();
            $data['o_createTime'] = time();
            $data['o_state']      = -1;

            $result = Db::name('car_order')->insert($data);
            if(!$result){
                throw new Exception('客户信息提交失败');
            }

            $insertId = Db::name('car_order')->getLastInsID();
            $data  = array('id' => $insertId, 'uid' => $userId);
            $data  = array_merge($data, input(''));
            $model = model('Car');
            $field = 'c_name as name,c_introPrice as price';
            $carInfo = $model->getCarById($data['cid'], $field);
            unset($data['appId'], $data['deviceId'], $data['token']);
            $data['offerType'] = $data['offerType'] == 1 ? '平台报价' : '平台竞价';
            $data = array_merge($data, $carInfo);

            Db::commit();
            $this->apiReturn(200, $data);
        }catch (\Exception $e){
            Db::rollback();
            $this->apiReturn(201, '', $e->getMessage());
        }
    }

    /*
     * 提交报价信息
     * */
    public function order(){
        unset($this->data['appId'], $this->data['deviceId']);

        $data = array();
        foreach($this->data as $key => $value){
            $data['o_' . $key] = $value;
        }

        $result = $this->validate($data, 'MakeOrder');
        if(true !== $result){
            $this->apiReturn(201, '', $result);
        }

        if($data['o_type'] != 1){
            $data['o_offerType'] = 0;
        }

        $data['o_updateTime'] = time();
        $data['o_state'] = 0;
        unset($data['o_type']);
        $result = Db::name('car_order')->update($data);
        $result === false && $this->apiReturn(201, '', '提交报价失败');
        $this->apiReturn(200, '', '提交成功');
    }
    
}