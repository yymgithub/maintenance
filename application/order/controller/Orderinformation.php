<?php
namespace app\order\controller;

use think\Controller;
use think\Db;
class Orderinformation extends Controller
{
    public function index()
    {
        if (!session('?user')){
            $this->error("使用系统前请先登录！！！");
            $this->redirect('/login/login/index');
        }

        $orderid = input('id');
        //$model1为OrderInformation的model
        $model1 = model('OrderInformation');
        //$model2为SendOrder的model
        $model2 = model('SendOrder');
            //获取一条订单的派单最新信息
        $sendorders = $model1->getSendOrderByOrderId($orderid,1);

        $this->assign('sendorders', $sendorders);
        //获取一条订单的报修人、订单描述等基本信息
        $baseorder = $model1->getCustomerAndBaseByOrderId($orderid);
        $this->assign('baseorder', $baseorder);

        //获取一条报修类别信息
        $repairtypes = $model1->getRepairTypesByOrderId($orderid);
        $this->assign('repairtypes', $repairtypes);

        //获取一条订单的维修结果信息
        $repairresults = $model1->getRepairResultsByOrderId($orderid);
        $this->assign('repairresults', $repairresults);

        //获取一条订单的评论结果信息
        $evaresults = $model1->getEvaluateResultsByOrderId($orderid);
        $this->assign('evaresults', $evaresults);

        //获取一条订单的异常结果最新或者0条信息
        $exresults = $model1->getExceptionByOrderId($orderid);
        $this->assign('exresults', $exresults);

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
}
