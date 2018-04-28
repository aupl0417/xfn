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

class Car extends Model
{

    protected $table = 'car';

    public function getCarById($id, $field = '*'){
        if(empty($id) || !is_numeric($id)){
            return false;
        }

        return Db::name('car c')->field($field)->join('car_info ci', 'ci.c_id=c.ac_cid', 'left')->where(['ac_id' => $id, 'ac_isDelete' => 0])->find();
    }

    /*
     * 现车专区：1  二手车专区：2
     * */
    public function getCarListByType($type = 1, $field = '*', $limit = 10){
        $where['ac_isDelete'] = 0;
        if($type == 1){
            $where['ac_hasCar'] = 1;
        }else if($type == 2){
            $where['ac_isNewCar'] = 1;
        }

        return Db::name('car c')
            ->field($field)->where($where)
            ->join('car_info ci', 'c.ac_id=ci.c_id')->select();
    }

    public function getCarByFamilyId($familyId, $field = '*'){
        if(empty($familyId) || !is_numeric($familyId)){
            return false;
        }

        $cacheKey = md5('get_car_by_family_id_' . $familyId . $field);
        if(!$data = cache($cacheKey)){
            $data = Db::name('car_info ci')->field($field)
                ->join('car c', 'ci.c_id=c.ac_cid', 'left')
                ->where(['c_familyId' => $familyId, 'ac_isDelete' => 0])
                ->order('ac_id desc')
                ->select();
            cache($cacheKey, $data, 3600);
        }
        return $data;
    }
}