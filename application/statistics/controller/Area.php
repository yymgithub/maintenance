<?php
namespace app\statistics\controller;

use think\Controller;
use think\Db;
class Area extends Controller
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

        //初始化数据
        $data = Db::table('area')->where('status',1)->field('area_id,area_description')->select();
        $max_id = Db::table('area')->max('area_id');
        $this->assign('max_id',$max_id);
        $this->assign('data',$data);

        return $this->fetch();
    }
    //  修改区域名称
    public function editarea(){
        $data = input('post.');
        if (empty($data['edit_content']) ){
            $this->error("输入不能为空！");
        }
        $isexist = Db::table('area')->where('area_description',$data['edit_content'])->find();
        if ($isexist){
            $this->error('该区域已存在,请重新输入！');
        }
        Db::table('area')->where('area_id',$data['edit_id'])
            ->update(['area_description' => $data['edit_content']]);
        $this->success("修改成功！");
    }

    //删除该区域
    public function delarea(){
        $data = input('post.');
        Db::table('area')->where('area_id',$data['del_id'])->update(['status' => 0]);
        return $this->success("删除成功！");
    }

    //创建区域
    public function addarea(){
        $data = input('post.');
        if (empty($data['area_description'])){
            $this->error("输入不能为空！");
        }
        $isexist = Db::table('area')->where('area_description',$data['area_description'])
            ->where('status',1)->find();
        if ($isexist){
            $this->error('该区域已存在,请重新输入！');
        }
        $data['create_time']=date('Y-m-d H:i:s',time());
        $data['modified_time']=date('Y-m-d H:i:s',time());
        $add = Db::table('area')->insert($data);
        if (empty($add)){
            $this->error('添加区域失败');
        }else{
            $this->success('添加区域成功！');
        }
    }

}
