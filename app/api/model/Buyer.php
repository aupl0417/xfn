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

class Buyer extends Model
{

    protected $table = 'user';

    public function getBuyerById($id, $field = '*'){
        if(empty($id) || !is_numeric($id)){
            return false;
        }
        $cacheKey = md5('get_buyer_by_id_' . $id . $field);
        if(!$data = cache($cacheKey)){
            $data = Db::name($this->table)->field($field)->where(['b_id' => $id])->find();
            cache($cacheKey, $data, 3600);
        }

        return $data;
    }
    
}