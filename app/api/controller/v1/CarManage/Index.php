<?php
/**
 * @title 活动管理
 * @author aupl
 * */
namespace app\api\controller\v1\CarManage;

use app\api\controller\v1\Home;
use think\Controller;
use think\Db;
use think\Exception;

class Index extends Home {

    public function index(){
        $type = isset($this->data['type']) && !is_null($this->data['type']) ? $this->data['type'] + 0 : 1;
        $page = isset($this->data['page']) && !empty($this->data['page']) ? $this->data['page'] + 0 : 1;
        
        $where = ['ac_isDelete' => 0];

        if($type == 1){
            $where['ac_hasCar'] = 1;
        }elseif($type == 2){
            $where['ac_isNewCar'] = 1;
        }

        $data = model('Car')->getCarList($where, 'ac_id as id,c_name as name,ac_num as number,ac_createTime as createTime', $page);
        !$data && $this->apiReturn(201);
        $this->apiReturn(200, $data);
    }

    /**
     * 添加车辆
     * @param userId 后台用户ID
     * @param type   活动类型ID
     * @param brandId   活动名称
     * @param familyId    活动名称
     * @param carId    活动名称
     * @param styleId    活动名称
     * @param familyId    活动名称
     * @return json
     * */
    public function add(){
        (!isset($this->data['type'])      || is_null($this->data['type'])) && $this->apiReturn(201, '', '车辆类型非法');
        (!isset($this->data['brandId'])  || empty($this->data['brandId'])) && $this->apiReturn(201, '', '请选择车辆品牌');
        (!isset($this->data['familyId'])     || empty($this->data['familyId'])) && $this->apiReturn(201, '', '请选择车系');
        (!isset($this->data['carId'])     || empty($this->data['carId'])) && $this->apiReturn(201, '', '请选择车型');
        (!isset($this->data['styleId'])     || empty($this->data['styleId'])) && $this->apiReturn(201, '', '请选择车款式');

        $type = $this->data['type'] + 0;

        if($type == 1){
            (!isset($this->data['num'])     || empty($this->data['num'])) && $this->apiReturn(201, '', '请输入数量');
        }elseif($type == 2){
            (!isset($this->data['boughtTime'])     || empty($this->data['boughtTime'])) && $this->apiReturn(201, '', '请选择购买时间');
            (!isset($this->data['mileage'])     || empty($this->data['mileage'])) && $this->apiReturn(201, '', '请输入已走里程');
            (!isset($this->data['soldPrice'])     || empty($this->data['soldPrice'])) && $this->apiReturn(201, '', '请输入销售价格');
        }

        $carId = $this->data['carId'] + 0;
        $brandId = $this->data['brandId'] + 0;

        $count = Db::name('car')->where(['ac_cid' => ])
        if($count){

        }

        $data = [
            'ac_cid'  => $this->data['carId'] + 0,
            'ac_soldPrice'  => isset($this->data['soldPrice']) ? $this->data['soldPrice'] + 0 : 0,
            'ac_createId'    => $this->data['userId'] + 0,
            'ac_createTime' => time(),
            'ac_updateTime' => time(),
            'ac_hasCar'     => $type == 1 ? 1 : 0,
            'ac_isNewCar'   => $type == 2 ? 1 : 0,
            'ac_num'        => isset($this->data['num']) ? $this->data['num'] + 0 : 0,
            'ac_boughtTime' => isset($this->data['boughtTime']) ? trim($this->data['boughtTime']) : '',
            'ac_mileage'    => isset($this->data['mileage']) ? strip_tags($this->data['mileage']) : 0
        ];

        $result = Db::name('car')->insert($data);
        !$result && $this->apiReturn(201, '', '添加失败');
        $this->apiReturn(200, '', '添加成功');
    }

    /**
     * 编辑活动
     * @param userId 后台用户ID
     * @param type   活动类型ID
     * @param name   活动名称
     * @param url    活动名称
     * @return json
     * */
    public function edit(){
        (!isset($this->data['id'])      || is_null($this->data['id'])) && $this->apiReturn(201, '', '活动ID非法');
        (!isset($this->data['type'])      || is_null($this->data['type'])) && $this->apiReturn(201, '', '销售员类型非法');
        (!isset($this->data['name'])  || empty($this->data['name'])) && $this->apiReturn(201, '', '请输入活动名称');
        (!isset($this->data['url'])     || empty($this->data['url'])) && $this->apiReturn(201, '', '请上传文件');

        !filter_var($this->data['url'], FILTER_VALIDATE_URL) && $this->apiReturn(201, '', '上传地址非法');

        //如果是视频的话，如果不是mp4格式的，还要转为mp4格式的，必须是H264格式的视频才能在浏览器上播放

        //如果是图片的话，要限制上传图片类型及最大上传大小

        $data = [
            'a_name'  => $this->data['name'],
            'a_type'  => $this->data['type'] + 0,
            'a_content'    => $this->data['url'],
            'a_updateTime' => time(),
            'a_editorId'   => $this->data['userId']
        ];

        $result = Db::name('activity')->where(['a_id' => $this->data['id'] + 0])->update($data);
        $result === false && $this->apiReturn(201, '', '编辑失败');
        $this->apiReturn(200, '', '编辑成功');
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

        $result = Db::name('activity')->where(['a_id' => $id])->update(['a_isDelete' => 1]);
        $result === false && $this->apiReturn(201, '', '删除失败');
        $this->apiReturn(200, '', '删除成功');
    }


}