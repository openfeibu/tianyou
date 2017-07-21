<?php

define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');
include('includes/cls_json.php');

$json   = new JSON;

$act= isset($_REQUEST['act']) ? $_REQUEST['act'] : 'detail';


$_REQUEST['id'] = isset($_REQUEST['id']) ? intval($_REQUEST['id']) : 0;
$author_id     = intval($_REQUEST['id']);
$position = assign_ur_here(0, '艺术家');
$smarty->assign('page_title',       $position['title']);    // 页面标题
$smarty->assign('ur_here',          $position['ur_here']);  // 当前位置

$author = get_author($author_id);
$smarty->assign('author',       $author);

$activities = get_author_activities($author_id);
$smarty->assign('activities',       $activities);

$sql = "SELECT * FROM ".$GLOBALS['ecs']->table('goods')." WHERE author_id = '$author_id'";
$goodses = $GLOBALS['db']->getAll($sql);
foreach($goodses as $key => $value)
{
    $goodses[$key]['name']         = $value['goods_name'];
    $goodses[$key]['goods_thumb']  =  $goodses[$key]['img'] = get_image_path($value['goods_id'], $value['goods_thumb'], true);
    $goodses[$key]['goods_img']    =  get_image_path($value['goods_id'], $value['goods_img']);
    $goodses[$key]['url']  = build_uri('goods', array('gid'=>$value['goods_id']), $value['goods_name']);
    list($width, $height, $type, $attr) = getimagesize($goodses[$key]['goods_thumb']);
    $goodses[$key]['width'] = '265';
    $goodses[$key]['height'] = $height/($width/265);
    $goodses[$key]['popularity'] = $value['click_count'].'人气';
}

$smarty->assign('goodses',       $goodses);
$smarty->assign('goods_list_json',$json->encode($goodses));
assign_template();
$smarty->display('author.dwt');
