<?php
namespace app\order\controller;

use think\Controller;
use think\Session;
use think\Db;
class Searchorder extends Controller
{
    public function index()
    {
        if (!session('?user')){
            $this->error("使用系统前请先登录！！！");
            $this->redirect('/login/login/index');
        }
        $all_order = \app\order\model\Searchorder::fuzsearch('',0);
        $this->assign('orders',$all_order);

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

    public function search(){
        $param = input('post.');
        $keyword = $param['keyword'];
        $status = $param['status'];
        $all_order= \app\order\model\Searchorder::fuzsearch($keyword,$status);
        $this->assign('orders',$all_order);

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
