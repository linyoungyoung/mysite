<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/3/24
 * Time: 12:05
 */
namespace app\index\controller;
use app\index\logic\DbLogic;
use app\index\service\NestedSets;
use think\Db;
use think\Loader;
use think\Request;
use think\Validate;
class Power extends Auth{


    public function index(){
        //var_dump(url('index/index/index'));exit;
        $array=[
            ['id'=>1,'name'=>'1顶级分类'],
            'children'=>[['id'=>1-1,'name'=>'1-1分类'],['id'=>1-2,'name'=>'1-2分类']

            ]
        ];
        

        $rows = Db::name('power')->order('left_key')->select();
        //var_dump($list->render());exit;
        // 把分页数据赋值给模板变量list
        //dump($list);exit;
//        foreach ($rows as $row){
//            dump($row);exit;
//        }
        $this->assign('list', $rows);
        return $this->fetch();

    }

    public function create(){
        if(Request::instance()->isPost()){
            $power_name = Request::instance()->post('power_name');
            $parent_id = Request::instance()->post('parent_id');
            $route = Request::instance()->post('route');
            if($parent_id == -1){
                $parent_id = 0;
            }

            /// 实例化DbLogic,,,,是NestedSets提供的数据库操作业务逻辑层
            $db = new DbLogic();
            // 实例化数据服务层的NestedSets
            $ns = new NestedSets($db, 'h_power', 'left_key', 'right_key', 'parent_id', 'id', 'level');

            // 调用insert方法,自动计算左右边界
            $res = $ns->insert($parent_id, [
                'name' => $power_name,'route'=>$route
            ]);

        }else{
            // 将所有的分类查询出来,为了让zTree显示
            $rows =  Db::name('power')->field('id, name, parent_id pId')->order('left_key')->select();

            foreach($rows as &$row){
                //dump($row['pid']);exit;
                //$row['pId'] = $row['pid'];
                $row['open'] = true;
            }
            //dump($rows);exit;
            // 因为js只能识别json对象,所以,需要转换成json
            $this->assign('rows', json_encode($rows));
            return $this->fetch();
        }

    }


    public function change(){
        $power_id = Request::instance()->param('id');

//        $info = Db::name('power as a')
//            ->join('power b',' b.id = a.parent_id')
//            ->field('a.*,b.name parent_name')
//            ->where(['a.id'=>$power_id])
//            ->find();
        $info = Db::name('power')->find(['id'=>$power_id]);
        if(!$info){
            $this->error('权限不存在');exit;
        }
       // dump(Db::name('power')->getLastSql());
//dump($info);exit;
        if(Request::instance()->isPost()){

            $parent_id = Request::instance()->param('parent_id');
            $name = Request::instance()->param('power_name');
            $route = Request::instance()->param('route');
            //$data = $power_model->create();
            if($parent_id == -1){
                $parent_id = 0;
            }

            //dump($parent_id);
            //dump($info['parent_id']);
            //dump($parent_id != $info['parent_id']);exit;
            if($parent_id != $info['parent_id']){
                // 修改了上级!
                $db = new DbLogic();
                $ns = new NestedSets($db, 'h_power', 'left_key', 'right_key', 'parent_id', 'id', 'level');
                // 移动到某一个分类的下面
                //dump($power_id);
                //dump($parent_id);exit;
                $res = $ns->moveUnder($power_id, $parent_id);
                //dump($res);exit;
            }

            $u = Db::name('power')->where(['id'=>$power_id])->update(['name'=>$name,'route'=>$route]);
            dump($u);exit;



        }

        $rows =  Db::name('power')->field('id, name, parent_id pId, left_key, right_key')->order('left_key')->select();
        foreach($rows as &$row){
            //$row['pId'] = $row['pid'];
            $row['open'] = true;
            // 如果是子分类,就不能点,如果左边界大于上级的左边界并且右边界小于上级的右边界,就不能选
            if($row['left_key'] >= $info['left_key'] && $row['right_key'] <= $info['right_key']){
                $row['noclick'] = true;
            }

        }
        //dump($rows);exit;
        $this->assign(['info'=>$info,'rows'=> json_encode($rows)]);

        //$this->assign(['info'=>$info]);

        return $this->fetch();

    }


    //删除
    public function del(){
        $power_id = intval(Request::instance()->param('id'));
        //var_dump($power_id);exit;
        // 执行删除
        $db = new DbLogic();
        $ns = new NestedSets($db, 'h_power', 'left_key', 'right_key', 'parent_id', 'id', 'level');
        $res = $ns->delete($power_id);
        dump($res);exit;
    }




    
    

}