<?php
define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');

if ((DEBUG_MODE & 2) != 2)
{
    $smarty->caching = true;
}
$_REQUEST['act'] = isset($_REQUEST['act']) ? $_REQUEST['act'] : 'about';

$shop_setting = get_shop_setting();
$smarty->assign('shop_setting',$shop_setting);
$smarty->assign('act',$_REQUEST['act']);

assign_template();

if($_REQUEST['act'] == 'about')
{
    $title = '关于我们';
    $smarty->assign('title',   $title);
    $smarty->assign('page_title',   $title);
    $smarty->assign('keywords',    '关于我们');
    $smarty->assign('description', '关于我们');
    $smarty->display('system_article.dwt');
}
else if($_REQUEST['act'] == 'link')
{
    $title = '联系我们';
    $smarty->assign('title',   $title);
    $smarty->assign('page_title',   $title);    // 页面标题
    $smarty->assign('keywords',    '联系我们');
    $smarty->assign('description', '联系我们');
    $smarty->display('system_article.dwt');
}

?>
