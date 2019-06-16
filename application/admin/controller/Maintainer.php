<?php
namespace app\admin\controller;

use think\Controller;
use think\Db;

class Maintainer extends Controller
{
    public function index()
    {
        if (!session('?user')){
            $this->error("使用系统前请先登录！！！");
            $this->redirect('/login/login/index');
        }
        $all_worker = \app\admin\model\Maintainer::getallworker();
        session('workers',$all_worker);

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

    public function modifymaintainer(){

        $workerid = $_POST['workerid'];
        $phone = $_POST['phone'];

        if(empty($workerid) || empty($phone)){
            $this->error("不能有空输入！！！");
        }

        if(!preg_match("/^1[34578]{1}\d{9}$/",$phone)){
            $this->error("请输入正确的电话号码！");
        }

        $updateworker= \app\admin\model\Maintainer::updateworker($workerid,$phone);
        if ($updateworker) {
            $this->success("修改成功!",'maintainer/index');
        }else{
            $this->error("修改失败，请重试！");
        }

    }

    public function deleteworker(){
        $workerid = input('id');
        $delete = \app\admin\model\Maintainer::deleteworker($workerid);
        if ($delete) {
            $this->success("删除成功!",'maintainer/index');
        }else{
            $this->error("删除失败，请重试！");
        }
    }

    public function addworker(){

        $id = $_POST['id'];
        $name = $_POST['name'];
        $phone = $_POST['phone'];

        if(empty($id) || empty($name) || empty($phone)){
            $this->error("不能有空输入！！！");
        }

        $exists = \app\admin\model\Maintainer::searchworker($id);
        if(!empty($exists)){
            $this->error("该工人已存在！");
        }

        if(!preg_match("/^1[34578]{1}\d{9}$/",$phone)){
            $this->error("请输入正确的电话号码！");
        }

        $add = \app\admin\model\Maintainer::addworker($id,$name,$phone);
        if ($add) {
            $this->success("添加成功!",'maintainer/index');
        }else{
            $this->error("添加失败，请重试！");
        }

    }

    public function search()
    {

        //提示信息
        $count = Db::table('work_order')->where('work_order_status',0)->count();
        $info = db('work_order')->alias('wo')
            ->where('wo.work_order_status',0)
            ->join('customer_info ci','ci.customer_id = wo.customer_id')
            ->field('wo.description,ci.customer_name,wo.create_time,wo.work_order_id')
            ->select();
        $this->assign('count',$count);
        $this->assign('info',$info);

        $param = input('post.');
        $keyword = $param['keyword'];
        $all_worker = \app\admin\model\Maintainer::search($keyword);
        session('workers', $all_worker);

        return $this->fetch('index');
    }
}
