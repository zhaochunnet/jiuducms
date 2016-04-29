<?php
/**
 * 标签管理类
 * @version        JiuduCMS 1.4.0 2013年11月07日 09:18:12 struggle $
 * @package        JiuduCMS.Administrator
 * @copyright      Copyright (c) 2011 - 2013, 9ducx, Inc.
 * @license        http://www.jiuducms.com/help/
 * @link           http://www.jiuducms.com/
 */
class TagLibJiudu extends TagLib{
    // 标签定义
    protected $tags   =  array(
        // 标签定义： attr 属性列表 close 是否闭合（0 或者1 默认1） alias 标签别名 level 嵌套层次
    	//前台
        'arclist' =>array('attr'=>'item,typeid,row,flag,orderby,channelid','level'=>1),
    	'list'    =>  array('attr'=>'item,empty','level'=>1),
    	'volist'    =>  array('attr'=>'name,item,empty','level'=>3),
    	'channel' =>array('attr'=>'item,type,reid,id,row,orderby,currentstyle','level'=>3),
    	'sonchannel' =>array('attr'=>'item,id,row,orderby','level'=>3),
    	'type'=>array('attr'=>'id,name,target','close'=>0,'alias'=>'typeurl,typename'),
    	'flink' =>array('attr'=>'item,type,typeid,row,orderby','level'=>1),
    	'comment' =>array(),
    	'tag' =>array('attr'=>'item,row,orderby'),
    	'sql' =>array('attr'=>'item,sql'),
    	'loop' =>array('attr'=>'item,table,row,orderby,where'),
    	'adlist'=>array('attr'=>'item,row,typeid,orderby','level'=>1),
    	'ad'=>array('attr'=>'id','close'=>0),
    	'adjs'=>array('attr'=>'id','close'=>0),
    	//表单标签
    	'input'=>array('attr'=>'itemname,id,class,name,type,value','close'=>1),
    	'textarea'=>array('attr'=>'itemname,id,class,name,value','close'=>1),
    	'date'=>array('attr'=>'itemname,id,class,name,type,value,format,readonly','close'=>1),
    	'uploadpic'=>array('attr'=>'itemname,id,class,name,type,value','close'=>1),
    	'uploadfile'=>array('attr'=>'itemname,id,class,name,type,value','close'=>1),
    	'select'=>array('attr'=>'itemname,id,class,name,value','close'=>1),
    	'radio'=>array('attr'=>'itemname,id,class,name,type,value','close'=>1),
    	'checkbox'=>array('attr'=>'itemname,id,class,name,type,value','close'=>1)
    );
    /**
     * arclist 标签解析 调用文章列表
     * 格式：
     * <jiudu:arclist typeid='1' row='15' orderby='pubdate desc'>
     * </jiudu:arclist>
     * @access public
     * @param string $attr 标签属性
     * @param string $val  标签内容
     * @return string|void
     */
    public function _arclist($attr,$val){
        $tag    = $this->parseXmlAttr($attr,'arclist');
        $typeid = isset($tag['typeid']) ? $tag['typeid'] : '';
        $typeid   = $this->autoBuildVar($typeid);
        $row = isset($tag['row']) ? $tag['row'] : 6;
        $item = isset($tag['item']) ? $tag['item'] : 'jiudu';
        $flag = isset($tag['flag']) ? $tag['flag'] : '';
        $orderby = isset($tag['orderby']) ? $tag['orderby'] : 'id desc';
        $channelid = isset($tag['channelid']) ? $tag['channelid'] : '';
        $parseStr = '<?php ';
        $parseStr .='$jd_val = Template::arclist('.$typeid.',\''.$row.'\',\''.$flag.'\',\''.$orderby.'\',\''.$channelid.'\');';
        $parseStr .='foreach ($jd_val as $key=>$'.$item.'){ ?>';
        $parseStr .=$val;
        $parseStr .='<?php }; ?>';
        return $parseStr;
    }   
    /**
     * channel 标签解析 调用栏目列表
     * 格式：
     * <jiudu:channel type="top" row="10">
     * </jiudu:channel>
     * @access public
     * @param string $attr 标签属性
     * @param string $val  标签内容
     * @return string|void
     */
    public function _channel($attr,$val){
        $tag = $this->parseXmlAttr($attr,'channel');
        $tid = isset($tag['id']) ? $tag['id'] : 0;
        $tid   = $this->autoBuildVar($tid);
        $type = $tag['type'];
        $row = isset($tag['row']) ? $tag['row'] : 6;
        $orderby = isset($tag['orderby']) ? $tag['orderby'] :'id ASC';
        $item = $tag['item'] ? $tag['item'] : 'jiudu';
        $parseStr = '<?php ';
        $parseStr .='$jd_val = Template::channel(\''.$type.'\','.$tid.',\''.$row.'\',\''.$orderby.'\');';
        $parseStr .='foreach ($jd_val as $key=>$'.$item.'){';
		if($tag['currentstyle']){
        	$current = str_replace('{','<',$tag['currentstyle']);
        	$current = str_replace('}','>',$current);
        	$current = str_replace('[','<?php echo ($jiudu["',$current);
        	$current = str_replace(']','"]); ?>',$current);
			$parseStr .='if($'.$item.'[\'id\'] == $_POST[\'typeid\']){';
			$parseStr .=' ?>'.$current.'<?php }else{?>';
		}else{
			$parseStr .=' ?>';
		}
        $parseStr .=$val;
        $parseStr .= $tag['currentstyle'] ? '<?php }}; ?>' : '<?php }; ?>';
        return $parseStr;
    }
    /**
     * sonchannel 标签解析 调用栏目列表
     * 格式：
     * <jiudu:channel type="top" row="10">
     * </jiudu:channel>
     * @access public
     * @param string $attr 标签属性
     * @param string $val  标签内容
     * @return string|void
     */
    public function _sonchannel($attr,$val){
    	$tag = $this->parseXmlAttr($attr,'sonchannel');
    	$tid = isset($tag['id']) ? $tag['id'] : 0;
    	$tid   = $this->autoBuildVar($tid);
    	$row = isset($tag['row']) ? $tag['row'] : 6;
    	$orderby = isset($tag['orderby']) ? $tag['orderby'] :'sortrank ASC,id ASC';
    	$item = $tag['item'] ? $tag['item'] : 'jiudu';
    	$parseStr = '<?php ';
    	$parseStr .='$jd_val = Template::sonchannel('.$tid.',\''.$row.'\',\''.$orderby.'\');';
    	$parseStr .='foreach ($jd_val as $'.$item.'){ ?>';
    	$parseStr .=$val;
    	$parseStr .=  '<?php }; ?>';
    	return $parseStr;
    }
    /**
     * type 标签解析 调用栏目名称
     * 格式：
     * <jiudu:channel type="top" row="10">
     * </jiudu:channel>
     * @access public
     * @param string $attr 标签属性
     * @param string $val  标签内容
     * @return string|void
     */
    public function _type($attr,$content,$type='all') {
    	$tag      = $this->parseXmlAttr($attr,'type');
    	$id   = $tag['id'];
		$type = $tag['name'] ? $tag['name'] : $type;
    	$target   = isset($tag['target']) ? $tag['target'] : '';
    	$parseStr  = '<?php echo Template::type(\''.$id.'\',\''.$type.'\',\''.$target.'\'); ?>';
    	return $parseStr;
    }
    public function _typeurl($attr,$content) {
    	return $this->_type($attr,$content,'typeurl');
    }
    public function _typename($attr,$content) {
    	return $this->_type($attr,$content,'typename');
    }
    /**
     * arclist 标签解析 调用友情链接
     * 格式：
     * <jiudu:flink type="top" row="10">
     * </jiudu:flink>
     * @access public
     * @param string $attr 标签属性
     * @param string $val  标签内容
     * @return string|void
     */
    public function _flink($attr,$val){
        $tag        = $this->parseXmlAttr($attr,'flink');
        $typeid = isset($tag['typeid']) ? $tag['typeid'] : '';
        $type = isset($tag['type']) ? $tag['type'] : '';
        $row = isset($tag['row']) ? $tag['row'] : 6;
        $orderby = isset($tag['orderby']) ? $tag['orderby'] :'id ASC';
        $item = $tag['item'] ? $tag['item'] : 'jiudu';
        $parseStr = '<?php ';
        $parseStr .='$jd_val = Template::flink(\''.$type.'\',\''.$typeid.'\',\''.$row.'\',\''.$orderby.'\');';
        $parseStr .='foreach ($jd_val as $'.$item.'){ ?>';
        $parseStr .=$val;
        $parseStr .='<?php }; ?>';
        return $parseStr;
    }
    /**
     * arclist 标签解析 调用评论列表
     * 格式：
     * <jiudu:comment type="top" row="10">
     * </jiudu:comment>
     * @access public
     * @param string $attr 标签属性
     * @param string $val  标签内容
     * @return string|void
     */
    public function _comment($attr,$val){
    	$where[] = 'aid='.$_GET['id'];
        $where[] = 'status=1';
        $where = implode(' AND ',$where);
        $sql = 'SELECT id,aid,name,email,ip,time,content,reid,reply,concat(path,\'-\',id) as bpath FROM '.C('DB_PREFIX').'comment WHERE '.$where.' ORDER BY bpath ASC';
        $parseStr = '<?php ';
        $parseStr .='$jd_val = Template::getcomment("'.$sql.'");';
        $parseStr .='foreach ($jd_val as $jiudu){';
        $parseStr .=' ?>';
        $parseStr .=$val;
        $parseStr .='<?php }; ?>';
        return $parseStr;
    }
    //调用搜索关键字
    public function _tag($attr,$val){
    	$tag      = $this->parseXmlAttr($attr,'tag');
    	$row = isset($tag['row']) ? $tag['row'] : 6;
    	$orderby = isset($tag['orderby']) ? $tag['orderby'] :'id DESC';
    	$item = $tag['item'] ? $tag['item'] : 'jiudu';
    	$parseStr = '<?php ';
    	$parseStr .='$jd_val = Template::tag('.$row.',\''.$orderby.'\');';
    	$parseStr .='foreach ($jd_val as $'.$item.'){';
    	$parseStr .=' ?>';
    	$parseStr .=$val;
    	$parseStr .='<?php }; ?>';
    	return $parseStr;
    }
    //按分类调用广告
    public function _adlist($attr,$val){
    	$tag      = $this->parseXmlAttr($attr,'adlist');
    	$typeid = isset($tag['typeid']) ? $tag['typeid'] : '';
    	$type = isset($tag['type']) ? $tag['type'] : '';
    	$row = isset($tag['row']) ? $tag['row'] : 6;
    	$orderby = isset($tag['orderby']) ? $tag['orderby'] :'id DESC';
    	$item = $tag['item'] ? $tag['item'] : 'jiudu';
    	$parseStr = '<?php ';
    	$parseStr .='$jd_val = Template::adlist('.$row.',\''.$orderby.'\',\''.$typeid.'\','.$type.');';
    	$parseStr .='foreach ($jd_val as $key => $'.$item.'){';
    	$parseStr .=' ?>';
    	$parseStr .=$val;
    	$parseStr .='<?php }; ?>';
    	return $parseStr;
    }
    //sql标签
    public function _sql($attr,$val){
    	$tag      = $this->parseXmlAttr($attr,'sql');
    	$sql = isset($tag['sql']) ? $tag['sql'] : '';
    	$item = $tag['item'] ? $tag['item'] : 'jiudu';
    	$parseStr = '<?php ';
    	$parseStr .='$jd_val = Template::sql("'.$sql.'");';
    	$parseStr .='foreach ($jd_val as $'.$item.'){';
    	$parseStr .=' ?>';
    	$parseStr .=$val;
    	$parseStr .='<?php }; ?>';
    	return $parseStr;
    }
    //loop 任意数据表调用
    public function _loop($attr,$val){
    	$tag      = $this->parseXmlAttr($attr,'loop');
    	$table = isset($tag['table']) ? $tag['table'] : '';
    	$row = isset($tag['row']) ? $tag['row'] : 6;
    	$orderby = isset($tag['orderby']) ? $tag['orderby'] :'id DESC';
    	$where = isset($tag['where']) ? $tag['where'] : '';
    	$item = $tag['item'] ? $tag['item'] : 'jiudu';
    	$parseStr = '<?php ';
    	$parseStr .='$jd_val = Template::loop(\''.$table.'\',\''.$row.'\',\''.$orderby.'\',\''.$where.'\');';
    	$parseStr .='foreach ($jd_val as $'.$item.'){';
    	$parseStr .=' ?>';
    	$parseStr .=$val;
    	$parseStr .='<?php }; ?>';
    	return $parseStr;
    }
    /**
     * list标签解析 循环输出数据集
     * 格式：
     * <volist name="userList" id="user" empty="" >
     * {user.username}
     * {user.email}
     * </volist>
     * @access public
     * @param string $attr 标签属性
     * @param string $content  标签内容
     * @return string|void
     */
    public function _list($attr,$val) {
    	$tag   =    $this->parseXmlAttr($attr,'list');
    	$empty =    isset($tag['empty'])?$tag['empty']:'该栏目没有内容';
    	$item = $tag['item'] ? $tag['item'] : 'jiudu';
    	$parseStr = '<?php ';
    	$parseStr .='if( count($list)==0 ){echo \''.$empty.'\';}else{';
    	$parseStr .='foreach ($list as $key=>$'.$item.'){';
    	$parseStr .=' ?>';
    	$parseStr .=$val;
    	$parseStr .='<?php };}; ?>';
    	return $parseStr;
    }
    /**
     * list标签解析 循环输出数据集
     * 格式：
     * <volist name="userList" id="user" empty="" >
     * {user.username}
     * {user.email}
     * </volist>
     * @access public
     * @param string $attr 标签属性
     * @param string $content  标签内容
     * @return string|void
     */
    public function _volist($attr,$val) {
    	$tag   =    $this->parseXmlAttr($attr,'volist');
    	$empty =    isset($tag['empty'])?$tag['empty']:'暂无信息';
    	$name = $this->autoBuildVar($tag['name']);
    	$item = $tag['item'] ? $tag['item'] : 'jiudu';
    	$parseStr = '<?php ';
    	$parseStr .='if(count('.$name.')==0 ){echo \''.$empty.'\';}else{';
    	$parseStr .='foreach ('.$name.' as $key=>$'.$item.'){';
    	$parseStr .=' ?>';
    	$parseStr .=$val;
    	$parseStr .='<?php };}; ?>';
    	return $parseStr;
    }
    //调用广告标签
    public function _ad($attr,$content) {
        $tag      = $this->parseXmlAttr($attr,'ad');
        $id   = $tag['id'];
        $parseStr  = '<?php echo Template::jiuduad("'.$id.'"); ?>';
        return $parseStr;
    }
    //调用广告JS
    public function _adjs($attr,$content) {
        $tag      = $this->parseXmlAttr($attr,'adjs');
        $id   = $tag['id'];
        $parseStr  = '<script type="text/javascript" src="'.__ROOT__.C('UPLOAD_PATH').'/advert/jiuduad'.$id.'.js"></script>';
        return $parseStr;
    }
    
