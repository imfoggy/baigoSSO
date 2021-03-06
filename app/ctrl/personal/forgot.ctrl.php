<?php
/*-----------------------------------------------------------------
！！！！警告！！！！
以下为系统文件，请勿修改
-----------------------------------------------------------------*/

namespace app\ctrl\personal;

use app\classes\personal\Ctrl;
use ginkgo\Loader;
use ginkgo\Crypt;
use ginkgo\Func;
use ginkgo\Smtp;
use ginkgo\Config;

//不能非法包含或直接执行
defined('IN_GINKGO') or exit('Access denied');

class Forgot extends Ctrl {

    protected function c_init($param = array()) {
        parent::c_init();

        $this->mdl_forgot     = Loader::model('Forgot');
    }

    function index() {
        $_arr_tplData = array(
        );

        $_arr_tpl = array_replace_recursive($this->generalData, $_arr_tplData);

        $this->assign($_arr_tpl);

        return $this->fetch();
    }


    function confirm() {
        $_str_userName = '';

        if (isset($this->param['user'])) {
            $_str_userName = $this->obj_request->input($this->param['user'], 'str', '');
        }

        if (Func::isEmpty($_str_userName)) {
            $this->generalData['rcode'] = 'x010203';
            $this->generalData['msg']   = 'Unable to get username';

            return $this->fetch('forgot' . DS . 'index', $this->generalData);
        }

        $_arr_userRow = $this->mdl_user->read($_str_userName, 'user_name');
        if ($_arr_userRow['rcode'] != 'y010102') {
            $this->generalData['rcode'] = $_arr_userRow['rcode'];
            $this->generalData['msg']   = 'User not found';

            return $this->fetch('forgot' . DS . 'index', $this->generalData);
        }

        if ($_arr_userRow['user_status'] == 'disabled') {
            $this->generalData['rcode'] = 'x010402';
            $this->generalData['msg']   = 'User is disabled';

            return $this->fetch('forgot' . DS . 'index', $this->generalData);
        }

        if (Func::isEmpty($_arr_userRow['user_mail']) && Func::isEmpty($_arr_userRow['user_sec_ques'])) {
            $this->generalData['rcode'] = 'x010403';
            $this->generalData['msg']   = 'You have not reserve mailbox and security question, cannot reset your password. Please contact your system administrator!';

            return $this->fetch('forgot' . DS . 'index', $this->generalData);
        }

        $_arr_tplData = array(
            'userRow'  => $_arr_userRow,
            'token'    => $this->obj_request->token(),
        );

        $_arr_tpl = array_replace_recursive($this->generalData, $_arr_tplData);

        $this->assign($_arr_tpl);

        return $this->fetch();
    }


    function bymail() {
        if (!$this->isAjaxPost) {
            return $this->fetchJson('Access denied', '', 405);
        }

        $_arr_inputByMail = $this->mdl_forgot->inputByMail();

        if ($_arr_inputByMail['rcode'] != 'y010201') {
            return $this->fetchJson($_arr_inputByMail['msg'], $_arr_inputByMail['rcode']);
        }

        $_arr_userRow = $this->mdl_user->read($_arr_inputByMail['user_name'], 'user_name');
        if ($_arr_userRow['rcode'] != 'y010102') {
            return $this->fetchJson('User not found', $_arr_userRow['rcode']);
        }

        if ($_arr_userRow['user_status'] == 'disabled') {
            return $this->fetchJson('User is disabled', 'x010402');
        }

        $_arr_submitResult    = $this->mdl_verify->submit($_arr_userRow['user_id'], $_arr_userRow['user_mail'], 'forgot');

        if ($_arr_submitResult['rcode'] != 'y120101' && $_arr_submitResult['rcode'] != 'y120103') {
            return $this->fetchJson('Failed to reset password', 'x010406');
        }

        $_str_verifyUrl = $this->generalData['url_personal'] . 'verify/pass/id/' . $_arr_submitResult['verify_id'] . '/token/' . $_arr_submitResult['verify_token'] . '/';

        $_arr_src   = array('{:verify_url}', '{:site_name}', '{:user_name}', '{:user_mail}');

        $_arr_dst   = array($_str_verifyUrl, $this->configBase['site_name'], $_arr_userRow['user_name'], $_arr_userRow['user_mail']);

        $_str_html  = str_ireplace($_arr_src, $_arr_dst, $this->configMailtpl['forgot_content']);

        //print_r($_str_html);

        $_obj_smtp  = Smtp::instance();

        if (!$_obj_smtp->connect()) {
            $_arr_error = $_obj_smtp->getError();
            return $this->fetchJson(end($_arr_error), 'x010406');
        }

        $_obj_smtp->addRcpt($_arr_userRow['user_mail']); //发送至
        $_obj_smtp->setSubject($this->configMailtpl['forgot_subject']); //主题
        $_obj_smtp->setContent($_str_html); //内容
        $_obj_smtp->setContentAlt(strip_tags($_str_html)); //内容

        if (!$_obj_smtp->send()) {
            $_arr_error = $_obj_smtp->getError();
            return $this->fetchJson(end($_arr_error), 'x010406');
        }

        return $this->fetchJson('A verification email has been sent to your mailbox, please verify.', 'y010406');
    }


    function bysecqa() {
        if (!$this->isAjaxPost) {
            return $this->fetchJson('Access denied', '', 405);
        }

        $_arr_inputBySecqa = $this->mdl_forgot->inputBySecqa();

        if ($_arr_inputBySecqa['rcode'] != 'y010201') {
            return $this->fetchJson($_arr_inputBySecqa['msg'], $_arr_inputBySecqa['rcode']);
        }

        $_arr_userRow = $this->mdl_user->read($_arr_inputBySecqa['user_name'], 'user_name');
        if ($_arr_userRow['rcode'] != 'y010102') {
            return $this->fetchJson('User not found', $_arr_userRow['rcode']);
        }

        if ($_arr_userRow['user_status'] == 'disabled') {
            return $this->fetchJson('User is disabled', 'x010402');
        }

        $_str_secAnsw = Crypt::crypt($_arr_inputBySecqa['user_sec_answ'], $_arr_userRow['user_name']);

        if ($_str_secAnsw != $_arr_userRow['user_sec_answ']) {
            return $this->fetchJson('Security answer is incorrect', 'x010201');
        }

        $_str_rand          = Func::rand();

        $_str_passNew       = Crypt::crypt($_arr_inputBySecqa['user_pass_new'], $_str_rand);

        $_arr_passResult    = $this->mdl_user->pass($_arr_userRow['user_id'], $_str_passNew, $_str_rand);

        return $this->fetchJson($_arr_passResult['msg'], $_arr_passResult['rcode']);
    }
}
