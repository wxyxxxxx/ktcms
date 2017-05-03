<?php
use think\Db;
   function get_fields_type(){
        $arr=db("sys_fieldtype")->select();
        $new_arr=[];
        foreach ($arr as $key => $e) {
        	$new_arr[]=['id'=>$e['id'],'name'=>$e['name']];
        }
        return $new_arr;
        // ejson(1,'获取成功',$arr);
    }

    
    function get_table_fields($table=""){
       $arr = Db::query("show full columns from `kt_$table`");	
   
        $new_arr=[];
        foreach ($arr as $key => $e) {
        	$new_arr[]=['id'=>$e['Field'],'name'=>$e['Field']];
        }
        return $new_arr;
        // ejson(1,'获取成功',$arr);
    }

    function get_tables(){
        $arr=Db::query("show table status");
        // 
        $new_arr=[];
        foreach ($arr as $key => $e) {
            $new_arr[]=['id'=>ltrim($e['Name'],'kt_'),'name'=>$e['Name']];
        }
        
        // dump($new_arr);exit;
        return $new_arr;
      
    }

    function get_fields_name($model_id=0){
    	$arr=db("sys_fields")->where("model_id",$model_id)->select();
    	return $arr;
    }

    function get_status(){
        return [['id'=>1,'name'=>'正常'],['id'=>-1,'name'=>'禁用']];
    }

    function get_sys_models(){
       $arr=db("sys_model")->order("id desc")->select();
       return $arr;
    }
    function get_sys_menus(){
       $arr=db("sys_menu")->order("sort desc")->select();
       return $arr;
    }


    function get_table_data($table=''){
        $arr=db($table)->select();
        return $arr;
    }
