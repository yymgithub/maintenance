<?php
namespace app\login\controller;

use think\Controller;

class Login extends Controller
{
    public function index()
    {
        return $this->fetch();
    }

    // 处理登录逻辑
    public function doLogin()
    {
        $param = input('post.');
        if(empty($param['user_name'])){

            $this->error('用户名不能为空','login/index');
        }

        if(empty($param['user_pwd'])){

            $this->error('密码不能为空','login/index');
        }

        // 验证用户名
        $user=\app\personal\model\Personal::search($param['user_name']);
        if(empty($user)){

            $this->error('用户名或密码错误','login/index');
        }

        //验证用户是否已被冻结
        if($user['status'] == 0){

            $this->error('该账户已被冻结','login/index');
        }

        // 验证密码
        if($user['password'] != md5($param['user_pwd'])){

            $this->error('用户名或密码错误','login/index');
        }

        // 用户
        session('user', $user);

        $this->redirect(url('order/searchorder/index'));
    }

    // 退出登录
    public function loginOut()
    {
        session('user', null);
        $this->redirect(url('login/index'));
    }



}
