<?php
/**
 * Jill Query module
 *
 * You may not change or alter any portion of this comment or credits
 * of supporting developers from this source code or any supporting source code
 * which is considered copyrighted (c) material of the original comment or credit authors.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * @copyright  The XOOPS Project http://sourceforge.net/projects/xoops/
 * @license    http://www.fsf.org/copyleft/gpl.html GNU public license
 * @package    Jill Query
 * @since      2.5
 * @author     jill lee(tnjaile@gmail.com)
 * @version    $Id $
 **/

/*-----------引入檔案區--------------*/
$isAdmin                      = true;
$xoopsOption['template_main'] = 'jill_query_adm_main.tpl';
include_once "header.php";
include_once "../function.php";

/*-----------功能函數區--------------*/

//jill_query編輯表單
function jill_query_form($qsn = '')
{
    global $xoopsDB, $xoopsTpl, $xoopsUser, $isAdmin;
    if (!$isAdmin) {
        redirect_header($_SERVER['PHP_SELF'], 3, _TAD_PERMISSION_DENIED);
    }

    //抓取預設值
    if (!empty($qsn)) {
        $DBV = get_jill_query($qsn);
    } else {
        $DBV = array();
    }

    //預設值設定

    //設定 qsn 欄位的預設值
    $qsn = !isset($DBV['qsn']) ? $qsn : $DBV['qsn'];
    $xoopsTpl->assign('qsn', $qsn);
    //設定 title 欄位的預設值
    $title = !isset($DBV['title']) ? '' : $DBV['title'];
    $xoopsTpl->assign('title', $title);
    //設定 directions 欄位的預設值
    $directions = !isset($DBV['directions']) ? '' : $DBV['directions'];
    $xoopsTpl->assign('directions', $directions);
    //設定 editorEmail 欄位的預設值
    $editorEmail = !isset($DBV['editorEmail']) ? '' : $DBV['editorEmail'];
    $xoopsTpl->assign('editorEmail', $editorEmail);
    //設定 isEnable 欄位的預設值
    $isEnable = !isset($DBV['isEnable']) ? '1' : $DBV['isEnable'];
    $xoopsTpl->assign('isEnable', $isEnable);
    //設定 counter 欄位的預設值
    $counter = !isset($DBV['counter']) ? '0' : $DBV['counter'];
    $xoopsTpl->assign('counter', $counter);
    //設定 uid 欄位的預設值
    $user_uid = $xoopsUser ? $xoopsUser->uid() : "";
    $uid      = !isset($DBV['uid']) ? $user_uid : $DBV['uid'];
    $xoopsTpl->assign('uid', $uid);
    //設定 passwd 欄位的預設值
    $passwd = !isset($DBV['passwd']) ? '' : $DBV['passwd'];
    $xoopsTpl->assign('passwd', $passwd);
    //設定 ispublic 欄位的預設值
    $ispublic = !isset($DBV['ispublic']) ? '0' : $DBV['ispublic'];
    $xoopsTpl->assign('ispublic', $ispublic);
    $op = empty($qsn) ? "insert_jill_query" : "update_jill_query";
    //$op = "replace_jill_query";

    //套用formValidator驗證機制
    if (!file_exists(TADTOOLS_PATH . "/formValidator.php")) {
        redirect_header("index.php", 3, _TAD_NEED_TADTOOLS);
    }
    include_once TADTOOLS_PATH . "/formValidator.php";
    $formValidator      = new formValidator("#myForm", true);
    $formValidator_code = $formValidator->render();

    //說明
    if (!file_exists(XOOPS_ROOT_PATH . "/modules/tadtools/ck.php")) {
        redirect_header("http://campus-xoops.tn.edu.tw/modules/tad_modules/index.php?module_sn=1", 3, _TAD_NEED_TADTOOLS);
    }
    include_once XOOPS_ROOT_PATH . "/modules/tadtools/ck.php";
    $ck = new CKEditor("jill_query", "directions", $directions);
    $ck->setHeight(200);
    $editor = $ck->render();
    $xoopsTpl->assign('directions_editor', $editor); //備註

    //加入Token安全機制
    include_once XOOPS_ROOT_PATH . "/class/xoopsformloader.php";
    $token      = new XoopsFormHiddenToken();
    $token_form = $token->render();
    $xoopsTpl->assign("token_form", $token_form);
    $xoopsTpl->assign('action', $_SERVER["PHP_SELF"]);
    $xoopsTpl->assign('formValidator_code', $formValidator_code);
    $xoopsTpl->assign('now_op', 'jill_query_form');
    $xoopsTpl->assign('next_op', $op);
}

