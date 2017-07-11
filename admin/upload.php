<?php

define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');
require_once(ROOT_PATH . '/' . ADMIN_PATH . '/includes/lib_goods.php');
include_once(ROOT_PATH . '/includes/cls_image.php');

$image = new cls_image($_CFG['bgcolor']);

if($_REQUEST['act'] == 'upload_goods_img')
{
    require(ROOT_PATH . 'languages/' .$_CFG['lang']. '/admin/goods.php');
    /* 检查图片：如果有错误，检查尺寸是否超过最大值；否则，检查文件类型 */
    if (isset($_FILES['goods_img']['error'])) // php 4.2 版本才支持 error
    {
        // 最大上传文件大小
        $php_maxsize = ini_get('upload_max_filesize');
        $htm_maxsize = '2M';

        // 商品图片
        if ($_FILES['goods_img']['error'] == 0)
        {
            if (!$image->check_img_type($_FILES['goods_img']['type']))
            {
                ajax_show_message($_LANG['invalid_goods_img']);
            }
        }
        elseif ($_FILES['goods_img']['error'] == 1)
        {
            ajax_show_message(sprintf($_LANG['goods_img_too_big'], $php_maxsize));
        }
        elseif ($_FILES['goods_img']['error'] == 2)
        {
            ajax_show_message(sprintf($_LANG['goods_img_too_big'], $htm_maxsize));
        }

        // 商品缩略图
        if (isset($_FILES['goods_thumb']))
        {
            if ($_FILES['goods_thumb']['error'] == 0)
            {
                if (!$image->check_img_type($_FILES['goods_thumb']['type']))
                {
                    ajax_show_message($_LANG['invalid_goods_thumb']);
                }
            }
            elseif ($_FILES['goods_thumb']['error'] == 1)
            {
                ajax_show_message(sprintf($_LANG['goods_thumb_too_big'], $php_maxsize));
            }
            elseif ($_FILES['goods_thumb']['error'] == 2)
            {
                ajax_show_message(sprintf($_LANG['goods_thumb_too_big'], $htm_maxsize));
            }
        }
        /*
        // 相册图片
        foreach ($_FILES['img_url']['error'] AS $key => $value)
        {
            if ($value == 0)
            {
                if (!$image->check_img_type($_FILES['img_url']['type'][$key]))
                {
                    ajax_show_message(sprintf($_LANG['invalid_img_url'], $key + 1));
                }
            }
            elseif ($value == 1)
            {
                ajax_show_message(sprintf($_LANG['img_url_too_big'], $key + 1, $php_maxsize));
            }
            elseif ($_FILES['img_url']['error'] == 2)
            {
                ajax_show_message(sprintf($_LANG['img_url_too_big'], $key + 1, $htm_maxsize));
            }
        }
        */
    }
    /* 4.1版本 */
    else
    {
        // 商品图片
        if ($_FILES['goods_img']['tmp_name'] != 'none')
        {
            if (!$image->check_img_type($_FILES['goods_img']['type']))
            {

                ajax_show_message($_LANG['invalid_goods_img']);
            }
        }

        // 商品缩略图
        if (isset($_FILES['goods_thumb']))
        {
            if ($_FILES['goods_thumb']['tmp_name'] != 'none')
            {
                if (!$image->check_img_type($_FILES['goods_thumb']['type']))
                {
                    ajax_show_message($_LANG['invalid_goods_thumb']);
                }
            }
        }
        /*
        // 相册图片
        foreach ($_FILES['img_url']['tmp_name'] AS $key => $value)
        {
            if ($value != 'none')
            {
                if (!$image->check_img_type($_FILES['img_url']['type'][$key]))
                {
                    ajax_show_message(sprintf($_LANG['invalid_img_url']));
                }
            }
        }*/
    }

    // 如果上传了商品图片，相应处理
    if ($_FILES['goods_img']['tmp_name'] != '' && $_FILES['goods_img']['tmp_name'] != 'none')
    {

        if ($_REQUEST['goods_id'] > 0)
        {
            /* 删除原来的图片文件 */
            $sql = "SELECT goods_thumb, goods_img, original_img " .
                    " FROM " . $ecs->table('goods') .
                    " WHERE goods_id = '$_REQUEST[goods_id]'";
            $row = $db->getRow($sql);
            if ($row['goods_thumb'] != '' && is_file('../' . $row['goods_thumb']))
            {
                @unlink('../' . $row['goods_thumb']);
            }
            if ($row['goods_img'] != '' && is_file('../' . $row['goods_img']))
            {
                @unlink('../' . $row['goods_img']);
            }
            if ($row['original_img'] != '' && is_file('../' . $row['original_img']))
            {
                /* 先不处理，以防止程序中途出错停止 */
                //$old_original_img = $row['original_img']; //记录旧图路径
            }
            /* 清除原来商品图片 */
            if ($proc_thumb === false)
            {
                get_image_path($_REQUEST[goods_id], $row['goods_img'], false, 'goods', true);
                get_image_path($_REQUEST[goods_id], $row['goods_thumb'], true, 'goods', true);
            }
        }

        $original_img   = $image->upload_image($_FILES['goods_img']); // 原始图片

        if ($original_img === false)
        {
            ajax_show_message($image->error_msg());
        }
        $goods_img      = $original_img;   // 商品图片

        /* 复制一份相册图片 */
        /* 添加判断是否自动生成相册图片 */
        if ($_CFG['auto_generate_gallery'])
        {
            $img        = $original_img;   // 相册图片
            $pos        = strpos(basename($img), '.');
            $newname    = dirname($img) . '/' . $image->random_filename() . substr(basename($img), $pos);
            if (!copy('../' . $img, '../' . $newname))
            {
                ajax_show_message('fail to copy file: ' . realpath('../' . $img));
            }
            $img        = $newname;

            $gallery_img    = $img;
            $gallery_thumb  = $img;
        }

        // 如果系统支持GD，缩放商品图片，且给商品图片和相册图片加水印
        if ($proc_thumb && $image->gd_version() > 0 && $image->check_img_function($_FILES['goods_img']['type']) || $is_url_goods_img)
        {

            if (empty($is_url_goods_img))
            {
                // 如果设置大小不为0，缩放图片
                if ($_CFG['image_width'] != 0 || $_CFG['image_height'] != 0)
                {
                    $goods_img = $image->make_thumb('../'. $goods_img , $GLOBALS['_CFG']['image_width'],  $GLOBALS['_CFG']['image_height']);
                    if ($goods_img === false)
                    {
                        ajax_show_message($image->error_msg());
                    }
                }

                /* 添加判断是否自动生成相册图片 */
                if ($_CFG['auto_generate_gallery'])
                {
                    $newname    = dirname($img) . '/' . $image->random_filename() . substr(basename($img), $pos);
                    if (!copy('../' . $img, '../' . $newname))
                    {
                        ajax_show_message('fail to copy file: ' . realpath('../' . $img));
                    }
                    $gallery_img        = $newname;
                }

                // 加水印
                if (intval($_CFG['watermark_place']) > 0 && !empty($GLOBALS['_CFG']['watermark']))
                {
                    if ($image->add_watermark('../'.$goods_img,'',$GLOBALS['_CFG']['watermark'], $GLOBALS['_CFG']['watermark_place'], $GLOBALS['_CFG']['watermark_alpha']) === false)
                    {
                        ajax_show_message($image->error_msg());
                    }
                    /* 添加判断是否自动生成相册图片 */
                    if ($_CFG['auto_generate_gallery'])
                    {
                        if ($image->add_watermark('../'. $gallery_img,'',$GLOBALS['_CFG']['watermark'], $GLOBALS['_CFG']['watermark_place'], $GLOBALS['_CFG']['watermark_alpha']) === false)
                        {
                            ajax_show_message($image->error_msg());
                        }
                    }
                }
            }

            // 相册缩略图
            /* 添加判断是否自动生成相册图片 */
            if ($_CFG['auto_generate_gallery'])
            {
                if ($_CFG['thumb_width'] != 0 || $_CFG['thumb_height'] != 0)
                {
                    $gallery_thumb = $image->make_thumb('../' . $img, $GLOBALS['_CFG']['thumb_width'],  $GLOBALS['_CFG']['thumb_height']);
                    if ($gallery_thumb === false)
                    {
                        ajax_show_message($image->error_msg());
                    }
                }
            }
        }

    }
    // 未上传，如果自动选择生成，且上传了商品图片，生成所略图
    if (!empty($original_img))
    {
        // 如果设置缩略图大小不为0，生成缩略图
        if ($_CFG['thumb_width'] != 0 || $_CFG['thumb_height'] != 0)
        {
            $goods_thumb = $image->make_thumb('../' . $original_img, $GLOBALS['_CFG']['thumb_width'],  $GLOBALS['_CFG']['thumb_height']);
            if ($goods_thumb === false)
            {
                ajax_show_message($image->error_msg());
            }
        }
        else
        {
            $goods_thumb = $original_img;
        }
    }
    $data = [
        'error' => 0,
        'goods_thumb' => $goods_thumb,
        'goods_img' => $goods_img,
        'original_img' => $original_img,
    ];
    die(json_encode($data));

}
if($_REQUEST['act'] == 'upload_article_file')
{
    $file_url = '';
    if (empty($_FILES['file']['error']) || (!isset($_FILES['file']['error']) && isset($_FILES['file']['tmp_name']) && $_FILES['file']['tmp_name'] != 'none'))
    {
        // 检查文件格式
        if (!check_file_type($_FILES['file']['tmp_name'], $_FILES['file']['name'], $allow_file_types))
        {
            ajax_show_message($_LANG['invalid_file']);
        }

        // 复制文件
        $res = upload_article_file($_FILES['file']);
        if ($res != false)
        {
            $file_url = $res;
        }
    }
    $data = [
        'url' => $file_url
    ];
    echo json_encode($data);
}
/* 上传文件 */
function upload_article_file($upload)
{
    if (!make_dir("../" . DATA_DIR . "/article"))
    {
        /* 创建目录失败 */
        return false;
    }

    $filename = cls_image::random_filename() . substr($upload['name'], strpos($upload['name'], '.'));
    $path     = ROOT_PATH. DATA_DIR . "/article/" . $filename;

    if (move_upload_file($upload['tmp_name'], $path))
    {
        return DATA_DIR . "/article/" . $filename;
    }
    else
    {
        return false;
    }
}
?>
