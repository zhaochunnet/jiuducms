<?php
/**
 +------------------------------------------------------------------------------
 * XML 工具类
 * 提供一系列的XML方法
 +------------------------------------------------------------------------------
 * @category   ORG
 * @package  ORG
 * @subpackage  Net
 * @author    struggle <struggle@9ducx.com>
 * @version   $Id: GetXmlData.class.php 2012-11-7 14:59:02Z struggle $
 +------------------------------------------------------------------------------
 */
class GetXmlData {
	public function readDatabase($filename){
	    // 读取 aminoacids 的 XML 数据
	    $data = implode("",file($filename));
	    $parser = xml_parser_create();
	    xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);
	    xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, 1);
	    xml_parse_into_struct($parser, $data, $values, $tags);
	    xml_parser_free($parser);
	
	    // 遍历 XML 结构
	    foreach ($tags as $key=>$val) {
	        if ($key == "item") {
	            $molranges = $val;
	            // each contiguous pair of array entries are the
	            // lower and upper range for each molecule definition
	            for ($i=0; $i < count($molranges); $i+=2) {
	                $offset = $molranges[$i] + 1;
	                $len = $molranges[$i + 1] - $offset;
	                $tdb[] = $this->parseMol(array_slice($values, $offset, $len));
	            }
	        } else {
	            continue;
	        }
	    }
	    return $tdb;
	}
	private function parseMol($mvalues){
	    for ($i=0; $i < count($mvalues); $i++) {
	        $mol[$mvalues[$i]["tag"]] = $mvalues[$i]["value"];
	    }
		return $mol;
	}
}