//新增資料到jill_query中
function insert_jill_query()
{
    global $xoopsDB, $xoopsUser, $isAdmin;
    if (!$isAdmin) {
        redirect_header($_SERVER['PHP_SELF'], 3, _TAD_PERMISSION_DENIED);
    }

    //XOOPS表單安全檢查
    if (!$GLOBALS['xoopsSecurity']->check()) {
        $error = implode("<br />", $GLOBALS['xoopsSecurity']->getErrors());
        redirect_header($_SERVER['PHP_SELF'], 3, $error);
    }

    $myts = MyTextSanitizer::getInstance();

    $qsn         = intval($_POST['qsn']);
    $title       = $myts->addSlashes($_POST['title']);
    $directions  = $myts->addSlashes($_POST['directions']);
    $editorEmail = $myts->addSlashes($_POST['editorEmail']);
    $isEnable    = intval($_POST['isEnable']);
    $passwd      = $myts->addSlashes($_POST['passwd']);
    $ispublic    = intval($_POST['ispublic']);
    //取得使用者編號
    $uid = ($xoopsUser) ? $xoopsUser->uid() : "";
    $uid = !empty($_POST['uid']) ? intval($_POST['uid']) : $uid;

    $sql = "insert into `" . $xoopsDB->prefix("jill_query") . "` (
        `title`,
        `directions`,
        `editorEmail`,
        `isEnable`,
        `uid`,
        `passwd`,
        `ispublic`
    ) values(
        '{$title}',
        '{$directions}',
        '{$editorEmail}',
        '{$isEnable}',
        '{$uid}',
        '{$passwd}',
        '{$ispublic}'
    )";
    //die($sql);
    $xoopsDB->query($sql) or web_error($sql);

    //取得最後新增資料的流水編號
    $qsn = $xoopsDB->getInsertId();

    return $qsn;
}

//更新jill_query某一筆資料
function update_jill_query($qsn = '')
{
    global $xoopsDB, $isAdmin, $xoopsUser;
    if (!$isAdmin) {
        redirect_header($_SERVER['PHP_SELF'], 3, _TAD_PERMISSION_DENIED);
    }

    //XOOPS表單安全檢查
    if (!$GLOBALS['xoopsSecurity']->check()) {
        $error = implode("<br />", $GLOBALS['xoopsSecurity']->getErrors());
        redirect_header($_SERVER['PHP_SELF'], 3, $error);
    }

    $myts = MyTextSanitizer::getInstance();

    $qsn         = intval($_POST['qsn']);
    $title       = $myts->addSlashes($_POST['title']);
    $directions  = $myts->addSlashes($_POST['directions']);
    $editorEmail = $myts->addSlashes($_POST['editorEmail']);
    $isEnable    = intval($_POST['isEnable']);
    $passwd      = $myts->addSlashes($_POST['passwd']);
    $ispublic    = intval($_POST['ispublic']);
    //取得使用者編號
    $uid = $xoopsUser->uid();

    $sql = "update `" . $xoopsDB->prefix("jill_query") . "` set
       `title` = '{$title}',
       `directions` = '{$directions}',
       `editorEmail` = '{$editorEmail}',
       `isEnable` = '{$isEnable}',
       `uid` = '{$uid}',
       `passwd` = '{$passwd}',
       `ispublic` = '{$ispublic}'
    where `qsn` = '$qsn'";
    $xoopsDB->queryF($sql) or web_error($sql);

    return $qsn;
}

