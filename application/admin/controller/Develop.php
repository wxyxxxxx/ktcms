<?php
namespace app\admin\controller;
use think\Db;
use think\Request;
use think\Controller;
use think\Cache;
class develop extends Controller
{   
    public $admin;
    public $admin_id;

    public function _initialize()
     {
 
        $this->assign("title","WXY");
        $this->assign("host_url","http://".$_SERVER['SERVER_NAME']);
        $action       = Request::instance()->action();
        $this->check_admin_login();
        
         
     }

     public function _empty($name)
     {  
        header("Content-type:text/html;charset=utf-8");
        exit("找不到该方法".$name);
         
     }

     public function index(){
        return $this->fetch();
     }

    public function check_admin_login(){
        $admin_id=session("admin_id");
        if ($admin_id>0) {
            $admin_name=session("account");
           $this->assign("admin_name",$admin_name);
        
           $this->admin_id=$admin_id;

        }else{
            $this->redirect("admin/login/index");exit;
        }
    }


//-------------------------------------------------------------数据库管理开始 ----------------------------------
//*********************************************
//*********************************************
//*********************************************

    public function table_list(){
      // $arr=db("sys_table")->select();
      $arr=Db::query("show table status");
      // wlog($arr);
      // dump($arr);
      $this->assign("list",$arr);
      return $this->fetch();
    }
    public function table_edit(){
      $id=input("get.id",0);
      $table=input("post.table",'aaa');
      $comment=input("post.comment",'');
      $engine=input("post.engine",'InnoDb');
      $arr=Db::query("show tables");
      foreach ($arr as $key => $e) {
        if ($table==$e['Tables_in_kt_cms']) {
           ejson(-1,'数据表已存在');
        }
      }
     
      $sql="CREATE TABLE `$table` (`id` int(11) unsigned NOT NULL AUTO_INCREMENT,  PRIMARY KEY (`id`)) ENGINE=$engine DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin COMMENT='".$comment."';";
      $res=Db::query($sql);
      ejson(1,'新增成功');
    }

    public function table_delete(){
      $table=input("post.table",'');
      $sql="DROP TABLE `$table`;";
      if (in_array($table, ['kt_sys_table','kt_sys_logs','kt_sys_fields','kt_sys_admin','kt_sys'])) {
        ejson(-1,'系统表不可删除');
      }
      $res=Db::query($sql);
      ejson(1,'删除数据表【'.$table.'】成功');
    }

    public function table_desgin(){
      $table=input("get.table",'');
      if ($table=='kt_') {
        echo "请先设置数据表";exit;
      }
      $arr = Db::query("show full columns from `$table`");
      wlog($arr);
      

      foreach ($arr as &$e) {
         // $e['Type']=rtrim($e['Type'],')');
         $arr2=explode("(", $e['Type']);
         $e['Type']=$arr2[0];
         if (isset($arr2[1])) {
           $arr3=explode(")", $arr2[1]);
           $e['Length']=$arr3[0];
         }else{
          $e['Length']='';
         }

      }
      // dump($arr);
      $this->assign("list",$arr);
      $this->assign("table",$table);
      return $this->fetch();
    }

    public function table_model(){
      $table=input("get.table",'');
      
      $arr=Db::table($table)->order("sort desc")->select();
      $this->assign("list",$arr);
      $this->assign("table",$table);
     
      return $this->fetch();
    }



    public function save_field(){
        $table=input("get.table",'');
        $data=input("post.");
        if ($data['cz']==0) {
          $op="ADD";
        }else{
          $op="CHANGE `".$data['old_field']."`";
        }
        if ($data['default']=='') {
          $default='';
        }else{
          $default="DEFAULT '".$data['default']."'";
        }
        if ($data['length']=='') {
          $length='';
        }else{
          $length="(".$data['length'].")";
        }
        $sql="ALTER TABLE `".$table."` $op `".$data['field']."` ".$data['type'].$length." NOT NULL COMMENT '".$data['comment']."' $default ;";
        $res=Db::query($sql);
        ejson(1,'保存成功');
      // ALTER TABLE `a` ADD `cc` INT(11) NULL DEFAULT '33' AFTER `bb`, ADD PRIMARY KEY (`cc`);
    }

    public function del_field(){
        $table=input("get.table",'');
        $data=input("post.");
        $sql="ALTER TABLE `".$table."` DROP COLUMN `".$data['field']."` ;";
        $res=Db::query($sql);
        ejson(1,'删除成功');

    }

    public function table_data(){
      $tb=input("get.table",'');
      $fields = Db::query("show full columns from `$tb`");

      
      $arr= Db::table($tb)->order("id asc")->paginate(10);
      // dump($arr);exit;
      $this->assign("list",$arr);
      $this->assign("fields",$fields);
      $this->assign("table",$tb);
  
      return $this->fetch();
    }


    //-------------------------------------------------------------数据库管理结束 ----------------------------------
    //*********************************************
    //*********************************************
    //*********************************************



    public function sys_menu(){
        $arr=db("sys_menu")->select();
        $this->assign("list",$arr);
        $this->assign("tb","sys_menu");
        return $this->fetch();
    }

    public function sys_model(){
        $arr=db("sys_model")->select();
        $this->assign("list",$arr);
        $this->assign("tb","sys_menu");
        return $this->fetch();
    }

  

    public function model_fields(){
        $model_id=input("get.model_id",0);

        $model=db("sys_model")->where("id",$model_id)->find();
        if (!isset($model['table'])) {
          echo "请先选择模型";exit;
        }
        $arr=db("sys_fields")->where("model_id",$model_id)->order("sort asc")->select();
        $this->assign("list",$arr);
        $this->assign("tb","sys_fields");
        $this->assign("model_id",$model_id);
        $this->assign("model",$model);
        return $this->fetch();
    }

    public function save_model_fields(){
        $id=input("get.id",'');
        $data=input("post.");
        $arr=db("sys_fields")->where('model_id',$data['model_id'])->where("field",$data['field'])->find();
        if ($id>0) {
          db("sys_fields")->where("id",$id)->update($data);
        }else{
          if ($arr) {
            ejson(-1,'已存在');exit;
          }
          $data['c_time']=time();
          $id=db("sys_fields")->insertGetId($data);
        }
        
        ejson(1,'保存成功',['id'=>$id]);
      // ALTER TABLE `a` ADD `cc` INT(11) NULL DEFAULT '33' AFTER `bb`, ADD PRIMARY KEY (`cc`);
    }

    public function del_model_fields(){
        $id=input("get.id",'');
        
        db("sys_fields")->where("id",$id)->delete();
        
        ejson(1,'删除成功');

    }


	
	

}

