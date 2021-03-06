<?php
/*-----------------------------------------------------------------
！！！！警告！！！！
以下为系统文件，请勿修改
-----------------------------------------------------------------*/

namespace app\classes;

use ginkgo\Ctrl as Gk_Ctrl;
use ginkgo\Route;
use ginkgo\Config;
use ginkgo\Func;

//不能非法包含或直接执行
defined('IN_GINKGO') or exit('Access denied');

/*-------------控制中心通用控制器-------------*/
abstract class Ctrl extends Gk_Ctrl {

    protected $isAjaxPost   = false;
    protected $isPost       = false;
    protected $isSuper      = false;
    protected $generalData  = array();
    protected $url          = array();
    protected $config;

    protected function c_init($param = array()) { //构造函数
        $this->configProcess();
        $this->pathProcess();

        $this->config = Config::get();

        if ($this->obj_request->isAjax() && $this->obj_request->isPost()) {
            $this->isAjaxPost = true;
        }

        if ($this->obj_request->isPost()) {
            $this->isPost = true;
        }

        $_arr_data = array(
            'config'    => $this->config,
            'route'     => $this->route,
            'param'     => $this->param,
        );

        $this->generalData = array_replace_recursive($this->generalData, $_arr_data);

        $this->configBase  = Config::get('base', 'var_extra');
    }


    protected function error($msg, $rcode = '', $status = 200, $langReplace = '') {
        $_arr_data = $this->dataProcess($msg, $rcode, $langReplace);

        $_arr_tpl = array_replace_recursive($this->generalData, $_arr_data);

        return $this->fetch('common' . DS . 'error', $_arr_tpl, '', $status);
    }


    protected function fetchJson($msg, $rcode = '', $status = 200, $langReplace = '') {
        $_arr_data = $this->dataProcess($msg, $rcode, $langReplace);

        return $this->json($_arr_data, $status);
    }


    protected function fetchJsonp($msg, $rcode = '', $status = 200, $langReplace = '') {
        $_arr_data = $this->dataProcess($msg, $rcode, $langReplace);

        return $this->jsonp($_arr_data, $status);
    }


    private function dataProcess($msg, $rcode = '', $langReplace = '') {
        $_arr_data = array(
            'msg'       => $this->obj_lang->get($msg, '', $langReplace),
            'rcode'     => $rcode,
            'rstatus'   => substr($rcode, 0, 1),
        );

        return $_arr_data;
    }

    protected function configProcess() {
        $_str_pathRoute  = BG_PATH_CONFIG . $this->route['mod'] . DS . 'common' . GK_EXT_INC;

        if (Func::isFile($_str_pathRoute)) {
            Config::load($_str_pathRoute, $this->route['mod']);
        }
    }


    protected function pathProcess() {
        $_str_dirRoot       = $this->obj_request->root();
        $_str_urlRoot       = $this->obj_request->baseUrl(true);
        $_str_routeRoot     = $this->obj_request->baseUrl();

        Route::setExclude('page_belong');

        $_arr_url = array(
            'dir_root'          => $_str_dirRoot,
            'dir_static'        => $_str_dirRoot . GK_NAME_STATIC . '/',
            'url_root'          => $_str_urlRoot,
            'url_api'           => $_str_urlRoot . '/api/',
            'url_personal'      => $_str_urlRoot . '/personal/',
            'route_root'        => $_str_routeRoot,
            'route_install'     => $_str_routeRoot . '/install/',
            'route_console'     => $_str_routeRoot . '/console/',
            'route_misc'        => $_str_routeRoot . '/misc/',
            'route_personal'    => $_str_routeRoot . '/personal/',
        );

        $this->url = array_replace_recursive($this->url, $_arr_url);;

        $this->generalData = array_replace_recursive($this->generalData, $_arr_url);
    }
}
