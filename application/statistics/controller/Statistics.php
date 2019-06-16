<?php
namespace app\statistics\controller;

use think\Controller;
use think\Db;
class Statistics extends Controller
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

    //    初始图表的数据,默认显示本周的数据
    //    chart1
    public function getData(){
        $data['categories'] = Db::table('worker_info')->where('status',1)->column('worker_name');
        $res = Db::table('worker_info')
            ->where('status',1)
            ->column('worker_id');
        $data['data'] = array();
        foreach ($res as $id){
            array_push($data['data'],
                Db::table('send_orders')
                    ->where('status',1)
                    ->where('worker_id',$id)
                    ->whereTime('create_time','week')
                    ->count()
            );
        }
        return json($data);
    }
    //    chart2
    public function getData1(){
        $data['categories'] = Db::table('worker_info')->where('status',1)->column('worker_name');
        $res = Db::table('worker_info')
            ->where('status',1)
            ->column('worker_id');
        $data['data'] = array();
        foreach ($res as $id){
            array_push($data['data'],
                Db::table('send_orders')
                    ->where('status',1)
                    ->where('worker_id',$id)
                    ->whereTime('create_time','week')
                    ->count()
            );
        }
        return json($data);
    }
    //    chart3
    public function getData2(){
        $data['categories'] = Db::table('repair_types')
            ->where('status',1)
            ->column('type_description');
        $res = Db::table('repair_types')
            ->where('status',1)
            ->column('type_id');
        $data['data'] = array();
        foreach ($res as $id){
            array_push($data['data'],
                Db::table('repair_types_record')
                    ->where('status',1)
                    ->where('record_type_id',$id)
                    ->whereTime('create_time','week')
                    ->count()
            );
        }
        return json($data);
    }
    //    chart4
    public function getData3(){
        $data['categories'] = Db::table('area')->where('status',1)->column('area_description');

        $res = Db::table('area')
            ->where('status',1)
            ->column('area_id');
        $data['data'] = array();
        foreach ($res as $id){
            array_push($data['data'],
                Db::table('work_order')
                    ->where('status',1)
                    ->where('area_id',$id)
                    ->whereTime('create_time','week')
                    ->count()
            );
        }
        return json($data);
    }

    //    绑定点击事件的数据
    public function getDatachart1(){

        $data = array();
        $id = request()->post('id');
        $data['categories'] = Db::table('worker_info')
            ->where('status',1)
            ->column('worker_name');
        $res = Db::table('worker_info')
            ->where('status',1)
            ->column('worker_id');
        if($id == 1){
            $data['data'] = array();
            foreach ($res as $id){
                array_push($data['data'],
                    Db::table('send_orders')
                        ->where('status',1)
                        ->where('worker_id',$id)
                        ->whereTime('create_time','month')
                        ->count()
                );
            }
        }
        else if ($id == 2){
            $data['data'] = array();
            foreach ($res as $id){
                array_push($data['data'],
                    Db::table('send_orders')
                        ->where('status',1)
                        ->where('worker_id',$id)
                        ->whereTime('create_time','last month')
                        ->count()
                );
            }
        }
        else if ($id == 3){
            $data['data'] = array();
            foreach ($res as $id){
                array_push($data['data'],
                    Db::table('send_orders')
                        ->where('status',1)
                        ->where('worker_id',$id)
                        ->whereTime('create_time','year')
                        ->count()
                );
            }
        }
        else if ($id == 4){
            $data['data'] = array();
            foreach ($res as $id){
                array_push($data['data'],
                    Db::table('send_orders')
                        ->where('status',1)
                        ->where('worker_id',$id)
                        ->whereTime('create_time','last year')
                        ->count()
                );
            }
        }
        return json($data);
    }
    public function getDatachart2(){
        $data = array();
        $id = request()->post('id');
        $data['categories'] = Db::table('worker_info')
            ->where('status',1)
            ->column('worker_name');
        $res = Db::table('worker_info')
            ->where('status',1)
            ->column('worker_id');
        if($id == 1){
            $data['data'] = array();
            foreach ($res as $id){
                array_push($data['data'],
                    Db::table('send_orders')
                        ->where('status',1)
                        ->where('worker_id',$id)
                        ->whereTime('create_time','month')
                        ->count()
                );
            }
        }
        else if ($id == 2){
            $data['data'] = array();
            foreach ($res as $id){
                array_push($data['data'],
                    Db::table('send_orders')
                        ->where('status',1)
                        ->where('worker_id',$id)
                        ->whereTime('create_time','last month')
                        ->count()
                );
            }
        }
        else if ($id == 3){
            $data['data'] = array();
            foreach ($res as $id){
                array_push($data['data'],
                    Db::table('send_orders')
                        ->where('status',1)
                        ->where('worker_id',$id)
                        ->whereTime('create_time','year')
                        ->count()
                );
            }
        }
        else if ($id == 4){
            $data['data'] = array();
            foreach ($res as $id){
                array_push($data['data'],
                    Db::table('send_orders')
                        ->where('status',1)
                        ->where('worker_id',$id)
                        ->whereTime('create_time','last year')
                        ->count()
                );
            }
        }
        return json($data);
    }
    public function getDatachart3(){
        $data = array();
        $id = request()->post('id');
        $data['categories'] = Db::table('repair_types')
            ->where('status',1)
            ->column('type_description');
        $res = Db::table('repair_types')
            ->where('status',1)
            ->column('type_id');

        if($id == 1){
            $data['data'] = array();
            foreach ($res as $id){
                array_push($data['data'],
                    Db::table('repair_types_record')
                        ->where('status',1)
                        ->where('record_type_id',$id)
                        ->whereTime('create_time','month')
                        ->count()
                );
            }

        }
        else if ($id == 2){
            $data['data'] = array();
            foreach ($res as $id){
                array_push($data['data'],
                    Db::table('repair_types_record')
                        ->where('status',1)
                        ->where('record_type_id',$id)
                        ->whereTime('create_time','last month')
                        ->count()
                );
            }
        }
        else if ($id == 3){
            $data['data'] = array();
            foreach ($res as $id){
                array_push($data['data'],
                    Db::table('repair_types_record')
                        ->where('status',1)
                        ->where('record_type_id',$id)
                        ->whereTime('create_time','year')
                        ->count()
                );
            }
        }
        else if ($id == 4){
            $data['data'] = array();
            foreach ($res as $id){
                array_push($data['data'],
                    Db::table('repair_types_record')
                        ->where('status',1)
                        ->where('record_type_id',$id)
                        ->whereTime('create_time','last year')
                        ->count()
                );
            }
        }
        return json($data);
    }
    public function getDatachart4(){
        $data = array();
        $id = request()->post('id');
        $data['categories'] = Db::table('area')->where('status',1)->column('area_description');
        $res = Db::table('area')
            ->where('status',1)
            ->column('area_id');

        if($id == 1){
            $data['data'] = array();
            foreach ($res as $id){
                array_push($data['data'],
                    Db::table('work_order')
                        ->where('status',1)
                        ->where('area_id',$id)
                        ->whereTime('create_time','month')
                        ->count()
                );
            }

        }
        else if ($id == 2){
            $data['data'] = array();
            foreach ($res as $id){
                array_push($data['data'],
                    Db::table('work_order')
                        ->where('status',1)
                        ->where('area_id',$id)
                        ->whereTime('create_time','last month')
                        ->count()
                );
            }
        }
        else if ($id == 3){
            $data['data'] = array();
            foreach ($res as $id){
                array_push($data['data'],
                    Db::table('work_order')
                        ->where('status',1)
                        ->where('area_id',$id)
                        ->whereTime('create_time','year')
                        ->count()
                );
            }
        }
        else if ($id == 4){
            $data['data'] = array();
            foreach ($res as $id){
                array_push($data['data'],
                    Db::table('work_order')
                        ->where('status',1)
                        ->where('area_id',$id)
                        ->whereTime('create_time','last year')
                        ->count()
                );
            }
        }
        return json($data);
    }

}
