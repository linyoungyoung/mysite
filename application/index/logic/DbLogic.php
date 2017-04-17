<?php
namespace app\index\logic;

use think\Db;

class DbLogic implements DbMysqlInterface
{
    /**
     * DB connect
     *
     * @access public
     *
     * @return resource connection link
     */
    public function connect(){
        echo __METHOD__,"<BR/>";
    }

    /**
     * Disconnect from DB
     *
     * @access public
     *
     * @return viod
     */
    public function disconnect(){

        echo __METHOD__,"<BR/>";
    }

    /**
     * Free result
     *
     * @access public
     * @param resource $result query resourse
     *
     * @return viod
     */
    public function free($result){

        echo __METHOD__,"<BR/>";
    }

    /**
     * Execute simple query
     *
     * @access public
     * @param string $sql SQL query
     * @param array $args query arguments
     *
     * @return resource|bool query result
     */
    public function query($sql, array $args = array()){
        $args = func_get_args();
        $args = $args[0];
        dump($args);//exit;
        // UPDATE ?T SET ?F = ?F + 2, ?F = IF(?F >= ?N, ?F + 2, ?F) WHERE ?F >= ?N
        $sql = array_shift($args);
        $reg = '/\?[A-Z]/';
        $sqlSplitArray = preg_split($reg, $sql);
        $lastSql = '';
        //dump($sqlSplitArray);exit;
        foreach($sqlSplitArray as $key => $sqlSplit){
            if($key < count($sqlSplitArray)-1){
                $lastSql .= $sqlSplit . $args[$key];
            }

        }
        //dump($lastSql);exit;
        dump(Db::execute($lastSql));
    }

    /**
     * Insert query method
     *
     * @access public
     * @param string $sql SQL query
     * @param array $args query arguments
     *
     * @return int|false last insert id
     */
    public function insert($sql, array $args = array()){
        $args = func_get_args();
        $args = $args[0];
        dump($args);//exit;
        $sql = array_shift($args);
        $tableName = array_shift($args);
        $data = array_shift($args);
        $sql = str_replace('?T', $tableName, $sql);
        // insert into table set name=1, age=2
        $sqlSet = [];
        foreach($data as $key=>$value){
            $sqlSet[] = $key . '="' . $value.'"';
        }
        //dump($sqlSet);exit;
        $sqlSet = join(',', $sqlSet);
        $sql = str_replace('?%', $sqlSet, $sql);
        return Db::execute($sql);
    }

    /**
     * Update query method
     *
     * @access public
     * @param string $sql SQL query
     * @param array $args query arguments
     *
     * @return int|false affected rows
     */
    public function update($sql, array $args = array()){

        echo __METHOD__,"<BR/>";
    }

    /**
     * Get all query result rows as associated array
     *
     * @access public
     * @param string $sql SQL query
     * @param array $args query arguments
     *
     * @return array associated data array (two level array)
     */
    public function getAll($sql, array $args = array()){

        echo __METHOD__,"<BR/>";
    }

    /**
     * Get all query result rows as associated array with first field as row key
     *
     * @access public
     * @param string $sql SQL query
     * @param array $args query arguments
     *
     * @return array associated data array (two level array)
     */
    public function getAssoc($sql, array $args = array()){

        echo __METHOD__,"<BR/>";
    }

    /**
     * Get only first row from query
     *
     * @access public
     * @param string $sql SQL query
     * @param array $args query arguments
     *
     * @return array associated data array
     */
    public function getRow($sql, array $args = array()){
        $args = func_get_args();
        $args = $args[0];
        //dump($args);
        //dump($args[7]);
        // SELECT ?F, ?F, ?F, ?F FROM ?T WHERE ?F = ?N
        $sql = array_shift($args);

        // 找到特殊字符串  以?开始,后面跟一个大写字母
        $reg = '/\?[A-Z]/';
        // 使用正则截取,获取到一个数组 这个数组刚好和args保持一致
        $sqlResult = preg_split($reg, $sql);
        $lastSql = '';
        //dump($sqlResult);
        foreach($sqlResult as $key=>$sqlSplit){
            // 先连接上使用正则截取过的SQL语句,之后再连接上args对应的字符串
            if($key < count($sqlResult)-1){
                $lastSql .= $sqlSplit . $args[$key];

            }

        }
        //dump($lastSql);exit;
        $result = Db::query($lastSql);
        //dump($result);exit;
        return $result ? $result[0] : Null;
    }

    /**
     * Get first column of query result
     *
     * @access public
     * @param string $sql SQL query
     * @param array $args query arguments
     *
     * @return array one level data array
     */
    public function getCol($sql, array $args = array()){

        echo __METHOD__,"<BR/>";
    }

    /**
     * Get one first field value from query result
     *
     * @access public
     * @param string $sql SQL query
     * @param array $args query arguments
     *
     * @return string field value
     */
    public function getOne($sql, array $args = array()){

//        $args = func_get_args();
//        $args = $args[0];
//        //dump($args);
//        //dump($args[7]);
//        // SELECT ?F, ?F, ?F, ?F FROM ?T WHERE ?F = ?N
//        $sql = array_shift($args);
//
//        // 找到特殊字符串  以?开始,后面跟一个大写字母
//        $reg = '/\?[A-Z]/';
//        // 使用正则截取,获取到一个数组 这个数组刚好和args保持一致
//        $sqlResult = preg_split($reg, $sql);
//        $lastSql = '';
//        //dump($sqlResult);
//        foreach($sqlResult as $key=>$sqlSplit){
//            // 先连接上使用正则截取过的SQL语句,之后再连接上args对应的字符串
//            if($key < count($sqlResult)-1){
//                $lastSql .= $sqlSplit . $args[$key];
//
//            }
//
//        }
//        //dump($lastSql);exit;
//        $result = Db::query($lastSql);
//        return $result[0]['MAX(right_key)']?$result[0]['MAX(right_key)']:null;
        //return $result ? $result[0] : Null;
    }
}