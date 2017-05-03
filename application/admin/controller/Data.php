<?php
namespace app\admin\controller;
use think\Db;
use think\Request;
use think\Controller;
use think\Cache;
class data extends common
{   
    public $admin;
    public $admin_id;




     public function index(){
        return $this->fetch();
     }

     public function b(){
        $aa='bbb';
        $$aa=22;
        var_dump($bbb);
     }



	
	

}

