<?php
/**
 * Created by PhpStorm.
 * User: liaozijie
 * Date: 2018-04-25
 * Time: 9:28
 */

namespace app\api\controller\v1;

use Qiniu\Storage\BucketManager;
use think\Controller;
use think\Db;
use Qiniu\Auth;
class Common extends Home
{

    public function area(){
        $pid = isset($this->data['id']) || empty($this->data['id']) ? $this->data['id'] + 0 : 0;
        $data = model('Area')->getAreaList($pid);
        $this->apiReturn(200, $data);
    }

    public function upload(){
        vendor('Qiniu.autoload');
        $auth = new Auth(config('qiniu.accesskey'), config('qiniu.secretkey'));
        $token = $auth->uploadToken(config('qiniu.bucket'));
        $qn    = new BucketManager($auth);
        dump($qn);
    }
    
    public function qcode(){
        $id = input('id', 0, 'intval');
        $wechatService = model('Wechat', 'service');
        $result = $wechatService->qcode($id);
        $this->apiReturn(200, ['url' => $result]);
    }

}