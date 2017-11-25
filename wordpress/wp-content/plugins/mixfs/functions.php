<?php
if (!defined('ABSPATH'))
    exit; // Exit if accessed directly


/**
 * ########## 菜单权限相关函数 ##########
 */

/**
 * 判断用户所属 MixFS 某个角色
 * 返回角色名
 * 如果不是管理员或 MixFS 某个角色，返回 False
 */
function get_mixfs_role() {
    global $current_user;
    switch ($current_user->roles[0]) {
        case 'mixfs_superman' :
            $cap = 'mixfs_superman';
            break;
        case 'mixfs_manager':
            $cap = 'mixfs_manager';
            break;
        case 'mixfs_operator':
            $cap = 'mixfs_operator';
            break;
        case 'mixfs_current_acc':
            $cap = 'mixfs_current_acc';
            break;
        case 'mixfs_barcode':
            $cap = 'mixfs_barcode';
            break;
        default :
            $cap = FALSE;
    }
    return $cap;
}

/**
 * 修改工厂表名前缀 mixfs_aaa_xxx => mixfs_bbb_xxx
 */
function update_acc_tables($old, $new) {
    global $wpdb;

    $counter = 0;
    $r = $wpdb->get_results("SHOW TABLES FROM {$wpdb->dbname}", ARRAY_N);

    foreach ($r as $v) {
        if (strstr($v[0], $old)) { // 表名匹配
            $new_full_name = str_replace($old, $new, $v[0]);
            $wpdb->query("RENAME TABLE {$v[0]} TO {$new_full_name}");
            ++$counter;
        }
    }

    return $counter; // 修改表名数量
}

/**
 * 判断账套表是否存在
 */
function table_name_exists($acc_prefix) {
    global $wpdb;

    $tbl_prefix_exist = $wpdb->get_var("SELECT ma_tbl_prefix FROM {$wpdb->prefix}mixfs_accounts WHERE ma_tbl_prefix='{$acc_prefix}'");

    return $tbl_prefix_exist;
}

/**
 * 多个用户ID转用户名
 * 3, 4 => 张三 | 李四
 */
function user_group2user_name($user_group_ID) {
    $arr_ID = explode(",", $user_group_ID);
    $users_name = "";
    foreach ($arr_ID as $user_ID) {
        $user_info = get_userdata($user_ID);
        $users_name .= $user_info->user_login . " | ";
    }
    return rtrim(trim($users_name), " | ");
}

/**
 * id转name, 产品ID=>品名，费用ID=>费用名称
 */
function id2name($field_name, $table, $source_id, $tbl_id) {
    global $wpdb;
    $name = $wpdb->get_var("SELECT {$field_name} FROM {$table} WHERE {$tbl_id}='{$source_id}'");

    return $name;
}

/**
 * 日期文本框
 * 2 个参数设置起止时间 2 个文本框
 * 默认 1 个参数设置
 * 参数为 <input id="tag_from" name="tag_from">
 */
function date_from_to($tag_from, $tag_to = '') {

    if ('' == $tag_to) {
        echo <<<DateJS
<script type="text/javascript">
    jQuery(document).ready(function($) {
        $( "#{$tag_from}" ).datepicker({
            //defaultDate: "-1M",
            numberOfMonths: 1,
            minDate: new Date(2015, 1 - 1, 1),
            maxDate: "+1d",
            monthNames: [ "一月","二月","三月","四月","五月","六月","七月","八月","九月","十月","十一月","十二月" ],
            dayNamesMin: [ "日","一","二","三","四","五","六" ],
            dateFormat: "yy-mm-dd"
        });
    });
</script>
DateJS;
    } else {

        echo <<<DateJS
<script type="text/javascript">
    jQuery(document).ready(function($) {
        $( "#{$tag_from}" ).datepicker({
          defaultDate: "-1M",
          numberOfMonths: 1,
            monthNames: [ "一月","二月","三月","四月","五月","六月","七月","八月","九月","十月","十一月","十二月" ],
            dayNamesMin: [ "日","一","二","三","四","五","六" ],
            dateFormat: "yy-mm-dd",
          onClose: function( selectedDate ) {
            $( "#{$tag_to}" ).datepicker( "option", "minDate", selectedDate );
          }
        });
        $( "#{$tag_to}" ).datepicker({
            //defaultDate: "-1M",
            numberOfMonths: 1,
            monthNames: [ "一月","二月","三月","四月","五月","六月","七月","八月","九月","十月","十一月","十二月" ],
            dayNamesMin: [ "日","一","二","三","四","五","六" ],
            dateFormat: "yy-mm-dd",
            onClose: function( selectedDate ) {
                $( "#{$tag_from}" ).datepicker( "option", "maxDate", selectedDate );
            }
        });
    });
</script>
DateJS;
    }
}

