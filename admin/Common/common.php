<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2012 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
// $Id$

// 循环创建目录
function mk_dir($dir, $mode = 0777) {
    if (is_dir($dir) || @mkdir($dir, $mode))
        return true;
    if (!mk_dir(dirname($dir), $mode))
        return false;
    return @mkdir($dir, $mode);
}
//读取整个文件
function freadfile($file){
  	if(!is_file($file)) {
  		$data = $file.'文件，不存在';
  		echo $data;
  		exit;
  	}
  	$handle = fopen($file, "r");
	$contents = fread($handle, filesize ($file));
	fclose($handle);
	return $contents;
}

//递归删除文件文件夹
function del_dir($path){
	if(is_dir($path)){
		$file_list= scandir($path);
		foreach ($file_list as $file){
			if( $file!='.' && $file!='..'){
				del_dir($path.'/'.$file);
			}
		}
		return rmdir($path);  	
	}else{
		return unlink($path);
	}
}
//往文件写内容
function write($filename,$data,$mode='w'){
	if (!$handle = fopen($filename,$mode)) {
       	die($filename.' 没有写入权限');
    }
    if (fwrite($handle, $data) === FALSE) {
    	die("不能写入到文件". $filename);
    }else{
    	fclose($handle);
    	return true;
    }
    return false;
}
//截取字符串
function esub($str,$length){
	import("String");
	$slice = String::msubstr($str,'0',$length);
	return $slice;
}
//返回URL
function url($id,$type='archives'){
	import('Url');
	return Url::$type($id);
}
/**
 * 系统邮件发送函数
 * @param string $to    接收邮件者邮箱
 * @param string $name  接收邮件者名称
 * @param string $subject 邮件主题 
 * @param string $body    邮件内容
 * @param string $attachment 附件列表
 * @return boolean 
 */
function send_mail($emailInfo){
	import('PHPMailer');
	$mail = new PHPMailer(); 	//PHPMailer对象
	$mail->CharSet = 'UTF-8';	//设定邮件编码，默认ISO-8859-1，如果发中文此项必须设置，否则乱码
	$mail->IsSMTP();	// 设定使用SMTP服务
	$mail->IsHTML(true);
	$mail->SMTPDebug = 0;	// 关闭SMTP调试功能 1 = errors and messages2 = messages only
	$mail->SMTPAuth = true;		// 启用 SMTP 验证功能
	if(C('JIUDU_SMTP_PORT') == 465){
		$mail->SMTPSecure = 'ssl';		// 使用安全协议
	}
	$mail->Host = C('JIUDU_SMTP_SERVER');		// SMTP 服务器
	$mail->Port = C('JIUDU_SMTP_PORT');		// SMTP服务器的端口号
	$mail->Username = C('JIUDU_SMTP_USER');		// SMTP服务器用户名
	$mail->Password = C('JIUDU_SMTP_PASSWORD');		// SMTP服务器密码
	$mail->SetFrom(C('JIUDU_FROM_MAIL'),C('JIUDU_FROM_NAME'));
	$replyEmail = C('JIUDU_REPLY_MAIL') ? C('JIUDU_REPLY_MAIL') : C('JIUDU_FROM_MAIL');
	$replyName = C('JIUDU_REPLY_NAME') ? C('JIUDU_REPLY_NAME') : C('JIUDU_FROM_NAME');
	$mail->AddReplyTo($replyEmail,$replyName);
	$mail->Subject = $emailInfo['subject'];
	$mail->MsgHTML($emailInfo['body']);
	$mail->AddAddress($emailInfo['to'],$emailInfo['name']);
	if (is_array($emailInfo['attachment'])) { // 添加附件
        foreach ($emailInfo['attachment'] as $file) {
            if (is_array($file)) {
                is_file($file['path']) && $mail->AddAttachment($file['path'], $file['name']);
            } else {
                is_file($file) && $mail->AddAttachment($file);
            }
        }
    }else{
		is_file($emailInfo['attachment']) && $mail->AddAttachment($emailInfo['attachment']);
    }
	return $mail->Send() ? true : $mail->ErrorInfo;
}

/**
 * 字节格式化 把字节数格式为 B K M G T 描述的大小
 * @return string
 */
function byte_format($size, $dec=2) {
	$a = array("B", "KB", "MB", "GB", "TB", "PB");
	$pos = 0;
	while ($size >= 1024) {
		$size /= 1024;
		$pos++;
	}
	return round($size,$dec).$a[$pos];
}