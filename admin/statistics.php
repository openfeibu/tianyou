<?php

define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');
require_once(ROOT_PATH . 'includes/lib_order.php');
$_REQUEST['act'] = isset($_REQUEST['act']) && $_REQUEST['act'] ? $_REQUEST['act'] : 'business';

$smarty->assign('act',$_REQUEST['act']);

if ($_REQUEST['act'] == 'business')
{


    $smarty->display($_CFG['template_name'].'statistics_business.htm');
}
elseif ($_REQUEST['act'] == 'user')
{
    $start = date('Y-m-d 00:00:00');
    $end = date('Y-m-d H:i:s');
    /* 有过订单的会员数 */
    $sql = 'SELECT COUNT(DISTINCT user_id) FROM ' .$ecs->table('order_info').
           " WHERE user_id > 0 " . order_query_sql('finished');
    $have_order_usernum = $db->getOne($sql);
    $smarty->assign('have_order_usernum',       $have_order_usernum);
    /* 新用户*/
    $sql = "SELECT COUNT(user_id) FROM " .$ecs->table('users'). " WHERE `reg_time` >= unix_timestamp( '$start' ) AND `reg_time` <= unix_timestamp( '$end' )";
    $new_usernum = $db->getOne($sql);
    $smarty->assign('new_usernum',       $new_usernum);
    /*累计客户*/
    $sql = "SELECT COUNT(user_id) FROM " .$ecs->table('users');
    $all_usernum = $db->getOne($sql);
    $smarty->assign('all_usernum',       $all_usernum);
    /* 取得会员排行数据 */
    $user_orderinfo = get_user_orderinfo(false,5);
    $smarty->assign('user_orderinfo', $user_orderinfo['user_orderinfo']);

    /*新用户记录*/
    $new_user_statistics = get_new_user_statistics();

    $smarty->assign('filter',           $new_user_statistics['filter']);
    $smarty->assign('record_count',     $new_user_statistics['record_count']);
    $smarty->assign('page_count',       $new_user_statistics['page_count']);
    $smarty->assign('full_page',        1);
    $smarty->assign('new_user_statistics', $new_user_statistics['user_statistics']);
    /* 页面显示 */
    assign_query_info();

    $smarty->display($_CFG['template_name'].'statistics_user.htm');
}
elseif ($_REQUEST['act'] == 'art')
{
    $sql = "SELECT COUNT(*) FROM ".$ecs->table('goods')." WHERE is_delete = 0";
    $goods_count = $db->getOne($sql);
    $smarty->assign('goods_count',        $goods_count);
    $sql = "SELECT COUNT(DISTINCT goods_id) FROM ".$ecs->table('collect_goods');
    $collect_goods_count = $db->getOne($sql);
    $smarty->assign('collect_goods_count',        $collect_goods_count);
    $sql = "SELECT COUNT(DISTINCT goods_id) FROM ".$ecs->table('order_goods')." WHERE buy_type = 1";
    $buy_count = $db->getOne($sql);
    $smarty->assign('buy_count',        $buy_count);
    $sql = "SELECT COUNT(DISTINCT goods_id) FROM ".$ecs->table('order_goods')." WHERE buy_type = 2";
    $rent_count = $db->getOne($sql);
    $smarty->assign('rent_count',       $rent_count);

    $sql = "SELECT COUNT('cg.*') AS count,g.goods_id,g.goods_name,g.goods_thumb,g.cat_id FROM ".$ecs->table('collect_goods') ." AS cg JOIN ".$ecs->table('goods')." AS g ON g.goods_id = cg.goods_id GROUP BY g.goods_id ORDER BY count DESC LIMIT 10";
    $goodses = $db->getAll($sql);
    foreach($goodses as $key => $goods)
    {
        $goodses[$key]['goods_thumb'] = get_image_path($goods['goods_id'], $goods['goods_thumb'], true);
        $sql = "SELECT author_name FROM ".$ecs->table('author')." WHERE author_id = '".$goods['author_id']."'";
        $goodses[$key]['author_name'] = $db->getOne($sql);
        $sql = "SELECT cat_name FROM ".$ecs->table('category')." WHERE cat_id = '".$goods['cat_id']."'";
        $goodses[$key]['cat_name'] = $db->getOne($sql);
    }
    $smarty->assign('goodses',       $goodses);
    $smarty->display($_CFG['template_name'].'statistics_art.htm');
}
else if($_REQUEST['act'] == 'get_cat_seller_data')
{
    $sql = "SELECT cat_name,cat_id FROM ".$ecs->table('category')." WHERE parent_id = 0";
    $cats = $db->getAll($sql);
    $cat_names = $cat_sellers = array();
    if($_REQUEST['is_date'])
    {
        $time = trim($_REQUEST['val']);
        $start_time = $time." 00:00:00";
        $end_time = $time." 23:59:59";
        $start = strtotime($start_time);
        $end = strtotime($end_time);
    }else{
        $start_time = date('Y-m-d 00:00:00');
        $end_time = date('Y-m-d 23:59:59');
        $num = intval($_REQUEST['val'])-1;
        $start = strtotime("$start_time-{$num}day");
        $end = strtotime($end_time);

    }
    foreach($cats as $key => $cat)
    {
        $children = get_children($cat['cat_id']);
        $sql = "SELECT COUNT(DISTINCT og.goods_id) FROM ".$GLOBALS['ecs']->table('order_goods')." AS og JOIN ".$GLOBALS['ecs']->table('goods')." AS g ON og.goods_id = g.goods_id JOIN ".$GLOBALS['ecs']->table('order_info')." AS o WHERE ($children OR " . get_extension_goods($children) . ") AND o.add_time >= $start AND o.add_time <= $end AND o.order_status = 5";
        $cat_seller_count = $GLOBALS['db']->getOne($sql);
        $cat_sellers[] = $cat_seller_count;
        $cat_names[] = $cat['cat_name'];
    }
    $data = array(
        'cat_sellers' => $cat_sellers,
        'cat_names' => $cat_names
    );
    echo json_encode($data);
}
elseif ($_REQUEST['act'] == 'author')
{
    $sql = "SELECT COUNT(*) FROM ".$ecs->table('author');
    $author_count = $db->getOne($sql);
    $smarty->assign('author_count',       $author_count);
    $sql = "SELECT COUNT(g.goods_id) FROM ".$ecs->table('collect_goods') ." AS cg JOIN ".$ecs->table('goods')." AS g ON cg.goods_id = g.goods_id ";
    $sql = "SELECT COUNT(g.goods_id) AS be_collected_count FROM ".$ecs->table('author')." AS a JOIN ".$ecs->table('goods')." as g ON g.author_id = a.author_id  JOIN ".$ecs->table('collect_goods')." AS cg ON cg.goods_id = g.goods_id ";
    $be_collected_count = $db->getOne($sql);
    $smarty->assign('be_collected_count',       $be_collected_count);

    $sql = "SELECT COUNT(DISTINCT g.goods_id) AS be_collected_goods_count,COUNT(g.goods_id) AS be_collected_count, a.author_name,a.goods_count,a.author_avatar FROM ".$ecs->table('author')." AS a JOIN ".$ecs->table('goods')." as g ON g.author_id = a.author_id  JOIN ".$ecs->table('collect_goods')." AS cg ON cg.goods_id = g.goods_id GROUP BY a.author_id ORDER BY be_collected_count DESC LIMIT 10";
    $author_ranking = $db->getAll($sql);
    $smarty->assign('author_ranking',       $author_ranking);
    $smarty->display($_CFG['template_name'].'statistics_author.htm');
}
elseif ($_REQUEST['act'] == 'get_user_data')
{
    $data = $date = array();
    if($_REQUEST['is_date'])
    {
        $time = trim($_REQUEST['val']);
        $start_time = $time." 00:00:00";
        $end_time = $time." 23:59:59";

        $start_1 = strtotime("$start_time-1day");
        $end_1 = strtotime("$end_time-1day");
        $sql = "SELECT COUNT(user_id) FROM " .$ecs->table('users'). " WHERE `reg_time` >= $start_1 AND `reg_time` <=  '$end_1' ";
        $new_usernum = $db->getOne($sql);
        $date[] = date('Y/m/d',$start_1);
        $data[] = $new_usernum;

        $start = strtotime($start_time);
        $end = strtotime($end_time);
        $sql = "SELECT COUNT(user_id) FROM " .$ecs->table('users'). " WHERE `reg_time` >= $start AND `reg_time` <=  '$end' ";
        $new_usernum = $db->getOne($sql);
        $date[] = date('Y/m/d',$start);
        $data[] = $new_usernum;

    }else{
        $start_time = date('Y-m-d 00:00:00');
        $end_time = date('Y-m-d 23:59:59');
        $num = intval($_REQUEST['val'])-1;
        for ($i=$num; $i >=0 ; $i--) {
            $start = strtotime("$start_time-{$i}day");
            $end = strtotime("$end_time-{$i}day");
            $sql = "SELECT COUNT(user_id) FROM " .$ecs->table('users'). " WHERE `reg_time` >= $start AND `reg_time` <=  '$end' ";
            $new_usernum = $db->getOne($sql);
            $date[] = date('Y/m/d',$start);
            $data[] = $new_usernum;
        }
    }
    $user_data = array(
        'data' => $data,
        'date' => $date
    );
    echo json_encode($user_data);
}

