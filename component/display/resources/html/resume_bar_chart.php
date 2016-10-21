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
                text: 'Total fruit consumption'
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
                        align: 'middle'
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
            color: 'green',
            data: [5, 3, 2, 1, 0]
        }, {
            name: 'CCM1',
            color: 'red',
            data: [7, 5, 3, 2, 1]
        }, {
            name: 'Resume sent',
            color: 'blue',
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