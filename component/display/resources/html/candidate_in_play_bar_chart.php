<!DOCTYPE HTML>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">

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
            text: 'Resume sent / CCM1 / MCCM (between xxx - xxx)'
        },
        xAxis: {
            categories: ['R.Pedersen', 'P.Thai', 'M.Moir', 'Y.Takagi', 'G.Young']
        },
        yAxis: {
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
            name: 'Resumes sent',
            style: {
                        fontSize: '20px',
                        fontWeight: 'bold'
                    },
            color: 'rgba(179, 0, 0,0.5)',//red
            opacity: '.4',
            data: [7, 5, 3, 2, 1]
        }, {
            name: 'Candidates in play',
            color: 'rgba(28, 139, 176,0.5)',//blue
            opacity: '.4',
            data: [19, 13, 10, 4, 2]
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