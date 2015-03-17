<style type="text/css">
    #query_selector table tr td.header {
        width: 200px;
        text-align: left;
        font-weight: bold;
    }

    #query_selector table tr td.body {
        text-align: left;
        vertical-align: top;
    }

    #query_results {
        float: left;
    }

    .green {
        color: black;
        background-color: #B1E111;
    }

    .yellow {
        color: black;
        background-color: yellow;
    }

    .red {
        color: black;
        background-color: #E78383;
    }

    .green:not(.jqx-grid-cell-hover):not(.jqx-grid-cell-selected), .jqx-widget .green:not(.jqx-grid-cell-hover):not(.jqx-grid-cell-selected) {
        color: black;
        background-color: #b6ff00;
    }

    .yellow:not(.jqx-grid-cell-hover):not(.jqx-grid-cell-selected), .jqx-widget .yellow:not(.jqx-grid-cell-hover):not(.jqx-grid-cell-selected) {
        color: black;
        background-color: yellow;
    }

    .red:not(.jqx-grid-cell-hover):not(.jqx-grid-cell-selected), .jqx-widget .red:not(.jqx-grid-cell-hover):not(.jqx-grid-cell-selected) {
        color: black;
        background-color: #e83636;
    }

    #date_selector {
        padding: 10px;
        margin: 10px;
    }

    #jqxDateTimeInput {
        margin-bottom: 20px;
    }

    th.header {
        padding-left: 10px;
        padding-bottom: 10px;
    }

    td.body {
        padding-left: 10px;
        padding-bottom: 10px;
    }

    #home_message {
        padding-top: 40px;
        padding-left: 40px;
        height: 800px;
        width: 800px;
    }
    /* This parent can be any width and height */
    .block {
        text-align: center;
    }

    /* The ghost, nudged to maintain perfect centering */
    .block:before {
        content: '';
        display: inline-block;
        height: 100%;
        vertical-align: middle;
        margin-right: -0.25em; /* Adjusts for spacing */
    }

    /* The element to be centered, can
       also be of any width and height */
    .centered {
        display: inline-block;
        vertical-align: middle;
        width: 300px;
    }
