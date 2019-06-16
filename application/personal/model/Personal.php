<?php
namespace app\personal\model;

use think\Model;
use think\Db;

class Personal extends Model
{
    /*查询一条管理员的数据*/
    public static function search($id){
        $user=db('admin_login')->where('admin_account_id', $id)
            ->find();
        return $user;
    }

    /*更新管理员账户密码*/
    public static function updatepassword($id,$newpassword){
        $user = db('admin_login')->where('admin_account_id', $id)->update(['password' => md5($newpassword)]);
        if ($user) {
            return true;
        }else{
            return false;
        }
    }
}