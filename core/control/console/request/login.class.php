<?php
/*-----------------------------------------------------------------
！！！！警告！！！！
以下为系统文件，请勿修改
-----------------------------------------------------------------*/

//不能非法包含或直接执行
if (!defined('IN_BAIGO')) {
    exit('Access Denied');
}


/*-------------登录控制器-------------*/
class CONTROL_CONSOLE_REQUEST_LOGIN {

    function __construct() { //构造函数
        $this->obj_console  = new CLASS_CONSOLE();
        $this->obj_console->dspType = 'result';
        $this->obj_console->chk_install();

        $this->obj_tpl          = $this->obj_console->obj_tpl;

        $this->mdl_admin        = new MODEL_ADMIN(); //设置管理员模型
        $this->mdl_user_profile = new MODEL_USER_PROFILE(); //设置管理员模型
        $this->mdl_user_api     = new MODEL_USER_API(); //设置管理员模型
    }

    /**
     * ctrl_login function.
     *
     * @access public
     */
    function ctrl_login() {
        $_arr_adminInput = $this->mdl_admin->input_login();
        if ($_arr_adminInput['rcode'] != 'ok') {
            $this->obj_tpl->tplDisplay('result', $_arr_adminInput);
        }

        $_arr_userRow = $this->mdl_user_profile->mdl_read($_arr_adminInput['admin_name'], "user_name");
        if ($_arr_userRow['rcode'] != 'y010102') {
            $this->obj_tpl->tplDisplay('result', $_arr_userRow);
        }

        if ($_arr_userRow['user_status'] == 'disable') {
            $_arr_tplData = array(
                'rcode'     => 'x010401',
            );
            $this->obj_tpl->tplDisplay('result', $_arr_tplData);
        }

        $_arr_adminRow = $this->mdl_admin->mdl_read($_arr_userRow['user_id']);
        if ($_arr_adminRow['rcode'] != 'y020102') {
            $this->obj_tpl->tplDisplay('result', $_arr_adminRow);
        }

        if ($_arr_adminRow['admin_status'] != 'enable') {
            $this->obj_tpl->tplDisplay('result', $_arr_adminRow);
        }

        switch ($_arr_userRow['user_crypt_type']) {
            case 0:
            case 1:
                $_str_crypt = fn_baigoCrypt($_arr_adminInput['admin_pass'], $_arr_userRow["user_rand"], false, $_arr_userRow['user_crypt_type']);
            break;

            default:
                $_str_crypt = fn_baigoCrypt($_arr_adminInput['admin_pass'], $_arr_userRow['user_name']);
            break;
        }

        if ($_str_crypt != $_arr_userRow['user_pass']) {
            $_arr_tplData = array(
                'rcode'     => "x010213",
            );
            $this->obj_tpl->tplDisplay('result', $_arr_tplData);
        }

        $_arr_userSubmit = array(
            "user_id" => $_arr_userRow['user_id'],
        );
        $_arr_loginRow = $this->mdl_user_api->mdl_login($_arr_userSubmit);

        if ($_arr_userRow['user_crypt_type'] < 2) {
            $_str_userPass  = fn_baigoCrypt($_arr_adminInput['admin_pass'], $_arr_userRow['user_name']);
            $this->mdl_user_profile->mdl_pass($_arr_userRow['user_id'], $_str_userPass);
        }

        /*print_r($_str_crypt . "<br>");
        print_r($_arr_userRow['user_pass']);
        exit;*/

        $_arr_ssin = $this->obj_console->ssin_login($_arr_loginRow);
        if ($_arr_ssin['rcode'] != 'ok') {
            $this->obj_tpl->tplDisplay('result', $_arr_ssin);
        }

        $_arr_tplData = array(
            'rcode'     => "y020401",
        );
        $this->obj_tpl->tplDisplay('result', $_arr_tplData);
    }
}
