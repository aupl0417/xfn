<?php

/**
 * Created by PhpStorm.
 * User: liaozijie
 * Date: 2018-04-25
 * Time: 9:47
 */
namespace app\api\model;

use think\Db;
use think\Model;

class Order extends Model
{

    protected $table = 'car_order';

    public function getOrderById($id, $field = '*'){
        if(empty($id) || !is_numeric($id)){
            return false;
        }

        return Db::name($this->table)->field($field)->where(['o_id' => $id])->find();
    }

    public function getOrderList($limit = 4){
        $field = 'a_id as id,a_name as name,a_type as type,a_content as content,a_description as description';
        return Db::name($this->table)->field($field)->where(['a_isDelete' => 0])->limit($limit)->select();
    }

    public function getOrderData($where, $page = 1, $pageSize = 10){
        $field = 'o_id as id,o_orderId as orderId,o_cid as cid,b_username as username,o_type as type,b_phone as phone,o_buystyle as buystyle,o_createTime as createTime,o_offerType as offerType,a.c_name as newCar,b.c_name as oldCar';
        $join = [
            ['car c1', 'o_cid=c1.ac_id', 'left'],
            ['car c2', 'o_changeCarId=c2.ac_id', 'left'],
            ['car_info a', 'c1.ac_cid=a.c_id', 'left'],
            ['car_info b', 'c2.ac_cid=b.c_id', 'left'],
            ['user', 'b_id=o_uid', 'left'],
        ];
        $data = Db::name($this->table)->where($where)->field($field)->join($join)->page($page, $pageSize)->order('o_id desc')->select();
        if($data){
            $typeArr = ['全款买车', '分期', '0首付', '1成首付', '其它' ];
            $offerTypeArr = ['平台报价', '平台竞价'];
            foreach($data as $key => &$value){
                $value['buystyle'] = $typeArr[$value['buystyle']];
                $value['offerType'] = $value['offerType'] ? $offerTypeArr[$value['offerType'] - 1] : '';
                $value['createTime'] = date('Y-m-d H:i:s', $value['createTime']);
            }
        }
        return $data;
    }

    public function findOrder($id, $field = '*'){
        if(empty($id) || !is_numeric($id)){
            return false;
        }

        $join = [
            ['user', 'o_uid=b_id', 'left'],
            ['car', 'ac_id=o_cid', 'left'],
            ['car_info', 'ac_cid=c_id', 'left'],
            ['seller', 'seller.s_id=o_sellerId', 'left'],
            ['shop', 'shop.s_id=seller.s_shopId', 'left']
        ];
        return Db::name($this->table)->where(['o_id' => $id])->join($join)->field($field)->find();
    }
}