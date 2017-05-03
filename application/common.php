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
require_once("data.php");
 function ejson($code,$msg,$data='')
{
	echo json_encode(['msg'=>$msg,'code'=>$code,'data'=>$data]);exit;
}

function wlog($arr){
	$arr=json_encode($arr);
	echo "<script>console.log(".$arr.")</script>";
}


function unicode_decode($name){
 
  $json = '{"str":"'.$name.'"}';
  $arr = json_decode($json,true);
  if(empty($arr)) return '';
  return $arr['str'];
}


function is_json($string) {
 json_decode($string);
 return (json_last_error() == JSON_ERROR_NONE);
}


function get_data_from($data_from){
	$arr=$data_from;
	$new_arr=[];
	if(strpos($arr,':')){

	    $arr2=explode(';', rtrim($arr,';'));

	    $new_arr=[];
	     
	    foreach ($arr2 as $key => $e) {
	    	$arr3=explode(':', $e);
	    	$new_arr[]=['id'=>$arr3[0],'name'=>$arr3[1]];
	    	
	    }


	}else{
		$arr2=explode('|',$arr);

		if (isset($arr2[1])) {
			$arr3=explode(',', $arr2[1]);
		}else{
			$arr3=array();
		}
		// dump($data_from);
	    $new_arr=call_user_func_array($arr2[0],$arr3);
	}
	return $new_arr;
}

function filter_input_data($data,$fields){
	
	foreach ($fields as $key => $e) {
		if ($e['edit_type']==7||$e['edit_type']==5||$e['edit_type']==16) {
			if(isset($data[$e['field']])){
				$data[$e['field']]=implode(',', $data[$e['field']]);
			}else{
				$data[$e['field']]='';
			}
						
		}
		if ($e['edit_type']==17) {
			$data[$e['field']]=md5($data[$e['field']]);
		}

		if ($e['edit_type']==9) {

			$data[$e['field']]=strtotime($data[$e['field']]);
		}
		if ($e['edit_type']==12||$e['edit_type']==13) {
			$data[$e['field']]=rtrim($data[$e['field']],',');
			$data[$e['field']]=ltrim($data[$e['field']],',');
		}
	}
	// dump($data);exit;
	return $data;
}


function get_list_show($str,$data_from){
	$arr=get_data_from($data_from);
	$str=explode(',', $str);
	$res='';

	foreach ($arr as $key2 => $e) {
		foreach ($str as $key => $n) {
			if ($e['id']==$n) {
				$res.=$e['name'].',';
			}
		}

	}
	return rtrim($res,',');
}

function get_menu($menu_id=0){
	return db("sys_menu")->where("id",$menu_id)->find();
}
function get_model($model_id=0){
	return db("sys_model")->where("id",$model_id)->find();
}

function get_fields($model_id=0,$where){
	return db("sys_fields")->where("model_id",$model_id)->where($where)->order("sort asc")->select();
}

// function get_sys_operation(){
// 	return db("sys_operation")->select();
// }
