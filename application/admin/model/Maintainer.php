<?php
namespace app\admin\model;

use think\Model;
use think\Db;

class Maintainer extends Model
{
    /*查询某个工人是否存在*/
    public static function searchworker($id){
        $worker=db('worker_info')->where('worker_id', $id)->find();
        return $worker;
    }

    /*查询所有工人的数据*/
    public static function getallworker(){
        $status = 1;
        $all_worker=db('worker_info')->where('status', $status)->select();
        return $all_worker;
    }

    /*修改工人信息*/
    public static function updateworker($id,$phone){
        $worker = db('worker_info')->where('worker_id', $id)->update(['phone' => $phone]);
        if ($worker) {
            return true;
        }else{
            return false;
        }
    }

    /*删除工人*/
    public static function deleteworker($id){
        $worker = db('worker_info')->where('worker_id', $id)->update(['status' => 0]);
        if ($worker) {
            return true;
        }else{
            return false;
        }
    }

    /*创建工人*/
    public static function addworker($id,$name,$phone){
        $worker = ['worker_id'=>$id,
            'worker_name'=>$name,
            'phone'=>$phone,
            'status'=>1,
            'worker_type'=>1];
        $ok = db('worker_info')->insert($worker);
        if ($ok) {
            return true;
        }else{
            return false;
        }
    }

    /*模糊查询*/
    public static function search($keyword){
        $where['worker_info.worker_name|worker_info.worker_id|worker_info.phone'] = ['like', '%' . $keyword . '%'];
        $all_user=db('worker_info')->where('status', 1)->where($where)->select();
        return $all_user;

    }
}