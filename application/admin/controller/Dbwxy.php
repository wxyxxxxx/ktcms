<?php
namespace app\admin\controller;
use think\Db;
use think\Request;
use think\Controller;
use think\Cache;
class dbwxy extends Controller
{   
    public $admin;
    public $admin_id;
    public $db;

    public function _initialize()
     {
 
        $this->assign("title","WXY");
        $this->assign("host_url","http://".$_SERVER['SERVER_NAME']);
        $action       = Request::instance()->action();
    
        $this->db=Db::connect('mysql://root:64400c8e61@120.24.214.120:3306/pg_ktcms#utf8mb4');
         
     }

     public function _empty($name)
     {  
        header("Content-type:text/html;charset=utf-8");
        exit("找不到该方法".$name);
         
     }

     public function index(){
        $arr=$this->db->name("kt_sys_fields")->find();
        dump($arr);exit;
        return $this->fetch();
     }


	
	

}

