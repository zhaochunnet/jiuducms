<?php
/**
 +------------------------------------------------------------------------------
 * 文件操作类
 +------------------------------------------------------------------------------
 * @category   ORG
 * @package  ORG
 * @subpackage  Net
 * @author    struggle <struggle@9ducx.com>
 * @version   $Id: File.class.php 2012-11-7 14:59:02Z struggle $
 +------------------------------------------------------------------------------
 */
class File{
	/*
	 * curl 抓取文件
	 * $url  文件URL
	 * $timeout    超时时间
	 */
	static function curl_file_get_contents($url, $timeout = 5){
		$ch = curl_init();
	    curl_setopt($ch, CURLOPT_URL, $url);
	    curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
	    curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	    $r = curl_exec($ch);
	    curl_close($ch);
	    return $r;
	}
	/*
	 * 抓取远程文件
	 * $url  文件URL
	 * $charset    文件编码
	 */
	static function GetHttps($url, $charset = "utf-8"){
		if (extension_loaded('curl')) {
	    	$file_contents = self::curl_file_get_contents($url);
	    } else {
	        $file_contents = @file_get_contents($url);
	    }
	    $charset = strtolower($charset);
	    if ($charset == "utf-8") {
	        return $file_contents;
	    } elseif ($charset == "gb2312") {
	        $file_contents = iconv("gb2312", "UTF-8", $file_contents);
	        return $file_contents;
	    }
	}
}
?>