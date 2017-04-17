<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/3/24
 * Time: 10:08
 */

namespace app\index\controller;

use think\Db;
use think\Request;

class Dynamic extends Auth{


    //动态首页
    public function index(){
        $page_count = 2;
        $page= empty(Request::instance()->get('page'))?1:Request::instance()->get('page');
        $list = Db::name('content')->order('top_time DESC,is_top DESC,create_time DESC')->limit($page,$page_count)->paginate($page_count);


        $all = Db::name('content')->order('top_time DESC,is_top DESC,create_time DESC')->select();
        //echo '动态首页';
//        echo '<pre/>';
        //var_dump($all[1]['content']);exit;
        //$all['intro']=mb_substr($all[1]['content'],0,100,'utf-8');
        //var_dump($all);exit;
        $real = [];
        foreach ($all as $key => $value){

            $real[$key]=$value;
            $real[$key]['intro']=mb_substr($value['content'],0,100,'utf-8');
        }
        //echo '<pre/>';
        //var_dump($real);exit;
        $this->assign(['all'=>$real,'list'=>$list]);
        return $this->fetch();
    }


    public function create(){

        //$content = Request::instance()->post('content');
        if(Request::instance()->isPost()){
            $content = Request::instance()->post('content');

            $encode = htmlspecialchars($content);

            $save = Db::name('content')->insert(['content'=>$encode,'create_time'=>time()]);
            if($save){
                $this->success('添加动态成功!',url('index/dynamic/index'));
            }else{
                $this->error('保存失败');
            }
        }

        return $this->fetch();
    }

    public function change(){
        $c_id = Request::instance()->param('id');
        //var_dump($c_id);
        if(Request::instance()->isPost()){
            $content = Request::instance()->post('content');

            $encode = htmlspecialchars($content);

            $save = Db::name('content')->where(['id'=>$c_id])->update(['content'=>$encode,'update_time'=>time()]);
            //var_dump($save);exit;
            if($save){
                $this->success('编辑成功!',url('index/dynamic/index'));
            }else{
                $this->error('编辑失败');
            }
            
        }

        //var_dump($c_id);

        $find = Db::name('content')->where(['id'=>$c_id])->find();
//        var_dump($find);exit;
        $this->assign(['content'=>html_entity_decode($find['content']),'c_id'=>$c_id]);
        return $this->fetch();

        
    }
    
    public function del(){
        $c_id = intval(Request::instance()->param('id'));
        if($c_id == 0){
            //return
            return json(['status'=>-1,'info'=>'id有误']);
        }

        $del = Db::name('content')->where(['id'=>$c_id])->delete();
        if($del){
            return  json(['status'=>1,'info'=>'删除成功']);
        }else{
            return json(['status'=>-1,'info'=>'删除失败']);
        }
    }

    //动态详情展示
    public function show(){
        $c_id = Request::instance()->param('id');
        $find = Db::name('content')->where(['id'=>$c_id])->find();

        $this->assign(['list'=>$find,'content'=>html_entity_decode($find['content'])]);
        return $this->fetch();
    }

    //置顶动态
    public function set_top(){
        $c_id = Request::instance()->param('id');
        $save = Db::name('content')->where(['id'=>$c_id])->update(['is_top'=>1,'top_time'=>time()]);
        if($save){
            $this->success('置顶成功',url('index/dynamic/index'));
        }else{
            $this->error('置顶失败');
        }
    }


}