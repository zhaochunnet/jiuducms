<?php
/**
 * 系统配置文件
 * @version        JiuduCMS 1.9.22 2014年09月22日09:00:00 struggle $
 * @package        JiuduCMS.Administrator
 * @copyright      Copyright (c) 2011 - 2014, 9ducx, Inc.
 * @license        http://www.jiuducms.com/help/
 * @link           http://www.jiuducms.com/
 */
class SysconfigAction extends AdminAction {
	public function index(){
		$Sysconfig = M('Sysconfig');
		$groupid = M('SysconfigType')->where('status=1')->field('id,name')->select();
		$config =array();
		foreach ($groupid as $v){
			$config[$v['id']]['name'] = $v['name'];
			$config[$v['id']]['config'] = $Sysconfig->order('id asc')->where('groupid='.$v['id'])->select();
		}
		$this->assign('voList',$config);
		$this->display();
	}
	public function add(){
		if(IS_POST && IS_AJAX){
			$Sysconfig = D('Sysconfig');
			if (!$Sysconfig->create()) {
				$this->error ( $Sysconfig->getError () );
			}
			//保存当前数据对象
			if ($Sysconfig->add ()) { //保存成功
				$this->freadconfig();
				$this->success ('新增成功!');
			} else {
				//失败提示
				$this->error ('新增失败!');
			}
		}else{
			$group = M('SysconfigType')->where('status=1')->field('id,name')->select();
			$this->assign('list',$group);
			$this->display();
		}
	}
	public function update(){
		if(IS_POST && IS_AJAX){
			$Sysconfig = M('sysconfig');
			$i=0;
			foreach($_POST as $k=>$v){
				$data['value'] = $v;
				$list=$Sysconfig->where('varname = \''.$k.'\'')->data($data)->save();
				$i+=$list;
			}
			$this->freadconfig();
			$this->success ('成功编辑'.$i.'条配置!');
		}
    }
    public function watermark(){
    	if(IS_POST && IS_AJAX){
    		$Sysconfig = M('sysconfig');
    		$i=0;
    		foreach($_POST as $k=>$v){
    			$data['value'] = $v;
    			$list=$Sysconfig->where('varname = \''.$k.'\'')->data($data)->save();
    			$i+=$list;
    		}
    		$this->freadconfig();
    		$this->success ('成功更新水印配置!');
    	}else{
    		$this->display();
    	}
    }
    public function soft(){
    	if(IS_POST && IS_AJAX){
    		$model = M('sysconfig');
    		$i=0;
    		foreach($_POST as $k=>$v){
    			$data['value'] = $v;
    			$list=$model->where('varname = \''.$k.'\'')->data($data)->save();
    			$i+=$list;
    		}
    		$this->freadconfig();
    		$this->success ('成功更新软件模型配置!');
    	}else{
    		$list = M('sysconfig')->where('groupid=8')->select();
    		foreach ($list as $k=>$v){
    			if($v['type'] == 'enum'){
    				$list[$k]['extra'] = unserialize($v['extra']);
    			}
    		}
    		$this->assign('list',$list);
    		$this->display();
    	}
    }
    public function freadconfig(){
    	del_dir(RUNTIME_PATH);
		$voList = M('sysconfig')->order('id asc')->field('varname,value')->select();
		$temphtml = '<?php
/**
 * 系统配置文件
 * @version        JiuduCMS '.JIUDU_VERSION.date(' Y年m月d日H:i:s').' struggle $
 * @package        JiuduCMS.Administrator
 * @copyright      Copyright (c) 2011 - '.date('Y').', 9ducx, Inc.
 * @license        http://www.jiuducms.com/help/
 * @link           http://www.jiuducms.com/
 */
/**
 * 系统会自动生成配置文件请勿自行修改，否则系统会出错误 
 */
return array('."\n";
		foreach ($voList as $v){
			$temphtml .= "\t".'\''.$v['varname'].'\'=>\''.$v['value'].'\','."\n";
		}
		$temphtml .= "\t".');
?>';
    	$filename = SITE_PATH.'/Conf/config.inc.php';
		$handle = fopen($filename,'w');
		$fwrite = fwrite($handle, $temphtml);
		fclose($handle);
    	if ($fwrite === FALSE) {
			return false;
    	}else{
    		return true;
    	}
    }
}