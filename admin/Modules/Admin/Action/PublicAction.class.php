<?php
/**
 * 系统公共方法查询
 * @version        JiuduCMS 1.9.22 2014年09月22日09:00:00 struggle $
 * @package        JiuduCMS.Administrator
 * @copyright      Copyright (c) 2011 - 2014, 9ducx, Inc.
 * @license        http://www.jiuducms.com/help/
 * @link           http://www.jiuducms.com/
 */
class PublicAction extends Action {
	// 检查用户是否登录
	function _initialize() {
		if(!isset($_SESSION[C('USER_AUTH_KEY')])) {
			if (IS_AJAX){
        		$data['status'] =   301;
        		$this->ajaxReturn($data);
        	} else {
        		//跳转到认证网关
        		redirect ( PHP_FILE . C ('USER_AUTH_GATEWAY' ));
        	}
		}
	}
    // 后台首页 查看系统信息
    public function main() {
        $info = array(
            '操作系统'=>PHP_OS,
            '运行环境'=>$_SERVER["SERVER_SOFTWARE"],
            'PHP运行方式'=>php_sapi_name(),
            '系统版本'=>JIUDU_VERSION,
        	'发布日期'=>date("Y年m月d日",JIUDU_RELEASE),
            '上传附件限制'=>ini_get('upload_max_filesize'),
            '执行时间限制'=>ini_get('max_execution_time').'秒',
            '服务器时间'=>date("Y年m月d日 H:i:s"),
            '北京时间'=>gmdate("Y年m月d日 H:i:s",time()+8*3600),
            '服务器域名/IP'=>$_SERVER['SERVER_NAME'].' [ '.gethostbyname($_SERVER['SERVER_NAME']).' ]',
            '剩余空间'=>round((@disk_free_space(".")/(1024*1024)),2).'M',
            'register_globals'=>get_cfg_var("register_globals")=="1" ? "ON" : "OFF",
            'magic_quotes_gpc'=>(1===get_magic_quotes_gpc())?'ON':'OFF',
            'magic_quotes_runtime'=>(1===get_magic_quotes_runtime())?'ON':'OFF',
            );
        $this->assign('info',$info);
        $this->display();
    }
    //修改登录用户资料
    public function profile(){
    	$model = M('User');
    	if(IS_POST && IS_AJAX){
    		$model->nickname	=	$_POST['nickname'];
    		$model->email	=	$_POST['email'];
    		$model->remark	=	$_POST['remark'];
    		$model->id			=	$_SESSION[C('USER_AUTH_KEY')];
    		if(false !== $model->save()) {
    			$this->success("资料修改成功，重新登录后生效");
    		}else {
    			$this->error('资料修改失败！');
    		}
    	}else{
    		$id  =  $_SESSION[C('USER_AUTH_KEY')];
    		$info = $model->field('nickname,email,remark')->find($id);
    		$this->assign('vo',$info);
    		$this->display();
    	}
    }
    //重置登录用户密码
    public function repassword(){
    	if(IS_POST && IS_AJAX){
    		$id  =  $_SESSION[C('USER_AUTH_KEY')];
    		$password = $_POST['password'];
    		$oldpassword = $_POST['oldpassword'];
    		if(''== trim($password)) {
    			$this->error('密码不能为空！');
    		}
    		$model = M('User');
    		$info = $model->field('password,verify')->find($id);
    		if(md5($oldpassword.$info['verify']) != $info['password']){
    			$this->error('旧密码不正确！');
    		}
    		$verify = String::randString(6,5);
    		$model->verify = $verify;
    		$model->password	=	md5($password.$verify);
    		$model->id			=	$id;
    		if(false !== $model->save()) {
    			$this->success("重置密码成功！");
    		}else {
    			$this->error('重置密码失败！');
    		}
    	}else{
    		$this->display();
    	}
    }
    //缩略图列表
	public function imglist(){
		$root_path = SITE_PATH.'/'.C(UPLOAD_PATH).'/';
		$root_url = '/'.C(UPLOAD_PATH).'/';
		$ext_arr = array('gif', 'jpg', 'jpeg', 'png', 'bmp');
		$dir_name = empty($_GET['dir']) ? '' : trim($_GET['dir']);
		if (!in_array($dir_name, array('', 'image', 'flash', 'media', 'file'))) {
			echo "Invalid Directory name.";
			exit;
		}
		if ($dir_name !== '') {
			$root_path .= $dir_name . "/";
			$root_url .= $dir_name . "/";
			if (!file_exists($root_path)) {
				mkdir($root_path);
			}
		}
		if (empty($_GET['path'])) {
			$current_path = realpath($root_path) . '/';
			$current_url = $root_url;
			$current_dir_path = '';
			$moveup_dir_path = '';
		} else {
			$current_path = realpath($root_path) . '/' . $_GET['path'];
			$current_url = $root_url . $_GET['path'];
			$current_dir_path = $_GET['path'];
			$moveup_dir_path = preg_replace('/(.*?)[^\/]+\/$/', '$1', $current_dir_path);
		}
		echo realpath($root_path);
		//排序形式，name or size or type
		$order = empty($_GET['order']) ? 'name' : strtolower($_GET['order']);
		//不允许使用..移动到上一级目录
		if (preg_match('/\.\./', $current_path)) {
			echo 'Access is not allowed.';
			exit;
		}
		//最后一个字符不是/
		if (!preg_match('/\/$/', $current_path)) {
			echo 'Parameter is not valid.';
			exit;
		}
		//目录不存在或不是目录
		if (!file_exists($current_path) || !is_dir($current_path)) {
			echo '目录不存在或不是目录。';
			exit;
		}
		//遍历目录取得文件信息
		$file_list = array();
		if ($handle = opendir($current_path)) {
			$i = 0;
			while (false !== ($filename = readdir($handle))) {
				if ($filename{0} == '.') continue;
				$file = $current_path . $filename;
				if (is_dir($file)) {
					$file_list[$i]['is_dir'] = true; //是否文件夹
					$file_list[$i]['has_file'] = (count(scandir($file)) > 2); //文件夹是否包含文件
					$file_list[$i]['filesize'] = 0; //文件大小
					$file_list[$i]['is_photo'] = false; //是否图片
					$file_list[$i]['filetype'] = ''; //文件类别，用扩展名判断
				} else {
					$file_list[$i]['is_dir'] = false;
					$file_list[$i]['has_file'] = false;
					$file_list[$i]['filesize'] = filesize($file);
					$file_list[$i]['dir_path'] = '';
					$file_ext = strtolower(array_pop(explode('.', trim($file))));
					$file_list[$i]['is_photo'] = in_array($file_ext, $ext_arr);
					$file_list[$i]['filetype'] = $file_ext;
				}
				$file_list[$i]['filename'] = $filename; //文件名，包含扩展名
				$file_list[$i]['datetime'] = date('Y-m-d H:i:s', filemtime($file)); //文件最后修改时间
				$i++;
			}
			closedir($handle);
		}
		
		//排序
		function cmp_func($a, $b) {
			global $order;
			if ($a['is_dir'] && !$b['is_dir']) {
				return -1;
			} else if (!$a['is_dir'] && $b['is_dir']) {
				return 1;
			} else {
				if ($order == 'size') {
					if ($a['filesize'] > $b['filesize']) {
						return 1;
					} else if ($a['filesize'] < $b['filesize']) {
						return -1;
					} else {
						return 0;
					}
				} else if ($order == 'type') {
					return strcmp($a['filetype'], $b['filetype']);
				} else {
					return strcmp($a['filename'], $b['filename']);
				}
			}
		}
		usort($file_list, 'cmp_func');
		
		$result = array();
		//相对于根目录的上一级目录
		$result['moveup_dir_path'] = $moveup_dir_path;
		//相对于根目录的当前目录
		$result['current_dir_path'] = $current_dir_path;
		//当前目录的URL
		$result['current_url'] = $current_url;
		//文件数
		$result['total_count'] = count($file_list);
		//文件列表数组
		$result['file_list'] = $file_list;
		//输出JSON字符串
		header('Content-type: application/json; charset=UTF-8');
		import("ServicesJson");
		$ServicesJson = new ServicesJson();
		echo $ServicesJson->encode($result);
    }
    //编辑器上传文件
    public function upload(){
    	$ext_arr = array(
			'image' => explode(',', C('UPLOAD_IMG_EXTS')),
			'flash' => array('swf', 'flv'),
			'media' => array('swf', 'flv', 'mp3', 'wav', 'wma', 'wmv', 'mid', 'avi', 'mpg', 'asf', 'rm', 'rmvb'),
			'file' => explode(',', C('UPLOAD_FILE_EXTS'))
		);
    	$size_arr = array(
    		'image' => C('UPLOAD_IMG_SIZE'),
    		'flash' => 3,
    		'media' => 3,
    		'file' => C('UPLOAD_FILE_SIZE')
    	);
    	$dir_name = empty($_GET['dir']) ? 'image' : trim($_GET['dir']);
    	import("UploadFile");
		$upload = new UploadFile();// 实例化上传类
		$upload->maxSize  = $size_arr[$dir_name]*1048576;// 设置附件上传大小
		$upload->autoSub   =  true;
    	$upload->subType   = 'date';
    	$upload->dateFormat = 'Y-m-d';
		$upload->saveRule = 'uniqid';
		$upload->allowExts  = $ext_arr[$dir_name];// 设置附件上传类型
		$savePath = '/'.C('UPLOAD_PATH').'/'.$dir_name.'/';
		$upload->savePath =  SITE_PATH.$savePath;// 设置附件上传目录
		import("ServicesJson");
		$json = new ServicesJson();
    	if(!$upload->upload()) {// 上传错误提示错误信息
			$err = $upload->getErrorMsg();
			echo $json->encode(array('error' => 1, 'message' => $err));
		}else{// 上传成功 获取上传文件信息
			$info = $upload->getUploadFileInfo();
			$url = $savePath.$info[0]['savename'];
			if(C('JIUDU_WATERMARK') == 1 && $dir_name == 'image' && $_POST['watermark'] == '1'){
				import('Image');
				Image::water(SITE_PATH.$url,SITE_PATH.C('JIUDU_WATERMARK_U'),null,C('JIUDU_WATERMARK_T'),C('JIUDU_WATERMARK_L'),C('JIUDU_WATERMARK_Z'));
			}
			echo $json->encode(array('error' => 0, 'url' => $url));
		}
    }
    public function delcache(){
    	if($_GET['path'] && IS_AJAX){
    		if($_GET['path'] == 'all'){
    			$path = RUNTIME_PATH;
    		}else{
    			$path = RUNTIME_PATH.$_GET['path'].'/';
    		}
    		del_dir($path);
    		$this->success ('更新缓存成功!');
    	}else{
    		$this->display();
    	}
    }
    public function renewconfig(){
    	if(IS_AJAX){
	    	if(A('Sysconfig')->freadconfig()){
	    		$this->success ('更新成功!');
	    	}else{
	    		$this->error ('更新失败!');
	    	}
    	}
    }
    function feedback(){
    	if(IS_POST && IS_AJAX){
    		$data = array(
    			'name'=>$_POST['name'],
    			'email'=>$_POST['email'],
    			'qq'=>$_POST['qq'],
    			'tid'=>$_POST['tid'],
    			'info'=>$_POST['info'],
    			'website'=>$_SERVER['SERVER_NAME'],
    			'siteip'=>gethostbyname($_SERVER['SERVER_NAME']),
    			'system'=>PHP_OS,
    			'software'=>$_SERVER["SERVER_SOFTWARE"],
    			'php_version'=>PHP_VERSION,
    			'sapiname'=>php_sapi_name(),
    			'ip'=>get_client_ip(),
    			'version'=>JIUDU_VERSION,
    			'version_code'=>JIUDU_VERSION_CODE,
    			'release'=>JIUDU_RELEASE
    		);
    		import("Curl");
    		$curl = new Curl();
    		$list = $curl->post(C('JIUDU_SERVICE_URL_FEEDBACK'), $data);
    		$vo = json_decode($list,true);
    		if ($vo['status'] == 1) { //保存成功
    			$this->success ('反馈成功!');
    		} else {
    			//失败提示
    			$this->error ('反馈失败!');
    		}
    	}else{
    		$this->display();
    	}
    }
    public function get_version(){
    	$json = file_get_contents(C('JIUDU_SERVICE_URL_VERSION'));
    	$vo = json_decode($json,true);
    	$info = array(
    		'版本名称'=>$vo['data']['name'],
    		'发布时间'=>date("Y年m月d日",$vo['data']['release']),
    		'下载地址'=>'<a href="'.$vo['data']['link'].'" target="_blank">点击下载</a>',
			'更新详细'=>'<a href="'.$vo['data']['link'].'" target="_blank">查看跟新详细</a>',
    		'更新信息'=>htmlspecialchars_decode($vo['data']['info']),
    		'更新日志'=>htmlspecialchars_decode($vo['data']['log']),
    	);
    	$this->assign('info',$info);
    	$this->display();
    }
}
?>