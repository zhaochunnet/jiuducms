<?php
/**
 * 模型字段管理
 * @version        JiuduCMS 1.9.22 2014年09月22日09:00:00 struggle $
 * @package        JiuduCMS.Administrator
 * @copyright      Copyright (c) 2011 - 2014, 9ducx, Inc.
 * @license        http://www.jiuducms.com/help/
 * @link           http://www.jiuducms.com/
 */
class FieldAction extends AdminAction {
	public function index(){
		$id = $_GET['id'];
		$list = M('Field')->field(C('DB_PREFIX').'field.id as id,itemname,fieldname,vdefault,isnull,autofield,fieldset,name')->order('id ASC')->where('mid='.$id)->join(C('DB_PREFIX').'field_type ON '.C('DB_PREFIX').'field_type.id = '.C('DB_PREFIX').'field.dtype')->select();
		$this->assign('mid',$id);
		$this->assign('list',$list);
		$this->display();
	}
	public function add(){
		if(IS_POST && IS_AJAX){
			$fields = $this->GetField($_POST['itemname'],$_POST['dtype'],$_POST['fieldname'],$_POST['vdefault'],$_POST['maxlength'],$_POST['isnull'],$_POST['msg']);
			$_POST['fieldset'] = $fields[1];
			$model = D('Field');
			if (false === $model->create ()) {
				$this->error ( $model->getError () );
			}
			//保存当前数据对象
			$table = D('conmodel')->where('id='.$_POST['mid'])->getField('addtable');
			$sql = 'ALTER TABLE '.C('DB_PREFIX').$table.' ADD '.$fields[0];
			if($model->execute($sql) === false){
				$this->error ($sql);
			}
			if ($model->add() !== false) { //保存成功
				$this->success ('新增成功!');
			}else{
				//失败提示
				$this->error ('新增失败!');
			}
		}else{
			$fieldtype = M('FieldType')->field('id,name,field')->where('status = 1')->select();
			$this->assign('fieldtype',$fieldtype);
			$this->display ();
		}
    }
	public function update(){
		if(IS_POST && IS_AJAX){
			$fields = $this->GetField($_POST['itemname'],$_POST['dtype'],$_POST['fieldname'],$_POST['vdefault'],$_POST['maxlength'],$_POST['isnull'],$_POST['msg']);
			$_POST['fieldset'] = $fields[1];
			$model = D('Field');
			$data = $model->create ();
			if (false === $data) {
				$this->error ( $model->getError () );
			}
			$table = D('conmodel')->where('id='.$_POST['mid'])->getField('addtable');
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
			$fieldtype = M('FieldType')->field('id,name,field')->where('status = 1')->select();
			$vo = M('Field')->find($_GET['id']);
			$this->assign('fieldtype',$fieldtype);
			$this->assign ('vo',$vo);
			$this->display ();
		}
    }
    public function _before_foreverdelete(){
    	$conmodel = M('conmodel');
		$table = $conmodel->field("addtable")->find($_GET['mid']);
		$table['addtable'] = C('DB_PREFIX').$table['addtable'];
		$field = M('Field')->field("fieldname,autofield")->find($_GET['id']);
		if($field['autofield'] == 0){
			$this->error ('固化字段不可以删除!');
		}
    	$sql = 'ALTER TABLE '.$table['addtable'].' DROP '.$field['fieldname'];
		if($conmodel->execute($sql) === false){
			$this->error ($sql);
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
    	$fields[1] = '<field:'.$field['label_type'].' itemname="'.$itemname.'" name="'.$fieldname.'" id="'.$fieldname.'" class="'.$field['class'].$isnull.'" type="'.$field['input_type'].'" value="'.$dfvalue.'" msg="'.$msg.'"></field:'.$field['label_type'].'>'."\n";
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