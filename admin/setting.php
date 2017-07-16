<?php

define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');
include_once(ROOT_PATH . 'includes/cls_image.php');
$image = new cls_image($_CFG['bgcolor']);
/*初始化数据交换对象 */
$qq_exc   = new exchange($ecs->table("qq"), $db, 'id', 'qq');
/* act操作项的初始化 */
if (empty($_REQUEST['act']))
{
    $_REQUEST['act'] = 'qq_list';
}
else
{
    $_REQUEST['act'] = trim($_REQUEST['act']);
}
$smarty->assign('act',$_REQUEST['act']);
/*------------------------------------------------------ */
//-- 广告列表页面
/*------------------------------------------------------ */
if ($_REQUEST['act'] == 'qq_list')
{
    $sql = "SELECT * FROM " .$GLOBALS['ecs']->table('qq'). " ORDER BY id ASC" ;
    $qq_list = $GLOBALS['db']->getAll($sql);
    $smarty->assign('qq_list',$qq_list);
    assign_query_info();
    $smarty->display($_CFG['template_name'].'setting_qq_list.htm');
}
elseif($_REQUEST['act'] == 'qq_add_submit')
{
    $account = trim($_REQUEST['account']);
    $insert = "INSERT INTO ".$GLOBALS['ecs']->table('qq')." (`account`) VALUES ('$account')";
    $GLOBALS['db']->query($insert);
    ajax_show_message('操作成功','success','',array('id' => $GLOBALS['db']->insert_id()));
}
elseif ($_REQUEST['act'] == 'remove')
{
    $id = intval($_REQUEST['id']);
    $sql = "DELETE FROM " . $GLOBALS['ecs']->table('qq') . " WHERE id = $id";
    $GLOBALS['db']->query($sql);
    ajax_show_message('操作成功','success');
}
elseif ($_REQUEST['act'] == 'repass')
{
    $smarty->display($_CFG['template_name'].'setting_repass.htm');
}
elseif ($_REQUEST['act'] == 'repass_submit')
{
    $ec_salt=rand(1,9999);
    $password = !empty($_POST['new_password']) ? ", password = '".md5(md5($_POST['new_password']).$ec_salt)."'"    : '';
    $admin_id = $_SESSION['admin_id'];
    $sql = "SELECT password FROM ".$ecs->table('admin_user')." WHERE user_id = '$admin_id'";
    $old_password = $db->getOne($sql);
    $sql ="SELECT ec_salt FROM ".$ecs->table('admin_user')." WHERE user_id = '$admin_id'";
    $old_ec_salt= $db->getOne($sql);
    if(empty($old_ec_salt))
    {
        $old_ec_password=md5($_POST['old_password']);
    }
    else
    {
        $old_ec_password=md5(md5($_POST['old_password']).$old_ec_salt);
    }
    if ($old_password <> $old_ec_password)
    {
       ajax_show_message('旧密码不正确');
    }

    /* 比较新密码和确认密码是否相同 */
    if ($_POST['new_password'] <> $_POST['pwd_confirm'])
    {
       ajax_show_message('新密码和确认密码不相同');
    }
    $sql = "UPDATE " .$ecs->table('admin_user'). " SET ".
           "ec_salt = '$ec_salt' ".
           $password.
           "WHERE user_id = '$admin_id'";
    $db->query($sql);

   /* 记录管理员操作 */
   admin_log($_SESSION['admin_name'], 'edit', 'privilege');
   $sess->delete_spec_admin_session($_SESSION['admin_id']);
   ajax_show_message('修改成功','success','privilege.php?act=login');
}
elseif ($_REQUEST['act'] == 'about')
{
    include_once(ROOT_PATH . 'includes/fckeditor/fckeditor.php');
    $shop_setting = get_shop_setting();
    /* 创建 html editor */
    create_html_editor('about', $shop_setting['about']);
    $smarty->assign('about',    $shop_setting['about']);
    $smarty->assign('shop_setting',$shop_setting);
    $smarty->display($_CFG['template_name'].'setting_about.htm');
}
elseif ($_REQUEST['act'] == 'about_submit')
{
    $about = $_POST['about'];
    $sql = "UPDATE ".$ecs->table('shop_setting')." SET `about` = '$about'";
    $db->query($sql);
    ecs_header("Location: setting.php?act=about\n");
    exit;
}
elseif ($_REQUEST['act'] == 'contact')
{
    $shop_setting = get_shop_setting();
    $smarty->assign('shop_setting',$shop_setting);
    $smarty->display($_CFG['template_name'].'setting_contact.htm');
}
elseif ($_REQUEST['act'] == 'contact_submit')
{
    $email = $_POST['email'];
    $address = $_POST['address'];
    $contacts = $_POST['contacts'];
    $tell = $_POST['tell'];
    $sql = "UPDATE ".$ecs->table('shop_setting')." SET `email` = '$email',`address` = '$address',`contacts` = '$contacts' ,`tell` = '$tell' WHERE id =1";
    $db->query($sql);
    ajax_show_message('修改成功','success');
}
