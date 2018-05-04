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

class Shop extends Model
{

    protected $table = 'shop';

    public function getShopById($id, $field = '*'){
        if(empty($id) || !is_numeric($id)){
            return false;
        }

        return Db::name($this->table)->field($field)->where(['s_id' => $id])->find();
    }

    public function getShopData($where = array(), $field = '', $page = 1, $pageSize = 10){
        $field = $field ?: 'sh.s_id as id,sh.s_name as name,sh.s_address as address,se.s_name as username,se.s_phone as phone';
        $data = Db::name('shop sh')->where($where)
              ->field($field)
              ->join('seller se', 'se.s_id=sh.s_shopkeeperId', 'left')
              ->page($page, $pageSize)
              ->order('sh.s_id desc')->select();

        return $data ? $data : false;
    }
}