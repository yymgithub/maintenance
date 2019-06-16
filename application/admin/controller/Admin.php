<?php

namespace app\admin\controller;

use think\Controller;
use think\Db;
class Admin extends Controller
{
    public function index()
    {
        if (!session('?user')) {
            $this->error("使用系统前请先登录！！！");
            $this->redirect('/login/login/index');
        }
        $all_admin = \app\admin\model\Admin::getalladmin();
        session('all_admin', $all_admin);

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

    public function modifyadmin()
    {
        $password = $_POST['passwd'];
        $rpassword = $_POST['rpasswd'];
        $level = $_POST['level'];
        $userid = $_POST['userid'];


        if (empty($password) || empty($rpassword)) {
            $this->error("不能有空输入！！！");
        }

        if ($password == $rpassword) {
            $updatepassword = \app\admin\model\Admin::updateadmin($userid, $level, $password);
            if ($updatepassword) {
                $this->success("修改成功!", 'admin/index');
            } else {
                $this->error("修改失败，请重试！");
            }
        } else {
            $this->error("两次输入密码不一致！");
        }
    }

    public function deleteadmin()
    {
        $userid = input('id');
        $delete = \app\admin\model\Admin::deleteadmin($userid);
        if ($delete) {
            $this->success("删除成功!", 'admin/index');
        } else {
            $this->error("删除失败，请重试！");
        }
    }

    public function addadmin()
    {

        $work_id = $_POST['work_id'];
        $username = $_POST['username'];
        $level = $_POST['level'];
        $passwd1 = $_POST['passwd1'];
        $passwd2 = $_POST['passwd2'];

        if (empty($work_id) || empty($username) || empty($level) || empty($passwd1) || empty($passwd2)) {
            $this->error("不能有空输入！！！");
        }

        $exists = \app\personal\model\Personal::search($work_id);
        if (!empty($exists)) {
            $this->error("该账户已存在！");
        }
        if ($passwd1 != $passwd2) {
            $this->error("两次输入密码不一致！");
        }

        $add = \app\admin\model\Admin::addadmin($work_id, $username, $level, $passwd1, $passwd2);
        if ($add) {
            $this->success("添加成功!", 'admin/index');
        } else {
            $this->error("添加失败，请重试！");
        }

    }

    public function search()
    {
        $param = input('post.');
        $keyword = $param['keyword'];
        $status = $param['status'];
        $all_admin = \app\admin\model\Admin::searchadmin($keyword, $status);
        session('all_admin', $all_admin);

        //提示信息
        $count = Db::table('work_order')->where('work_order_status',0)->count();
        $info = db('work_order')->alias('wo')
            ->where('wo.work_order_status',0)
            ->join('customer_info ci','ci.customer_id = wo.customer_id')
            ->field('wo.description,ci.customer_name,wo.create_time,wo.work_order_id')
            ->select();
        $this->assign('count',$count);
        $this->assign('info',$info);

        return $this->fetch('index');
    }


}
