<?php
/**
 * TIANYOU 帮助信息接口
 * ============================================================================
 * * 版权所有 广州飞步信息科技有限公司，并保留所有权利。
 * 网站地址: http://www.feibu.info；
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和
 * 使用；不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * $Author: feibu $
 * $Id: respond.php 16220 2009-06-12 02:08:59Z liubo $
 */

define('IN_ECS', true);
require(dirname(__FILE__) . '/includes/init.php');

$get_keyword = trim($_GET['al']); // 获取关键字
header("location:http://ecshop.ecmoban.com/do.php?k=".$get_keyword."&v=".$_CFG['ecs_version']."&l=".$_CFG['lang']."&c=".EC_CHARSET);
?>