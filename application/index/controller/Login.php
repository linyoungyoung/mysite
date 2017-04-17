<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/3/24
 * Time: 11:52
 */
namespace app\index\controller;
use geetest\GeetestLib;
use think\Config;
use think\Controller;

class Login extends Controller{

    public function login()
    {
        return $this->fetch();
    }

    //登录
    public function do_login(){

        $username = $this->request->instance()->post('username');
        $password = $this->request->instance()->post('password');

        //echo json_encode($username);


    }


    /**
     * gee 验证验证码
     */
    public function verify(){
        $gee_config=Config::get('gee');
        //var_dump($gee_config);exit;
        $GtSdk = new GeetestLib($gee_config['captcha_id'], $gee_config['private_key']);
        //var_dump($GtSdk);exit;
        session_start();
        $user_id = "test";
        $status = $GtSdk->pre_process($user_id);
        $_SESSION['gtserver'] = $status;
        $_SESSION['user_id'] = $user_id;
        echo $GtSdk->get_response_str();
    }

    /**
     * gee 刷新获取验证码
     */
    public function login_gee(){
        session_start();
        $gee_config=Config::get('gee');
        //var_dump($gee_config);exit;
        $GtSdk = new GeetestLib($gee_config['captcha_id'], $gee_config['private_key']);

        $user_id = $_SESSION['user_id'];
        if ($_SESSION['gtserver'] == 1) {   //服务器正常
            $result = $GtSdk->success_validate($_POST['geetest_challenge'], $_POST['geetest_validate'], $_POST['geetest_seccode'], $user_id);
            if ($result) {
                echo '{"status":"success"}';
            } else{
                echo '{"status":"fail"}';
            }
        }else{  //服务器宕机,走failback模式
            if ($GtSdk->fail_validate($_POST['geetest_challenge'],$_POST['geetest_validate'],$_POST['geetest_seccode'])) {
                echo '{"status":"success"}';
            }else{
                echo '{"status":"fail"}';
            }
        }
    }

}