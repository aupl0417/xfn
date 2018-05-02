<?php
namespace app\admin\controller\v1;

use think\Controller;
use think\Db;
use think\Exception;
use think\Request;
use think\Session;

class Index extends Controller {

    public function index(){
        if(Request::instance()->isPost()){
            $data['username'] = input('username', '', 'trim');
            $data['username'] = input('password', '', 'trim');

            try{
                $result = $this->validate($data, 'Login');

                if(true !== $result){
                    throw new Exception($result, 1);
                }
                dump($data);die;
                $user = Db::table('xfn_member')->where(['user_login' => $data['UserName']])->find();
                if(!$user){
                    throw new Exception('用户不存在', 2);
                }
//                dump(getSuperMD5($data['Password']));die;
                if(getSuperMD5($data['Password']) !== $user['user_pass']){
                    throw new Exception('密码输入不正确', 3);
                }

                $userInfo = array(
                    'userId'   => $user['id'],
                    'username' => $user['user_nickname'],
                );
                Session::set('user', $userInfo);
                return json(['code' => 1, 'data' => ['msg' => '登录成功', 'url' => url('seller/index')]]);
            }catch (Exception $e){
                return json(['code' => 0, 'data' => $e->getMessage()]);
            }
        }
    }

    public function logout(){
        if(session('?user')){
            session('user', null);
        }
        $this->redirect('login/index');
    }

}