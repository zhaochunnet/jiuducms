<?php
/**
 * 自定义表单字段管理
 * @version        JiuduCMS 1.9.22 2014年09月22日09:00:00 struggle $
 * @package        JiuduCMS.Administrator
 * @copyright      Copyright (c) 2011 - 2014, 9ducx, Inc.
 * @license        http://www.jiuducms.com/help/
 * @link           http://www.jiuducms.com/
 */
class FormfieldAction extends AdminAction {
	public function index(){
		$id = $_GET['id'];
		$list = M('Formfield')->field(C('DB_PREFIX').'formfield.id as id,itemname,fieldname,vdefault,isnull,listfield,fieldset,name')->order('id ASC')->where('fid='.$id)->join(C('DB_PREFIX').'field_type ON '.C('DB_PREFIX').'field_type.id = '.C('DB_PREFIX').'formfield.dtype')->select();
		$this->assign('fid',$id);
		$this->assign('list',$list);
		$this->display();
	}
	public function add(){
		if(IS_POST && IS_AJAX){
			$fields = $this->GetField($_POST['itemname'],$_POST['dtype'],$_POST['fieldname'],$_POST['vdefault'],$_POST['maxlength'],$_POST['isnull'],$_POST['msg']);
			$_POST['fieldset'] = $fields[1];
			$model = D('Formfield');
			if (false === $model->create ()) {
				$this->error ( $model->getError () );
			}
			//保存当前数据对象
			$table = D('Diyforms')->where('id='.$_POST['fid'])->getField('table');
			$sql = 'ALTER TABLE '.C('DB_PREFIX').$table.' ADD '.$fields[0];
			if($model->execute($sql) === false){
				$this->error ('新增失败!');
			}
			if ($model->add() !== false) { //保存成功
				$this->success ('新增成功!');
			}else{
				//失败提示
				$this->error ('新增失败!');
			}
		}else{
			$where['diyform'] = 1;
			$where['status'] = 1;
			$fieldtype = M('FieldType')->field('id,name,field')->where($where)->select();
			$this->assign('fieldtype',$fieldtype);
			$this->display ();
		}
    }
	public function update(){
		if(IS_POST && IS_AJAX){
			$fields = $this->GetField($_POST['itemname'],$_POST['dtype'],$_POST['fieldname'],$_POST['vdefault'],$_POST['maxlength'],$_POST['isnull'],$_POST['msg']);
			$_POST['fieldset'] = $fields[1];
			$model = D('Formfield');
			$data = $model->create ();
			if (false === $data) {
				$this->error ( $model->getError () );
			}
			$table = D('Diyforms')->where('id='.$_POST['fid'])->getField('table');
			$sql = 'ALTER TABLE '.C('DB_PREFIX').$table.' CHANGE `'.$_POST['oldfieldname'].'` '.$fields[0];
			if($model->execute($sql) === false){
				$this->error ('修改模型字段失败!');
			}
			$list=$model->where('id = '.$_POST['id'])->data($data)->save();
			if (false !== $list) {
				//成功提示
				$this->success ('修改成功!');
			} else {
				//错误提示
				$this->error ('修改失败!');
			}
		}else{
			$where['diyform'] = 1;
			$where['status'] = 1;
			$fieldtype = M('FieldType')->field('id,name,field')->where($where)->select();
			$vo = M('Formfield')->find($_GET['id']);
			$this->assign('fieldtype',$fieldtype);
			$this->assign ('vo',$vo);
			$this->display ();
		}
    }
    public function _before_foreverdelete(){
		$table = D('Diyforms')->where('id='.$_GET['fid'])->getField('table');
		$table = C('DB_PREFIX').$table;
		$fieldname = D('Formfield')->where('id='.$_GET['id'])->getField('fieldname');
		$sql = 'ALTER TABLE '.$table.' DROP '.$fieldname;
		if(M()->execute($sql) === false){
			$this->error ('删除字段失败');
		}
    }
	/*
	 * 获得字段创建信息
	 */
    private function GetField($itemname,$typeid,$fieldname,$dfvalue,$length,$isnull,$msg=''){
    	$field = M('FieldType')->field('id,field,field_type,label_type,input_type,length,verify,msg,class')->find($typeid);
    	$field_val = ($field['field_type'] =='enum' || $field['field_type'] =='set') ? $dfvalue : $length;
    	if(!$this->fieldverify($field_val, $field['verify'])){
    		 $this->error($field['msg']);
    	}
    	$field_type = $field['field_type'];
    	if($field['field_type'] =='enum' || $field['field_type'] =='set'){
    		$field_type .= '(\''.str_replace(",","','",$dfvalue).'\')';
    	}elseif($field['field'] =='imgfile' || $field['field'] =='media' || $field['field'] =='file'){
    		$field_type .= '(150)';
    	}else{
    		if($field['length']){
    			$length = $length ? $length : $field['length'];
    			$field_type .= '('.$length.')';
    		}
    	}
    	$fields[0] = ' `'.$fieldname.'` '.$field_type.' NOT NULL ';
    	$isnull = $isnull ? ' required' : '';
    	$fields[1] = '<jiudu:'.$field['label_type'].' itemname="'.$itemname.'" name="'.$fieldname.'" id="'.$fieldname.'" class="'.$field['class'].$isnull.'" type="'.$field['input_type'].'" value="'.$dfvalue.'" msg="'.$msg.'"></jiudu:'.$field['label_type'].'>'."\n";
    	return $fields;
    }
    private function fieldverify($field,$verify){
    	if(!$verify){
    		return true;
    	}
    	$vo = false;
    	$verify = unserialize($verify);
    	if($verify[0] == 'BETWEEN'){
    		$vo = ($field >= $verify[1][0] && $field <= $verify[1][1]) ? true : false;
    	}elseif($verify[0] == 'LT'){
    		$vo = ($field < $verify[1]) ? true : false;
    	}elseif($verify[0] == 'GT'){
    		$vo = ($field > $verify[1]) ? true : false;
    	}elseif($verify[0] == 'LTE'){
    		$vo = ($field <= $verify[1]) ? true : false;
    	}elseif($verify[0] == 'GTE'){
    		$vo = ($field >= $verify[1]) ? true : false;
    	}elseif($verify[0] == 'REGEX'){
	    	$vo = preg_match('/'.$verify[1].'/i',$field) ? true : false;
    	}
    	return $vo;
    }
}