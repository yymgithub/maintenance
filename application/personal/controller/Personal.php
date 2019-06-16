<?php
namespace app\personal\controller;

use think\Controller;
use think\Db;
class Personal extends Controller
{
    public function index()
    {
        if (!session('?user')){
            $this->error("使用系统前请先登录！！！");
            $this->redirect('/login/login/index');
        }

        //提示信息
        $count = Db::table('work_order')->where('work_order_status',0)->count();
        $info = db('work_order')->alias('wo')
            ->where('wo.work_order_status',0)
            ->join('customer_info ci','ci.customer_id = wo.customer_id')
            ->field('wo.description,ci.customer_name,wo.create_time,wo.work_order_id')
            ->select();
        $this->assign('count',$count);
        $this->assign('info',$info);

        return $this->fetch();
    }

    public function newpassword(){
        $param = input('post.');
        $password = $param['password'];
        $rpassword = $param['repeat_password'];

        $user_id = session('user')['admin_account_id'];

        if(empty($password) | empty($rpassword)){
            $this->error("不能有空输入！！！");
        }

        if($password == $rpassword){
            $updatepassword= \app\personal\model\Personal::updatepassword($user_id,$password);
            if ($updatepassword) {
                session("user", NULL);
                $this->success("修改密码成功，请重新登录！",'login/login/index');
                $this->redirect('login/login/index');
            }else{
                $this->error("修改密码失败，请重试！");
            }
        }else{
                $this->error("两次输入密码不一致！");
        }
    }
}
