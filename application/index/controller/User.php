<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/3/24
 * Time: 12:05
 */
namespace app\index\controller;
use think\Db;
use think\Loader;
use think\Request;
use think\Validate;
class User extends Auth{


    public function index(){
        //var_dump(url('index/index/index'));exit;
        $page_count = 2;
        $page= empty(Request::instance()->get('page'))?1:Request::instance()->get('page');
        $list = Db::name('user')->limit($page,$page_count)->paginate($page_count);
        //var_dump($list->render());exit;
        // 把分页数据赋值给模板变量list
        $this->assign('list', $list);
        return $this->fetch();

    }

    public function create(){
        if(Request::instance()->isPost()){
            $user = Request::instance()->post('username');
            $psd = Request::instance()->post('psd');
            $is_admin = Request::instance()->post('is_admin');

            $res = $this->user_validate();
            //var_dump($res);exit;

            if($res['status'] == -1){
                $this->error($res['info']);
            }

            $user_model = new \app\index\model\User();
            $r = $user_model->register(['user'=>$user,'password'=>$psd,'is_admin'=>$is_admin]);

            if($r == 1){
                $this->success('用户添加成功！',url('index/user/index'));
            }else{
                $this->error('用户添加失败！');
            }

        }else{
            return $this->fetch();
        }

    }


    public function change(){
        $user_id = Request::instance()->param('id');
        $user_info = Db::name('user')->where('id',$user_id)->find();

        if(Request::instance()->isPost()){
            //$user_id = Request::instance()->post('id');
            //var_dump($user_id);

            $user = Request::instance()->post('username');
            $psd = Request::instance()->post('psd');
            $confirm_psd = Request::instance()->post('confirm_psd');
            $is_admin = Request::instance()->post('is_admin');

            if(empty($user) && empty($psd)){
                //都为空不修改
                $this->error('不能将用户改为空');
            }else if($user_info['username'] == $user && !empty($psd)){
                //只修改密码
                if($confirm_psd !== $psd){
                    $this->error('确认密码不同');exit;
                }else{
                    $new_psd =psd_md5($psd);
                    $data = ['password'=>$new_psd];
                }

            }else if($user_info['username'] !== $user && empty($psd)){
                //只修改用户名
                $rule = [
                    'username' => 'unique:user',
                ];
                $msg = [
                    'unique:user'=>'用户名已存在',
                ];
                $data = [
                    'username'=>$user
                ];
                $validate = new Validate($rule,$msg);
                $res = $validate->check($data);
                if($res == false){
                    $this->error($validate->getError());
                }else{
                    $data = ['username'=>$user,'is_admin'=>$is_admin];

                }

            }else if($user_info['username'] !== $user && !empty($psd)){
                //用户名  密码都修改
                $rule=[
                    'username'=>'unique:user',
                    'confirm_psd' => 'confirm:psd',
                ];

                $msg = [
                    'username.unique'     => '用户名已存在',
                    'confirm_psd.confirm'        =>'确认密码不一致',
                ];

                $data = [
                    'username'=>$user,
                    'psd'=>$psd,
                    'confirm_psd'=>$confirm_psd
                ];

                $validate = new Validate($rule,$msg);
                $res = $validate->check($data);
                if($res == false){
                    $this->error($validate->getError());
                }else{
                    $data = ['user'=>$user,'password'=>psd_md5($psd)];
                }

            }
            //统一保存
            $save = Db::name('user')->where('id', $user_id)->update($data);
            if($save){
                $this->success('修改成功',url('index/user/index'));
            }else{
                $this->error('修改失败');
            }



        }else{
            $this->assign(['user'=>$user_info,'user_id'=>$user_id]);

            return $this->fetch();
        }
    }


    public function role(){
        $user_id = intval(Request::instance()->param('id'));


        //dump($user_id);exit;

        $user_info = Db::name('user')->find(['id'=>$user_id]);
        //dump($u)

        if(!$user_info){
            $this->error('用户不存在!');exit;
        }

        $roles = Db::name('role')->select();
        $has = Db::name('user_role')->field('role_id')->where(['user_id'=>$user_id])->select();
        $has_roles = [];
        foreach ($has as $v){
            $has_roles[]=$v['role_id'];
        }
        //dump(in_array(1,$has_roles));exit;

        if(Request::instance()->isPost()){
            $select_role  = input('param.role/a');
            //dump($select_role);exit;
            $all = [];
            //$a=[];
            foreach ($select_role as $value){
                $all[]=['user_id'=>$user_id,'role_id'=>$value];
            }

            //dump($all);exit;
            Db::name('user_role')->where(['user_id'=>$user_id])->delete();
            //exit;
            $i = Db::name('user_role')->insertAll($all);

            if($i){
                $this->success('修改成功！',url('@index/user/index'));
            }else{
                $this->error('修改失败！');
            }

            

        }


        $this->assign(['userinfo'=>$user_info,'role'=>$roles,'has_role'=>$has_roles]);
        return $this->fetch();

    }



    public function user_validate(){
        $user = Request::instance()->post('username');
        $psd = Request::instance()->post('psd');
        $confirm_psd = Request::instance()->post('confirm_psd');

        $rule=[
            'username'=>'require|unique:user',
            'psd'=>'require',
            'confirm_psd' => 'require|confirm:psd',
        ];

        $msg = [
            'username.require' => '用户名不能为空',
            'username.unique'     => '用户名已存在',
            'psd.require'   => '密码不能为空',
            'confirm_psd.require'  => '确认密码不能为空',
            'confirm_psd.confirm'        =>'确认密码不一致',
        ];

        $data = [
            'username'=>$user,
            'psd'=>$psd,
            'confirm_psd'=>$confirm_psd
        ];

        $validate = new Validate($rule,$msg);
        $res = $validate->check($data);
        if($res){
            return ['status'=>1,'info'=>''];
        }else{
            return ['status'=>-1,'info'=>$validate->getError()];
        }

    }

}