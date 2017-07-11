<!DOCTYPE html>
<html lang="en">
<?php echo $this->fetch('library/head.lbi'); ?>
<body>
    
    <div class="header min1320">
        <div class="header_top">
            <?php echo $this->fetch('library/nav.lbi'); ?>
        </div>
    </div>
    
    
    <div class="content  fb-position-relative" >
        <div class="clock fb-position-absolute"></div>
        <div class="artwork-list ">
            <div class="artwork-list-top">
                <div class="artwork-class">
                    <dl class="w1320">
                        <dt class="fb-inlineBlock fb-font16"><b>条件筛选</b></dt>
                        <?php $_from = $this->_var['categories']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'val');if (count($_from)):
    foreach ($_from AS $this->_var['val']):
?>
                        <dd class="fb-inlineBlock fb-font16 artwork-class-item <?php if ($this->_var['category'] == $this->_var['val'] [ 'id' ]): ?>on<?php endif; ?>" cat_id='<?php echo $this->_var['val']['id']; ?>'><?php echo $this->_var['val']['name']; ?></dd>
                        <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
                    </dl>
                    <div class="fb-position-relative w1320 z999">
                    <div class="screen-box fb-position-absolute">
                        <?php $_from = $this->_var['cats_all_attr_list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('cat_all_attr_key', 'cat_all_attr_list');if (count($_from)):
    foreach ($_from AS $this->_var['cat_all_attr_key'] => $this->_var['cat_all_attr_list']):
?>
                        
                        <div class="screen-item  fb-center <?php if ($this->_var['cat_all_attr_list']['class']): ?><?php echo $this->_var['cat_all_attr_list']['class']; ?><?php else: ?>screen-item-three<?php endif; ?>">
                            <form action="">
                                <?php $_from = $this->_var['cat_all_attr_list']['cats']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'cat');$this->_foreach['cat'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['cat']['total'] > 0):
    foreach ($_from AS $this->_var['cat']):
        $this->_foreach['cat']['iteration']++;
?>
                                <div class="screen-theme fb-float-left screen-theme<?php echo $this->_foreach['cat']['iteration']; ?>">
                                    <div class="title fb-font18"><?php echo $this->_var['cat']['name']; ?></div>
                                    <div class="theme-item fb-position-relative fb-float-left fb-font16">
                                        <label >全部</label>
                                        <input type="radio" name="theme" value="<?php echo $this->_var['cat']['id']; ?>"checked/>
                                    </div>
                                    <?php $_from = $this->_var['cat']['cat_id']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'cat_child');if (count($_from)):
    foreach ($_from AS $this->_var['cat_child']):
