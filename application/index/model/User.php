<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/3/27
 * Time: 9:35
 */

namespace app\index\model;

use think\Model;

class User extends Model{



    protected $autoWriteTimestamp = true;

    public function register($user){
        $data=[
          'username'=>$user['user'],
          'password'=>psd_md5($user['password']),
          'is_admin'=>$user['is_admin']
        ];
        $res = $this->save($data);
        return $res;
    }
    
    


}