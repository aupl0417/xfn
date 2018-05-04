<?php
/**
 * Created by PhpStorm.
 * User: liaozijie
 * Date: 2018-04-25
 * Time: 9:28
 */

namespace app\admin\controller;

use Qiniu\Config;
use Qiniu\Storage\BucketManager;
use Qiniu\Storage\UploadManager;
use think\Controller;
use think\Db;
use Qiniu\Auth;
class Upload extends Controller
{

    public function area(){
        $pid = isset($this->data['id']) || empty($this->data['id']) ? $this->data['id'] + 0 : 0;
        $data = model('Area')->getAreaList($pid);
        $this->apiReturn(200, $data);
    }

    public function upload(){

        $file = request()->file('image');

        // 要上传图片的本地路径
        $filePath = $file->getRealPath();
        $ext = pathinfo($file->getInfo('name'), PATHINFO_EXTENSION);  //后缀
        $rule = [
            'size' => 2000000,
            'ext'  => ['jpg', 'png', 'bmp', 'jpeg'],
        ];
        if(!$file->check($rule)){
            $this->apiReturn(201, '', $file->getError());
        }

        // 上传到七牛后保存的文件名
        $key = substr(md5($filePath) , 0, 5). date('YmdHis') . rand(0, 9999) . '.' . $ext;

        vendor('Qiniu.autoload');
        $auth  = new Auth(config('qiniu.accesskey'), config('qiniu.secretkey'));
        $token = $auth->uploadToken(config('qiniu.bucket'));
//        $qn    = new BucketManager($auth);
//        $config = new Config();
        $upload = new UploadManager();
        list($ret, $err) = $upload->putFile($token, $key, $filePath);
        if ($err !== null) {
            $this->apiReturn(201, ['state' => 'error', 'msg' => $err]);
        } else {
            //返回图片的完整URL
            $this->apiReturn(200, ['state' => 'success', 'url' => 'http://' . config('qiniu.domain') . '/' . $ret['key']]);
        }
    }
    
    public function qcode(){
        $id = input('id', 0, 'intval');
        $wechatService = model('Wechat', 'service');
        $result = $wechatService->qcode($id);
        $this->apiReturn(200, ['url' => $result]);
    }

    /*
	* 	返回数据到客户端
	*	@param $code type : int		状态码
	*   @param $info type : string  状态信息
	*	@param $data type : mixed	要返回的数据
	*	return json
	*/
    protected function apiReturn($code, $data = null, $msg = ''){
        header('Content-Type:application/json; charset=utf-8');//返回JSON数据格式到客户端 包含状态信息

        $jsonData = array(
            'code' => $code,
            'msg'  => $msg ?: ($code == 200 ? '操作成功' : '操作失败'),
            'data' => $data ? $data : null
        );

        exit(json_encode($jsonData));
    }

}