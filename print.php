<?php
/*
 * You may not change or alter any portion of this comment or credits
 * of supporting developers from this source code or any supporting source code
 * which is considered copyrighted (c) material of the original comment or credit authors.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */

/**
 * @copyright   {@link http://xoops.org/ XOOPS Project}
 * @license     {@link http://www.fsf.org/copyleft/gpl.html GNU public license}
 * @package
 * @since
 * @author       XOOPS Development Team,
 * @author      GIJ=CHECKMATE (PEAK Corp. http://www.peak.ne.jp/)
 */

require_once __DIR__ . '/../../mainfile.php';
require_once XOOPS_ROOT_PATH . '/class/template.php';

error_reporting(0);
$xoopsLogger->activated = false;

// for "Duplicatable"
$moduleDirName = basename(__DIR__);
if (!preg_match('/^(\D+)(\d*)$/', $moduleDirName, $regs)) {
    echo('invalid dirname: ' . htmlspecialchars($moduleDirName));
}
$mydirnumber = $regs[2] === '' ? '' : (int)$regs[2];

$conn = $GLOBALS['xoopsDB']->conn;

// setting physical & virtual paths
$mod_path = XOOPS_ROOT_PATH . "/modules/$moduleDirName";
$mod_url  = XOOPS_URL . "/modules/$moduleDirName";

// ���饹������ɤ߹���
if (!class_exists('APCal_xoops')) {
    require_once "$mod_path/class/APCal.php";
    require_once "$mod_path/class/APCal_xoops.php";
}

// creating an instance of APCal
$cal = new APCal_xoops('', $xoopsConfig['language'], true);

// setting properties of APCal
$cal->conn = $conn;
include "$mod_path/include/read_configs.php";
$cal->base_url    = $mod_url;
$cal->base_path   = $mod_path;
$cal->images_url  = "$mod_url/assets/images/$skin_folder";
$cal->images_path = "$mod_path/assets/images/$skin_folder";

// Include our module's language file
if (file_exists(XOOPS_ROOT_PATH . '/modules/' . $xoopsModule->dirname() . '/language/' . $xoopsConfig['language'] . '/main.php')) {
    require_once XOOPS_ROOT_PATH . '/modules/' . $xoopsModule->dirname() . '/language/' . $xoopsConfig['language'] . '/main.php';
    require_once XOOPS_ROOT_PATH . '/modules/' . $xoopsModule->dirname() . '/language/' . $xoopsConfig['language'] . '/modinfo.php';
} else {
    require_once XOOPS_ROOT_PATH . '/modules/' . $xoopsModule->dirname() . '/language/english/main.php';
    require_once XOOPS_ROOT_PATH . '/modules/' . $xoopsModule->dirname() . '/language/english/modinfo.php';
}

$myts = MyTextSanitizer::getInstance();

header('Content-Type:text/html; charset=' . _CHARSET);
$tpl = new XoopsTpl();
$tpl->xoops_setTemplateDir(XOOPS_ROOT_PATH . '/themes');
$tpl->xoops_setCaching(2);
$tpl->xoops_setCacheTime(0);

$tpl->assign('for_print', true);

$tpl->assign('charset', _CHARSET);
$tpl->assign('sitename', $xoopsConfig['sitename']);
$tpl->assign('site_url', XOOPS_URL);

$tpl->assign('lang_comesfrom', sprintf(_MB_APCAL_COMESFROM, $xoopsConfig['sitename']));

// �ڡ���ɽ����Ϣ�ν���ʬ��
if (!empty($_GET['event_id'])) {
    $tpl->assign('contents', $cal->get_schedule_view_html(true));
} else {
    switch ($_GET['smode']) {
        case 'Yearly':
            $tpl->assign('for_event_list', false);
            $tpl->assign('contents', $cal->get_yearly('', '', true));
            break;
        case 'Weekly':
            $tpl->assign('for_event_list', false);
            $tpl->assign('contents', $cal->get_weekly('', '', true));
            break;
        case 'Daily':
            $tpl->assign('for_event_list', false);
            $tpl->assign('contents', $cal->get_daily('', '', true));
            break;
        case 'List':
            $tpl->assign('for_event_list', true);
            $cal->assign_event_list($tpl);
            break;
        case 'Monthly':
        default:
            $tpl->assign('for_event_list', false);
            $tpl->assign('contents', $cal->get_monthly('', '', true));
            break;
    }
}

echo '<link rel="stylesheet" type="text/css" href="' . XOOPS_URL . '/modules/apcal/assets/css/apcal.css" />';
$tpl->display('db:apcal_print.tpl');
