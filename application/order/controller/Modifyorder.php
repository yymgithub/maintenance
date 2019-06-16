<?php

namespace app\order\controller;
use think\Db;
use think\Controller;

class Modifyorder extends Controller
{
    public function index()
    {
        if (!session('?user')) {
            $this->error("使用系统前请先登录！！！");
            $this->redirect('/login/login/index');
        }
        $modify_order= \app\order\model\Searchorder::fuzsearch('', 2);
        $this->assign('modifyorders', $modify_order);

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

    public function updateOrder(){
        //获取参数
        $worderIds = $_POST['worderids'];
        $orderId = $_POST['orderid'];
        $orderStatus = $_POST['orderstatus'];
        $status = $_POST['status'];
        //创建数据库模型
        $model1 = model('SendOrder');
        $model2 = model('ModifyOrder');
        //创建要使用的模块
        $contrller = controller('Sendorder');
        //获取改单之前的workids
        $needmodifyworkers = $model2->getModifyWorkers($orderId);
        $ids1 = $contrller->objectToArray($needmodifyworkers);
        $res2 = $model2->updateOrder($orderId,$worderIds);
        if($res2){
            //将工人转换为企业需要的格式
            $str1 = $contrller->parsrWorkers($ids1);
            $str2 = $contrller->parsrWorkers($worderIds);
            $code1 = $contrller->urlSend($str1,2);
            $code2 = $contrller->urlSend($str2,1);
            if($status == 1){
                $this->success($str1,'modifyorder/index');
            }
            elseif ($status == 2){
                $this->success('详情改单成功',url('orderinformation/index',['id' => $orderId]));
            }
            elseif ($status == 3){
                $this->success('查询改单成功','searchorder/index');
            }
        }
        else{
            $this->error('系统错误');
        }
    }

    public function search()
    {
        $param = input('post.');
        $keyword = $param['keyword'];
        $modify_order = \app\order\model\Searchorder::fuzsearch($keyword, 2);
        $this->assign('modifyorders', $modify_order);

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
