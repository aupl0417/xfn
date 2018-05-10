<?php
/**
 * Created by PhpStorm.
 * User: liaozijie
 * Date: 2018-04-25
 * Time: 9:28
 */

namespace app\api\controller\v1\Seller;

use app\api\controller\v1\Home;
use think\Controller;
use think\Db;
class User extends Home
{

    /**
     * 通过汽车品牌ID获取车系
     * @param type 客户意向  0：购买新车 1：买二手车 2：卖二手车 3：二手车置换
     * @return json
     * */
    public function create(){
        $type = isset($this->data['type']) ? $this->data['type'] + 0 : 0;
        unset($this->data['appId'], $this->data['deviceId'], $this->data['type']);

        $type == 2 && $this->apiReturn(201, '产品类型非法');

        $data = array();
        foreach($this->data as $key => $value){
            $data['b_' . $key] = $value;
        }

        $result   = $this->validate($data, 'AddUser');
        if(true !== $result){
            $this->apiReturn(201, '', $result);
        }

        if(isset($data['b_workSituation']) && in_array($data['b_workSituation'], [1, 2])){
            if(!isset($data['b_position']) || empty($data['b_position'])){
                $this->apiReturn(201, '', '请填写职位或单位');
            }
        }

        $data['b_createTime'] = time();

        $res = Db::name('user')->insert($data);
        !$res && $this->apiReturn(201, '', '添加客户信息失败');
        
        $this->apiReturn(200, ['userId' => Db::name('user')->getLastInsID()]);
    }

    public function search(){
        (!isset($this->data['fid']) || empty($this->data['fid'])) && $this->apiReturn(201, '', '系列ID非法');
        $page = isset($this->data['page']) ? $this->data['page'] + 0: 1;
        
        $familyId = $this->data['fid'] + 0;
        $field = 'ac_id as id,c_name as name,c_image as image,c_introPrice as price,c_output as output,c_styleName as styleName';
        $model = model('Car');
        $data  = $model->getCarByFamilyId($familyId, $field);
        !$data && $this->apiReturn(201);
        $this->apiReturn(200, $data);
    }

    public function userInfo(){
        (!isset($this->data['userId']) || empty($this->data['userId'])) && $this->apiReturn(201, '', '客户ID非法');

        $userId = $this->data['userId'] + 0;
        $buyer   = model('Buyer')->getBuyerById($userId);
        !$buyer && $this->apiReturn(201, '', '买家不存在');

        foreach($buyer as $key => $value){
            $arr = explode('_', $key);
            $k = end($arr);
            $data[$k] = $value;
        }
        unset($key, $k, $value);
        $this->apiReturn(201, $data);
    }
}