<?php
/**
 * 网站地图
 * @version        JiuduCMS 1.9.22 2014年09月22日09:00:00 struggle $
 * @package        JiuduCMS.Administrator
 * @copyright      Copyright (c) 2011 - 2014, 9ducx, Inc.
 * @license        http://www.jiuducms.com/help/
 * @link           http://www.jiuducms.com/
 */
class IndexAction extends AdminAction {
	public function index(){
		$this->display ();
	}
	public function html(){
		$Index['url'] = C('JIUDU_BASEHOST');
		$Index['sitemapurl'] = C('JIUDU_BASEHOST').'/'.$_POST['filename'];
		$Index['webname'] = C('JIUDU_WEBNAME');
		$this->assign('index',$Index);
		$arctype = M('arctype')->field('id,typename')->where()->order('id desc')->select();
		foreach ($arctype as $k => $v){
			$arctype[$k]['typeurl'] = C('JIUDU_BASEHOST').Url::arctype($v['id']);
		}
		$this->assign('arctype',$arctype);
		$archives = M('Archives')->field('id,title')->where('status=1')->order('id desc')->select();
		foreach ($archives as $k => $v){
			$archives[$k]['titleurl'] = C('JIUDU_BASEHOST').Url::archives($v['id']);
		}
		$this->assign('archives',$archives);
		$path = SITE_PATH.'/'.$_POST['filename'];
		if($this->buildHtml($path,C('TEMPLETS_PATH').'/'.C('TEMPLETS_STYLE').'/'.GROUP_NAME.'/index.html')){
			$this->success('生成成功');
		}else{
			$this->error('生成失败');
		}
	}
	public function xml(){
		$Indexdata[0]['loc'] = C('JIUDU_BASEHOST');
		$Indexdata[0]['lastmod'] = date('c',time());
		$Indexdata[0]['changefreq'] = 'daily';
		$Indexdata[0]['priority'] = '1.0';
		$Arctype = M('Arctype')->field('id')->where()->order('id desc')->select();
		foreach ($Arctype as $k => $v){
			$Arctypedata[$k]['loc'] = C('JIUDU_BASEHOST').Url::arctype($v['id']);
			$Arctypedata[$k]['lastmod'] = date('c',time());
			$Arctypedata[$k]['changefreq'] = 'Weekly';
			$Arctypedata[$k]['priority'] = '0.8';
		}
		$archives = M('Archives')->field('id,pubdate,url')->where('status =1')->order('id desc')->select();
		foreach ($archives as $k => $v){
			$archivesdata[$k]['loc'] = C('JIUDU_BASEHOST').Url::archives($v['id']);
			$archivesdata[$k]['lastmod'] = date('c',$v['pubdate']);
			$archivesdata[$k]['changefreq'] = 'monthly';
			$archivesdata[$k]['priority'] = '0.6';
		}
		$data = array_merge($Indexdata,$Arctypedata,$archivesdata);
		$xmldate = $this->xml_encode($data);
		$path = SITE_PATH.'/'.$_POST['filename'];
		if(!is_dir(dirname($path))){
			mk_dir(dirname($path));
		}
		if(write($path,$xmldate)){
			$this->success('生成成功');
		}else{
			$this->error('生成失败');
		}	
	}
	private function xml_encode($data, $encoding='utf-8') {
		$xml    = '<?xml version="1.0" encoding="' . $encoding . '"?>'."\n";
		$xml   .= '<!--jiudu-sitemap-generator-url="'.C('SITEMAP_URL').'" jiudu-sitemap-generator-version="'.C('SITEMAP_VERSION').'"--><!-- generated-on="'.date('Y-m-d H:i:s',time()).'"-->'."\n";
		$xml   .= '<urlset xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd" xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'."\n";
		$xml   .= $this->data_to_xml($data);
		$xml   .= '</urlset>';
		return $xml;
	}
	
	/**
	 * 数据XML编码
	 * @param mixed $data 数据
	 * @return string
	 */
	private function data_to_xml($data) {
		$xml = '';
		foreach ($data as $key => $val) {
			is_numeric($key) && $key = "url";
			$xml    .=  "<$key>";
			$xml    .=  ( is_array($val) || is_object($val)) ? $this->data_to_xml($val) : $val;
			list($key, ) = explode(' ', $key);
			$xml    .=  "</$key>";
		}
		return $xml;
	}
}
?>