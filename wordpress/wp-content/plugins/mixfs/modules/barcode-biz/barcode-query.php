<?php
if (!defined('ABSPATH'))
    exit; // Exit if accessed directly
?>
<script type="text/javascript">
    jQuery(document).ready(function ($) {
        $("#datepicker").datepicker({
            //defaultDate: "-1M",
            numberOfMonths: 1,
            minDate: new Date(2015, 1 - 1, 1),
            maxDate: "+1d",
            monthNames: ["一月", "二月", "三月", "四月", "五月", "六月", "七月", "八月", "九月", "十月", "十一月", "十二月"],
            dayNamesMin: ["日", "一", "二", "三", "四", "五", "六"],
            dateFormat: "yy-mm-dd"
        });
        $(function () {
            $("#datepicker").datepicker();
        });

    });
</script>

<p>Date: <input type="text" id="datepicker"></p>

<script type="text/javascript">
    jQuery(document).ready(function ($) {
        var availableTags = [
            "ActionScript",
            "AppleScript",
            "Asp",
            "BASIC",
            "C",
            "C++",
            "Clojure",
            "COBOL",
            "ColdFusion",
            "Erlang",
            "Fortran",
            "Groovy",
            "Haskell"
        ];
        $("#tags").autocomplete({
            source: availableTags
        });
    });
</script>
<div class="ui-widget">
    <label for="tags">Tags: </label>
    <input id="tags">
</div>