<?php
use think\Cache;
use think\Db;



function table_fields_info($table=''){

	$arr = Db::query("show full columns from `$table`");

	foreach ($arr as &$e) {
	   $arr2=explode("(", $e['Type']);
	   $e['Type']=$arr2[0];
	   if (isset($arr2[1])) {
	     $arr3=explode(")", $arr2[1]);
	     $e['Length']=$arr3[0];
	   }else{
	    $e['Length']='';
	   }

	}

	return $arr;
}

function table_fields($table=''){
	$arr=db("sys_fields")->where("table",$table)->select();
	return $arr;
}


function get_admin_menus(){
  $admin=session("admin");
  $auth_arr=json_decode($admin['auth_ids'],true);
  // dump($auth_arr);exit;
  if (!isset($auth_arr)) {
  	$auth_arr=[];
  }
  $menu_ids=[];
  foreach ($auth_arr as $key => $e) {
  	$menu_ids[]=$key;
  }
  unset($e);
  $arr_top=db("sys_menu")->distinct(true)->where("status",1)->where("id",'in',$menu_ids)->field("up_id")->select();
  foreach ($arr_top as $key => $e) {
  	$menu_ids[]=$e['up_id'];
  }
  unset($e);
  $arr2=db("sys_menu")->where("status",1)->where("id",'in',$menu_ids)->order("sort asc")->select();
  // dump($arr2);exit;
  $new_arr=digui_menu($arr2);
  // dump($new_arr);exit;
  $nn=make_menus($new_arr);
  // dump($nn);exit;
  return $nn;
  // unset($e);
}

function make_menus($arr){

	
	// dump(session('admin'));exit;
	$str='';
	foreach ($arr as $key => $e) {
		if (isset($e['child'])) {
			$str.='<li>';
			$str.='<a href="#"><i class="'.$e['class'].'"></i><span class="nav-label">'.$e['name'].'</span><span class="fa arrow"></span></a>';
			$str.='<ul class="nav nav-second-level">';
			$str.=make_menus($e['child']);
			$str.='</ul></li>';
		}else{
			if ($e['url']=='') {
				$url=url('admin/common/data_list',['menu_id'=>$e['id']]);
				if ($e['param']!='') {
					$url=$url.$e['param'];
				}
			}else{
				$url=$e['url'];
			}

			$str.='<li><a class="J_menuItem" href="'.$url.'" data-index="0">'.$e['name'].'</a></li>';
		}
	}
	return $str;
}

function check_auth($operation_id,$menu_id){
	$admin=session("admin");
	$auth_arr=json_decode($admin['auth_ids'],true);
	if (!isset($auth_arr[$menu_id])) {
		$auth_arr[$menu_id]=[];
	}
	// dump($auth_arr);exit;
	if (in_array($operation_id, $auth_arr[$menu_id])) {
		return true;
	}else{
		if (request()->isAjax()) {
			ejson(-1,'没有权限');
		}else{
			echo "权限不足";exit;
		}
		
	}
}


function get_all_auth(){
	$arr2=db("sys_menu")->where("status",1)->field("id,name,operation,up_id")->order("sort asc")->select();

	$new_arr=digui_menu($arr2);
	return $new_arr;
	// dump($new_arr);exit;
}

function digui_menu($arr2=array()){
	$new_arr=[];
	$arr=[];
	foreach ($arr2 as $key => $e) {
	  $arr[$e['id']]=$e;
	}
	unset($e);
	foreach ($arr as $key => $e) {
	  if ($e['up_id']==0) {
	    $new_arr[]=& $arr[$key];
	  }else{
	    $arr[$e['up_id']]['child'][]=& $arr[$key];
	  }
	}
	unset($arr);
	return $new_arr;
}


function case_model_id($model_id=0,$id=0,$data=array()){
	$time=time();
	switch ($model_id) {
		case '1':
		return;
			$arr=$data['operation'];
			$arr=explode(',', $arr);
			// $data=[];
			foreach ($arr as $key => $e) {
				$data=['menu_id'=>$id,'operation_id'=>$e,'c_time'=>$time];
				$arr=db("sys_auth")->where("menu_id",$id)->where("operation_id",$e)->find();
				if (!$arr) {
					db("sys_auth")->insert($data);
				}
			}
			// db("sys_auth")->insertAll($data);
			unset($e);
			break;
		
		default:
			# code...
			break;
	}
}


function get_sys_operation($ids){
	// if ($type>0) {
	// 	$where="type=$type";
	// }else{
	// 	$where="1=1";
	// }


	$arr=db("sys_operation")->where("id",'in',$ids)->order("sort asc")->select();
	$new_arr=['top'=>[],'list'=>[]];
	foreach ($arr as $key => $e) {
		if ($e['type']==1) {
			$new_arr['top'][]=$e;
		}elseif ($e['type']==2) {
			$new_arr['list'][]=$e;
		}
	}
	return $new_arr;
}

function get_operation(){
	return $arr=db("sys_operation")->order("sort asc")->select();
}





