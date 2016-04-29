<?php
/**
 +------------------------------------------------------------------------------
 * 文件夹操作类
 +------------------------------------------------------------------------------
 * @category   ORG
 * @package  ORG
 * @subpackage  Net
 * @author    struggle <struggle@9ducx.com>
 * @version   $Id: Dir.class.php 2012-11-7 14:59:02Z struggle $
 +------------------------------------------------------------------------------
 */
class Dir{//类定义开始
    /**
      +----------------------------------------------------------
     * 取得目录下面的文件信息
      +----------------------------------------------------------
     * @access public
      +----------------------------------------------------------
     * @param mixed $pathname 路径
      +---------------------------------------------------------- 
     * @param num $type 读取类型  0 全部  type 1 只读文件夹   type 2 只读文件 
      +----------------------------------------------------------
     */
    public function listFile($pathname,$type=0,$pattern = '*') {
        static $_listDirs = array();
        $guid = md5($pathname . $pattern);
        if (!isset($_listDirs[$guid])) {
            $dir = array();
            $list = glob($pathname . $pattern);
            foreach ($list as $i => $file) {
                //$dir[$i]['filename']    = basename($file);
                //basename取中文名出问题.改用此方法
                //编码转换.把中文的调整一下.
            	if(is_dir($file)){
            		if($type != 2){
            			$dir[$i]['filename'] = preg_replace('/^.+[\\\\\\/]/', '', $file);
               			$dir[$i]['pathname'] = realpath($file);
                		$dir[$i]['owner'] = fileowner($file);
		                $dir[$i]['perms'] = substr(sprintf("%o",fileperms($file)),-4);
		                $dir[$i]['group'] = filegroup($file);
		                $dir[$i]['path'] = dirname($file);
		                $dir[$i]['atime'] = fileatime($file);
		                $dir[$i]['ctime'] = filectime($file);
		                //$dir[$i]['size'] = filesize($file);
		                $dir[$i]['type'] = 'dir';
		                $dir[$i]['mtime'] = filemtime($file);
		                $dir[$i]['isDir'] = true;
		                $dir[$i]['isFile'] = false;
            		}
            	}elseif(is_file($file)){
            		if($type != 1){
            			$dir[$i]['filename'] = preg_replace('/^.+[\\\\\\/]/', '', $file);
		                $dir[$i]['pathname'] = realpath($file);
		                $dir[$i]['owner'] = fileowner($file);
		                $dir[$i]['perms'] = substr(sprintf("%o",fileperms($file)),-4);
		                $dir[$i]['inode'] = fileinode($file);
		                $dir[$i]['group'] = filegroup($file);
		                $dir[$i]['path'] = dirname($file);
		                $dir[$i]['atime'] = fileatime($file);
		                $dir[$i]['ctime'] = filectime($file);
		                $dir[$i]['size'] = filesize($file);
		                $dir[$i]['type'] = filetype($file);
		                $dir[$i]['ext'] = is_file($file) ? strtolower(substr(strrchr(basename($file), '.'), 1)) : '';
		                $dir[$i]['mtime'] = filemtime($file);
		                $dir[$i]['isDir'] = false;
		                $dir[$i]['isFile'] = true;
		                $dir[$i]['isExecutable']= function_exists('is_executable')?is_executable($file):'';
		                $dir[$i]['isReadable'] = is_readable($file);
		                $dir[$i]['isWritable'] = is_writable($file);
            		}
            	}
                
            }
            $cmp_func = create_function('$a,$b', '
			$k  =  "isDir";
			if($a[$k]  ==  $b[$k])  return  0;
			return  $a[$k]>$b[$k]?-1:1;
			');
            // 对结果排序 保证目录在前面
            usort($dir, $cmp_func);
            $_listDirs[$guid] = $dir;
            return $_listDirs[$guid];
        } else {
            return $_listDirs[$guid];
        }
    }

    /**
      +----------------------------------------------------------
     * 判断目录是否为空
      +----------------------------------------------------------
     * @access static
      +----------------------------------------------------------
     * @return void
      +----------------------------------------------------------
     */
    public function isEmpty($directory) {
        $handle = opendir($directory);
        while (($file = readdir($handle)) !== false) {
            if ($file != "." && $file != "..") {
                closedir($handle);
                return false;
            }
        }
        closedir($handle);
        return true;
    }

    /**
      +----------------------------------------------------------
     * 取得目录中的结构信息
      +----------------------------------------------------------
     * @access static
      +----------------------------------------------------------
     * @return void
      +----------------------------------------------------------
     */
    public function getList($directory) {
        return scandir($directory);
    }

    /**
      +----------------------------------------------------------
     * 删除目录（包括下面的文件）
      +----------------------------------------------------------
     * @access static
      +----------------------------------------------------------
     * @return void
      +----------------------------------------------------------
     */
    public function delDir($directory, $subdir = true) {
        if (is_dir($directory) == false) {
            exit("The Directory Is Not Exist!");
        }
        $handle = opendir($directory);
        while (($file = readdir($handle)) !== false) {
            if ($file != "." && $file != "..") {
                is_dir("$directory/$file") ?
                                Dir::delDir("$directory/$file") :
                                unlink("$directory/$file");
            }
        }
        if (readdir($handle) == false) {
            closedir($handle);
            rmdir($directory);
        }
    }

    /**
      +----------------------------------------------------------
     * 删除目录下面的所有文件，但不删除目录
      +----------------------------------------------------------
     * @access static
      +----------------------------------------------------------
     * @return void
      +----------------------------------------------------------
     */
    public function del($directory) {
        if (is_dir($directory) == false) {
            exit("The Directory Is Not Exist!");
        }
        $handle = opendir($directory);
        while (($file = readdir($handle)) !== false) {
            if ($file != "." && $file != ".." && is_file("$directory/$file")) {
                unlink("$directory/$file");
            }
        }
        closedir($handle);
    }

    /**
      +----------------------------------------------------------
     * 复制目录
      +----------------------------------------------------------
     * @access static
      +----------------------------------------------------------
     * @return void
      +----------------------------------------------------------
     */
    public function copyDir($source, $destination,$overwrite = false) {
        if (is_dir($source) == false) {
            exit("The Source Directory Is Not Exist!");
        }
        if (is_dir($destination) == false) {
            mkdir($destination, 0700);
        }
        $handle = opendir($source);
        while (false !== ($file = readdir($handle))) {
            if ($file != "." && $file != "..") {
                is_dir("$source/$file") ?
                                Dir::copyDir("$source/$file", "$destination/$file") :
                                $this->copyFile("$source/$file", "$destination/$file",$overwrite);
            }
        }
        closedir($handle);
    }
    /**
     +----------------------------------------------------------
     * 复制文件
     +----------------------------------------------------------
     * @access static
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
     */
    public function copyFile($source,$destination,$overwrite = false) {
    	if(!file_exists($source) || !is_file($source)) {
    		return false;
    	}
    	if(file_exists($destination) && !$overwrite) {
    		return false;
    	}else{
    		if(copy($source,$destination)){
    			return true;
    		}else{
    			return false;
    		}
    	}
    }
    /**
     +----------------------------------------------------------
     * 移动目录
     +----------------------------------------------------------
     * @access static
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
     */
    public function moveDir($source,$destination,$overwrite = false){
    	if(is_dir($source) == false) {
    		exit("The Source Directory Is Not Exist!");
    	}
    	if (is_dir($destination) == false) {
    		mkdir($destination, 0700);
    	}
    	$handle = opendir($source);
    	while (false !== ($file = readdir($handle))) {
    		if ($file != "." && $file != "..") {
    			is_dir("$source/$file") ?
    			Dir::moveDir("$source/$file", "$destination/$file") :
    			$this->moveFile("$source/$file", "$destination/$file",$overwrite);
    		}
    	}
    	closedir($handle);
    	return rmdir($source);
    }
    /**
     +----------------------------------------------------------
     * 移动文件
     +----------------------------------------------------------
     * @access static
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
     */
    public function moveFile($source,$destination,$overwrite = false) {
    	if(!file_exists($source) || !is_file($source)) {
    		return false;
    	}
    	if(file_exists($destination) && !$overwrite) {
            return false;
        }else{
        	if(copy($source,$destination)){
        		unlink($source);
        		return true;
        	}else{
        		return false;
        	}
        }
    }
}
?>