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

    var user_130 = "<?php echo $new_candidate_met[130]['formatted']; ?>";
    var user_276 = "<?php echo $new_candidate_met[276]['formatted']; ?>";
    var user_314 = "<?php echo $new_candidate_met[314]['formatted']; ?>";
    var user_343 = "<?php echo $new_candidate_met[343]['formatted']; ?>";
    var user_347 = "<?php echo $new_candidate_met[347]['formatted']; ?>";
    var user_345 = "<?php echo $new_candidate_met[345]['formatted']; ?>";
    var user_374 = "<?php echo $new_candidate_met[374]['formatted']; ?>";
    var user_388 = "<?php echo $new_candidate_met[388]['formatted']; ?>";
    var user_431 = "<?php echo $new_candidate_met[431]['formatted']; ?>";
    var user_433 = "<?php echo $new_candidate_met[433]['formatted']; ?>";
    var user_459 = "<?php echo $new_candidate_met[459]['formatted']; ?>";
    var user_466 = "<?php echo $new_candidate_met[466]['formatted']; ?>";
    var user_481 = "<?php echo $new_candidate_met[481]['formatted']; ?>";
    var user_493 = "<?php echo $new_candidate_met[493]['formatted']; ?>";

    alert(php_var);


    $('#container').highcharts({
        chart: {
            type: 'bar'
        },
        title: {
            text: 'Candidates Met 01.05.2016 to Present'
        },
        xAxis: {
            categories: [user_130,user_276,user_314,user_343,user_347,user_345,user_374,user_388,user_431,user_433,user_459,user_466,user_481,user_493 ]
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
            data: [19, 13, 10, 4, 2,0,0,0,0,0,0,0,0,0]
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