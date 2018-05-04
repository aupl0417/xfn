<?php
namespace app\admin\controller;


use think\Db;
use think\Request;

class Seller extends Base{

    public function index(){

        $where = ['s_isDelete' => 0];
        $type  = input('type');
        if(isset($type) && !is_null($type)){
            $where['s_type'] = $type;
        }

        $field = 's_id as id,s_name as name,s_phone as phone,s_createTime as createTime,s_type as type,s_isDelete as isDelete';
        $data  = Db::name('Seller')->where($where)->field($field)->order('s_id desc')->select();

        $this->assign('data', $data);
        $this->assign('title', '销售员管理-喜蜂鸟后台管理');
        $this->assign('nav', '销售员管理');
        $this->assign('type', is_null($type) ? '' : $type);
        return $this->fetch();
    }

    public function create(){
        if(Request::instance()->isPost()){
            $data = array(
                's_name'   => input('name', '', 'htmlspecialchars,strip_tags,trim'),
                's_phone'  => input('phone', '', 'htmlspecialchars,strip_tags,trim'),
            );
            $result = $this->validate($data, 'AddSeller');
//            dump($result);die;
            $result !== true && $this->ajaxReturn(['code' => 201, 'message' => $result]);
            $data['s_createTime'] = time();
            $data['s_createId']   = session('user.userId');
            $data['s_updateTime'] = time();
            $res = Db::name('seller')->insert($data);
            !$res && $this->ajaxReturn(['code' => 201, 'message' => '添加失败']);
            $this->ajaxReturn(['code' => 200, 'message' => '添加成功']);
        }else{
            $this->assign('nav', '添加物品');
            return $this->fetch();
        }
    }

    public function edit(){
        $id = input('id', 0, 'intval');
        $where = ['ID' => $id, 'IsDelete' => 0];
        if(Request::instance()->isPost()){
            $data = array(
                'Name'      => input('Name', '', 'htmlspecialchars,strip_tags,trim'),
                'ShotName'  => input('ShotName', '', 'htmlspecialchars,strip_tags,trim'),
                'Type'      => input('type', '', 'htmlspecialchars,strip_tags,trim'),
                'BatchSn'   => input('batchSn', '', 'htmlspecialchars,strip_tags,trim'),
                'Price'     => input('Price', '', 'htmlspecialchars,strip_tags,trim'),
                'SoldPrice' => input('SoldPrice', '', 'htmlspecialchars,strip_tags,trim'),
                'Stock'     => input('stock', '', 'htmlspecialchars,strip_tags,trim'),
                'Status'    => input('status', '', 'htmlspecialchars,strip_tags,trim'),
                'Image'     => input('Image', '', 'htmlspecialchars,strip_tags,trim'),
            );
            $result = $this->validate($data, 'EditGoods');
            if($data['Type'] != 2){
                $data['BatchSn'] = '';
            }
            $result !== true && $this->ajaxReturn(['code' => 201, 'message' => $result]);
            $res = Db::name('Goods')->where($where)->update($data);
            $res === false && $this->ajaxReturn(['code' => 201, 'message' => '编辑失败']);
            $this->ajaxReturn(['code' => 200, 'message' => '编辑成功']);
        }else{
            $data = Db::name('Goods')->where($where)->find();
            !$data && $this->error('记录存在', url('index/index'));
            $this->assign('data', $data);
            $this->assign('nav', '编辑物品');
            return $this->fetch();
        }
    }

    public function remove(){
        $id = input('id', 0, 'intval');
        if(Db::name('Goods')->where(['ID' => $id])->count()){
            $res = Db::name('Goods')->where(['ID' => $id])->update(['IsDelete' => 1]);
            $res === false && $this->ajaxReturn(['code' => 201, 'message' => '删除失败']);
            $this->ajaxReturn(['code' => 200, 'message' => '删除成功']);
        }else{
            $this->ajaxReturn(['code' => 201, 'message' => '数据不存在']);
        }
    }

    public function shelf(){
        $id  = input('id', 0, 'intval');
        $act = input('act', 'on', 'htmlspecialchars,strip_tags,trim');

        if(!$id || !in_array($act, ['on', 'off'], true)){
            $this->ajaxReturn(['code' => 201, 'message' => '非法参数']);
        }

        $data = array(
            'Status' => $act == 'on' ? 1 : 0
        );

        $res = Db::name('Goods')->where(['ID' => $id])->update($data);
        $msg = $act == 'on' ? '上架' : '下架';
        $res === false && $this->ajaxReturn(['code' => 201, 'message' => $msg . '失败']);
        $this->ajaxReturn(['code' => 200, 'message' => $msg . '成功']);
    }
}
