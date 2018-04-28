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

class BusinessOpportunity extends Model
{

    protected $table = 'business_opportunity';

    /*
     * 通过分销员ID来获取分销员数据
     * */
    public function getDataById($id, $field = '*'){
        $cacheKey = md5('get_seller_by_id_' . $id . $field);
        if(!$data = cache($cacheKey)){
            if(empty($id) || !is_numeric($id)){
                return false;
            }

            $data = Db::name($this->table)->field($field)->where(['s_id' => $id, 's_isDelete' => 0])->find();
            cache($cacheKey, $data, 3600);
        }
        return $data;
    }

    /*
     * 通过店长ID来获取分销员数据
     * */
    public function getDataList($where = array(), $field = '', $page = 1, $pageSize = 10){
        if(!$field){
            $field = 'bo_id as id,bo_phone as phone,bo_createTime as createTime,bo_type as type,bo_state as state,w_nickname as nickname,s_name as shopName,a.c_name as newCar,b.c_name as oldCar';
        }
        $cacheKey = md5('get_business_by_shopId_' . serialize($where) . $field);
        cache($cacheKey, null);
        if(!$data = cache($cacheKey)){
            $join = [
                ['wechat_info', 'bo_wid=w_id', 'left'],
                ['car c1', 'bo_cid=c1.ac_id', 'left'],
                ['car c2', 'bo_changeCarId=c2.ac_id', 'left'],
                ['car_info a', 'c1.ac_cid=a.c_id', 'left'],
                ['car_info b', 'c2.ac_cid=b.c_id', 'left'],
                ['shop', 'bo_shopId=s_id', 'left'],
            ];
            $data = Db::name($this->table)->where($where)->page($page, $pageSize)->field($field)->join($join)->order('bo_id desc')->select();
            if($data){
                foreach($data as $key => &$value){
                    $value['createTime'] = date('Y-m-d', $value['createTime']);
                }
            }
            cache($cacheKey, $data, 30);
        }
        return $data;
    }
    
}