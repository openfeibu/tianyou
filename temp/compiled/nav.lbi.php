<div class="w1320">
    <div class="nav fb-float-left">
        <ul>
            <?php $_from = $this->_var['navigator_list']['middle']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'nav');$this->_foreach['nav_middle_list'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['nav_middle_list']['total'] > 0):
    foreach ($_from AS $this->_var['nav']):
        $this->_foreach['nav_middle_list']['iteration']++;
?>
            <li>
                <a href="<?php echo $this->_var['nav']['url']; ?>"><?php echo $this->_var['nav']['name']; ?></a>
            </li>
            <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
            <li>
                <a href="index.php#author">艺术家排名</a>
            </li>
        </ul>
    </div>
    
    <div class="mine fb-float-right">
        <div class="search fb-float-left">
               <a href=""></a>
        </div>
        <div class="cart fb-float-left" id="ECS_CARTINFO">
            <?php 
$k = array (
  'name' => 'cart_info',
);
echo $this->_echash . $k['name'] . '|' . serialize($k) . $this->_echash;
?>
        </div>

        <div class="user fb-float-left fb-position-relative">
           <a href="user.php">

           </a>
           <div class="user-box fb-position-absolute">
               <div class="tagT fb-position-absolute"></div>
               <ul >
                  <li><a href="user.php">管理账户</a></li>
                  <li><a href="user.php?act=order_list">已购艺术品</a></li>
                  <li><a href="user.php?act=logout">退出</a></li>
               </ul>
            </div>
        </div>
    </div>
    <div class="logo">
        <a href="index.php">
            <img src="<?php echo $this->_var['ec_themes']; ?>/images/logo.png" alt="">
        </a>
    </div>
</div>
</div>
