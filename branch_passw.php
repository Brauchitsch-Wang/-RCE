<?php

/**
 * 分支密码的统一管理
 */
define('IN', true);     //定位该文件是入口文件
define('DS', DIRECTORY_SEPARATOR);  //定义系统目录分隔符
define('AROOT', dirname(__FILE__) . DS);    //定义入口所在的目录
include_once(dirname(dirname(__FILE__)) . DS . 'mvc' . DS . 'controller' . DS . 'core.controller.php');

class defaultController extends coreController {

    private $filename;
    private $cli = "evpn-server config all-clients file \"/data/evpn/cfg_pwd.text\"";

    function __construct() {
        parent::__construct();
        $this->filename = DS . "data" . DS . "evpn" . DS . "cfg_pwd.text";
    }

    /**
     * 设置分支设备的密码
     * @param string post(pass) 分支密码
     */
    public function setAction() {
        $pass = p("pass");
        if ($pass == FALSE) {
            json_echo(false);
            return;
        }
        $command = "/usr/local/evpn/server/cfg_pass.sh config " . $pass;
        evpnShell($command);
    }
    
    /**
     * 关闭集中控制功能
     */
    function closeAction(){
        evpnShell("/usr/local/evpn/server/cfg_pass.sh disable");
    }

    /**
     * 获取分支密码
     */
    function getAction() {
        $command = "/usr/local/evpn/server/echo_pass.sh";
        $content = [];
        exec(EscapeShellCmd($command), $content);
        $data = array("status" => true,
            "data" => isset($content[0]) && $content[0] === "" ? "" : $content);
        json_echo($data);
    }
    
    /**
     * 获取分支密码设置列表
     */
    function listAction(){
        $province = p("province");
        $city = p("city");
        $district = p("district");
        $shell = "/usr/local/evpn/server/sh_clients_pass.sh";
        if($province !== FALSE && $province != ""){
            $shell .= " province ".iconv("UTF-8", "GB2312//IGNORE", $province);
        }
        if($city !== FALSE && $city != ""){
            $shell .= " city ".iconv("UTF-8", "GB2312//IGNORE", $city);
        }
        if($district !== FALSE && $district != ""){
            $shell .= " district ".iconv("UTF-8", "GB2312//IGNORE", $district);
        }
        header("Content-type: text/html;charset=gbk");
        header("Cache-Control: no-cache, must-revalidate");
        header("Pragma: no-cache");
        echo `$shell`;
    }

}

include_once dirname(dirname(__FILE__)) . '/init.php';     //mvc架构初始化