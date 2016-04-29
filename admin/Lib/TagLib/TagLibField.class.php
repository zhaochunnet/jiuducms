<?php
/**
 * 标签管理类
 * @version        JiuduCMS 1.9.22 2014年09月22日09:00:00 struggle $
 * @package        JiuduCMS.Administrator
 * @copyright      Copyright (c) 2011 - 2014, 9ducx, Inc.
 * @license        http://www.jiuducms.com/help/
 * @link           http://www.jiuducms.com/
 */
class TagLibField extends TagLib{
    // 标签定义
    protected $tags   =  array(
        // 标签定义： attr 属性列表 close 是否闭合（0 或者1 默认1） alias 标签别名 level 嵌套层次
    	'input'=>array('attr'=>'itemname,id,class,name,type,value','close'=>1),
    	'textarea'=>array('attr'=>'itemname,id,class,name,value','close'=>1),
    	'editor'=>array('attr'=>'itemname,id,class,name,value','close'=>1),
    	'date'=>array('attr'=>'itemname,id,class,name,type,value,format,readonly','close'=>1),
    	'uploadpic'=>array('attr'=>'itemname,id,class,name,type,value','close'=>1),
    	'imglists'=>array('attr'=>'itemname,id,class,name,type,value','close'=>1),
    	'uploadfile'=>array('attr'=>'itemname,id,class,name,type,value','close'=>1),
    	'filelists'=>array('attr'=>'itemname,id,class,name,type,value','close'=>1),
    	'select'=>array('attr'=>'itemname,id,class,name,value','close'=>1),
    	'radio'=>array('attr'=>'itemname,id,class,name,type,value','close'=>1),
    	'checkbox'=>array('attr'=>'itemname,id,class,name,type,value','close'=>1),
    );
    //单行文本框
    public function _input($attr){
    	$tag        = $this->parseXmlAttr($attr,'input');
    	$itemname       = $tag['itemname'];
    	$class       = $tag['class'];
    	$name       = $tag['name'];
    	$type       = $tag['type'];
    	$value       = (ACTION_NAME == 'update') ? '<{$addon.'.$name.'}>' : $tag['value'];
    	$parseStr   = '<dl class="nowrap">
			<dt>'.$itemname.'：</dt>
			<dd><input name="addon['.$name.']" type="'.$type.'" class="'.$class.'" value="'.$value.'"/><span class="info" style="float:none;">&nbsp;'.$tag['msg'].'</span></dd>
			</dl>';
    	return $parseStr;
    }
    //多行文本框
    public function _textarea($attr){
    	$tag        = $this->parseXmlAttr($attr,'textarea');
    	$itemname       = $tag['itemname'];
    	$class       = $tag['class'];
    	$name       = $tag['name'];
    	$value       = ACTION_NAME =='update' ? '<{$addon.'.$name.'}>' : $tag['value'];
    	$parseStr   = '<dl class="nowrap">
			<dt>'.$itemname.'：</dt>
			<dd><textarea name="addon['.$name.']" class="'.$class.'" style="width:400px; height:60px;">'.$value.'</textarea><span class="info" style="float:none;">&nbsp;'.$tag['msg'].'</span></dd>
			</dl>';
    	return $parseStr;
    }
    //编辑器
    public function _editor($attr){
    	$tag        = $this->parseXmlAttr($attr,'_editor');
    	$itemname       = $tag['itemname'];
    	$class       = $tag['class'];
    	$name       = $tag['name'];
    	$id       = ACTION_NAME =='update' ? $tag['id'].'_'.ACTION_NAME : $tag['id'];
    	$value       = ACTION_NAME =='update' ? '<{$addon.'.$name.'}>' : $tag['value'];
    	$parseStr   = '<dl class="nowrap">
			<dt>'.$itemname.'：</dt>
			<dd><textarea name="addon['.$name.']" id="'.$id.'" style="width:700px;height:400px;visibility:hidden;">'.$value.'</textarea><span class="info">'.$tag['msg'].'</span></dd>
			</dl>
			<script type="text/javascript">
var editor = KindEditor.create(\'#'.$id.'\',{
	uploadJson : \'index.php?g=Admin&m=Public&a=upload\',
	fileManagerJson : \'index.php?g=Admin&m=Public&a=imglist\',
	allowFileManager : true
});
$(\'#submit\').click(function(){
	$(\'#'.$id.'\').val(editor.html());
});
</script>';
    	return $parseStr;
    }
    //时间类型
    public function _date($attr){
    	$tag        = $this->parseXmlAttr($attr,'date');
    	$itemname       = $tag['itemname'];
    	$class       = $tag['class'];
    	$name       = $tag['name'];
    	$type       = $tag['type'];
    	$value       = ACTION_NAME =='update' ? '<{$addon.'.$name.'}>' : date('Y-m-d H:i:s');
    	$parseStr   = '<dl class="nowrap">
			<dt>'.$itemname.'：</dt>
			<dd><input type="'.$type.'" name="addon['.$name.']" id="'.$name.'" class="date '.$class.'" dateFmt="yyyy-MM-dd HH:mm:ss" value="'.$value.'" readonly="true"/>
				<a class="inputDateButton" href="javascript:;">选择</a><span class="info" style="float:none;">&nbsp;'.$tag['msg'].'</span></dd>
			</dl>';
    	return $parseStr;
    }
    //上传图片
    public function _uploadpic($attr){
    	$tag        = $this->parseXmlAttr($attr,'uploadpic');
    	$itemname       = $tag['itemname'];
    	$class       = $tag['class'];
    	$id       = $tag['id'];
    	$name       = $tag['name'];
    	$type       = $tag['type'];
    	$value       = ACTION_NAME =='update' ? '<{$addon.'.$name.'}>' : $tag['value'];
    	$parseStr   = '<dl class="nowrap">
			<dt>'.$itemname.'：</dt>
			<dd><input type="'.$type.'" name="addon['.$name.']" id="'.$id.'" class="'.$class.'" value="'.$value.'"/>
				<a class="button uploadpic" onclick="upload(\''.$id.'\')" href="javascript:;"><span>选择图片</span></a><span class="info" style="float:none;">&nbsp;'.$tag['msg'].'</span></dd>
			</dl>';
    	return $parseStr;
    }
    //上传文件
    public function _uploadfile($attr){
    	$tag        = $this->parseXmlAttr($attr,'uploadfile');
    	$itemname       = $tag['itemname'];
    	$class       = $tag['class'];
    	$id       = $tag['id'];
    	$name       = $tag['name'];
    	$type       = $tag['type'];
    	$value       = ACTION_NAME =='update' ? '<{$addon.'.$name.'}>' : $tag['value'];
    	$parseStr   = '<dl class="nowrap">
			<dt>'.$itemname.'：</dt>
			<dd><input type="'.$type.'" name="addon['.$name.']" id="'.$id.'" size="50" class="'.$class.'" value="'.$value.'"/>
				<a class="button uploadpic" onclick="uploadfile(\'#'.$id.'\')" href="javascript:;"><span>选择文件</span></a><span class="info" style="float:none;">&nbsp;'.$tag['msg'].'</span></dd>
			</dl>';
    	return $parseStr;
    }
    //下拉框
    public function _select($attr){
    	$tag        = $this->parseXmlAttr($attr,'select');
    	$itemname       = $tag['itemname'];
    	$class       = $tag['class'];
    	$name       = $tag['name'];
    	$value       = $tag['value'];
    	$val = explode(',',$value);
    	if(ACTION_NAME=='update'){
    		foreach ($val as $v){
    			$select .= '<option <eq name="addon.'.$name.'" value="'.$v.'">selected="selected"</eq> value="'.$v.'">'.$v.'</option>';
    		}
    	}else{
    		foreach ($val as $v){
    			$select .= '<option value="'.$v.'">'.$v.'</option>';
    		}
    	}
    	$parseStr   = '<dl class="nowrap">
			<dt>'.$itemname.'：</dt>
			<dd><select name="addon['.$name.']" class="'.$class.'">
				'.$select.'
				</select><span class="info" style="float:none;">&nbsp;'.$tag['msg'].'</span></dd>
			</dl>';
    	return $parseStr;
    }
    //单选框
    public function _radio($attr){
    	$tag        = $this->parseXmlAttr($attr,'radio');
    	$itemname       = $tag['itemname'];
    	$name       = $tag['name'];
    	$type       = $tag['type'];
    	$value       = $tag['value'];
    	$val = explode(',',$value);
    	if(ACTION_NAME=='update'){
    		foreach ($val as $v){
    			$select .= '<label><input type="'.$type.'" <eq name="addon.'.$name.'" value="'.$v.'">checked="checked"</eq> name="addon['.$name.']" value="'.$v.'">'.$v.'</label>';
    		}
    	}else{
    		foreach ($val as $v){
    			$select .= '<label><input type="'.$type.'" name="addon['.$name.']" value="'.$v.'">'.$v.'</label>';
    		}
    	}
    	$parseStr   = '<dl class="nowrap">
			<dt>'.$itemname.'：</dt>
			<dd>'.$select.'<span class="info" style="float:none;">&nbsp;'.$tag['msg'].'</span></dd>
			</dl>';
    	return $parseStr;
    }
    //多选框
    public function _checkbox($attr){
    	$tag        = $this->parseXmlAttr($attr,'checkbox');
    	$itemname       = $tag['itemname'];
    	$class       = $tag['class'];
    	$name       = $tag['name'];
    	$type       = $tag['type'];
    	$value       = $tag['value'];
    	$val = explode(',',$value);
    	if(ACTION_NAME=='update'){
    		$select = '<php>$'.$name.'_check = explode(\',\',$addon[\''.$name.'\']);</php>';
    		foreach ($val as $v){    			
    			$select .= '<label><input type="'.$type.'" <php>if(in_array(\''.$v.'\',$'.$name.'_check)){echo \'checked="checked"\';}</php> name="addon['.$name.'][]" value="'.$v.'">'.$v.'</label>';
    		}
    	}else{
    		foreach ($val as $v){
    			$select .= '<label><input type="'.$type.'" name="addon['.$name.'][]" value="'.$v.'">'.$v.'</label>';
    		}
    	}
    	$parseStr   = '<dl class="nowrap">
			<dt>'.$itemname.'：</dt>
			<dd>'.$select.'<span class="info" style="float:none;">&nbsp;'.$tag['msg'].'</span></dd>
			<input type="hidden" name="model_checkbox[]" value="'.$name.'" /></dl>';
    	return $parseStr;
    }
    //多图片上传
    public function _imglists($attr){
    	$tag        = $this->parseXmlAttr($attr,'imglists');
    	$itemname       = $tag['itemname'];
    	$id       = $tag['id'];
    	$name       = $tag['name'];
    	if(ACTION_NAME=='update'){
    		$content = '<php>$'.$name.'_list = unserialize($addon[\''.$name.'\']);
    				foreach($'.$name.'_list as $v){
    					echo \'<p class="list_pic"><img src="\'.$v[\'pic\'].\'" alt="双击删除此图片" title="双击删除此图片" /><input name="addon['.$name.'_text][]" type="text" value="\'.$v[\'text\'].\'"/><input type="hidden" name="addon['.$name.'_pic][]" value="\'.$v[\'pic\'].\'" /></p>\';
    				}
    				</php>';
    	}
    	$parseStr = '<dl class="nowrap"><dt>'.$itemname.'：</dt><dd style="width:635px;">
				<style type="text/css">#image_list_'.$id.' p.list_pic{width:100px; height:128px; float:left; margin-right:5px; padding:0px;}#image_list_'.$id.' p img{width:100px; height:100px;}#image_list_'.$id.' p input{margin-top:3px;width:94px;}</style>
				<div id="image_list_'.$id.'">'.$content.'
					<p class="list_pic"><a onclick="uploadimglist(\'#image_list_'.$id.'\',\''.$name.'\')" href="javascript:;"><img src="Public/Images/upload_pic.jpg" /></a></p>
					<div style="clear:both"></div>
				</div>
				<script>$(".list_pic").dblclick(function(){$(this).remove();})</script>
				<input type="hidden" name="model_image[]" value="'.$name.'" />
			</dd></dl>';
    	return $parseStr;
    }
    //多文件列表
    public function _filelists($attr){
    	
    	$tag        = $this->parseXmlAttr($attr,'filelists');
    	$itemname       = $tag['itemname'];
    	$name       = $tag['name'];
    	$id       = $tag['id'];
    	$value       = ACTION_NAME =='update' ? '<{$addon.'.$name.'_local}>' : $tag['value'];
    	$sites_class = C('JIUDU_DOWN_MORESITEDO') ? null : ' class="'.$id.'"';
    	if(ACTION_NAME=='update'){
    		$content1 = '<php>$'.$name.'_list1 = unserialize($addon[\''.$name.'_list1\']);
    				foreach($'.$name.'_list1 as $k=>$v){
    					$checked = $v[\'status\'] ? \' checked="checked"\' : null;
    					echo \'<tr><td><input type="text" name="addon['.$name.'_list1_host][\'.$k.\']" value="\'.$v[\'host\'].\'" style="width:155px;"/></td><td><input type="text" name="addon['.$name.'_list1_url][\'.$k.\']"'.$sites_class.' value="\'.$v[\'url\'].\'" style="width:235px;"/></td><td><input type="text" name="addon['.$name.'_list1_name][\'.$k.\']" value="\'.$v[\'name\'].\'" style="width:105px;"/></td><td align="center"><input type="checkbox" name="addon['.$name.'_list1_status][\'.$k.\']" value="1"\'.$checked.\'></td></tr>\';
    				}
    				</php>';
    		$content2 = '<php>$'.$name.'_list2 = unserialize($addon[\''.$name.'_list2\']);
    				foreach($'.$name.'_list2 as $v){
    					echo \'<tr class="unitBox"><td><input type="text" name="addon['.$name.'_list2_url][]" value="\'.$v[\'url\'].\'" size="60" class="url"></td><td><input type="text" name="addon['.$name.'_list2_name][]" value="\'.$v[\'name\'].\'" size="20" class="textInput"></td><td><a href="javascript:void(0)" class="btnDel ">删除</a></td></tr>\';
    				}
    				</php>';
    	}else{
    		$sites = explode("\n",C('JIUDU_DOWN_SITES'));
    		foreach ($sites as $k=>$v){
    			$site = explode('|',$v);
    			$content1 .='<tr>
					<td><input type="text" name="addon['.$name.'_list1_host]['.$k.']" value="'.$site[0].'" style="width:155px;"/></td>
					<td><input type="text" name="addon['.$name.'_list1_url]['.$k.']"'.$sites_class.' style="width:235px;"/></td>
					<td><input type="text" name="addon['.$name.'_list1_name]['.$k.']" value="'.$site[1].'" style="width:105px;"/></td>
					<td align="center"><input type="checkbox" name="addon['.$name.'_list1_status]['.$k.']" value="1" checked="checked"></td>
				</tr>';
    		}
    	}
    	$parseStr = (C('JIUDU_DOWN_ISMORESITE') == 0 || (C('JIUDU_DOWN_ISLOCAL') == 1 && C('JIUDU_DOWN_ISMORESITE') == 1)) ? '<dl class="nowrap">
			<dt>'.$itemname.'：</dt>
			<dd><input type="text" name="addon['.$name.'_local]" size="50" class="'.$id.' required" value="'.$value.'"/>
				<a class="button uploadpic" onclick="uploadfile(\'.'.$id.'\')" href="javascript:;"><span>选择文件</span></a></dd>
			</dl>' : null;
    	$parseStr .= C('JIUDU_DOWN_ISMORESITE') ? '<dl class="nowrap"><dt>预设镜像地址：</dt><dd style="border:1px solid #CCC">
	    	<table class="list"width="100%"><thead><tr>
			<th type="text" width="160">服务器地址</th>
	    	<th type="text" width="240">软件地址</th>
	    	<th type="text" width="110">服务器名称</th>
	    	<th align="center">启用</th></tr></thead>
    		<tbody>'.$content1.'</tbody></table></dl>' : null;
    	$parseStr .= '<dl class="nowrap"><dt>手动指定地址：</dt><dd style="border:1px solid #CCC">
	    	<table class="list nowrap itemDetail" addButton="添加地址" width="100%"><thead><tr>
	    	<th type="text" name="addon['.$name.'_list2_url][]" width="360" size="60" fieldClass="url">软件地址</th>
	    	<th type="text" name="addon['.$name.'_list2_name][]" width="150" size="20">服务器名称</th>
	    	<th align="center" type="del">删除</th></tr>
	    	</thead><tbody>'.$content2.'</tbody></table><input type="hidden" name="model_file" value="'.$name.'"/></dd></dl>';
    	return $parseStr;
    }
}
?>