<?php
/*-----------------------------------------------------------------
！！！！警告！！！！
以下为系统文件，请勿修改
-----------------------------------------------------------------*/

//不能非法包含或直接执行
if (!defined('IN_BAIGO')) {
    exit('Access Denied');
}


/*-------------控制中心通用类-------------*/
class CLASS_MISC {

    public $config;
    public $dspType = '';

    function __construct() { //构造函数
        $this->config   = $GLOBALS['obj_base']->config;

        $this->obj_tpl  = new CLASS_TPL(BG_PATH_TPLSYS . 'misc' . DS . BG_DEFAULT_UI); //初始化视图对象

        //语言文件
        $this->obj_tpl->lang = array(
            //'common'        => fn_include(BG_PATH_LANG . $this->config['lang'] . DS . 'common.php'), //通用
            'rcode'         => fn_include(BG_PATH_LANG . $this->config['lang'] . DS . 'rcode.php'), //返回代码
        );

        if (file_exists(BG_PATH_LANG . $this->config['lang'] . DS . 'my' . DS . $GLOBALS['mod'] . '.php')) {
            $this->obj_tpl->lang['mod'] = fn_include(BG_PATH_LANG . $this->config['lang'] . DS . 'my' . DS . $GLOBALS['mod'] . '.php');
        }
    }
}
