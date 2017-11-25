<?php

if (!defined('ABSPATH'))
    exit; // Exit if accessed directly

?>
<div class="wrap">
<h1>Your Plugin Name</h1>
<?php
$page_id = 4; 
$page_data = get_page( $page_id ); 
echo '<h3>'. $page_data->post_title .'</h3>';// 标题
echo apply_filters('the_content', $page_data->post_content);  //内容

?>
</div>