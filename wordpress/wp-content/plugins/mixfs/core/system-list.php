<?php
if (!defined('ABSPATH'))
    exit;

require_once ( 'schema-invoicing.php');
require_once ( 'schema-current-acc.php');
require_once ( 'schema-barcode.php');

$url_entrance = admin_url('admin.php?page=app-entrance'); // 所有页面共用的返回入口链接URL == mixfs-entrance
$html = '<div class="wrap">'
        . '<div id="icon-themes" class="icon32"><br></div>'
        . '<h2 class="nav-tab-wrapper">'
        . '<a href="' . $url_entrance . '" class="nav-tab">财务软件入口</a>'
        . '<a href="' . $url_entrance . '" class="nav-tab nav-tab-active">账套列表</a>';
echo $html . '<a href="' . wp_logout_url() . '" class="nav-tab">退出软件</a></h2><br />';


global $wpdb;
if (isset($_POST['btn_tbl_add'])) {
    $tbl_prefix = preg_replace("/\s|　/", "", $_POST['tbl_prefix']); //删除所有空格和全角空格
    $tbl_prefix = ctype_alnum($tbl_prefix) ? $tbl_prefix : 0;
    $tbl_name = trim($_POST['tbl_name']);
    $tbl_detail = trim($_POST['tbl_detail']);
    $acc_type = $_POST['acc_type'];
    if (empty($acc_type) || empty($tbl_prefix) || empty($tbl_name)) {
        echo '<div id="message" class="updated"><p>表名不能为空，且必须是字母或数字</p></div>';
    } else {
        if (table_name_exists($tbl_prefix)) {
            echo '<div id="message" class="updated"><p>账套已存在，请重新命名后再次提交</p></div>';
        } else {
            if ($acc_type == 'invoicing') {
                create_invoicing_tables($wpdb->prefix . 'mixfs_' . $tbl_prefix);
            } elseif ($acc_type == 'current_acc') {
                create_current_acc_tables($wpdb->prefix . 'mixfs_' . $tbl_prefix);
            } elseif ($acc_type == 'barcode') {
                create_barcode_tables($wpdb->prefix . 'mixfs_' . $tbl_prefix);
            }
            $wpdb->insert(
                    $wpdb->prefix . 'mixfs_accounts', array(
                'ma_tbl_prefix' => $tbl_prefix,
                'ma_tbl_name' => $tbl_name,
                'ma_tbl_detail' => $tbl_detail,
                'ma_create_md5' => md5($tbl_prefix . get_option('mixfs_md5_week')),
                'ma_acc_type' => $acc_type,
                'ma_create_date' => date('Y-m-d')
                    )
            ); // md5(lc + week_num)

            echo '<div id="message" class="updated"><p>添加账套成功</p></div>';
        }
    }
} elseif (isset($_POST['btn_tbl_update']) && count($_POST['ma_id'])) {
    $counter = 0;
    foreach ($_POST['ma_id'] as $id) {
        $tbl_prefix = preg_replace("/\s|　/", "", $_POST['ma_tbl_prefix' . $id]); //删除所有空格和全角空格
        $tbl_prefix = ctype_alnum($tbl_prefix) ? $tbl_prefix : 0;
        $tbl_name = trim($_POST['ma_tbl_name' . $id]);
        $tbl_detail = trim($_POST['ma_tbl_detail' . $id]);
        $tbl_barcode = trim($_POST['ma_tbl_barcode' . $id]);
        if (empty($tbl_prefix) || empty($tbl_name)) {
            echo '<div id="message" class="updated"><p>表名不能为空，且必须是字母或数字</p></div>';
        } else {
            if (table_name_exists($tbl_prefix)) {
                $wpdb->update($wpdb->prefix . 'mixfs_accounts', array(
                    'ma_tbl_name' => $tbl_name,
                    'ma_tbl_detail' => $tbl_detail,
                    'ma_create_md5' => md5($tbl_prefix . get_option('mixfs_md5_week')),
                    'ma_link_barcode' => $tbl_barcode,
                    'ma_update_date' => current_time('mysql')
                        ), array('ma_id' => $id)
                );
                echo '<div id="message" class="updated"><p>' . $tbl_name . ' 表的信息已更新！</p></div>';
            } else {
                $old = $wpdb->get_row("SELECT ma_tbl_prefix FROM {$wpdb->prefix}mixfs_accounts WHERE ma_id={$id}", ARRAY_A);
                $old = $wpdb->prefix . 'mixfs_' . $old['ma_tbl_prefix'] . '_';
                $new = $wpdb->prefix . 'mixfs_' . $tbl_prefix . '_';
                $counter += update_acc_tables($old, $new);

                $wpdb->update($wpdb->prefix . 'mixfs_accounts', array(
                    'ma_tbl_prefix' => $tbl_prefix,
                    'ma_tbl_name' => $tbl_name,
                    'ma_tbl_detail' => $tbl_detail,
                    'ma_create_md5' => md5($tbl_prefix . get_option('mixfs_md5_week')),
                    'ma_update_date' => current_time('mysql')
                        ), array('ma_id' => $id)
                );
            }
        }
    }
    if ($counter) {
        echo '<div id="message" class="updated"><p>信息更新成功，共 ' . $counter . ' 个表被更新！</p></div>';
    }
} elseif (isset($_POST['btn_tbl_update']) && empty($_POST['ma_id'])) {
    echo '<div id="message" class="updated"><p>必须选择一项且内容必须填充完整才能更新，表名必须是字母！</p></div>';
}
?>


