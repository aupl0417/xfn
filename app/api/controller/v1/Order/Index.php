<?php
/**
 * @title 销售员管理
 * @author aupl
 * */
namespace app\api\controller\v1\Order;

use app\api\controller\v1\Home;
use think\Controller;
use think\Db;
use think\Exception;

class Index extends Home {

    /**
     * 订单管理
     * @param type 订单类型
     * @param buystyle 购买方式
     * @return json
     * */
    public function index(){
        $type = isset($this->data['type']) && !is_null($this->data['type']) ? $this->data['type'] + 0 : '';
        $buystyle = isset($this->data['buystyle']) && !is_null($this->data['buystyle']) ? $this->data['buystyle'] + 0 : '';

        $where = [];
        if(in_array($buystyle, [0, 1, 2, 3, 4], true)){
            $where['o_buystyle'] = $buystyle;
        }

        if(in_array($type, [0, 1, 2, 3], true)){
            $where['o_type'] = $type;
        }

        $orderModel = model('Order');
        $data  = $orderModel->getOrderData($where);
        !$data && $this->apiReturn(201);
        $this->apiReturn(200, $data);
    }

    /**
     * 订单详情
     * @param id 订单ID 不是orderId，也可修改为orderId
     * @return json
     * */
    public function detail(){
        (!isset($this->data['id']) || empty($this->data['id'])) && $this->apiReturn(201, '', '订单ID非法');

        $field = 'o_id as id,o_orderId as orderId,o_changeCarId as changeCarId,c_name as carName,o_carcolor as carcolor,o_trim as trim,b_username as username,b_phone as phone,o_registerAddr as registerAddr,seller.s_name as sellerName,shop.s_name as shopName,o_buystyle as buystyle,o_deliveryTime as deliveryTime,o_choice as choice,o_boutique as boutique,o_offerType as offerType,o_quotation as quotation,o_quotationRemark as quotationRemark,o_remark as remark,o_price as price,c_introPrice as introPrice';
        $data = model('Order')->findOrder($this->data['id'] + 0, $field);
        !$data && $this->apiReturn(201);
        if($data['changeCarId']){
            $carInfo = model('Car')->getCarById($data['changeCarId'], 'c_name as userCarName');
            $data = array_merge($data, $carInfo);
        }
        $data['carcolor'] = Db::name('car_color')->where(['cc_id' => $data['carcolor']])->field('cc_name')->find()['cc_name'];
        $data['trim'] = Db::name('car_trim')->where(['ct_id' => $data['trim']])->field('ct_name')->find()['ct_name'];
        $field = 'os_id as id,os_content as content,os_isUse as isUse';
        $data['scheme'] = Db::name('order_scheme')->where(['os_oid' => $data['id'], 'os_isDelete' => 0])->field($field)->order('os_id desc')->select();
        $this->apiReturn(200, $data);
    }

    public function edit(){
        (!isset($this->data['id']) || empty($this->data['id'])) && $this->apiReturn(201, '', '订单ID非法');
        (!isset($this->data['schemeId']) || empty($this->data['schemeId'])) && $this->apiReturn(201, '', '方案ID非法');

        $id = $this->data['id'] + 0;
        $schemeId = $this->data['schemeId'] + 0;

        $result = Db::name('order_scheme')->where(['os_oid' => $id, 'os_id' => $schemeId])->update(['os_isUse' => 1, 'os_updateTime' => time()]);
        $result === false && $this->apiReturn(201, '', '更新方案失败');
        $this->apiReturn(200, '', '提交成功');
    }

    public function addScheme(){
        (!isset($this->data['id']) || empty($this->data['id'])) && $this->apiReturn(201, '', '订单ID非法');
        (!isset($this->data['content']) || empty($this->data['content'])) && $this->apiReturn(201, '', '订单ID非法');

        $data = [
            'os_oid' => $this->data['id'] + 0,
            'os_content' => htmlspecialchars(strip_tags(trim($this->data['content']))),
            'os_createTime' => time(),
            'os_updateTime' => time(),
            'os_createId'   => $this->data['userId'] + 0,
        ];
        $result = Db::name('order_scheme')->insert($data);
        !$result && $this->apiReturn(201, '', '添加方案失败');
        $this->apiReturn(200, '', '添加方案成功');
    }

    /**
     * 删除销售员
     * @param userId 后台用户ID
     * @param id int 销售员ID
     * @return json
     * */
    public function remove(){
        (!isset($this->data['id']) || empty($this->data['id'])) && $this->apiReturn(201, '', 'ID非法');
        $id = $this->data['id'] + 0;

        $result = Db::name('seller')->where(['s_id' => $id])->update(['s_isDelete' => 1]);
        $result === false && $this->apiReturn(201, '', '删除失败');
        $this->apiReturn(200, '', '删除成功');
    }


}