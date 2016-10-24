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
</style>
<script type="text/javascript">
$(function () {
    alert( <?php echo "\"Hello\""; ?> );

    <?php
        $data = array();
        foreach ($new_candidate_met as $key => $value)
        {
            $data[] = $value['consultant_name']."|".$value['count']."|";
        }

    ?>
    var test = <?php echo implode(',', $data); ?>;
    alert(test);

    $('#container').highcharts({
        chart: {
            type: 'bar'
        },
        title: {
            text: 'Candidates Met 01.05.2016 to Present'
        },
        xAxis: {
            categories: [  ]
            //categories: ['R.Pedersen |19|', 'P.Thai |13|', 'M.Moir |10|', 'Y.Takagi |4|', 'G.Young |2|',]
        },
        yAxis: {
            //tickInterval:2,
            min: 0,
            title: {
                text:'',
                style: {
                    color: 'red'
                }
            }
            ,
            plotLines:[{
                value:27,
                color: 'black',
                width:3,
                zIndex:4,
                label:{
                        text:'CUSTOM TARGET',
                        verticalAlign: 'middle',
                        textAlign: 'center',
                        style: {
                            fontSize: '20px',
                            fontWeight: 'bold'
                        }
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
        series: [ {
            name: 'Candidate met',
            color: 'rgba(28, 139, 176,0.5)',//blue
            opacity: '.4',
            data: [19, 13, 10, 4, 2,]
        },
        {
                name: 'Goal',
                        type: 'scatter',
                        marker: {
                    enabled: false
                },
                data: [27]
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