<form action="" method="post">
    <div class="manage-menus">

        <div class="alignleft actions">
            <select name="acc_type" id="acc_type">
                <option selected="selected" value=""> 请选择账套类型...</option>
                <option value="invoicing"> 1. 进销存账套</option>
                <option value="current_acc"> 2. 客户往来账</option>
                <option value="barcode"> 3. 条形码</option>
            </select>
            <input type="text" id="tbl_prefix" name="tbl_prefix" value="输入指定账套字母表名..." maxlength="20" size="25" style="color: #ccc;" 
                   onblur="if (this.value == '') {
                               this.value = '输入指定账套字母表名...';
                               this.style.color = '#ccc';
                           }" 
                   onfocus="if (this.value == '输入指定账套字母表名...') {
                               this.value = '';
                               this.style.color = '#333';
                           }" />
            <input type="text" id="tbl_name" name="tbl_name" value="输入账套中文名称..." maxlength="20" size="25" style="color: #ccc;" 
                   onblur="if (this.value == '') {
                               this.value = '输入账套中文名称...';
                               this.style.color = '#ccc';
                           }" 
                   onfocus="if (this.value == '输入账套中文名称...') {
                               this.value = '';
                               this.style.color = '#333';
                           }" />
            <input type="text" id="tbl_detail" name="tbl_detail" value="输入备注..." maxlength="20" size="25" style="color: #ccc;" 
                   onblur="if (this.value == '') {
                               this.value = '输入备注...';
                               this.style.color = '#ccc';
                           }" 
                   onfocus="if (this.value == '输入备注...') {
                               this.value = '';
                               this.style.color = '#333';
                           }" />
            <input type="submit" name="btn_tbl_add" id="btn_tbl_add" class="button" value="添加新账套"  />
        </div>
        <br class="clear" />
    </div>
    <br />
    <table class="wp-list-table widefat fixed users" cellspacing="1">
        <thead>
            <tr>
                <th class='manage-column column-cb check-column'  style="width: 35px;">
                    <input id="cb-select-all-1" type="checkbox" />
                </th>
                <th class='manage-column' style="width: 200px;">账目数据库表名</th>
                <th class='manage-column' style="">账目名称</th>
                <th class='manage-column'  style="">信息摘要</th>
                <th class='manage-column'  style="">账套类型</th>
                <th class='manage-column'  style="">关联条形码</th>
                <th class='manage-column'  style="">启用账套日期</th>
                <th class='manage-column'  style="">账套更新日期</th>
            </tr>
        </thead>

        <tfoot>
            <tr>
                <th class='manage-column column-cb check-column'  style="width: 35px;">
                    <input id="cb-select-all-1" type="checkbox" />
                </th>
                <th class='manage-column' style="width: 200px;">账目数据库表名</th>
                <th class='manage-column' style="">账目名称</th>
                <th class='manage-column'  style="">信息摘要</th>
                <th class='manage-column'  style="">账套类型</th>
                <th class='manage-column'  style="">关联条形码</th>
                <th class='manage-column'  style="">启用账套日期</th>
                <th class='manage-column'  style="">账套更新日期</th>
            </tr>
        </tfoot>

        <tbody>
            <?php
            $results_accounts = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}mixfs_accounts ORDER BY ma_order_by", ARRAY_A);

            foreach ($results_accounts as $tbl) {

                switch ($tbl['ma_acc_type']) {
                    case 'invoicing':
                        $acc_type = '进销存账';
                        break;
                    case 'current_acc':
                        $acc_type = '往来账';
                        break;
                    case 'barcode':
                        $acc_type = '条形码';
                        break;
                    default:
                        $acc_type = '未知';
                        break;
                }

                echo "<tr class='alternate'>
                        <th scope='row' class='check-column'>
                            <input type='checkbox' name='ma_id[]' class='administrator' value='{$tbl['ma_id']}' />
                        </th>
                        <td class='name'>
                            <code>{$wpdb->prefix}mixfs_</code>
                            <input name='ma_tbl_prefix{$tbl['ma_id']}' type='text' value='{$tbl['ma_tbl_prefix']}' size='10' />
                            <code>_xxx</code>
                        </td>
                        <td class='name'>
                            <input name='ma_tbl_name{$tbl['ma_id']}' type='text' value='{$tbl['ma_tbl_name']}' size='15' />
                        </td>
                        <td class='name'>
                            <input name='ma_tbl_detail{$tbl['ma_id']}' type='text' value='{$tbl['ma_tbl_detail']}' size='15' />
                        </td>
                        <td class='name'>{$acc_type}</td>
                        <td class='name'>
                            <input name='ma_tbl_barcode{$tbl['ma_id']}' type='text' value='{$tbl['ma_link_barcode']}' size='10' />
                        </td>
                        <td class='name'>{$tbl['ma_create_date']}</td>
                        <td class='name'>{$tbl['ma_update_date']}</td>
                    </tr>";
            }
            ?>
        </tbody>
    </table>

    <div class="tablenav bottom">

        <div class="alignleft actions">
            <input type="submit" name="btn_tbl_update" id="btn_tbl_update" class="button button-primary" value="更新指定账套信息"  />
        </div>
        <br class="clear" />
    </div>
</form>


<?php mixfs_bottom(); ?>