</style>
<script type="text/javascript">
//<![CDATA[
/* Javascript has shortcomings in date formatting */
$(document).ready(function () {

    months = new Array("January", "February", "March",
        "April", "May", "June", "July", "August", "September",
        "October", "November", "December");

    days = new Array("Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday");

    $.date = function (dateObject) {
        var d = new Date(dateObject);
        var day = d.getDate();
        var month = d.getMonth() + 1;
        var year = d.getFullYear();

        if (day < 10) {
            day = "0" + day;
        }

        if (month < 10) {
            month = "0" + month;
        }

        var date = year + "-" + month + "-" + day;

        return date;
    };

    $.timestamp = function (dateObject) {
        var d = new Date(dateObject);
        var day = d.getDate();
        var month = d.getMonth() + 1;
        var year = d.getFullYear();

        if (day < 10) {
            day = "0" + day;
        }

        if (month < 10) {
            month = "0" + month;
        }

        var date = year + month + day;

        return date;
    };

    function set_dates(start_date, end_date) {

        $('#start_date').jqxCalendar('setDate', start_date);
        $('#end_date').jqxCalendar('setDate', end_date);
    }

    $("#start_date").jqxCalendar({
        width: 220,
        height: 220
    });

    $("#end_date").jqxCalendar({
        width: 220,
        height: 220
    });

    $("input[name='predefined_dates']").change(function (e) {

        today = new Date();

        switch ($(this).val()) {

            case "today":
                set_dates(today, today);
                break;

            case "yesterday":
                yesterday = new Date();
                yesterday.setDate(today.getDate() - 1);
                set_dates(yesterday, yesterday);
                break;

            case "mtd":
                first_day_of_month = new Date(today.getFullYear(), today.getMonth(), 1);
                set_dates(first_day_of_month, today);
                break;

            case "last_month":
                first_day_of_last_month = new Date(today.getFullYear(), today.getMonth() - 1, 1);
                last_date_of_last_month = new Date(today.getFullYear(), today.getMonth(), 0);
                set_dates(first_day_of_last_month, last_date_of_last_month);
                break;

            case "ytd":
                first_of_year = new Date(today.getFullYear(), 0, 1);
                set_dates(first_of_year, new Date());
                break;

            case "current_week":
                var first_day_of_week = new Date(today.getTime() - (today.getDay() * 86400000)); // First day is the day of the month - the day of the week
                var last_day_of_week = new Date(first_day_of_week.getTime() + (6 * 86400000)); // last day is the first day + 6
                console.log("first_day:" + first_day_of_week);
                console.log("last:" + last_day_of_week);
                set_dates(first_day_of_week, last_day_of_week);
                break;

            case "last_week":
                last_week = new Date(today.getTime() - 604800000);
                var first_day_of_last_week = new Date(last_week.getTime() - (last_week.getDay() * 86400000)); // First day is the day of the month - the day of the week
                var last_day_of_last_week = new Date(first_day_of_last_week.getTime() + (6 * 86400000)); // last day is the first day + 6
                set_dates(first_day_of_last_week, last_day_of_last_week);
                break;

            case "last_7_days":
                first_day = new Date();
                first_day.setDate(today.getDate() - 7);
                set_dates(first_day, today);
                break;

            case "last_30_days":
                first_day = new Date();
                first_day.setDate(today.getDate() - 30);
                set_dates(first_day, today);
                break;

            case "custom":
                break;
        }
    });

    $("#query_type_id").on("change", function () {

        $("#submit_query").attr("disabled", "disabled");
        $("#query_id").attr("disabled", "disabled");

        $.getJSON("/jqx_grid_engine/reports_by_type/" + $("#query_type_id").val(), function (response) {

            $('#query_id').find('option').remove().end();

            $.each(response, function (index, value) {

                option = document.createElement("option");
                option.value = index;
                option.text = value.name;

                $("#query_id").append(option);
            });

            options = $("#query_id option");
            options.sort(function (a, b) {
                if (a.text > b.text) return 1;
                else if (a.text < b.text) return -1;
                else return 0
            })

            $("#query_id").empty().append(options);
            $("#submit_query").removeAttr("disabled");
            $("#query_id").removeAttr("disabled");
        });
    });

    $("#query_type_id").trigger("change");
    $("#predefined_dates_yesterday").trigger("click");

    $('#jqxTabs').jqxTabs({ theme: theme, width: '100%', height: '100%', showCloseButtons: true, reorder: true, scrollPosition: 'both'});

    $("#submit_query").on("click", function (event) {

        //using the ecpm controller on purpose since there's a lot of formatting already in there.
        var url = "/jqx_grid_engine/" + $("#query_id").val() + "/?start_date=" + $.date($("#start_date").val()) + "&end_date=" + $.date($("#end_date").val())
        new_resultset_grid_div = "query_result_" + $("#query_id").val() + $.timestamp($("#start_date").val()) + "_" + $.timestamp($("#end_date").val()) + random_string();
        new_tab_name = $("#query_id option:selected").text() + " " + $.date($("#start_date").val()) + " - " + $.date($("#end_date").val());

        $('#jqxTabs').jqxTabs('addLast', new_tab_name, '<div id="' + new_resultset_grid_div + '"><div class="block" style="height: 600px;"><div class="centered"><img src=/images/ajax-loader.gif></div></div></div>');
        $('#jqxTabs').jqxTabs('ensureVisible', 1);

        $.getJSON(url, function (data) {

            var source = {
                datatype: "array",
                localdata: data["results"],
                sortcolumn: 'ecpm',
                sortdirection: 'desc'

            };

            var dataAdapter = new $.jqx.dataAdapter(source);


            $("#" + new_resultset_grid_div).jqxGrid({
                source: dataAdapter,
                columns: data["columns"],
                theme: theme,
                autorowheight: true,
                autoheight: true,
                autowidth: true,
                width: '100%',
                sortable: true,
                altrows: true,
                showfilterrow: true,
                filterable: true,
                showstatusbar: true,
                statusbarheight: 35,
                showaggregates: true,
                groupable: true,
//                autoloadstate: true,
//                autosavestate: true,
                columnsresize: true,
                columnsreorder: true
            });
            $("#" + new_resultset_grid_div).jqxGrid('autoresizecolumns')
        });
    });


    $("#export_query").on("click", function () {
        var url = "/jqx_grid_engine/" + $("#query_id").val() + "/?command=Export&query_type_id=" + $("#query_type_id").val() + "&start_date=" + $.date($("#start_date").val()) + "&end_date=" + $.date($("#end_date").val())
        window.location = url;
    });

    function random_string() {
        var chars = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXTZabcdefghiklmnopqrstuvwxyz'.split('');

        var string = '';
        for (var i = 0; i < 40; i++) {
            string += chars[Math.floor(Math.random() * chars.length)];
        }
        return string;
    }
});
//]]>
</script>

