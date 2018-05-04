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

class Activity extends Model
{

    protected $table = 'Activity';

    public function getActivityById($id){
        if(empty($id) || !is_numeric($id)){
            return false;
        }

        return Db::name($this->table)->field('')->where(['a_id' => $id])->find();
    }

    public function getActivityList($limit = 4){
        $field = 'a_id as id,a_name as name,a_type as type,a_content as content,a_description as description';
        return Db::name($this->table)->field($field)->where(['a_isDelete' => 0])->limit($limit)->select();
    }

    public function getActivityData($where, $field = '*', $page = 1, $pageSize = 10){
        $data = Db::name($this->table)->where($where)->field($field)->page($page, $pageSize)->select();
        if($data){
            $typeArr = ['海报', '视频'];
            foreach($data as $key => &$value){
                $value['type'] = $typeArr[$value['type']];
                $value['createTime'] = date('Y-m-d H:i:s', $value['createTime']);
            }
        }
        return $data;
    }
}