//刪除jill_query某筆資料資料
function delete_jill_query($qsn = '')
{
    global $xoopsDB, $isAdmin;

    if (!$isAdmin) {
        redirect_header($_SERVER['PHP_SELF'], 3, _TAD_PERMISSION_DENIED);
    }

    if (empty($qsn)) {
        return;
    }
    delete_data($qsn);
    $sql = "delete from `" . $xoopsDB->prefix("jill_query_col") . "`
        where `qsn`='{$qsn}' ";
    $xoopsDB->queryF($sql) or web_error($sql);
    $sql = "delete from `" . $xoopsDB->prefix("jill_query") . "`
    where `qsn` = '{$qsn}'";
    $xoopsDB->queryF($sql) or web_error($sql);

}

//以流水號秀出某筆jill_query資料內容
function show_one_jill_query($qsn = '')
{
    global $xoopsDB, $xoopsTpl, $isAdmin;

    if (empty($qsn)) {
        return;
    } else {
        $qsn = intval($qsn);
    }

    $myts = MyTextSanitizer::getInstance();

    $sql = "select * from `" . $xoopsDB->prefix("jill_query") . "`
    where `qsn` = '{$qsn}' ";
    $result = $xoopsDB->query($sql) or web_error($sql);
    $all    = $xoopsDB->fetchArray($result);

    //以下會產生這些變數： $qsn, $title, $directions, $editorEmail, $isEnable, $counter, $uid
    foreach ($all as $k => $v) {
        $$k = $v;
    }

    //將是/否選項轉換為圖示
    $isEnable = ($isEnable == 1) ? '<img src="' . XOOPS_URL . '/modules/jill_query/images/yes.gif" alt="' . _YES . '" title="' . _YES . '">' : '<img src="' . XOOPS_URL . '/modules/jill_query/images/no.gif" alt="' . _NO . '" title="' . _NO . '">';

    //將 uid 編號轉換成使用者姓名（或帳號）
    $uid_name = XoopsUser::getUnameFromId($uid, 1);
    if (empty($uid_name)) {
        $uid_name = XoopsUser::getUnameFromId($uid, 0);
    }

    //過濾讀出的變數值
    $title       = $myts->htmlSpecialChars($title);
    $directions  = $myts->displayTarea($directions, 1, 1, 0, 1, 0);
    $editorEmail = $myts->htmlSpecialChars($editorEmail);
    $counter     = $myts->displayTarea($counter, 1, 1, 0, 1, 0);
    $passwd      = $myts->htmlSpecialChars($passwd);

    $xoopsTpl->assign('qsn', $qsn);
    $xoopsTpl->assign('title', $title);
    $xoopsTpl->assign('directions', $directions);
    $xoopsTpl->assign('editorEmail', $editorEmail);
    $xoopsTpl->assign('isEnable', $isEnable);
    $xoopsTpl->assign('counter', $counter);
    $xoopsTpl->assign('uid_name', $uid_name);
    $xoopsTpl->assign('passwd', $passwd);
    $xoopsTpl->assign('ispublic', $ispublic);
    if (!file_exists(XOOPS_ROOT_PATH . "/modules/tadtools/sweet_alert.php")) {
        redirect_header("index.php", 3, _MA_NEED_TADTOOLS);
    }

    include_once XOOPS_ROOT_PATH . "/modules/tadtools/sweet_alert.php";
    $sweet_alert_obj        = new sweet_alert();
    $delete_jill_query_func = $sweet_alert_obj->render('delete_jill_query_func', "{$_SERVER['PHP_SELF']}?op=delete_jill_query&qsn=", "qsn");
    $xoopsTpl->assign('delete_jill_query_func', $delete_jill_query_func);

    $xoopsTpl->assign('action', $_SERVER['PHP_SELF']);
    $xoopsTpl->assign('now_op', 'show_one_jill_query');
}

