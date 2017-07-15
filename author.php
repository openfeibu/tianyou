<?php

define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');

$_REQUEST['id'] = isset($_REQUEST['id']) ? intval($_REQUEST['id']) : 0;
$author_id     = $_REQUEST['id'];
$position = assign_ur_here(0, '艺术家');
$smarty->assign('page_title',       $position['title']);    // 页面标题
$smarty->assign('ur_here',          $position['ur_here']);  // 当前位置

$author = get_author($author_id);
$smarty->assign('author',       $author);

// $activities = ;

$smarty->display('author.dwt');
