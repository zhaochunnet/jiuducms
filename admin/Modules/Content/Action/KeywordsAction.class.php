<?php
/**
 * 文档关键字管理
 * @version        JiuduCMS 1.9.22 2014年09月22日09:00:00 struggle $
 * @package        JiuduCMS.Administrator
 * @copyright      Copyright (c) 2011 - 2014, 9ducx, Inc.
 * @license        http://www.jiuducms.com/help/
 * @link           http://www.jiuducms.com/
 */
class KeywordsAction extends AdminAction {
	public function index(){
		$Keywords = M('Keywords');
		$voList = $Keywords->select();
		$this->assign ( 'list', $voList );
		$this->display();
	}
	public function add() {
		if(IS_POST && IS_AJAX){
			$Keywords = D('Keywords');
			if (!$Keywords->create()) {
				$this->error ( $Keywords->getError () );
			}
			//保存当前数据对象
			if ($Keywords->add ()) { //保存成功
				$this->freadkeywords();
				$this->assign ( 'jumpUrl', Cookie::get ( '_currentUrl_' ) );
				$this->success ('新增关键字成功!');
			}else{
				//失败提示
				$this->error ('新增关键字失败!');
			}
		}else{
			$this->display();
		}
	}
	public function update(){
		if(IS_POST && IS_AJAX){
			$edit = $del = 0;
			$Keywords = D('Keywords');
			foreach ($_POST['zhi'] as $vl){
				if($_POST[$vl]['del']){
					$list=$Keywords->delete($_POST[$vl]['id']);
					$del+=$list;
				}else{
					$Keywords->create($_POST[$vl]);
					$list=$Keywords->save();
					$edit+=$list;
				}
			}
			$this->freadkeywords();
			$this->assign ( 'jumpUrl', Cookie::get ( '_currentUrl_' ) );
			$this->success ('成功编辑'.$edit.'/删除'.$del.'个关键字!，');
		}
    }
    public function renewkeywords(){
    	if($this->freadkeywords()){
    		$this->success ('更新成功!');
    	}else{
    		$this->error ('更新失败!');
    	}
    }
    private function freadkeywords(){
    	$filename = SITE_PATH.'/'.C('UPLOAD_PATH').'/keywords.xml';
    	$Keywords = M('Keywords');
		$voList = $Keywords->where('status=1')->field('name,url,rate')->select();
		$xml = xml_encode($voList);
		$handle = fopen($filename,'w');
    	$fwrite = fwrite($handle, $xml);
		fclose($handle);
    	if ($fwrite === FALSE) {
			return false;
    	}else{
    		unset($_SESSION['archives_keywords']);
    		return true;
    	}
    }
}