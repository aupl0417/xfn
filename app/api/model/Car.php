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

    public function getCarList($where, $field = '*', $page = 1, $pageSize = 10){
        $data = Db::name($this->table)->where($where)->field($field)->join('car_info', 'ac_cid=c_id', 'left')->page($page, $pageSize)->select();
        if($data){
            foreach($data as $key => &$value){
                if(isset($value['createTime'])){
                    $value['createTime'] = date('Y-m-d H:i:s', $value['createTime']);
                }
            }
        }
        
        return $data;
    }

    public function getCarInfoById($id){
        if(empty($id) || !is_numeric($id)){
            return false;
        }

        $cacheKey = md5('get_car_paremeter_by_car_info_id_' . $id);
        if(!$data = cache($cacheKey)){
            $field = 'cpd_type code,cpd_name name,cp_value value';
            $where = [
                'cp_cid' => $id,
                'cpd_type' => array('in', [1, 2])
            ];

            $parameters = Db::name('car_parameter c')->field($field)->join('car_parameter_detail', 'cp_pid = cpd_id', 'left')->where($where)->select();
            if($parameters){
                $attr = Db::name('dictionary')->where(['d_typeid' => 8])->field('d_key as id,d_value as value')->order('d_sort asc')->select();
                $attr = array_column($attr, 'value', 'id');
                foreach($parameters as $key => $value){
                    $data[$attr[$value['code']]][] = ['name' => $value['name'], 'value' => $value['value']];
                }
                cache($cacheKey, $data, 3600);
				unset($attr, $parameters, $value);
            }
        }

        return $data;
    }
}