<?php
/**
 +------------------------------------------------------------------------------
 * 模块安装操作类
 +------------------------------------------------------------------------------
 * @category   ORG
 * @package  ORG
 * @subpackage  Net
 * @author    struggle <struggle@9ducx.com>
 * @version   $Id: Module.class.php 2012-11-7 14:59:02Z struggle $
 +------------------------------------------------------------------------------
 */
class Module extends Action {

    public $configpath, $config;

    //安装
    public function install(){
        define("INSTALL", true);
        import("Dir");
        //安装目录
        $path = APP_PATH.C("APP_GROUP_PATH").'/'.$this->config['module'].'/Install/';
        $Dir = new Dir();
        //SQL文件
        if (file_exists($path . $this->config['module'] . '.sql')) {
            $sql = file_get_contents($path . $this->config['module'] . '.sql');
            $sql_split = $this->sql_split($sql, C("DB_PREFIX"));
            $db = M();
            if (is_array($sql_split)) {
                foreach ($sql_split as $s) {
                    $db->execute($s);
                }
            }
        }

        //Extention，菜单添加
        define("MENUID", 32); //添加一个菜单到后台“模块->模块列表”ID=74
        if (file_exists($path . 'Extention.inc.php')) {
            @include ($path . 'Extention.inc.php');
        }

        //后台模板
//        if (file_exists($path . "Tpl" . DIRECTORY_SEPARATOR)) {
//            $Dir->copyDir($path . "Tpl", TMPL_PATH);
//        }
        //前台模板
        if (file_exists($path . "Template" . DIRECTORY_SEPARATOR)) {
            $Dir->copyDir($path . "Template", TEMPLATE_PATH . CONFIG_THEME . DIRECTORY_SEPARATOR);
        }
        return true;
    }

    //卸载
    public function uninstall($module) {
        if (!$module) {
            $this->error("参数出错！");
        }
        $info = M("Module")->where(array("module" => $module))->find();
        if ($info) {
            define("UNINSTALL", true);
            import("Dir");
            //卸载目录
            $path = APP_PATH . C("APP_GROUP_PATH") . DIRECTORY_SEPARATOR . $module . DIRECTORY_SEPARATOR . 'Uninstall' . DIRECTORY_SEPARATOR;
            $Dir = new Dir();
            //SQL文件
            if (file_exists($path . $module . '.sql')) {
                $sql = file_get_contents($path . $module . '.sql');
                $sql_split = $this->sql_split($sql, C("DB_PREFIX"));
                $db = M('');
                if (is_array($sql_split)) {
                    foreach ($sql_split as $s) {
                        $db->execute($s);
                    }
                }
            }
            if (file_exists($path . 'Extention.inc.php')) {
                @include ($path . 'Extention.inc.php');
            }
            //后台模板删除
//            if (file_exists(TMPL_PATH . $module.DIRECTORY_SEPARATOR)) {
//                $Dir->delDir(TMPL_PATH . $module.DIRECTORY_SEPARATOR);
//            }
            //前台模板
            if (file_exists(TEMPLATE_PATH . CONFIG_THEME . DIRECTORY_SEPARATOR.$module.DIRECTORY_SEPARATOR)) {
                $Dir->delDir(TEMPLATE_PATH . CONFIG_THEME . DIRECTORY_SEPARATOR.$module.DIRECTORY_SEPARATOR);
            }
            M("Module")->where(array("module" => $module))->delete();
            //删除权限
            M("Access")->where(array("g" => $module))->delete();
            return true;
        } else {
            $this->error("该模块不存在，无法卸载！");
        }
    }

    //验证安装
    public function check($module) {
        if (!$module) {
            $this->error("参数出错！");
        }
        $info = M("Module")->where(array("module" => $module,'status'=>0))->count('id');
        if ($info) {
            $this->error("该模块已经安装过！");
        }
        $this->configpath = APP_PATH . C("APP_GROUP_PATH") . DIRECTORY_SEPARATOR . $module . DIRECTORY_SEPARATOR . 'Install' . DIRECTORY_SEPARATOR . 'Config.inc.php';
        if (!file_exists($this->configpath)) {
            $this->error("配置文件不存在！$module");
        }
        require $this->configpath;
        $this->config = array(
            "module" => $module,
            "modulename" => $modulename,
            "introduce" => $introduce,
            "author" => $author,
            "authorsite" => $authorsite,
            "authoremail" => $authoremail,
        );
        return true;
    }

    /**
     * 处理sql语句，执行替换前缀都功能。
     * @param string $sql 原始的sql
     * @param string $tablepre 表前缀
     */
    private function sql_split($sql, $tablepre) {
        if ($tablepre != "think_")
            $sql = str_replace("think_", $tablepre, $sql);
        $sql = preg_replace("/TYPE=(InnoDB|MyISAM|MEMORY)( DEFAULT CHARSET=[^; ]+)?/", "ENGINE=\\1 DEFAULT CHARSET=utf8", $sql);
        if ($r_tablepre != $s_tablepre)
            $sql = str_replace($s_tablepre, $r_tablepre, $sql);
        $sql = str_replace("\r", "\n", $sql);
        $ret = array();
        $num = 0;
        $queriesarray = explode(";\n", trim($sql));
        unset($sql);
        foreach ($queriesarray as $query) {
            $ret[$num] = '';
            $queries = explode("\n", trim($query));
            $queries = array_filter($queries);
            foreach ($queries as $query) {
                $str1 = substr($query, 0, 1);
                if ($str1 != '#' && $str1 != '-')
                    $ret[$num] .= $query;
            }
            $num++;
        }
        return $ret;
    }

}

?>
