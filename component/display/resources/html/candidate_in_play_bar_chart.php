<!DOCTYPE HTML>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">

        <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script>
        <style type="text/css">

        </style>
        <script type="text/javascript">
$(function () {

    var title = "<?php echo $title; ?>";

    var users = "<?php echo $inplay_formatted; ?>";
    users = users.split(';');

    var inplay_count = "<?php echo $inplay_count; ?>";
    inplay_count = inplay_count.split(';');

    for(var i=0; i<inplay_count.length; i++) { inplay_count[i] = parseInt(inplay_count[i], 10); }

    var inplay_rsc = "<?php echo $inplay_rsc; ?>";
    inplay_rsc = inplay_rsc.split(';');

    for(var i=0; i<inplay_rsc.length; i++) { inplay_rsc[i] = parseInt(inplay_rsc[i], 10); }


    $('#container').highcharts({
        chart: {
            type: 'bar'
        },
        title: {
            text: title
        },
        xAxis: {
            categories: users
        },
        yAxis: {
            tickInterval:1,
            min: 0,
            title: {
                text:'',
                style: {
                    color: 'red'
                }
            }
        },
        legend: {
            reversed: true
        },
        plotOptions: {
            series: {
                stacking: 'normal'
            }
        },
        series: [ {
            name: 'Resume sent',
            style: {
                        fontSize: '20px',
                        fontWeight: 'bold'
                    },
            color: 'rgba(179, 0, 0,0.5)',//red
            opacity: '.4',
            data: inplay_rsc
        }, {
            name: 'Candidate in play',
            color: 'rgba(28, 139, 176,0.5)',//blue
            opacity: '.4',
            data: inplay_count
        }]

    });
});
        </script>
    </head>
    <body>


<script src="/common/lib/highcharts5/js/highcharts.js" ></script>
<script src="/common/lib/highcharts5/js/modules/exporting.js" ></script>

<div id="container" style="width:100%; height: 100%; margin: 0 auto"></div>

    </body>
</html>