?>
                                    <div class="theme-item fb-position-relative fb-float-left fb-font16">
                                        <label ><?php echo $this->_var['cat_child']['name']; ?></label>
                                        <input type="radio" name="cat_id" value="<?php echo $this->_var['cat_child']['id']; ?>" checked/>
                                    </div>
                                    <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
                                </div>
                                <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
                                
                                <?php $_from = $this->_var['cat_all_attr_list']['all_attr_list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'filter_attr_0_37018700_1499703120');if (count($_from)):
    foreach ($_from AS $this->_var['filter_attr_0_37018700_1499703120']):
?>
                                <div class="<?php if ($this->_var['filter_attr_0_37018700_1499703120']['special'] == 1): ?> screen-size <?php elseif ($this->_var['filter_attr_0_37018700_1499703120']['special'] == 2): ?> screen-author <?php else: ?> screen-theme <?php endif; ?> fb-float-left ">
                                    <div class="title fb-font18"><?php echo htmlspecialchars($this->_var['filter_attr_0_37018700_1499703120']['filter_attr_name']); ?></div>
                                    <?php if ($this->_var['filter_attr_0_37018700_1499703120']['special'] == 1): ?>
                                        
                                            <div class="screen-size-item" attr_id="<?php echo $this->_var['filter_attr_0_37018700_1499703120']['attr_list']['0']['attr_id']; ?>">
                                                <label >长</label>
                                                <input type="text" name="slong" value=""/>
                                                <span>--</span>
                                                <input type="text" name="llong" value=""/>
                                            </div>
                                            <div class="screen-size-item" attr_id="<?php echo $this->_var['filter_attr_0_37018700_1499703120']['attr_list']['1']['attr_id']; ?>">
                                                <label >高</label>
                                                <input type="text" name="sheight" value=""/>
                                                <span>--</span>
                                                <input type="text" name="lheight" value=""/>
                                            </div>
                                        
                                    <?php elseif ($this->_var['filter_attr_0_37018700_1499703120']['special'] == 2): ?>
                                    <div class="screen-author-overflow">
                                        <div class="screen-author-item">
                                            <p></p>
                                            <ul>
                                                <li>
                                                    <label class="on">全部</label>
                                                    <input type="radio" name="author" value="a" checked>
                                                </li>

                                            </ul>
                                        </div>
                                        <?php $_from = $this->_var['filter_attr_0_37018700_1499703120']['attr_list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('k', 'attrs');if (count($_from)):
    foreach ($_from AS $this->_var['k'] => $this->_var['attrs']):
?>
                                        <div class="screen-author-item">
                                            <p><?php echo $this->_var['k']; ?></p>
                                            <ul>
                                                <?php $_from = $this->_var['attrs']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'attr');if (count($_from)):
    foreach ($_from AS $this->_var['attr']):
?>
                                                <li>
                                                    <label ><?php echo $this->_var['attr']['author_name']; ?></label>
                                                    <input type="radio" name="author" value="<?php echo $this->_var['attr']['author_id']; ?>">
                                                </li>
                                                <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
                                            </ul>
                                        </div>
                                        <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
                                    </div>
                                    <?php else: ?>
                                        <?php $_from = $this->_var['filter_attr_0_37018700_1499703120']['attr_list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'attr');if (count($_from)):
    foreach ($_from AS $this->_var['attr']):
?>
                                        <div class="theme-item fb-position-relative fb-float-left fb-font16">
                                            <label <?php if ($this->_var['attr']['selected']): ?> class="on" <?php endif; ?> ><?php echo $this->_var['attr']['attr_value']; ?></label>
                                            <input type="radio" name="theme" <?php if ($this->_var['attr']['selected']): ?> checked <?php endif; ?> value='<?php echo $this->_var['attr']['value']; ?>'/>
                                        </div>
                                        <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
                                    <?php endif; ?>
                                    </div>
                                <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
                                

                                
                                <div class="screen-value fb-float-left ">
                                    <div class="title fb-font18">价格（¥）</div>
                                    <?php $_from = $this->_var['price_grades'][$this->_var['cat_all_attr_key']]; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'grade');if (count($_from)):
    foreach ($_from AS $this->_var['grade']):
?>
                                    <div class="screen-value-item ">
                                       <label <?php if ($this->_var['grade']['selected']): ?> class="on" <?php endif; ?> ><?php echo $this->_var['grade']['price_range']; ?></label>
                                       <input type="radio" name="price" value="<?php echo $this->_var['grade']['value']; ?>" <?php if ($this->_var['grade']['selected']): ?> checked <?php endif; ?> />
                                    </div>
                                    <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
                                </div>
                                
                                
                                <div class="screen-state fb-float-left ">
                                    <div class="title fb-font18">状态</div>
                                    <div class="screen-state-item">
                                       <label class="on">可租可购</label>
                                       <input type="radio" name="nature" value="1" checked/>
                                    </div>
                                    <div class="screen-state-item">
                                       <label>租用</label>
                                       <input type="radio" name="nature" value="2"/>
                                    </div>
                                    <div class="screen-state-item">
                                       <label>购买</label>
                                       <input type="radio" name="nature" value="3"/>
                                    </div>
                                </div>
                                
                                <div class="form-button ">
                                    <input type="submit" class="fb-inlineBlock fb-font16 opa_active category_search_btn" value="确定" / >
                                    <input type="button" class="fb-inlineBlock close-form  fb-font16 opa_active" value="取消" / >
                                </div>
                            </form>
                        </div>
                        <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
                    </div>
                    </div>
                </div>
                <!--
                <div class="collocation w1320">
                    <div class="fb-figureCarousel fb-position-relative" style="padding-top:10px;">
                        <div class="fb-figureCarousel-boxOverflow" style="width: 1200px">
                            <div class="fb-figureCarousel-box fb-position-relative">
                                <div class="fb-figureCarousel-item fb-float-left">
                                    <a href=""><img src="<?php echo $this->_var['ec_themes']; ?>/images/icon_18.png" alt=""></a>
                                </div>
                                <div class="fb-figureCarousel-item fb-float-left">
                                    <a href=""><img src="<?php echo $this->_var['ec_themes']; ?>/images/icon_18.png" alt=""></a>
                                </div>
                                <div class="fb-figureCarousel-item fb-float-left">
                                    <a href=""><img src="<?php echo $this->_var['ec_themes']; ?>/images/icon_18.png" alt=""></a>
                                </div>
                                <div class="fb-figureCarousel-item fb-float-left">
                                    <a href=""><img src="<?php echo $this->_var['ec_themes']; ?>/images/icon_18.png" alt=""></a>
                                </div>
                                <div class="fb-figureCarousel-item fb-float-left">
                                    <a href=""><img src="<?php echo $this->_var['ec_themes']; ?>/images/icon_18.png" alt=""></a>
                                </div>
                                <div class="fb-figureCarousel-item fb-float-left">
                                    <a href=""><img src="<?php echo $this->_var['ec_themes']; ?>/images/icon_118.png" alt=""></a>
                                </div>
                                <div class="fb-figureCarousel-item fb-float-left">
                                    <a href=""><img src="<?php echo $this->_var['ec_themes']; ?>/images/icon_18.png" alt=""></a>
                                </div>
                                <div class="fb-figureCarousel-item fb-float-left">
                                    <a href=""><img src="<?php echo $this->_var['ec_themes']; ?>/images/icon_18.png" alt=""></a>
                                </div>
                                <div class="fb-figureCarousel-item fb-float-left">
                                    <a href=""><img src="<?php echo $this->_var['ec_themes']; ?>/images/icon_18.png" alt=""></a>
                                </div>

                                <div class="fb-figureCarousel-item fb-float-left">
                                    <a href=""><img src="<?php echo $this->_var['ec_themes']; ?>/images/icon_18.png" alt=""></a>
                                </div>
                                <div class="fb-figureCarousel-item fb-float-left">
                                    <a href=""><img src="<?php echo $this->_var['ec_themes']; ?>/images/icon_18.png" alt=""></a>
                                </div>
                                <div class="fb-figureCarousel-item fb-float-left">
                                    <a href=""><img src="<?php echo $this->_var['ec_themes']; ?>/images/icon_18.png" alt=""></a>
                                </div>

                            </div>
                        </div>
                        <div class="fb-figureCarousel-left fb-position-absolute"></div>
                        <div class="fb-figureCarousel-right fb-position-absolute"></div>
                    </div>
                </div>
            </div>
            -->
            <div class="artwork-list-con fb-waterfall-con fb-position-relative" >

            </div>
            <div class="fb-loading fb-font16"></div>
        </div>

    </div>
    
    
    <?php echo $this->fetch('library/footer.lbi'); ?>
    
</body>
    
    <link rel="stylesheet" type="text/css" href="<?php echo $this->_var['ec_themes']; ?>/css/jquery.mCustomScrollbar.css">
    <script type="text/javascript" src="<?php echo $this->_var['ec_themes']; ?>/js/jquery.mCustomScrollbar.js"></script>
    <script type="text/javascript" src="<?php echo $this->_var['ec_themes']; ?>/js/jquery.mousewheel.min.js"></script>
<script>

    $fb(".fb-figureCarousel").figureCarousel({
            speed : 500,
            autoPlay: true,
            showNum:5
    });
    var goods_list = <?php echo $this->_var['goods_list_json']; ?>;

    $fb.waterfall({
                "el":".artwork-list-con",
                "margin":20,
                'data':goods_list
            });
    ajaxCallUrl = '<?php echo $this->_var['app_pager']['page_next']; ?>';
    $fb.monitorBottom({
        bottom:100,
        arriveFun:function(){
            if(ajaxCallUrl != '')
            {
                $(".fb-loading").text("正在加载中...");
                $.ajax({
                    cache: true,
                    type: "POST",
                    url:ajaxCallUrl,
                    data:{'ajax':1},// 你的formid
                    async: false,
                    error: function(request) {
                        alert("Connection error");
                    },
                    success: function(data) {
                        $(".fb-loading").text("");
                        var obj = JSON.parse(data);
                        ajaxCallUrl = obj.app_pager.page_next;
                        console.log(ajaxCallUrl);
                        $fb.waterfall({
                            "el":".artwork-list-con",
                            'data':obj.goods_list,
                            "margin":20
                        })
                        if(ajaxCallUrl == ''){

                            $(".fb-loading").text("已经到底了")
                            return false;
                        }
                         $fb.monitorBottom.again();
                    }
                });
            }
        }
    });
    $(".screen-item-one .screen-author .screen-author-overflow").mCustomScrollbar();
    $(".category_search_btn").click(function(){
        //c
        $fb.resetWaterfall({
            "el":".artwork-list-con",
        })
        var slong = $(this).parent().parent().find("input[name='slong']").val();
        var llong = $(this).parent().parent().find("input[name='llong']").val();
        var sheight = $(this).parent().parent().find("input[name='sheight']").val();
        var lheight = $(this).parent().parent().find("input[name='lheight']").val();
        var filter_attr = $(this).parent().parent().find("input[name='theme']:checked").val();
        var prices = $(this).parent().parent().find("input[name='price']:checked").val();
        var author_id = $(this).parent().parent().find("input[name='author']:checked").val();
        var cat_id = $(this).parent().parent().find("input[name='cat_id']:checked").val();
        var nature = $(this).parent().parent().find("input[name='nature']:checked").val();
        var id = '<?php echo $this->_var['category']; ?>';
        var long_attr_id = 0;
        var height_attr_id = 0;
        var price_min = 0;
        var price_max = 0;
        if(prices){
            var price_arr = prices.split('_');
            var price_min = price_arr[0];
            var price_max = price_arr[1];
        }

        if(slong || llong){
            var long_attr_id = $(this).parent().parent().find("input[name='slong']").parent().attr('attr_id');
        }
        if(sheight || lheight){
            var height_attr_id = $(this).parent().parent().find("input[name='sheight']").parent().attr('attr_id');
        }
        if(!cat_id)
        {
            id = $('.artwork-class-item.on').attr('cat_id');
        }
        var url = 'category.php?id='+id+'&price_min='+price_min+'&price_max='+price_max+'&filter_attr='+filter_attr+'&author_id='+author_id+'&slong='+slong+'&llong='+llong+'&sheight='+sheight+'&lheight='+lheight+'&long_attr_id='+long_attr_id+'&height_attr_id='+height_attr_id+'&nature='+nature;

        $.ajax({
            cache: true,
            type: "POST",
            url:url,
            data:{'ajax':1},// 你的formid
            async: false,
            error: function(request) {
                alert("Connection error");
                return false;
            },
            success: function(data) {
                $(".screen-box").fadeOut(200);
        		$(".clock").fadeOut(200);
                $(".fb-loading").text("");
                var obj = JSON.parse(data);
                ajaxCallUrl = obj.app_pager.page_next;
                console.log(ajaxCallUrl);
                $fb.waterfall({
                    "el":".artwork-list-con",
                    'data':obj.goods_list,
                    "margin":20
                })
                if(ajaxCallUrl == ''){

                    $(".fb-loading").text("已经到底了")
                    return false;
                }
                 $fb.monitorBottom.again();

            }
        });
        return false;
    });
</script>
</html>