//列出所有jill_query資料
function list_jill_query()
{
    global $xoopsDB, $xoopsTpl, $isAdmin;

    $myts = MyTextSanitizer::getInstance();

    $sql = "select * from `" . $xoopsDB->prefix("jill_query") . "` order by isEnable desc,qsn desc";

    //getPageBar($原sql語法, 每頁顯示幾筆資料, 最多顯示幾個頁數選項);
    $PageBar = getPageBar($sql, 20, 10);
    $bar     = $PageBar['bar'];
    $sql     = $PageBar['sql'];
    $total   = $PageBar['total'];

    $result = $xoopsDB->query($sql) or web_error($sql);

    $all_content = array();
    $i           = 0;
    while ($all = $xoopsDB->fetchArray($result)) {
        //以下會產生這些變數： $qsn, $title, $directions, $editorEmail, $isEnable, $counter, $uid
        foreach ($all as $k => $v) {
            $$k = $v;
        }

        //將是/否選項轉換為圖示
        $isEnable = $isEnable == 1 ? '<img src="' . XOOPS_URL . '/modules/jill_query/images/yes.gif" alt="' . _YES . '" title="' . _YES . '">' : '<img src="' . XOOPS_URL . '/modules/jill_query/images/no.gif" alt="' . _NO . '" title="' . _NO . '">';
        $ispublic = $ispublic == 1 ? '<img src="' . XOOPS_URL . '/modules/jill_query/images/yes.gif" alt="' . _YES . '" title="' . _YES . '">' : '<img src="' . XOOPS_URL . '/modules/jill_query/images/no.gif" alt="' . _NO . '" title="' . _NO . '">';
        //將 uid 編號轉換成使用者姓名（或帳號）
        $uid_name = XoopsUser::getUnameFromId($uid, 1);
        if (empty($uid_name)) {
            $uid_name = XoopsUser::getUnameFromId($uid, 0);
        }

        //過濾讀出的變數值
        $title       = $myts->htmlSpecialChars($title);
        $directions  = $myts->displayTarea($directions, 1, 1, 0, 1, 0);
        $editorEmail = $myts->htmlSpecialChars($editorEmail);

        $all_content[$i]['qsn']         = $qsn;
        $all_content[$i]['title']       = $title;
        $all_content[$i]['directions']  = $directions;
        $all_content[$i]['editorEmail'] = $editorEmail;
        $all_content[$i]['isEnable']    = $isEnable;
        $all_content[$i]['counter']     = $counter;
        $all_content[$i]['uid']         = $uid;
        $all_content[$i]['uid_name']    = $uid_name;
        $all_content[$i]['passwd']      = $passwd;
        $all_content[$i]['ispublic']    = $ispublic;
        $data_total                     = count_jill_query_sn($qsn);
        $all_content[$i]['total']       = (empty($data_total)) ? _MD_JILLQUERY_NODATA : $data_total;
        $all_content[$i]['cols']        = count_jill_query_col_qsn($qsn);
        $i++;
    }
    //die(var_dump($all_content));
    //刪除確認的JS
    if (!file_exists(XOOPS_ROOT_PATH . "/modules/tadtools/sweet_alert.php")) {
        redirect_header("index.php", 3, _MA_NEED_TADTOOLS);
    }
    include_once XOOPS_ROOT_PATH . "/modules/tadtools/sweet_alert.php";
    $sweet_alert_obj        = new sweet_alert();
    $delete_jill_query_func = $sweet_alert_obj->render('delete_jill_query_func',
        "{$_SERVER['PHP_SELF']}?op=delete_jill_query&qsn=", "qsn");
    $xoopsTpl->assign('delete_jill_query_func', $delete_jill_query_func);

    $xoopsTpl->assign('bar', $bar);
    $xoopsTpl->assign('action', $_SERVER['PHP_SELF']);
    $xoopsTpl->assign('isAdmin', $isAdmin);
    $xoopsTpl->assign('all_content', $all_content);
    $xoopsTpl->assign('now_op', 'list_jill_query');
}

