<?php
namespace app\admin\model;

use think\Model;
use think\Db;

class Admin extends Model
{
    /*查询所有管理员的数据*/
    public static function getalladmin(){
        $status = 1;
        $all_user=db('admin_login')->where('status', $status)->select();
        return $all_user;
    }

    /*修改管理员信息*/
    public static function updateadmin($id,$level,$npasswd){
        $user = db('admin_login')->where('admin_account_id', $id)->update(['password' => md5($npasswd), 'admin_level' => $level]);
        if ($user) {
            return true;
        }else{
            return false;
        }
    }

    /*删除管理员*/
    public static function deleteadmin($id){
        $user = db('admin_login')->where('admin_account_id', $id)->update(['status' => 0]);
        if ($user) {
            return true;
        }else{
            return false;
        }
    }

    /*创建管理员*/
    public static function addadmin($work_id,$username,$level,$passwd){
        $user = ['admin_account_id'=>$work_id,
            'username'=>$username,
            'admin_level'=>$level,
            'password'=>md5($passwd),
            'status'=>1,
            'admin_id'=>$work_id,
            'operator_id'=>$work_id];
        $ok = db('admin_login')->insert($user);
        if ($ok) {
            return true;
        }else{
            return false;
        }
    }

    /*搜索*/
    public static function searchadmin($keyword,$status){
        $where['admin_login.username|admin_login.admin_id'] = ['like', '%' . $keyword . '%'];
        if ($status==0){
            $all_user=db('admin_login')->where('status', 1)->where($where)->select();
        }
        else{
            $all_user=db('admin_login')->where('status', 1)->where($where)->where('admin_level', $status)->select();
        }
        return $all_user;

    }
}