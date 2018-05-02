<?php
/**
 * @title 销售员管理
 * @author aupl
 * */
namespace app\api\controller\v1\SellerManage;

use app\api\controller\v1\Home;
use think\Controller;
use think\Db;
use think\Exception;

class Index extends Home {

    public function index(){
        $type = isset($this->data['type']) && !is_null($this->data['type']) ? $this->data['type'] + 0 : '';
        $page = isset($this->data['page']) && !empty($this->data['page']) ? $this->data['page'] + 0 : 1;

        $where = [];
        if(in_array($type, [0, 1, 2], true)){
            $where['s_type'] = $type;
        }

        $sellerModel = model('Seller');
        $data = $sellerModel->getSellerList($where, 's_id as id,s_name as name,s_phone as phone,s_type as type,s_createTime as createTime', $page);
        !$data && $this->apiReturn(201);
        $this->apiReturn(200, $data);
    }

    /**
     * 添加销售员
     * @param userId 后台用户ID
     * @param type   销售员类型ID
     * @param username 销售员姓名
     * @param phone    手机号码
     * @return json
     * */
    public function add(){
        (!isset($this->data['type'])      || is_null($this->data['type'])) && $this->apiReturn(201, '', '销售员类型非法');
        (!isset($this->data['username'])  || empty($this->data['username'])) && $this->apiReturn(201, '', '请输入姓名');
        (!isset($this->data['phone'])     || empty($this->data['phone'])) && $this->apiReturn(201, '', '请输入手机号码');
        !checkPhone($this->data['phone']) && $this->apiReturn(201, '', '手机号码格式非法');

        if(Db::name('seller')->where(['s_phone' => $this->data['phone'], 's_isDelete' => 0])->count()){
            $this->apiReturn(201, '', '手机号码已存在');
        }

        $data = [
            's_name'  => $this->data['username'],
            's_type'  => $this->data['type'] + 0,
            's_phone' => $this->data['phone'],
            's_pid'   => 0,
            's_createTime' => time(),
            's_updateTime' => time(),
            's_createId'   => $this->data['userId']
        ];

        $result = Db::name('seller')->insert($data);
        !$result && $this->apiReturn(201, '', '添加失败');
        $this->apiReturn(200, '', '添加成功');
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