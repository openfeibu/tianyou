<?php

define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');
// require_once(ROOT_PATH . '/' . ADMIN_PATH . '/includes/lib_goods.php');
include_once(ROOT_PATH . '/includes/cls_image.php');
$image = new cls_image($_CFG['bgcolor']);
$exc = new exchange($ecs->table('article'), $db, 'article_id', 'title');

if ($_REQUEST['act'] == 'list' )
{
    $banner_list = get_banner_list();
    $smarty->assign('full_page', 1);
    $smarty->assign('banner_list',$banner_list);
    $smarty->display($_CFG['template_name'].'banner_list.htm');
}
else if ($_REQUEST['act'] == 'add')
{
    $is_add = $_REQUEST['act'] == 'add';

    $smarty->assign('form_action', $is_add ? 'insert' : ($_REQUEST['act'] == 'edit' ? 'update' : 'insert'));
    $smarty->display($_CFG['template_name'].'banner_add.htm');
}
else if ($_REQUEST['act'] == 'edit')
{
    $banner_list = get_banner_list();

    $smarty->assign('form_action', 'update');
    $smarty->assign('banner_list',$banner_list);
    $smarty->display($_CFG['template_name'].'banner_info.htm');
}
else if ($_REQUEST['act'] == 'insert')
{
    $activity_type = isset($_POST['activity_type']) ? $_POST['activity_type'] : '';
    $activity_address = isset($_POST['activity_address']) ? $_POST['activity_address'] : '';
    $activity_time = isset($_POST['birthdayYear']) ? trim($_POST['birthdayYear']) .'-'. trim($_POST['birthdayMonth']) .'-'.trim($_POST['birthdayDay']) : '';
    $province      = isset($_POST['province'])  ? intval($_POST['province']) : '';
    $city          = isset($_POST['city'])      ? intval($_POST['city'])     : '';
    $district      = isset($_POST['district'])  ? intval($_POST['district']) : '';
    $vedio_url     = isset($_POST['vedio_url']) ? $_POST['vedio_url'] : '';
    $add_time = time();

    $sql = "INSERT INTO ".$ecs->table('article')."(title, cat_id, article_type, is_open, author, ".
                "author_email, keywords, content, add_time, file_url, open_type, link, description,activity_type,activity_address,activity_time,province,city,district,vedio_url) ".
            "VALUES ('$_POST[title]', '16', '$_POST[article_type]', '$_POST[is_open]', ".
                "'$_POST[author]', '$_POST[author_email]', '$_POST[keywords]', '$_POST[FCKeditor1]', ".
                "'$add_time', '$_POST[file_url]', '$open_type', '$_POST[link]', '$_POST[description]','$activity_type','$activity_address','$activity_time','$province','$city','$district','$vedio_url')";

    $db->query($sql);

    /* 处理关联商品 */
    $article_id = $db->insert_id();
    $sql = "UPDATE " . $ecs->table('goods_article') . " SET article_id = '$article_id' WHERE article_id = 0";
    $db->query($sql);

    admin_log($_POST['title'],'add','article');

    clear_cache_files(); // 清除相关的缓存文件

    sys_msg('添加成功',0, $link);
}
else if($_REQUEST['act'] == 'update')
{

    $file_urls = $_POST['file_url'];
    $links = $_POST['link'];
    $article_ids = $_POST['article_id'];

    foreach ($article_ids as $key => $article_id) {
        $file_url = $file_urls[$key];
        $link_url = $links[$key];
        $exc->edit("file_url='$file_url', link='$link_url'", $article_id);
    }
    $link[0]['text'] = $_LANG['back_list'];
    $link[0]['href'] = 'banner.php?act=edit';
    sys_msg("修改成功", 0, $link);
}
elseif ($_REQUEST['act'] == 'query')
{
    $banner_list = get_banner_list();
    $smarty->assign('banner_list',$banner_list);

    make_json_result($smarty->fetch($_CFG['template_name'].'banner_list.htm'));
}
elseif ($_REQUEST['act'] == 'remove')
{

    $id = intval($_GET['id']);

    $name = $exc->get_name($id);
    if ($exc->drop($id))
    {
        clear_cache_files();
    }

    $url = 'banner.php?act=query&' . str_replace('act=remove', '', $_SERVER['QUERY_STRING']);

    ecs_header("Location: $url\n");
    exit;
}
function get_banner_list()
{
    $sql = 'SELECT a.* , ac.cat_name '.
           'FROM ' .$GLOBALS['ecs']->table('article'). ' AS a '.
           'LEFT JOIN ' .$GLOBALS['ecs']->table('article_cat'). ' AS ac ON ac.cat_id = a.cat_id '.
           'WHERE a.cat_id = 16 ORDER by a.article_id ASC';

    $data = $GLOBALS['db']->getAll($sql);
    return $data;
}
