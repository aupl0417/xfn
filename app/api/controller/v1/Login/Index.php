<?php
namespace app\api\controller\v1\Login;

use app\api\controller\v1\Home;
use think\Controller;
use think\Db;
use think\Exception;
use think\Request;
use think\Session;

class Index extends Home {

    public function index(){
        if(Request::instance()->isPost()){
            $data['user_login'] = input('username', '', 'trim');
            $data['user_pass'] = input('password', '', 'trim');

            try{
                $result = $this->validate($data, 'Login');

                if(true !== $result){
                    throw new Exception($result, 1);
                }

                $user = Db::table('xfn_member')->where(['user_login' => $data['user_login']])->find();
                if(!$user){
                    throw new Exception('用户不存在', 2);
                }

                if(getSuperMD5($data['user_pass']) !== $user['user_pass']){
                    throw new Exception('密码输入不正确', 3);
                }

                $userInfo = array(
                    'userId'   => $user['id'],
                    'username' => $user['user_nickname'],
                );
                Session::set('user', $userInfo);
                return $this->apiReturn(200, ['msg' => '登录成功', 'url' => url('seller/index')]);
            }catch (Exception $e){
                return $this->apiReturn(201, '', ['msg' => $e->getMessage()]);
            }
        }
    }

//    public function logout(){
//        if(session('?user')){
//            session('user', null);
//        }
//        $this->redirect('login/index');
//    }

}