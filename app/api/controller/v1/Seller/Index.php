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
class Index extends Home
{
    /*
     * 销售员列表
     * */
    public function index(){

    }

    /*
     * 店长、分销员、销售员信息
     * */
    public function info(){
        (!isset($this->data['userId']) || empty($this->data['userId'])) && $this->apiReturn(201, '', '分销员ID非法');

        $userId = $this->data['userId'] + 0;
        $model  = model('Seller');
        $field  = 's_id as id,s_name as name,s_phone as phone,s_type as type,s_shopId as shopId,s_pid as pid';
        $user   = $model->getSellerById($userId, $field);
        !$user && $this->apiReturn(201, '', '分销员不存在');
        $user['role'] = $user['type'] == 0 ? '店长' : ($user['type'] == 1 ? '分销员' : '销售员');
        unset($user['pid']);
        $this->apiReturn(200, $user);
    }

    /**
     * 分销员列表
     * */
    public function lists(){
        (!isset($this->data['userId']) || empty($this->data['userId'])) && $this->apiReturn(201, '', '分销员ID非法');
        (!isset($this->data['type']) ||   is_null($this->data['type'])) && $this->apiReturn(201, '', '分销员类型非法');

        $userId = $this->data['userId'] + 0;
        $type   = $this->data['type'] + 0;
        $count  = Db::name('seller')->where(['s_id' => $userId, 's_type' => $type])->count();
        if(!$count){
            $this->apiReturn(201, '', '您不是店长');
        }

        $model = model('Seller');
        $data  = $model->getSellerByPid($userId, 's_id as id,s_name as name,s_phone as phone');
        if($data){
            foreach($data as $key => &$value){
                $totalCounts   = Db::name('car_order')->where(['o_sellerId' => $value['id']])->count();
                $successCounts = Db::name('car_order')->where(['o_sellerId' => $value['id'], 'o_state' => 6])->count();
                $value['counts'] = $totalCounts;
                $value['rate']   = $totalCounts == 0 ? 0 . '%' : intval($successCounts * 100 / $totalCounts) . '%';
            }
        }

        $this->apiReturn(201, $data);

    }

    /**
     * 商机列表
     * */
    public function business(){
        (!isset($this->data['userId']) || empty($this->data['userId'])) && $this->apiReturn(201, '', '分销员ID非法');
        (!isset($this->data['type']) || is_null($this->data['type'])) && $this->apiReturn(201, '', '分销员类型非法');

        $type   = $this->data['type'] + 0;
        $userId = $this->data['userId'] + 0;
        $keyword= isset($this->data['keyword']) ? trim($this->data['keyword']) : '';
        $page   = input('page', 1, 'intval');

        !in_array($type, [0, 1, 2]) && $this->apiReturn(201, '', '分销员类型非法');

        $sellerModel = model('Seller');
        $seller = $sellerModel->findSeller(['s_id' => $userId, 's_type' => $type], 's_shopId');//查找一条记录
        !$seller && $this->apiReturn(201, '', '您不具有查看商机列表的权限');

        $result = Db::name('mailbox')->where(['m_sellerId' => $userId, 'm_state' => 0])->update(['m_updateTime' => date('Y-m-d H:i:s'), 'm_state' => 1]);

        $where = array();
        if(in_array($type, [0, 1])){//店长或分销员
            $where = ['bo_shopId' => $seller['s_shopId']];
        }

        if($keyword){
            if(checkPhone($keyword)){
                $where['bo_phone'] = $keyword;
            }else{
                if(substr_count($keyword, ' ') > 3){
                    $ids = Db::name('car_info')->where('c_name', 'like', '%' . $keyword . '%')->join('car', 'ac_cid=c_id', 'left')->field('ac_id')->select();
                    if($ids){
                        $ids = array_column($ids, 'ac_id');
                        $ids = implode(',', $ids);
                        $where['bo_cid'] = ['in', $ids];
                    }
                }else{
                    $wids = Db::name('wechat_info')->where('w_nickname', 'like', '%' . $keyword . '%')->field('w_id')->select();
                    if($wids){
                        $wids = array_column($wids, 'w_id');
                        $wids = implode(',', $wids);
                        $where['bo_wid'] = ['in', $wids];
                    }
                }
            }
        }

        $model  = model('BusinessOpportunity');
        $field  = '';
        $data   = $model->getDataList($where, $field, $page);
        $this->apiReturn(200, $data);
    }

    public function message(){
        (!isset($this->data['userId']) || empty($this->data['userId'])) && $this->apiReturn(201, '', '分销员ID非法');
        $userId = $this->data['userId'] + 0;

        $count  = Db::name('mailbox')->where(['m_sellerId' => $userId, 'm_state' => 0])->count();
        $this->apiReturn(200, ['count' => $count]);
    }

}