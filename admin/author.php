<?php

define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');
// require_once(ROOT_PATH . '/' . ADMIN_PATH . '/includes/lib_goods.php');
include_once(ROOT_PATH . '/includes/cls_image.php');
$image = new cls_image($_CFG['bgcolor']);
$exc = new exchange($ecs->table('author'), $db, 'author_id', 'author_name');

if ($_REQUEST['act'] == 'list' )
{
    $author_list = author_list();

    $smarty->assign('author_list',   $author_list['authors']);
    $smarty->assign('filter',       $author_list['filter']);
    $smarty->assign('record_count', $author_list['record_count']);
    $smarty->assign('page_count',   $author_list['page_count']);
    $smarty->assign('full_page',    1);
    $smarty->display($_CFG['template_name'].'author_list.htm');
}

else if ($_REQUEST['act'] == 'edit' || $_REQUEST['act'] == 'add')
{
    include_once(ROOT_PATH . 'includes/fckeditor/fckeditor.php');

    $is_add = $_REQUEST['act'] == 'add';
    if($is_add)
    {
        $author = array(
            'author_name' => '',
            'content' => '',
            'author_avatar' => '',
            'birthday' => '',
        );
        $goods_list = array();
    }
    else{
        $sql = "SELECT * FROM ".$ecs->table('author') ." WHERE author_id = '$_REQUEST[author_id]'";
        $author = $db->getRow($sql);
        $goods_list = get_author_goods_list($_REQUEST[author_id]);
    }
    create_html_editor('content', $author['content']);
    $smarty->assign('content',    $author['content']);

    if ($_REQUEST['act'] == 'add')
    {
        $smarty->assign('is_add', true);
    }

    $smarty->assign('author', $author);
    $smarty->assign('goods_list', $goods_list);
    $smarty->assign('form_act', $is_add ? 'insert' : ($_REQUEST['act'] == 'edit' ? 'update' : 'insert'));
    $smarty->display($_CFG['template_name'].'author_info.htm');
}
else if ($_REQUEST['act'] == 'insert')
{
    $birthday = trim($_POST['birthdayYear']) .'-'. trim($_POST['birthdayMonth']) .'-'.trim($_POST['birthdayDay']);
    $sql = "INSERT INTO ".$ecs->table('author')." (author_name,content,birthday,author_avatar) VALUES ('$_POST[author_name]','$_POST[content]','$birthday','$_POST[author_avatar]')";
    $db->query($sql);
    sys_msg($_LANG['articleadd_succeed'],0, $link);
}
else if($_REQUEST['act'] == 'update')
{
    $birthday = trim($_POST['birthdayYear']) .'-'. trim($_POST['birthdayMonth']) .'-'.trim($_POST['birthdayDay']);

    if ($exc->edit("author_name='$_POST[author_name]', content='$_POST[content]', birthday='$birthday', author_avatar='$_POST[author_avatar]'", $_POST['id']))
    {
        $link[0]['text'] = '返回作者列表';
        $link[0]['href'] = 'author.php?act=list&' . list_link_postfix();

        clear_cache_files();

        sys_msg("修改成功", 0, $link);
    }
    else
    {
        die($db->error());
    }
}
elseif ($_REQUEST['act'] == 'query')
{
    $author_list = author_list();

    $smarty->assign('author_list',   $author_list['authors']);
    $smarty->assign('filter',       $author_list['filter']);
    $smarty->assign('record_count', $author_list['record_count']);
    $smarty->assign('page_count',   $author_list['page_count']);

    $sort_flag  = sort_flag($article_list['filter']);
    $smarty->assign($sort_flag['tag'], $sort_flag['img']);

    make_json_result($smarty->fetch($_CFG['template_name'].'author_list.htm'), '',
        array('filter' => $author_list['filter'], 'page_count' => $author_list['page_count']));
}
elseif ($_REQUEST['act'] == 'remove')
{

    $id = intval($_GET['id']);

    $name = $exc->get_name($id);
    if ($exc->drop($id))
    {
        clear_cache_files();
    }

    $url = 'author.php?act=query&' . str_replace('act=remove', '', $_SERVER['QUERY_STRING']);

    ecs_header("Location: $url\n");
    exit;
}
function get_author_goods_list($author_id,$number = 0)
{
    $sql = "SELECT goods_thumb ,goods_id,goods_name FROM ".$GLOBALS['ecs']->table('goods') ." WHERE author_id = '$author_id' ORDER BY goods_id DESC";
    if($number)
    {
        $sql .= " LIMIT 0,".$number;
    }
    $goods_list = $GLOBALS['db']->getAll($sql);
    return $goods_list;
}
function author_list()
{
    $is_delete = 0;
    $filter['keyword']          = empty($_REQUEST['keyword']) ? '' : trim($_REQUEST['keyword']);
    $filter['sort_by']          = empty($_REQUEST['sort_by']) ? 'author_id' : trim($_REQUEST['sort_by']);
    $filter['sort_order']       = empty($_REQUEST['sort_order']) ? 'DESC' : trim($_REQUEST['sort_order']);
    $where = "";
    if (!empty($filter['keyword']))
    {
        $where .= " AND (author_name LIKE '%" . mysql_like_quote($filter['keyword']) . "%')";

    }
    $sql = "SELECT COUNT(*) FROM " .$GLOBALS['ecs']->table('author'). " WHERE is_delete='$is_delete' $where";
    $filter['record_count'] = $GLOBALS['db']->getOne($sql);
    /* 分页大小 */
    $filter = page_and_size($filter);

    $sql =" SELECT * FROM " . $GLOBALS['ecs']->table('author') . " WHERE is_delete='$is_delete' $where"." ORDER BY $filter[sort_by] $filter[sort_order] "." LIMIT " . $filter['start'] . ",$filter[page_size]";

    $filter['keyword'] = stripslashes($filter['keyword']);
    set_filter($filter, $sql, $param_str = '');

    $authors = $GLOBALS['db']->getAll($sql);
    foreach($authors as $key => $author)
    {
        $goods_list = get_author_goods_list($author['author_id'],4);
        $authors[$key]['goods_list'] = $goods_list;
    }

    return array('authors' => $authors, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count']);
}
