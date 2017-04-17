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
class Role extends Auth{


    public function index(){
        //var_dump(url('index/index/index'));exit;
        $page_count = 5;
        $page= empty(Request::instance()->get('page'))?1:Request::instance()->get('page');
        $list = Db::name('role')->limit($page,$page_count)->paginate($page_count);
        //var_dump($list->render());exit;
        // 把分页数据赋值给模板变量list
        $this->assign('list', $list);
        return $this->fetch();

    }

    public function create(){
        if(Request::instance()->isPost()){
            $role = Request::instance()->post('role');

            $res  = $this->validate_role($role);

            if($res['status'] == -1){
                $this->error($res['info']);
            }

            $save = Db::name('role')->insert(['role_name'=>$role]);
            if($save){
                $this->success('添加角色成功',url('index/role/index'));
            }else{
                $this->error('添加角色失败');
            }

        }else{
            return $this->fetch();
        }

    }


    public function change(){
        $role_id = Request::instance()->param('id');
        $role_info = Db::name('role')->where('id',$role_id)->find();

        if(Request::instance()->isPost()){

            $role = Request::instance()->post('role');
            $res = $this->validate_role($role);
            if($res['status'] == -1){
                $this->error($res['info']);
            }

            $save = Db::name('role')->where('id', $role_id)->update(['role_name'=>$role]);
            if($save){
                $this->success('修改成功',url('index/role/index'));
            }else{
                $this->error('修改失败');
            }



        }else{
            $this->assign(['role'=>$role_info]);

            return $this->fetch();
        }
    }


    public function power(){
        $role_id = Request::instance()->param('id');

        //dump($role_id);exit;


        $role = Db::name('role')->find(['role_id'=>$role_id]);
        if(!$role){
            $this->error('角色不存在!');

        }

        $all_power = Db::name('power')->field('id, name, parent_id pId')->order('left_key')->select();;

        $role_power= Db::name('role_power')->field('role_id')->where(['role_id'=>$role_id])->select();

        //dump($role);exit;

        if(Request::instance()->isPost()){


        }


        foreach($all_power as &$row){
            //dump($row['pid']);exit;
            //$row['pId'] = $row['pid'];
            $row['open'] = true;
        }


        $this->assign(['role'=>$role,'rows'=>json_encode($all_power)]);
        return $this->fetch();


    }



    public function validate_role($role){
        $rule=[
            'role_name'=>'unique:role',
        ];

        $msg = [
            'role_name.unique'=> '角色名已存在',
        ];

        $data = [
            'role_name'=>$role
        ];

        $validate = new Validate($rule,$msg);
        $res = $validate->check($data);

        if($res == false){
            return ['status'=>-1,'info'=>$validate->getError()];
        }else{
            return ['status'=>1];
        }

    }



}