<?php
/**
 +------------------------------------------------------------------------------
 * 模版标签方法
 +------------------------------------------------------------------------------
 * @category   ORG
 * @package  ORG
 * @subpackage  Net
 * @author    struggle <struggle@9ducx.com>
 * @version   $Id: Template.class.php 2012-11-7 14:59:02Z struggle $
 +------------------------------------------------------------------------------
 */
class Template{
	//调用栏目
	public static function channel($type,$tid='',$row="6",$orderby="sortrank ASC"){
		$model = M('Arctype');
		$tid = $tid ? $tid : $_POST['typeid'];
		if($type == 'self'){
			$reid = $model->where('id='.$tid)->getField('pid');
			$where['pid'] = $reid;
		}elseif($type == 'son'){
			if($model->where('pid='.$tid)->count()){
				$where['pid'] = $tid;
			}else{
				$reid = $model->where('id='.$tid)->getField('pid');
				$where['pid'] = $reid;
			}
		}elseif($type == 'top'){
			$where['pid'] = 0;
		}
		$where['status'] = 1;
		$val = $model->where($where)->field('id,typename,seotitle,typeimg,keywords,description,ispart,typedir,typeurl')->order($orderby)->limit($row)->select();
		$val = Template::gettypeurl($val);
		return $val;
	}
	//调用子栏目
	public static function sonchannel($tid=0,$row=6,$orderby="sortrank ASC"){
		$tid = $tid ? $tid : $_POST['typeid'];
		$where['pid'] = $tid;
		$where['status'] = 1;
		$val = M('Arctype')->where($where)->field('id,typename,seotitle,typeimg,keywords,description,ispart,typedir,typeurl')->order($orderby)->limit($row)->select();
		$val = Template::gettypeurl($val);
		return $val;
	}
	public static function type($tid,$type,$target){
		if(!$tid){
			return '';
		}
		$vo = '';
		if($type == 'all'){
			$val = M('Arctype')->field('typename,ispart,typeurl')->find($tid);
			$target = $target ? ' target="'.$target.'"' : $target;
			if($val['ispart'] == 3){
				$url = $val['typeurl'];
			}else{
				$url = Url::arctype($tid);
			}
			$vo = '<a href="'.$url.'"'.$target.'>'.$val['typename'].'</a>';
		}elseif($type == 'typeurl'){
			$vo = Url::arctype($tid);
		}elseif(in_array($type,array('typename','seotitle','keywords','description','typeimg','content'))){
			$vo = M('Arctype')->where('id='.$tid)->getField($type);
		}
		return $vo;
	}
	//调用文档
	public static function arclist($typeid,$row="6",$flag='',$orderby="id desc",$channelid=''){
		$model = M('Archives');
		$where[$model->getTableName().'.status'] = 1;
		if($typeid == 'selfinfo'){
			$where['typeid'] = ($_POST['typeid']+0);
		}elseif($typeid != ''){
			$where['typeid'] = array('in',$typeid);
		}
		if($typeid != '' && is_numeric($typeid)){
			if(M('Arctype')->where('id='.$typeid)->getField('ispart') == 1){
				$typeid = M('Arctype')->where('pid='.$typeid)->getField('id',true);
				if($typeid){$where['typeid'] = array('in',$typeid);}
			}
		}
		if($flag){
			$flag     	= explode(',',$flag);
			for($i=0; isset($flag[$i]); $i++){
				$flags[] = "FIND_IN_SET('{$flag[$i]}',flag)>0";
			}
			$where['_string'] = implode(' AND ',$flags);
		}
		$join[] = C('DB_PREFIX').'arctype ON '.C('DB_PREFIX').'arctype.id = '.$model->getTableName().'.typeid';
		$field = $model->getTableName().'.id as id,typeid,url,click,title,shorttitle,writer,source,litpic,'.$model->getTableName().'.description as description,pubdate,typename';
		if($channelid){
			$tableName = M('conmodel')->field('addtable')->find($channelid);
			$tableName = C('DB_PREFIX').$tableName['addtable'];
			$fields = M('field')->field('fieldname')->where('mid='.$channelid)->select();
			foreach ($fields as $v){
				$field .= ','.$tableName.'.'.$v['fieldname'].' AS '.$v['fieldname'];
			}
			$join[] = $tableName.' ON '.$tableName.'.id = '.$model->getTableName().'.aid';
		}
		$val = $model->where($where)->field($field)->join($join)->order('weight ASC,'.$orderby)->limit($row)->select();
		$val = Template::getconurl($val);
		return $val;
	}
	//调用友情链接
	public static function flink($type='',$typeid='',$row="6",$orderby="id ASC"){
		if($typeid){
        	$where['typeid'] = $typeid;
        }
    	if($type == 'image'){
        	$where['type'] = 1;
        }elseif($type == 'text'){
        	$where['type'] = 0;
        }
		$where['status'] = 1;
		$val = M('Friendlink')->where($where)->field('id,webname,type,picurl,pic_width,pic_height,weburl')->order($orderby)->limit($row)->select();
		return $val;
	}
	//调用搜索关键字
	public static function tag($row="6",$orderby="id DESC"){
		$val = M('Searchkey')->field('id,keyword,spwords,count,lasttime')->order($orderby)->limit($row)->select();
		return $val;
	}
	//按分类调用广告
	public static function adlist($row="6",$orderby="id DESC",$typeid='',$type=''){
		if($typeid){
			$where['typeid'] = $typeid;
		}
		$field = ',type,picurl,pic_width,pic_height,text,description,htmlcode,alt,target';
		$fieldarr = array(',picurl,pic_width,pic_height,description,alt,target',',text,target,description,alt',',htmlcode');
		if($type !== ''){
			if($type == 'image'){
				$adtype = 0;
			}elseif($type == 'text'){
				$adtype = 1;
			}elseif($type == 'html'){
				$adtype = 2;
			}
			if(isset($adtype)){
				$where['type'] = $adtype;
				$field = $fieldarr[$adtype];
			}
			
		}
		$where['starttime'] = array('lt',time());
		$where['endtime'] = array(array('gt',time()),array('eq',''),'OR');
		$val = M('Ad')->field('id,title,starttime,endtime,reptext'.$field)->where($where)->order($orderby)->limit($row)->select();
		foreach ($val as $k=>$v){
			if($adtype == 0 || $adtype == 1){
				$val[$k]['adurl'] = Url::adurl($v['id']);
			}
		}
		return $val;
	}
	//根据SQL语句调用
	public static function sql($sql=''){
		if(!$sql){
			return 'sql 不能为空';
		}
		$sql = strtr($sql,'__PREFIX__',C('DB_PREFIX'));
		$val = M()->query($sql);
		return $val;
	}
	//调用任意表数据
	public static function loop($table='',$row=6,$orderby='id DESC',$where=''){
		if(!$table){
			return '表名不能为空';
		}
		$val = M($table)->where($where)->order($orderby)->limit($row)->select();
		return $val;
	}
	//获取文章url
	public static function getconurl($void,$type=false){
		$num = count($void);
		for($i=0;$i<$num;$i++){
			if($void[$i]['url']){
				$void[$i]['titleurl'] = $void[$i]['url'];
			}else{
				$void[$i]['titleurl'] = Url::archives($void[$i]['id']);
			}
			$void[$i]['typeurl'] = Url::arctype($void[$i]['typeid']);
		}
		return $void;
	}
	//获取栏目url
	public static function gettypeurl($void){
		$num = count($void);
		for($i=0;$i<$num;$i++){
			if($void[$i]['ispart']==3){
				$void[$i]['typeurl'] = $void[$i]['typeurl'];
			}else{
				$void[$i]['typeurl'] = Url::arctype($void[$i]['id']);
			}
		}
		return $void;
	}
    //执行评论内容
    public static function getcomment($sql){
    	$model = new Model();
    	$voList = $model->query($sql);
   		foreach($voList as $key=>$value){
   			$voList[$key]['url'] = Url::comment($voList[$key]['aid']);
	    }
    	return $voList;
    }
    //获取广告内容
	public static function jiuduad($id){
    	$vo = M('Ad')->getById($id);
    	$url = Url::adurl($id);
    	if($vo['endtime'] == 0 || $vo['endtime'] > time()){
	    	if($vo['type'] == 0){
	    		$width = $vo['pic_width'] ?' width="'.$vo['pic_width'].'"':null;
	    		$height = $vo['pic_height'] ?' height="'.$vo['pic_height'].'"':null;
	    		return '<a href="'.$url.'" target="'.$vo['target'].'"><img src="'.$vo['picurl'].'"'.$width.$height.' alt="'.$vo['alt'].'" title="'.$vo['alt'].'" /></a>';
	    	}elseif($vo['type'] == 1){
	    		return '<a href="'.$url.'" target="'.$vo['target'].'">'.$vo['picurl'].'</a>';
	    	}elseif($vo['type'] == 2){
	    		return $vo['htmlcode'];
	    	}else{
	    		return '广告类型有误';
	    	}
    	}else{
    		return $vo['reptext'];
    	}
	}
}