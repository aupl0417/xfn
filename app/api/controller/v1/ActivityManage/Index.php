<?php
/**
 * @title 活动管理
 * @author aupl
 * */
namespace app\api\controller\v1\ActivityManage;

use app\api\controller\v1\Home;
use think\Controller;
use think\Db;
use think\Exception;

class Index extends Home {

    public function index(){
        $type = isset($this->data['type']) && !is_null($this->data['type']) ? $this->data['type'] + 0 : '';
        $page = isset($this->data['page']) && !empty($this->data['page']) ? $this->data['page'] + 0 : 1;
        
        $where = ['a_isDelete' => 0];
        if(in_array($type, [0, 1], true)){
            $where['a_type'] = $type;
        }

        $data = model('Activity')->getActivityData($where, 'a_id as id,a_name as name,a_type as type,a_createTime as createTime', $page);
        !$data && $this->apiReturn(201);
        $this->apiReturn(200, $data);
    }

    /**
     * 添加活动
     * @param userId 后台用户ID
     * @param type   活动类型ID
     * @param name   活动名称
     * @param url    活动名称
     * @return json
     * */
    public function add(){
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
            'a_createTime' => time(),
            'a_updateTime' => time(),
            'a_createId'   => $this->data['userId']
        ];

        $result = Db::name('activity')->insert($data);
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