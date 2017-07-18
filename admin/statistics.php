<?php

define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');

$_REQUEST['act'] = isset($_REQUEST['act']) && $_REQUEST['act'] ? $_REQUEST['act'] : 'business';

$smarty->assign('act',$_REQUEST['act']);

if ($_REQUEST['act'] == 'business')
{

    $smarty->display($_CFG['template_name'].'statistics_business.htm');
}
elseif ($_REQUEST['act'] == 'user')
{
    $smarty->display($_CFG['template_name'].'statistics_user.htm');
}
elseif ($_REQUEST['act'] == 'art')
{
    $smarty->display($_CFG['template_name'].'statistics_art.htm');
}
elseif ($_REQUEST['act'] == 'author')
{
    $smarty->display($_CFG['template_name'].'statistics_author.htm');
}
