<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件


//用户密码加密
function psd_md5($psd){
    return substr(md5($psd.'hqc'),5,25);
}

//截取过滤图片
function img_empty($content){
    $content=eregi_replace("<IMG ([a-zA-Z0-9~!& ?:\"/._#=~&%]+)>","",$content);
  return $content;
 }