/**
 * 自动提示补全文本框
 * 双击显示全部
 * 用来提示 产品名称或费用项目 ...
 */
function autocompletejs($cols_format, $tag) {
    echo <<<autoJS
<script type="text/javascript">
jQuery(document).ready(function ($) {
    $.widget("custom.catcomplete", $.ui.autocomplete, {
            _renderMenu: function (ul, items) {
                var that = this,
                        currentCategory = "";
                $.each(items, function (index, item) {
                    if (item.category != currentCategory) {
                        ul.append("<li class='ui-autocomplete-category'>" + item.category + "</li>");
                        currentCategory = item.category;
                    }
                    that._renderItemData(ul, item);
                });
            }
        });
        $("#{$tag}").catcomplete({
            delay: 0,
            source: [$cols_format]
        });
        $("#{$tag}").catcomplete({
            minLength: 0
        }).dblclick(function () {
            $(this).catcomplete('search', '');
        });
        $("#{$tag}").focus(function () {
            $(this).val("");
        });
    });
</script>
autoJS;
} // autocompletejs

/**
 * 
 * 产成品专用自动补全，订单模式
 * 双击显示全部, 自动填充 每件双数 input
 */
function ordercompletejs($cols_format, $tag) {
    echo <<<autoJS
<script type="text/javascript">
    jQuery(document).ready(function ($) {
        $.widget("custom.catcomplete", $.ui.autocomplete, {
            _create: function () {
                this._super();
                this.widget().menu("option", "items", "> :not(.ui-autocomplete-category)");
            },
            _renderMenu: function (ul, items) {
                var that = this,
                        currentCategory = "";
                $.each(items, function (index, item) {
                    var li;
                    if (item.category != currentCategory) {
                        ul.append("<li class='ui-autocomplete-category'>" + item.category + "</li>");
                        currentCategory = item.category;
                    }
                    li = that._renderItemData(ul, item);
                    if (item.category) {
                        li.attr("aria-label", item.category + " : " + item.label);
                    }
                });
            }
        });
        $(".{$tag}").catcomplete({
            delay: 0,
            source: [$cols_format],
            select: function (event, ui) {
                var i = $(".goodsbiz_order").index(this);
                $("input[name='per_pack[]']").eq(i).val(ui.item.per_pack);
                $("input[name='qty[]']").eq(i).val(1);
                $("input[name='qty[]']").eq(i).focus();
            }
        });
        $(".{$tag}").catcomplete({
            minLength: 2
        }).dblclick(function () {
            $(this).catcomplete('search', '');
        });
        var FieldCount = 5;
        var flag = 1;
        $(".{$tag}").live('focus', function () {
            var MaxInputs = 200; //maximum input boxes allowed
            var InputsWrapper = $("#createuser tbody"); //Input boxes wrapper ID

            var x = InputsWrapper.length + 5; //initlal text box count
            $(this).val("");
            if ((x++) <= MaxInputs && (++flag > FieldCount)) { //max input box allowed
                FieldCount++;
                $(InputsWrapper).append('<tr><td>' + FieldCount + '</td>\
                        <td><input type="text" name="goodsbiz_name[]" class="goodsbiz_order" value="" /></td>\
                        <td style="width: 50px;">\
                            <input type="text" class="per_pack" name="per_pack[]" value="" style="width: 50px;background-color:#EEE;" />\
                        </td>\
                        <td><input type="text" name="qty[]" value="" onfocus="this.select()" /></td>\
                        <td><input type="text" name="price[]"  value=""/></td>\
                        <td><input type="text" name="sum[]" value="" disabled="disabled" /></td>\
                        <td><input type="text" name="summary[]" value="" /></td>\
                    </tr>');
                $(".{$tag}").catcomplete({
                    delay: 0,
                    source: [$cols_format],
                    select: function (event, ui) {
                        var i = $(".goodsbiz_order").index(this);
                        $("input[name='per_pack[]']").eq(i).val(ui.item.per_pack);
                        $("input[name='qty[]']").eq(i).val(1);
                        $("input[name='qty[]']").eq(i).focus();
                    }
                });
                $(".{$tag}").catcomplete({
                    minLength: 0
                }).dblclick(function () {
                    $(this).catcomplete('search', '');
                });
                $(".{$tag}").focus(function () {
                    $(this).val("");
                });
                $("input").keypress(function (event) {
                    var keynum = (event.keyCode ? event.keyCode : event.which);
                    if (keynum == '13') {
                        if (!confirm("提交请点[确认]或按回车，否则点[取消]或按ESC")) {
                            return false;
                        }
                    }
                });
            }
        });
        $("input").keypress(function (event) {
            var keynum = (event.keyCode ? event.keyCode : event.which);
            if (keynum == '13') {
                if (!confirm("提交请点[确认]或按回车，否则点[取消]或按ESC")) {
                    return false;
                }
            }
        });
    });
</script>
autoJS;
}

