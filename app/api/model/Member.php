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

class Member extends Model
{

    protected $table = 'member';

    /*
     * 通过分销员ID来获取分销员数据
     * */
    public function getMemberById($id, $field = '*'){
        $cacheKey = md5('get_member_by_id_' . $id . $field);
        if(!$data = cache($cacheKey)){
            if(empty($id) || !is_numeric($id)){
                return false;
            }

            $data = Db::name($this->table)->field($field)->where(['id' => $id])->find();
            cache($cacheKey, $data, 3600);
        }
        return $data;
    }
    
}