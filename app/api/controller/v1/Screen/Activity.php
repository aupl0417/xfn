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
class Activity extends Home
{
    public function index(){
//        $id = input('id', 0, 'intval');
        $method = request()->method();
        switch (strtolower($method)){
            case 'get':     //查询
                $this->read();
                break;
            case 'post':    //新增
                $this->add();
                break;
            case 'put':     //修改
                $this->update();
                break;
            case 'delete':  //删除
                $this->delete();
                break;

        }
    }

    /*
     * 获取活动列表
     * */
    public function read(){
        $model = model('Activity');
        $data  = $model->getActivityList();
        return $this->apiReturn(200, $data);
    }

    public function add(){
//        $model = model('News');
        $param = Request::instance()->param();//获取当前请求的所有变量（经过过滤）
//        if($model->save($param)){
//            return json(["status"=>1]);
//        }else{
//            return json(["status"=>0]);
//        }
        $data = array('status' => 'add');
        return apiReturn(200, $data);
    }
    public function update($id){
//        $model = model('News');
        $param = Request::instance()->param();
//        if($model->where("id",$id)->update($param)){
//            return json(["status"=>1]);
//        }else{
//            return json(["status"=>0]);
//        }
        $data = array('status' => 'update');
        return $this->apiReturn(200, $data);
    }
    
    public function delete($id){

        /*$model = model('News');
        $rs=$model::get($id)->delete();
        if($rs){
            return json(["status"=>1]);
        }else{
            return json(["status"=>0]);
        }*/
        $data = array('status' => 'delete');
        return $this->apiReturn(200, $data);
    }
}