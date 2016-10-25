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

    var users = "<?php echo $rs_ccm1_mccm_formatted; ?>";
    users = users.split(';');

    var rs_count = "<?php echo $rs_ccm1_mccm_rsc; ?>";
    rs_count = rs_count.split(';');

    for(var i=0; i<rs_count.length; i++) { rs_count[i] = parseInt(rs_count[i], 10); }

    var ccm1_count = "<?php echo $rs_ccm1_mccm_ccm1; ?>";
    ccm1_count = ccm1_count.split(';');

    for(var i=0; i<ccm1_count.length; i++) { ccm1_count[i] = parseInt(ccm1_count[i], 10); }

    var mccm_count = "<?php echo $rs_ccm1_mccm_mccm; ?>";
    mccm_count = mccm_count.split(';');

    for(var i=0; i<mccm_count.length; i++) { mccm_count[i] = parseInt(mccm_count[i], 10); }

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
        series: [{
            name: 'MCCM',
            style: {
                        fontSize: '20px',
                        fontWeight: 'bold'
                    },
            color: 'rgba(28, 176, 28,0.5)',//green
            opacity: '.4',
            data: mccm_count
        }, {
            name: 'CCM1',
            style: {
                        fontSize: '20px',
                        fontWeight: 'bold'
                    },
            color: 'rgba(179, 0, 0,0.5)',//red
            opacity: '.4',
            data: ccm1_count
        }, {
            name: 'Resume sent',
            color: 'rgba(28, 139, 176,0.5)',//blue
            opacity: '.4',
            data: rs_count
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