// ordercompletejs

/**
 * ######################### 需要优化，没用上小数点分割 ########################
 * 
 * 自定义数字格式化
 * number_format = 保留几位小数
 */
function mix_num($old_num, $cash = FALSE, $placeholder = '') {
    if ($cash) {
        return ($old_num != 0) ? number_format($old_num, $cash) : $placeholder;
    }
    return ($old_num == 0) ? '' : $old_num;
}

/**
 * 所有页面输出的框架
 * 顶部：mixfs_top()
 * 底部：mixfs_bottom()
 */
function mixfs_top($title, $acc_name = '') {

    global $current_user;

    $url_entrance = admin_url('admin.php?page=app-entrance'); // 所有页面共用的返回入口链接URL == app-entrance

    $html = '<div class="wrap">'
            . '<div id="icon-themes" class="icon32"><br></div>'
            . '<h2 class="nav-tab-wrapper">';

    if ($_GET['page'] == 'app-entrance') {
        $html .= '<a href="' . $url_entrance . '" class="nav-tab nav-tab-active">财务软件入口</a>';
    } else {
        if (isset($_SESSION['mas']['acc_tbl']) && isset($_SESSION['mas']['acc_name'])) {
            $html .= '<a href="' . $url_entrance . '" class="nav-tab">财务软件入口 &#187 ' . $acc_name . '</a>';
            $html .= '<a href="" class="nav-tab nav-tab-active">' . $title . '</a>';
        } else {
            echo "<script type='text/javascript'>location.href='$url_entrance'</script>";
            exit();
        }
    }

    echo $html . '<a href="' . wp_logout_url() . '" class="nav-tab">退出软件</a></h2><br />';
}

/**
 * MixPage 底部代码 + JS(选中行高亮)
 * 外币鼠标悬停字体加粗
 */
function mixfs_bottom() {
    ?>
    </div>

    <script type="text/javascript">

        jQuery(document).ready(function ($) {
            if ($(".alternate").length > 0) {
                var bg = '';
                $(".alternate:odd").css("background-color", "#FAFAFA");

                $(".alternate").mouseover(function () {
                    bg = $(this).css("background-color");
                    $(this).css("background-color", "#CFC");
                });
                $(".alternate").mouseout(function () {
                    $(this).css("background-color", bg);
                });
            }
            $(".wp-list-table span").mouseover(function () {
                $(this).css("font-weight", "bold");
            });
            $(".wp-list-table span").mouseout(function () {
                $(this).css("font-weight", "");
            });
        });

    </script>

<?php } ?>