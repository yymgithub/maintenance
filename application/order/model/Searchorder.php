<?php

namespace app\order\model;

use think\Model;
use think\Db;

class Searchorder extends Model
{
    /*模糊查询工单数据*/
    public static function fuzsearch($keyword, $status)
    {
        $where['work_order.description|work_order.address|work_order.work_order_id|work_order.phone '] = ['like', '%' . $keyword . '%'];
        $where2['t2.identity|t2.customer_name'] = ['like', '%' . $keyword . '%'];
        $where3['t3.area_description'] = ['like', '%' . $keyword . '%'];

        if ($status == 0) {
            $orders = Db::name('work_order')
                ->alias('t1')
                ->join('customer_info t2', 't1.customer_id=t2.customer_id')
                ->join('area t3', 't1.area_id=t3.area_id')
                ->where($where)
                ->whereOr($where2)
                ->whereOr($where3)
                ->where('t1.status',1)
                ->where('t2.status',1)
                ->where('t3.status',1)
                ->field('t1.work_order_id,t2.customer_name,t2.identity,t1.create_time,t1.description,t1.address,t1.phone,t1.work_order_status,t3.area_description')
                ->select();
        } else if ($status == 14) {
            $orders = Db::name('work_order')
                ->alias('t1')
                ->join('customer_info t2', 't1.customer_id=t2.customer_id')
                ->join('area t3', 't1.area_id=t3.area_id')
                ->where('t1.status',1)
                ->where('t2.status',1)
                ->where('t3.status',1)
                ->where(function ($query) use ($status, $where) {
                    $query->where('work_order.work_order_status', 'in', [0, 3])
                        ->where($where)->where('t1.status', 1);
                })
                ->whereOr(function ($query) use ($status, $where2) {
                    $query->where('work_order.work_order_status', 'in', [0, 3])
                        ->where($where2)->where('t1.status', 1);
                })
                ->whereOr(function ($query) use ($status, $where3) {
                    $query->where('work_order.work_order_status', 'in', [0, 3])
                        ->where($where3)->where('t1.status', 1);
                })
                ->field('t1.work_order_id,t2.customer_name,t2.identity,t1.create_time,t1.description,t1.address,t1.phone,t1.work_order_status,t3.area_description')
                ->select();
        } else {
            $orders = Db::name('work_order')
                ->alias('t1')
                ->join('customer_info t2', 't1.customer_id=t2.customer_id')
                ->join('area t3', 't1.area_id=t3.area_id')
                ->where('t1.status',1)
                ->where('t2.status',1)
                ->where('t3.status',1)
                ->where(function ($query) use ($status, $where) {
                    $query->where('work_order.work_order_status', $status - 1)
                        ->where($where)->where('t1.status', 1);
                })
                ->whereOr(function ($query) use ($status, $where2) {
                    $query->where('work_order.work_order_status', $status - 1)
                        ->where($where2)->where('t1.status', 1);
                })
                ->whereOr(function ($query) use ($status, $where3) {
                    $query->where('work_order.work_order_status', $status - 1)
                        ->where($where3)->where('t1.status', 1);
                })
                ->field('t1.work_order_id,t2.customer_name,t2.identity,t1.create_time,t1.description,t1.address,t1.phone,t1.work_order_status,t3.area_description')
                ->select();
        }
        return $orders;
    }
}