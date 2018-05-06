<?php
/**
 * Created by PhpStorm.
 * User: liaozijie
 * Date: 2018-04-25
 * Time: 9:28
 */

namespace app\api\controller\v1\Screen;

use app\api\controller\v1\Home;
use think\Controller;
use think\Db;
use think\Exception;

class Car extends Home
{
    public function index(){
        $type = input('get.type', 0, 'intval');
        $id   = input('get.id', 0, 'intval');
        $method = request()->method();
        switch (strtolower($method)){
            case 'get':     //查询
                if(isset($type) && !empty($type) && !isset($id)){
                    $this->read($type);
                }else if(isset($id) && !empty($id)){
                    $this->info($id);
                }
                break;
//            case 'post':    //新增
//                $this->add();
//                break;
//            case 'put':     //修改
//                $this->update();
//                break;
//            case 'delete':  //删除
//                $this->delete();
//                break;

        }
    }

    /*
     * 获取活动列表
     * */
    public function read($type){
        $model = model('Car');
        $field = 'ac_id as id,c_name as name,c_image as image,c_introPrice as price,c_output as output,c_styleName as styleName';
        $data  = $model->getCarListByType($type, $field);
        !$data && $this->apiReturn(201);
        return $this->apiReturn(200, $data);
    }

    /*
     * 汽车详情
     * */
    public function info($id){
        $model = model('Car');
        $field = 'ac_id as id,c_id as carId,c_name as name,c_image as image,c_introPrice as price,c_output as output,c_styleName as styleName,c_vehicleName as vehicleName,c_oilConsumption as oilConsuption,c_gearboxName as gearboxName,c_driveStyle as driveStyle,ac_soldPrice as soldPrice,ac_num as num';
        $data  = $model->getCarById($id, $field);
        !$data && $this->apiReturn(201);
        if(isset($this->data['type']) && $this->data['type'] == 2){
            $data['price'] = $data['soldPrice'];//如果是二手车，则把销售价替换官方指导价
            unset($data['soldPrice']);
        }

        $data['parameter'] = $model->getCarInfoById($data['carId']);

        $data['type'] = input('get.type');
        $this->apiReturn(200, $data);
    }

    /*
     * 添加手机号
     * */
    public function phone(){
        (!isset($this->data['type']) || is_null($this->data['type'])) && $this->apiReturn(201, '', '专区类型不能为空');
        (!isset($this->data['cid'])  || empty($this->data['cid'])) && $this->apiReturn(201, '', '汽车ID不能为空');
        (!isset($this->data['sid'])  || empty($this->data['sid'])) && $this->apiReturn(201, '', '门店ID不能为空');
        (!isset($this->data['phone']) || empty($this->data['phone'])) && $this->apiReturn(201, '', '手机号码不能为空');
        (!isset($this->data['mode'])  || empty($this->data['mode'])) && $this->apiReturn(201, '', '客户意向不能为空');

        !checkPhone($this->data['phone']) && $this->apiReturn(201, '', '请输入正确的手机号码');
        //如果是置换车，则置换车ID必须
        if(intval($this->data['mode']) == 3){
            (!isset($this->data['changeCarId'])  || empty($this->data['changeCarId'])) && $this->apiReturn(201, '', '请选择要置换的车');
        }

        Db::startTrans();
        try{
            $data = [
                'bo_cid' => $this->data['cid'] + 0,//汽车id,
                'bo_changeCarId' => isset($this->data['changeCarId']) ? $this->data['changeCarId'] : 0,
                'bo_shopId' => $this->data['sid'] + 0,//销售员id,
                'bo_phone'    => $this->data['phone'],
                'bo_createTime' => time(),
                'bo_updateTime' => time(),
                'bo_type'       => $this->data['mode'] + 0,//专区类型
                'bo_mode'       => $this->data['type'] + 0,//客户意向  0：购买新车 1：买二手车 2：卖二手车 3：二手车置换,
            ];

            $result = Db::name('business_opportunity')->insert($data);
            if(!$result){
                throw new Exception('添加手机号码失败');
            }
            $insertId = Db::name('business_opportunity')->getLastInsID();

            $sellerModel = model('Seller');
            $where       = ['s_shopId' => $data['bo_shopId']];
            $whereOr     = ['s_type'   => 2];
            $seller      = $sellerModel->findSellerAll($where, $whereOr, 's_id as m_sellerId');
            if(!$seller){
                throw new Exception('无销售员数据');
            }

            foreach($seller as $key => &$value){
                $value['m_businessId'] = $insertId;
                $value['m_createTime'] = date('Y-m-d H:i:s');
            }

            $result = Db::name('mailbox')->insertAll($seller);
            if(!$result){
                throw new Exception('插入消息表格失败');
            }

            Db::commit();
            $this->apiReturn(200, ['rid' => $insertId], '添加手机号码成功');
        }catch (Exception $e){
            Db::rollback();
            $this->apiReturn(201, '', $e->getMessage());
        }
    }

}