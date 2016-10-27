<!DOCTYPE HTML>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">

		<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script>
		<style type="text/css">
            .highcharts-xaxis-labels
            {
                font-weight: bold !important;
            }
            .highcharts-title
            {
                padding-bottom: 30px;
                padding-top: -20px;
                font-weight: bold !important;
            }
            .highcharts-legend-item text
            {
                font-size: 20px !important;
                margin-top: 2cm !important;
            }
            .highcharts-legend-item rect
            {
                font-size: 20px !important;
                margin-top: 2cm !important;
            }
            .menunav1pos_top
            {
                margin-top: -40px !important;
            }
		</style>
		<script type="text/javascript">
$(function () {

    var title = "<?php echo $title; ?>";

    var users = "<?php echo $rs_ccm1_mccm_formatted; ?>";
    users = users.split(';');
//-----------------------------------------------------------------------------------------//
    var rs_count = "<?php echo $rs_ccm1_mccm_rsc; ?>";
    rs_count = rs_count.split(';');

    for(var i=0; i<rs_count.length; i++) { rs_count[i] = parseInt(rs_count[i], 10); }
//-----------------------------------------------------------------------------------------//
    var ccm1_count = "<?php echo $rs_ccm1_mccm_ccm1; ?>";
    ccm1_count = ccm1_count.split(';');

    for(var i=0; i<ccm1_count.length; i++) { ccm1_count[i] = parseInt(ccm1_count[i], 10); }
//-----------------------------------------------------------------------------------------//
    var ccm2_count = "<?php echo $rs_ccm1_mccm_ccm2; ?>";
    ccm2_count = ccm2_count.split(';');

    for(var i=0; i<ccm2_count.length; i++) { ccm2_count[i] = parseInt(ccm2_count[i], 10); }
//-----------------------------------------------------------------------------------------//
    var mccm_count = "<?php echo $rs_ccm1_mccm_mccm; ?>";
    mccm_count = mccm_count.split(';');

    for(var i=0; i<mccm_count.length; i++) { mccm_count[i] = parseInt(mccm_count[i], 10); }
//-----------------------------------------------------------------------------------------//

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
        yAxis: [{

            tickInterval:1,
            title: {
                text:'',
                style: {
                    color: 'black'
                }
            }
        }, {
            tickInterval:1,
            linkedTo: 0,
            opposite: true,
            title: {
                text:'',
                style: {
                    color: 'black'
                }
            }
        }],
        legend: {
            //reversed: true,
            verticalAlign: 'top',
            itemMarginTop: 50,
            //itemMarginBottom: 50
        },
        plotOptions: {
            /*series: {
                stacking: 'normal'
            }*/
            series: {
                //groupPadding: 0.5,  // Exactly overlap
                //pointWidth: 20
            }
        },
        series: [
        {
            name: 'Resume sent',
            color: 'rgba(32, 115, 204,1)',//blue
            opacity: '.4',
            data: rs_count
        },
        {
            name: 'CCM1',
            style: {
                        fontSize: '20px',
                        fontWeight: 'bold'
                    },
            color: 'rgba(137, 40, 40,1)',//red
            opacity: '.4',
            data: ccm1_count
        },
        {
            name: 'CCM1',
            style: {
                        fontSize: '20px',
                        fontWeight: 'bold'
                    },
            color: 'rgba(237, 218, 116 ,1)',//yellow
            opacity: '.4',
            data: ccm2_count
        },
        {
            name: 'MCCM',
            style: {
                        fontSize: '20px',
                        fontWeight: 'bold'
                    },
            color: 'rgba(45, 185, 68,1)',//green
            opacity: '.4',
            data: mccm_count
        }  ]

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