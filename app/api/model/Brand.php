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

class Brand extends Model
{

    protected $table = 'car_brand';

    public function getActivityById($id){
        if(empty($id) || !is_numeric($id)){
            return false;
        }

        return Db::name($this->table)->field('')->where(['a_id' => $id])->find();
    }

    public function getBrandList(){
        $cacheKey = md5('brand_list');
        if(!$data = cache($cacheKey)){
            $field = 'cb_id as id,cb_name as name,cb_code as code,cb_initial as initial,cb_image as image';
            $res   = Db::name($this->table)->field($field)->order('cb_initial asc')->select();
            if($res){
                foreach($res as $key => $val){
                    $data[$val['initial']][] = $val;
                }
                unset($res, $val);
            }
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
                unset($val, $list);
            }
        }

        return $data;
    }
}