<?php
/**
 * Created by PhpStorm.
 * User: liaozijie
 * Date: 2018-04-23
 * Time: 16:11
 */

namespace app\api\controller\ad\v1;
use think\controller\Rest;
use think\Request;

class News extends Rest
{
    public function rest(){
        $id = input('id', 0, 'intval');
        $method = request()->method();
        switch (strtolower($method)){
            case 'get':     //查询
                $this->read($id);
                break;
            case 'post':    //新增
                $this->add();
                break;
            case 'put':     //修改
                $this->update($id);
                break;
            case 'delete':  //删除
                $this->delete($id);
                break;

        }
    }
    public function read($id){
//        $model = model('News');
        //$data = $model::get($id)->getData();
        //$model = new NewsModel();
//        $data=$model->where('id', $id)->find();// 查询单个数据
        $data = array('status' => 'read');
        return apiReturn(200, $data);
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
        return apiReturn(200, $data);
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
        return apiReturn(200, $data);
    }
}