<?php
if (!defined('ABSPATH'))
    exit; // Exit if accessed directly


mixfs_top('财务软件入口');

unset($_SESSION['mas']);


/**
 * 1. update: md5()
 * 2. create: $_SESSION['mixfs_login_id'] + $_SESSION['mixfs_access list'];
 */
global $wpdb, $current_user;        // $current_user->ID;

$current_week = date('W', time());
$old_week = get_option('mixfs_week_number');

if ($current_week != $old_week) {
    update_option('mixfs_week_number', $current_week);

    $tables_prefix = $wpdb->get_results("SELECT ma_tbl_prefix FROM {$wpdb->prefix}mixfs_accounts", ARRAY_A);

    foreach ($tables_prefix as $prefix) {
        $new_md5 = md5($prefix['ma_tbl_prefix'] . $current_week);
        $wpdb->update(
                "{$wpdb->prefix}mixfs_accounts", array('ma_create_md5' => $new_md5), array('ma_tbl_prefix' => $prefix['ma_tbl_prefix'])
        );
    }
}

// 获取全部账套信息：账表英文名 + 中文名 + 用户权限 + 账套说明 + 账套类型
$mixfs_acc_list = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}mixfs_accounts ORDER BY ma_order_by", ARRAY_A);
?>

<div class="manage-menus"> 请点击下方的【账套名称】后再进行操作 ... </div>

<br class="clear" />
<form action="" method="POST">
    <table class="wp-list-table widefat fixed users" id="entrance" cellspacing="1">
        <thead>
            <tr>
                <th class='manage-column' style="width: 80px;">序号</th>
                <th class='manage-column'  style="">账套名称</th>
                <th class='manage-column' style="">账套缩写</th>
                <th class='manage-column'  style="">账套说明</th>
                <th class='manage-column'  style="">账套类型</th>
            </tr>
        </thead>
        <tfoot>
            <tr>
                <th class='manage-column' style="width: 80px;">序号</th>
                <th class='manage-column'  style="">账套名称</th>
                <th class='manage-column' style="">账套缩写</th>
                <th class='manage-column'  style="">账套说明</th>
                <th class='manage-column'  style="">账套类型</th>
            </tr>
        </tfoot>
        <tbody>
            <?php
            // mas=MixFS Acounting Session, http://url/?mas=xxx
            $_SESSION["mas"] = array();

            if (empty($mixfs_acc_list)) {
                echo "<tr class='alternate'><td class='name' colspan='4' style='padding: 50px;'> 没有账套数据，请先添加账套再使用本软件！ </td></tr>";
            } else {
                $counter = 0; // 账套序号

                foreach ($mixfs_acc_list as $acc_name) {
                    //判断用户是否拥有访问某账套权限
                    $permission = in_array($current_user->ID, explode(",", $acc_name['ma_ID_permission']));
                    ++$counter;
                    if ($permission) {
                        switch ($acc_name['ma_acc_type']) {
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
                        ?>
                        <tr class='alternate'>
                            <td class='name'><?php echo $counter; ?></td>
                            <td class='name'>
                                <?php acc_type_jump_btn($acc_name['ma_acc_type'], $acc_name['ma_tbl_name'], $acc_name['ma_create_md5']) ?>
                            </td>
                            <td class='name'><?php echo $acc_name['ma_tbl_prefix']; ?></td>
                            <td class='name'><?php echo $acc_name['ma_tbl_detail']; ?></td>
                            <td class='name'><?php echo $acc_type; ?></td>
                        </tr>
                        <?php
                        // $_SESSION["mas"]["md5***xxx"] = lc | 老厂, $_SESSION["mas"]["md5***yyy"] = zs | 注塑
                        $_SESSION["mas"][$acc_name['ma_create_md5']] = $acc_name['ma_tbl_prefix'] . '|' . $acc_name['ma_tbl_name'];
                    }
                }
                if ($counter < 1) {
                    echo "<tr class='alternate'><td class='name' colspan='4' style='padding: 50px;'> 没有访问账目的权限 </td></tr>";
                }
            }
            ?>
        </tbody>
    </table>
</form>

<?php

/**
 * 选择账套名按钮，按对应账套类型跳转相应页面 xxx_guide.php
 */
function acc_type_jump_btn($acc_type, $btn_value, $acc_secret) {
    if ('invoicing' == $acc_type) {
        ?>
        <input type='button' class='button' 
               value='<?php echo $btn_value; ?>' 
               style="width: 200px;"
               onclick="location.href = location.href.substring(0, location.href.indexOf('?page')) + '?page=invoicing-query-guide&secret=<?php echo $acc_secret; ?>'" />
               <?php
           } elseif ('current_acc' == $acc_type) {
               ?>
        <input type='button' class='button' 
               value='<?php echo $btn_value; ?>' 
               style="width: 200px;"
               onclick="location.href = location.href.substring(0, location.href.indexOf('?page')) + '?page=current-acc-guide&secret=<?php echo $acc_secret; ?>'" />
               <?php
           } elseif ('barcode' == $acc_type) {
               ?>
        <input type='button' class='button' 
               value='<?php echo $btn_value; ?>' 
               style="width: 200px;"
               onclick="location.href = location.href.substring(0, location.href.indexOf('?page')) + '?page=barcode-guide&secret=<?php echo $acc_secret; ?>'" />
        <?php
    }
}

mixfs_bottom();
?>