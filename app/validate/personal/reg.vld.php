<?php
/*-----------------------------------------------------------------
！！！！警告！！！！
以下为系统文件，请勿修改
-----------------------------------------------------------------*/

namespace app\validate\personal;

use ginkgo\Validate;
use ginkgo\Lang;

//不能非法包含或直接执行
defined('IN_GINKGO') or exit('Access denied');

/*-------------管理员模型-------------*/
class Reg extends Validate {

    protected $rule     = array(
        'user_name' => array(
            'require' => true,
        ),
        'captcha' => array(
            'length'    => '4,4',
            'format'    => 'alpha_number',
            'captcha'   => true,
        ),
        '__token__' => array(
            'require' => true,
            'token'   => true,
        ),
    );

    protected $scene = array(
        'nomail' => array(
            'user_name',
            'captcha',
            '__token__',
        ),
    );


    function __construct() { //构造函数
        $this->obj_lang         = Lang::instance();

        $_arr_attrName = array(
            'user_name' => $this->obj_lang->get('Username'),
            'captcha'   => $this->obj_lang->get('Captcha'),
            '__token__' => $this->obj_lang->get('Token'),
        );

        $_arr_typeMsg = array(
            'require'   => $this->obj_lang->get('{:attr} require'),
            'length'    => $this->obj_lang->get('Size of {:attr} must be {:rule}'),
            'token'     => $this->obj_lang->get('Form token is incorrect'),
            'captcha'   => $this->obj_lang->get('Captcha is incorrect'),
        );

        $_arr_formatMsg = array(
            'alpha_number'  => $this->obj_lang->get('{:attr} must be alpha-numeric'),
        );

        $this->setAttrName($_arr_attrName);
        $this->setTypeMsg($_arr_typeMsg);
        $this->setFormatMsg($_arr_formatMsg);
    }
}
