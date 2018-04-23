<?php
namespace app\admin\controller;

use think\Controller;
use think\Db;
use think\Exception;
use think\Request;
use think\Session;

class Login extends Controller {

    public function index(){
        return $this->fetch();
    }

    public function doLogin(){
        if(Request::instance()->isPost()){
            $data['UserName'] = input('username', '', 'trim');
            $data['Password'] = input('password', '', 'trim');

            try{
                $result = $this->validate($data, 'Login');

                if(true !== $result){
                    throw new Exception($result, 1);
                }
//                dump($data);die;
                $user = Db::table('xfn_user')->where(['user_login' => $data['UserName']])->find();
                if(!$user){
                    throw new Exception('用户不存在', 2);
                }

                if(getSuperMD5($data['Password']) !== $user['user_pass']){
                    throw new Exception('密码输入不正确', 3);
                }

                $userInfo = array(
                    'userId'   => $user['ID'],
                    'username' => $user['UserName'],
                );
                Session::set('user', $userInfo);
                return json(['code' => 1, 'data' => ['msg' => '登录成功', 'url' => url('index/index')]]);
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