//複製查詢
function copy_cols($qsn = "")
{
    global $xoopsDB, $xoopsUser, $isAdmin;
    if (!$isAdmin || empty($qsn)) {
        redirect_header($_SERVER['PHP_SELF'], 3, _TAD_PERMISSION_DENIED);
    }
    $sourceArr               = get_jill_query($qsn);
    $source_colsArr          = get_jill_query_allcol_qsn($qsn);
    $myts                    = MyTextSanitizer::getInstance();
    $sourceArr['title']      = $myts->addSlashes($sourceArr['title']);
    $sourceArr['directions'] = $myts->addSlashes($sourceArr['directions']);
    //die(var_dump($source_colsArr));
    //取得使用者編號
    $uid         = $xoopsUser->uid();
    $editorEmail = $xoopsUser->email();
    $sql         = "insert into `" . $xoopsDB->prefix("jill_query") . "` (
        `title`,
        `directions`,
        `editorEmail`,
        `isEnable`,
        `uid`
    ) values(
        'copy_{$sourceArr['title']}',
        '{$sourceArr['directions']}',
        '{$editorEmail}',
        '1',
        '{$uid}'
    )";
    //die($sql);
    $xoopsDB->queryF($sql) or web_error($sql);

    //取得最後新增資料的流水編號
    $qsn = $xoopsDB->getInsertId();
    foreach ($source_colsArr as $qcsn => $cols) {
        $sql = "insert into `" . $xoopsDB->prefix("jill_query_col") . "`
                (`qsn` , `qc_title` , `qcsnSearch`,`search_operator`,`isShow`,`qcSort`)
                values('{$qsn}' , '{$cols['qc_title']}' , '{$cols['qcsnSearch']}','{$cols['search_operator']}','{$cols['isShow']}','{$cols['qcSort']}')";
        //die($sql);
        $xoopsDB->queryF($sql) or web_error($sql);
    }

    return $qsn;
}
/*-----------執行動作判斷區----------*/
include_once $GLOBALS['xoops']->path('/modules/system/include/functions.php');
$op  = system_CleanVars($_REQUEST, 'op', '', 'string');
$qsn = system_CleanVars($_REQUEST, 'qsn', '', 'int');

switch ($op) {
    /*---判斷動作請貼在下方---*/
    //新增資料
    case "copy_cols":
        $qsn = copy_cols($qsn);
        redirect_header("{$_SERVER['PHP_SELF']}?op=jill_query_form&qsn=$qsn", 3, _MA_JILLQUERY_COPYSUCCESS);
        exit;

    //新增資料
    case "insert_jill_query":
        $qsn = insert_jill_query();
        header("location: setcol.php?qsn=$qsn");
        exit;

    //更新資料
    case "update_jill_query":
        update_jill_query($qsn);
        header("location: setcol.php?qsn=$qsn");
        exit;

    case "jill_query_form":
        jill_query_form($qsn);
        break;

    case "delete_jill_query":
        delete_jill_query($qsn);
        header("location: {$_SERVER['PHP_SELF']}");
        exit;

    default:
        if (empty($qsn)) {
            list_jill_query();
            //$main .= jill_query_form($qsn);
        } else {
            show_one_jill_query($qsn);
        }
        break;

        /*---判斷動作請貼在上方---*/
}

/*-----------秀出結果區--------------*/
$xoopsTpl->assign("isAdmin", true);
$xoTheme->addStylesheet(XOOPS_URL . '/modules/tadtools/css/xoops_adm3.css');
include_once 'footer.php';