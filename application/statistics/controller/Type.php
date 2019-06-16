<?php
namespace app\statistics\controller;

use think\Controller;
use think\Db;
class Type extends Controller
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

        //加载初始化数据
        $data = Db::table('repair_types')->where('status',1)->field('type_id,type_description')->select();
        $this->assign('data',$data);
        $max_id = Db::table('repair_types')->max('type_id');
        $this->assign('max_id',$max_id);

        return $this->fetch();
    }
    //  添加维修类别
    public function addtype(){
        $data = input('post.');
        if (empty($data['type_description'])){
            $this->error("输入不能为空！");
        }
        $exist = Db::table('repair_types')->where('type_description',$data['type_description'])
            ->where('status',1)->find();
        if ($exist){
            $this->error('该类别已存在！');
        }

        $add = Db::table('repair_types')->insert($data);
        if (empty($add)){
            $this->error('添加维修类别失败');
        }else{
            $this->success('添加维修类别成功！');
        }
    }
    //  修改维修类别
    public function edittype(){
        $data = input('post.');
        if (empty($data['edit_content'])){
            $this->error("输入不能为空！");
        }
        $exist = Db::table('repair_types')->where('type_description',$data['edit_content'])
            ->where('status',1)->find();
        if ($exist){
            $this->error('该类别已存在！');
        }
        Db::table('repair_types')
            ->where('type_id',$data['edit_id'])
            ->update(['type_description' => $data['edit_content']]);
        $this->success("修改成功！");
    }
    //  删除维修类别
    public function deltype(){
        $data = input('post.');
        Db::table('repair_types')->where('type_id',$data['del_id'])->update(['status' => 0]);
        return $this->success("删除成功！");
    }
}
