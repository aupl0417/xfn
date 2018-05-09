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

class Seller extends Model
{

    protected $table = 'seller';

    /*
     * 通过分销员ID来获取分销员数据
     * */
    public function getSellerById($id, $field = '*'){
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
    public function getSellerByPid($pid, $field = '*'){
        $cacheKey = md5('get_selle_by_pid_' . $pid . $field);
        cache($cacheKey, null);
        if(!$data = cache($cacheKey)){
            $data = Db::name($this->table)->where(['s_pid' => $pid, 's_isDelete' => 0])->field($field)->order('s_id desc')->select();
            cache($cacheKey, $data, 3600);
        }
        return $data;
    }

    public function isBelongToShop($userId, $shopId){
        $count = Db::name($this->table)->where(['s_shopId' => $shopId + 0, 's_id' => $userId + 0])->count();
        return $count ? true : false;
    }

    /*
     * 查询一条记录
     * */
    public function findSeller($where, $field = '*'){
        if(!$where || !is_array($where)){
            return false;
        }

        return Db::name('seller')->where($where)->field($field)->find();
    }

    public function findSellerAll($where = array(), $whereOr = array(), $field = '*'){
        return Db::name('seller')->where($where)->whereOr($whereOr)->field($field)->select();
    }

    public function getSellerList($where, $field = '', $page = 1, $pageSize = 10){
        if($field == '' || $field == '*'){
            $field = 's_id as id,s_name as name,s_phone as phone,s_type as type,s_createTime as createTime';
        }
        $data = Db::name($this->table)->where($where)->field($field)->page($page, $pageSize)->select();
        if($data){
            $typeArr = ['店长', '分销员', '销售员'];
            foreach($data as $key => &$value){
                if(isset($value['type'])){
                    $value['type'] = $typeArr[$value['type']];
                }
                $value['createTime'] = date('Y-m-d H:i:s', $value['createTime']);
            }
        }
        return $data;
    }
    
}