    //单行文本框
    public function _input($attr){
    	$tag        = $this->parseXmlAttr($attr,'input');
    	$itemname       = $tag['itemname'];
    	$class       = $tag['class'];
    	$name       = $tag['name'];
    	$type       = $tag['type'];
    	$parseStr  = '<tr><td class="text">'.$itemname.'：</td><td class="input"><input name="'.$name.'" type="'.$type.'" class="input-text '.$class.'"/><span>'.$tag['msg'].'</span></td></tr>';
    	return $parseStr;
    }
    //多行文本框
    public function _textarea($attr){
    	$tag        = $this->parseXmlAttr($attr,'textarea');
    	$itemname       = $tag['itemname'];
    	$class       = $tag['class'];
    	$name       = $tag['name'];
    	$parseStr  = '<tr><td class="text">'.$itemname.'：</td><td class="input"><textarea name="'.$name.'" class="textarea-text '.$class.'" cols="50" rows="5"></textarea><span>'.$tag['msg'].'</span></td></tr>';
    	return $parseStr;
    }
    //时间类型
    public function _date($attr){
    	$tag        = $this->parseXmlAttr($attr,'date');
    	$itemname       = $tag['itemname'];
    	$class       = $tag['class'];
    	$name       = $tag['name'];
    	$type       = $tag['type'];
    	$parseStr  = '<tr><td class="text">'.$itemname.'：</td><td class="input"><input name="'.$name.'" type="'.$type.'" onClick="WdatePicker({dateFmt:\'yyyy-MM-dd HH:mm:ss\'})" class="input-text Wdate '.$class.'"/><span>'.$tag['msg'].'</span></td></tr>';
    	return $parseStr;
    }
    //上传图片
    public function _uploadpic($attr){
    	$tag        = $this->parseXmlAttr($attr,'date');
    	$itemname       = $tag['itemname'];
    	$class       = $tag['class'];
    	$name       = $tag['name'];
    	$type       = $tag['type'];
    	$parseStr  = '<tr><td class="text">'.$itemname.'：</td><td class="input"><input name="'.$name.'" type="file" class="input-text '.$class.'"/><span>'.$tag['msg'].'</span></td></tr>';
    	return $parseStr;
    }
    //上传附件
    public function _uploadfile($attr){
    	$tag        = $this->parseXmlAttr($attr,'date');
    	$itemname       = $tag['itemname'];
    	$class       = $tag['class'];
    	$name       = $tag['name'];
    	$type       = $tag['type'];
    	$parseStr  = '<tr><td class="text">'.$itemname.'：</td><td class="input"><input name="'.$name.'" type="file" class="input-text '.$class.'"/><span>'.$tag['msg'].'</span></td></tr>';
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
    	foreach ($val as $v){
    		$select .= '<option value="'.$v.'">'.$v.'</option>';
    	}
    	$parseStr  = '<tr><td class="text">'.$itemname.'：</td><td class="input"><select name="'.$name.'" class="'.$class.'">'.$select.'</select><span>'.$tag['msg'].'</span></td></tr>';
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
    	foreach ($val as $k=>$v){
    		$select .= '<input name="'.$name.'" id="'.$name.'_'.$k.'" type="'.$type.'" value="'.$v.'"><label for="'.$name.'_'.$k.'">'.$v.'</label>';
    	}
    	$parseStr  = '<tr><td class="text">'.$itemname.'：</td><td class="input">'.$select.'<span>'.$tag['msg'].'</span></td></tr>';
    	return $parseStr;
    }
    //多选框
    public function _checkbox($attr){
    	$tag        = $this->parseXmlAttr($attr,'checkbox');
    	$itemname       = $tag['itemname'];
    	$name       = $tag['name'];
    	$type       = $tag['type'];
    	$value       = $tag['value'];
    	$val = explode(',',$value);
    	foreach ($val as $k=>$v){
    		$select .= '<input name="'.$name.'[]" id="'.$name.'_'.$k.'" type="'.$type.'" value="'.$v.'"><label for="'.$name.'_'.$k.'">'.$v.'</label>';
    	}
    	$parseStr  = '<tr><td class="text">'.$itemname.'：</td><td class="input">'.$select.'<span>'.$tag['msg'].'</span></td></tr>';
    	return $parseStr;
    }
    /**
     * 自动识别构建变量
     * @access public
     * @param string $name 变量描述
     * @return string
     */
    public function autoBuildVar($name) {
    	if('$' == substr($name,0,1)){
    		if(strpos($name,'.')){
    			$vars = explode('.',$name);
    			$name  =  array_shift($vars);
    			foreach ($vars as $key=>$val){
    				if(0===strpos($val,'$')) {
    					$name .= '["{'.$val.'}"]';
    				}else{
    					$name .= '["'.$val.'"]';
    				}
    			}
    		}
    	}else{
    		$name = '\''.$name.'\'';
    	}
    	return $name;
    }
}
?>