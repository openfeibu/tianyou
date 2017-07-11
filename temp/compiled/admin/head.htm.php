<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="apple-mobile-web-app-capable" content="yes"/>
    <meta name="apple-mobile-web-app-status-bar-style" content="default"/>
    <meta content="telephone=no" name="format-detection"/>
    <meta name="screen-orientation" content="portrait">
    <meta name="x5-orientation" content="portrait">
    <title>天佑艺术馆</title>
    <link rel="stylesheet" type="text/css" href="<?php echo $this->_var['admin_themes']; ?>/css/fbUi.css">
    <link rel="stylesheet" type="text/css" href="<?php echo $this->_var['admin_themes']; ?>/css/other.css">
    <link rel="stylesheet" type="text/css" href="<?php echo $this->_var['admin_themes']; ?>/css/css.css">
    <script type="text/javascript" src="<?php echo $this->_var['admin_themes']; ?>/js/jquery-1.7.2.min.js"></script>
    <script type="text/javascript" src="<?php echo $this->_var['admin_themes']; ?>/js/fbUi.js"></script>
    <script type="text/javascript" src="<?php echo $this->_var['admin_themes']; ?>/js/main.js"></script>
    <?php echo $this->smarty_insert_scripts(array('files'=>'../js/jquery.json.js')); ?>
    <?php echo $this->smarty_insert_scripts(array('files'=>'../js/transport_jquery.js,common.js,respond.src.js')); ?>
</head>
<body>
<!--头部-->
<div class="outArea head">
       <div class="inArea">
            <a href="index.php" class="logo"><img src="<?php echo $this->_var['admin_themes']; ?>/images/logo.png" alt=""></a>
            <div class="login">
                <span style="background: transparent url('<?php echo $this->_var['admin_themes']; ?>/images/icon1.png') no-repeat 10px center;">本人本人</span>
                <a href="privilege.php?act=logout">退出</a>
            </div>
       </div>
</div>