/*------------------------------------------------------ */
//--会员排行需要的函数
/*------------------------------------------------------ */
/*
 * 取得会员订单量/购物额排名统计数据
 * @param   bool  $is_pagination  是否分页
 * @return  array   取得会员订单量/购物额排名统计数据
 */
 function get_user_orderinfo($is_pagination = true,$number = 0)
 {
    global $db, $ecs, $start_date, $end_date;

    $filter['sort_by'] = empty($_REQUEST['sort_by']) ? 'order_num' : trim($_REQUEST['sort_by']);
    $filter['sort_order'] = empty($_REQUEST['sort_order']) ? 'DESC' : trim($_REQUEST['sort_order']);

    $where = "WHERE u.user_id = o.user_id ".
             "AND u.user_id > 0 " . order_query_sql('finished', 'o.');

    $sql = "SELECT count(distinct(u.user_id)) FROM " .$ecs->table('users')." AS u, ".$ecs->table('order_info')." AS o " .$where;
    $filter['record_count'] = $GLOBALS['db']->getOne($sql);
    /* 分页大小 */
    $filter = page_and_size($filter);

    /* 计算订单各种费用之和的语句 */
    $total_fee = " SUM(" . order_amount_field() . ") AS turnover ";

    $sql = "SELECT u.user_id, u.user_name,u.avatar, max(o.goods_amount) as max_turnover,COUNT(*) AS order_num, " .$total_fee.
               "FROM ".$ecs->table('users')." AS u, ".$ecs->table('order_info')." AS o " .$where .
               " GROUP BY u.user_id"." ORDER BY " . $filter['sort_by'] . " " . $filter['sort_order'];
    if ($is_pagination)
    {
        $sql .= " LIMIT " . $filter['start'] . ', ' . $filter['page_size'];
    }
    if($number)
    {
        $sql .= " LIMIT ".$number;
    }
    $user_orderinfo = array();
    $res = $db->query($sql);

    while ($items = $db->fetchRow($res))
    {
        $items['turnover'] = price_format($items['turnover']);
        $items['max_turnover'] = price_format($items['max_turnover']);
        $user_orderinfo[] = $items;
    }
    $arr = array('user_orderinfo' => $user_orderinfo, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count']);
    return $arr;
}

function get_new_user_statistics()
{
    global $db, $ecs;

    $filter['sort_by'] = empty($_REQUEST['sort_by']) ? 'reg_time_desc' : trim($_REQUEST['sort_by']);
    $filter['sort_order'] = empty($_REQUEST['sort_order']) ? 'DESC' : trim($_REQUEST['sort_order']);

    $sql = "SELECT COUNT(t.counts) FROM (SELECT COUNT(user_id) AS counts ,FROM_UNIXTIME(`reg_time`, '%Y-%m-%d') AS reg_time_desc  FROM ".$ecs->table('users')."  GROUP BY reg_time_desc) t  ";

    $filter['record_count'] = $GLOBALS['db']->getOne($sql);

    $sql = "SELECT COUNT(user_id) AS new_usernum,reg_time,FROM_UNIXTIME(`reg_time`, '%Y-%m-%d') AS reg_time_desc  FROM ".$ecs->table('users')."  GROUP BY reg_time_desc "." ORDER BY " . $filter['sort_by'] . " " . $filter['sort_order'];
    $filter = page_and_size($filter);
    $sql .= " LIMIT " . $filter['start'] . ', ' . $filter['page_size'];
    $res = $GLOBALS['db']->query($sql);
    while ($items = $db->fetchRow($res))
    {
        $time = strtotime("{$items['reg_time_desc']}+1day");
        $sql = "SELECT COUNT(user_id) AS old_usernum FROM " .$ecs->table('users')." WHERE reg_time < $time";
        $items['old_usernum'] = $GLOBALS['db']->getOne($sql);
        $user_statistics[] = $items;
    }
    $arr = array('user_statistics' => $user_statistics, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count']);
    return $arr;
}

function get_seller_statistics($cat_id)
{
    $children = get_children($cat_id);
    $sql = "SELECT COUNT(DISTINCT og.goods_id) FROM ".$GLOBALS['ecs']->table('order_goods')." AS og JOIN ".$GLOBALS['ecs']->table('goods')." AS g ON og.goods_id = g.goods_id WHERE ($children OR " . get_extension_goods($children) . ') ';
    $count = $GLOBALS['db']->getOne($sql);
    return $count;
}
