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
function encode($string = '', $skey = 'jiuducms') {
	$skey = str_split(base64_encode($skey));
	$strArr = str_split(base64_encode($string));
	$strCount = count($strArr);
	foreach ($skey as $key => $value) {
		$key < $strCount && $strArr[$key].=$value;
	}
	return str_replace('=', 'jiudu', join('', $strArr));
}
function decode($string = '', $skey = 'jiuducms') {
	$skey = str_split(base64_encode($skey));
	$strArr = str_split(str_replace('jiudu', '=', $string), 2);
	$strCount = count($strArr);
	foreach ($skey as $key => $value) {
		$key < $strCount && $strArr[$key][1] === $value && $strArr[$key] = $strArr[$key][0];
	}
	return base64_decode(join('', $strArr));
}
/**
 * 友好的时间显示
 *
 * @param int    $sTime 待显示的时间
 * @param string $type  类型. normal | mohu
 * @return string
 */
function fdate($sTime,$type = 'normal') {
	//sTime=源时间，cTime=当前时间，dTime=时间差
	$cTime        =    time();
	$dTime        =    $cTime - $sTime;
	$dDay        =    intval(date("z",$cTime)) - intval(date("z",$sTime));
	//$dDay        =    intval($dTime/3600/24);
	$dYear        =    intval(date("Y",$cTime)) - intval(date("Y",$sTime));
	//normal：n秒前，n分钟前，n小时前，日期
	if($type=='normal'){
		if( $dTime < 60 ){
			return $dTime."秒前";
		}elseif( $dTime < 3600 ){
			return intval($dTime/60)."分钟前";
		}elseif( $dTime >= 3600 && $dDay == 0  ){
			return intval($dTime/3600)."小时前";
		}elseif($dYear==0){
			return date("Y-m-d H:i:s",$sTime);
		}else{
			return date("Y-m-d H:i:s",$sTime);
		}
	}elseif($type=='mohu'){
		if( $dTime < 60 ){
			return $dTime."秒前";
		}elseif( $dTime < 3600 ){
			return intval($dTime/60)."分钟前";
		}elseif( $dTime >= 3600 && $dDay == 0  ){
			return intval($dTime/3600)."小时前";
		}elseif( $dDay > 0 && $dDay<=7 ){
			return intval($dDay)."天前";
		}elseif( $dDay > 7 &&  $dDay <= 30 ){
			return intval($dDay/7) . '周前';
		}elseif( $dDay > 30 ){
			return intval($dDay/30) . '个月前';
		}
	}else{
		return date("Y-m-d , H:i:s",$sTime);
	}
}