<div id="query_selector">
    <table>
        <tr>
            <td class="header">Start Date</td>
            <td class="header">End Date</td>
            <td class="header"></td>
            <td class="header">Query</td>
        </tr>
        <tr>

            <td class="body">
                <div id="start_date"></div>
            </td>
            <td class="body">
                <div id="end_date"></div>
            </td>
            <td class="body" rowspan="2">
                <div id="predefined_date_selector">
                    <form action="#" method="POST">
                        <input type="radio" name="predefined_dates" value="today"/> Today<br/>
                        <input type="radio" name="predefined_dates" id="predefined_dates_yesterday" value="yesterday"/> Yesterday<br/>
                        <input type="radio" name="predefined_dates" value="mtd"/> MTD<br/>
                        <input type="radio" name="predefined_dates" value="last_month"/> Last Month<br/>
                        <input type="radio" name="predefined_dates" value="ytd"/> YTD<br/>
                        <input type="radio" name="predefined_dates" value="current_week"/> Current Week<br/>
                        <input type="radio" name="predefined_dates" value="last_week"/> Last Week<br/>
                        <input type="radio" name="predefined_dates" id="predefined_dates_last_30_days" value="last_30_days"/> Last 30 Days<br/>
                        <input type="radio" name="predefined_dates" value="last_7_days"/> Last 7 Days<br/>
                        <input type="radio" name="predefined_dates" value="custom"/> Custom<br/>
                    </form>
                </div>
            </td>
            <td class="body">
                <div class="fieldset">
                    <div class="select">
                        <select id="query_type_id" name="query_type_id">
                            <?php
                            foreach ($query_types as $query_type_id => $query_type) {
                                printf("<option value='%s'>%s</option>", $query_type_id, $query_type);
                            }
                            ?>
                        </select>
                    </div>
                    <div class="select">
                        <select name="query_id" id="query_id">
                            <?php

                            foreach ($queries as $option_id => $option) {

                                $option_value = $option['name'];
                                $use_date_selector = $option['use_date_selector'];

                                if ($option_id == $query_id) {
                                    print "<option use_date_selector={$use_date_selector} selected value='{$option_id}'>{$option_value}</option>";
                                } else {
                                    print "<option use_date_selector={$use_date_selector} value='{$option_id}'>{$option_value}</option>";
                                }
                            }
                            ?>
                        </select>
                    </div>

                    <div class="submit">
                        <input type="button" name="submit_query" id="submit_query" value="Query"/>
                    </div>
                    <div class="submit">
                        <input type="button" name="export_query" id="export_query" value="Export"/>
                    </div>
                </div>
            </td>
        </tr>
    </table>
</div>

<div id="query_results">

</div>


<div id="jqxTabs">
    <ul>
        <li>Home</li>
    </ul>
    <div>
        <div id="home_message">Start by selecting a date and report type. Your result set show show up here in a new tab.</div>
    </div>
</div>
