<?php

/**
 * Created by PhpStorm.
 * User: liaozijie
 * Date: 2018-04-25
 * Time: 9:47
 */
namespace app\api\model;

use think\Cache;
use think\Db;
use think\Model;

class Area extends Model
{

    protected $table = 'area';

    public function getAreaById($id){
        if(empty($id) || !is_numeric($id)){
            return false;
        }

        return Db::name($this->table)->where(['a_id' => $id])->find();
    }

    public function getAreaList($pid = 0){
        $cacheKey = md5('area_list_' . $pid);
        cache($cacheKey, null);
        if(!$data = cache($cacheKey)){
            $field = 'a_id as id,a_name as name,a_code as code,a_parentId as parentId';
            $data   = Db::name($this->table)->where(['a_parentId' => $pid])->field($field)->order('a_code asc')->select();
            cache($cacheKey, $data, 86400);
        }

        return $data;
    }

    public function getCarFamilyByBrandId($brandId){
        if(!$brandId || !is_numeric($brandId)){
            return false;
        }

        $cacheKey = md5('brand_cars_family_' . $brandId);
        if(!$data = cache($cacheKey)){
            $list = Db::name('car_family')->where(['cf_brandId' => $brandId])->field('cf_id as id,cf_name as name,cf_type as type')->select();
            if($list){
                foreach($list as $key => $val){
                    $data[$val['type']][] = $val;
                }
                unset($val);
            }
        }
        return $data;
    }
}