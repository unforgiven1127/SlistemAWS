<!DOCTYPE HTML>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<title>Highcharts Example</title>

		<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script>
		<style type="text/css">

		</style>
		<script type="text/javascript">
$(function () {
    $('#container').highcharts({
        chart: {
            type: 'bar'
        },
        title: {
            text: 'Resume sent / CCM1 / MCCM'
        },
        xAxis: {
            categories: ['R.Pedersen', 'P.Thai', 'M.Moir', 'Y.Takagi', 'G.Young']
        },
        yAxis: {
            min: 0,
            title: {
                text: 'Counts'
            }
        },
        yAxis: {
            plotLines:[{
                value:5,
                color: 'black',
                width:2,
                zIndex:4,
                label:{
                        text:'CUSTOM TARGET',
                        align: 'bottom'
                      }
            }]
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
            color: 'rgba(28, 176, 28,0.5)',//green
            opacity: '.4',
            data: [5, 3, 2, 1, 0]
        }, {
            name: 'CCM1',
            color: 'rgba(179, 0, 0,0.5)',//red
            opacity: '.4',
            data: [7, 5, 3, 2, 1]
        }, {
            name: 'Resume sent',
            color: 'rgba(64, 0, 255,0.5)',//blue
            opacity: '.4',
            data: [19, 13, 10, 4, 2]
        }]

    });
});
		</script>
	</head>
	<body>
<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/modules/exporting.js"></script>

<div id="container" style="width:100%; height: 100%; margin: 0 auto"></div>

	</body>
</html>