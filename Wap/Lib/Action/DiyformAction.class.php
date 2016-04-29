<?php
/**
 * 自定义表单
 * @version        JiuduCMS 1.9.22 2014年09月22日09:00:00 struggle $
 * @package        JiuduCMS.Administrator
 * @copyright      Copyright (c) 2011 - 2014, 9ducx, Inc.
 * @license        http://www.jiuducms.com/help/
 * @link           http://www.jiuducms.com/
 */
class DiyformAction extends HomeAction {
	public function _before_index(){
    	$where['id'] = $_GET['formid'];
		$field = M('Diyforms')->field('id,name,verify,tempfile,status')->where($where)->find();
		if(!$field){
			$this->error('你访问的表单不存在');
		}elseif($field['status'] == 0){
			$this->error('你访问的表单已被禁用');
		}
		$this->formdata($field);
		$this->tempfile = $field['tempfile'];
		$this->assign('field',$field);
    }
	public function post(){
		$formid = $_POST['formid'];
		if(!$formid){
			$this->error('非法请求');
		}
		$info = M('Diyforms')->field('table,verify,auditing')->find($formid);
		if(!$info){
			$this->error('参数错误');
		}
		$field = M('formfield')->field('itemname,fieldname,dtype,isnull')->where('fid='.$formid)->select();
		foreach ($field as $k=>$v){
			if($v['isnull'] == 1){
				if(!$_POST[$v['fieldname']] && !$_FILES[$v['fieldname']]['name']){
					$this->error($v['itemname'].'必须填写');
				}
			}
			if($v['dtype'] == 13){
				$_POST[$v['fieldname']] = join(',',$_POST[$v['fieldname']]);
			}elseif($v['dtype'] == 9 || $v['dtype'] == 10){
				if($_FILES[$v['fieldname']]['name']){
					$upload = $this->upload($_FILES[$v['fieldname']],$v['dtype']);
					if($upload['error'] == 0){
						$_POST[$v['fieldname']] = $upload['url'];
					}else{
						$this->error('文件上传失败,'.$upload['message']);
					}
				}
			}
		}
		$_POST['status'] = $info['auditing'] ? 0 : 1;
		$_POST['create_time'] = time();
		if($info['verify'] == 1 && $_SESSION['formverify'] != md5($_POST['formverify'])) {
			$this->error('验证码错误！');
		}
		$model = M($info['table']);
		if(false === $model->create ()) {
			$this->error ( $model->getError () );
		}
		//保存当前数据对象
		if($model->add()!==false) { //保存成功
			$this->success ('提交成功!');
		}else{
			//失败提示
			$this->error ('提交失败!');
		}
	}
	private function formdata($field){
		$formdata = '<form enctype="multipart/form-data" method="POST" name="diyform_'.$field['id'].'" class="diyform_'.$field['id'].'" action="'.U('Diyform/post').'"><table class="form_table" >';
		$formdata .= $this->tiaocode.$this->fetch(C('TEMPLETS_PATH').'/plus/diyform_'.$field['id'].'.html');
		if($field['verify'] == 1){
			$formdata .= '<tr><td class="text">验证码</td><td class="input"><input name="formverify" type="text" class="code" id="code" size="6" maxlength="8" style="width:50px" /><img align="absbottom" src="'.U('Diyform/verify').'"  onclick="this.src=\''.U('Diyform/verify').'&\'+Math.random()" style="cursor: pointer;" title="看不清？点击更换验证码"/></td></tr>';
		}
		$formdata .= '<tr><td class="text"></td><td class="submint"><input type="hidden" name="formid" value="'.$field['id'].'" /><input type="submit" name="submit" value="提交信息" class="submit button orange"></td></tr>';
		$formdata .= '</table></form>';
		$this->assign('formdata',$formdata);
		return $formdata;
	}
	public function verify(){
		$type	 =	 isset($_GET['type'])?$_GET['type']:'jpeg';
		import("Image");
		Image::buildImageVerify(4,1,$type,48,22,'formverify');
	}
	private function upload($file,$dtype = 9){
		$ext_arr = array(9=>C('MB_UPLOAD_IMG_EXTS'),10=>C('MB_UPLOAD_FILE_EXTS'));
		$size_arr = array(9=>C('MB_UPLOAD_IMG_SIZE'),10=>C('MB_UPLOAD_FILE_SIZE'));
		$dir_name = array(9=>'image',10=>'file');
		import("UploadFile");
		$upload = new UploadFile();// 实例化上传类
		$upload->maxSize  = $size_arr[$dtype]*1048576;// 设置附件上传大小
		$upload->autoSub   =  true;
		$upload->subType   = 'date';
		$upload->dateFormat = 'Y-m-d';
		$upload->saveRule = 'uniqid';
		$upload->allowExts  = explode(',',$ext_arr[$dtype]);// 设置附件上传类型
		$savePath = '/'.C('UPLOAD_PATH').'/'.$dir_name[$dtype].'/';
		$upload->savePath =  SITE_PATH.$savePath;// 设置附件上传目录
		$info = $upload->uploadOne($file);
		if($info === false) {// 上传错误提示错误信息
			$data = array('error' => 1, 'message' =>$upload->getErrorMsg());
		}else{// 上传成功 获取上传文件信息
			$url = $savePath.$info[0]['savename'];
			if(C('JIUDU_WATERMARK') == 1 && $dir_name == 'image' && is_file(SITE_PATH.C('JIUDU_WATERMARK_U'))){
				import('Image');
				Image::water(SITE_PATH.$url,SITE_PATH.C('JIUDU_WATERMARK_U'),null,C('JIUDU_WATERMARK_T'),C('JIUDU_WATERMARK_L'),C('JIUDU_WATERMARK_Z'));
			}
			$data = array('error' => 0, 'url' => $url);
		}
		return $data;
	}
	
}