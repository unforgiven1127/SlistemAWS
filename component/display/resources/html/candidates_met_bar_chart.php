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

    //var new_candidate_met_json = "<?php $new_candidate_met_json ?>";

    //alert(new_candidate_met_json);

    var user_130 = "<?php echo $new_candidate_met[130]['formatted']; ?>";
    var user_276 = "<?php echo $new_candidate_met[276]['formatted']; ?>";
    var user_314 = "<?php echo $new_candidate_met[314]['formatted']; ?>";
    var user_343 = "<?php echo $new_candidate_met[343]['formatted']; ?>";
    var user_347 = "<?php echo $new_candidate_met[347]['formatted']; ?>";
    var user_354 = "<?php echo $new_candidate_met[354]['formatted']; ?>";
    var user_374 = "<?php echo $new_candidate_met[374]['formatted']; ?>";
    var user_388 = "<?php echo $new_candidate_met[388]['formatted']; ?>";
    var user_431 = "<?php echo $new_candidate_met[431]['formatted']; ?>";
    var user_443 = "<?php echo $new_candidate_met[443]['formatted']; ?>";
    var user_459 = "<?php echo $new_candidate_met[459]['formatted']; ?>";
    var user_466 = "<?php echo $new_candidate_met[466]['formatted']; ?>";
    var user_481 = "<?php echo $new_candidate_met[481]['formatted']; ?>";
    var user_493 = "<?php echo $new_candidate_met[493]['formatted']; ?>";


    var asd = " ['R.Pedersen |19|', 'P.Thai |13|', 'M.Moir |10|', 'Y.Takagi |4|', 'G.Young |2|',]";

    var user_130_count = parseInt("<?php echo $new_candidate_met[130]['count']; ?>");
    var user_276_count = parseInt("<?php echo $new_candidate_met[276]['count']; ?>");
    var user_314_count = parseInt("<?php echo $new_candidate_met[314]['count']; ?>");
    var user_343_count = parseInt("<?php echo $new_candidate_met[343]['count']; ?>");
    var user_347_count = parseInt("<?php echo $new_candidate_met[347]['count']; ?>");
    var user_354_count = parseInt("<?php echo $new_candidate_met[354]['count']; ?>");
    var user_374_count = parseInt("<?php echo $new_candidate_met[374]['count']; ?>");
    var user_388_count = parseInt("<?php echo $new_candidate_met[388]['count']; ?>");
    var user_431_count = parseInt("<?php echo $new_candidate_met[431]['count']; ?>");
    var user_443_count = parseInt("<?php echo $new_candidate_met[443]['count']; ?>");
    var user_459_count = parseInt("<?php echo $new_candidate_met[459]['count']; ?>");
    var user_466_count = parseInt("<?php echo $new_candidate_met[466]['count']; ?>");
    var user_481_count = parseInt("<?php echo $new_candidate_met[481]['count']; ?>");
    var user_493_count = parseInt("<?php echo $new_candidate_met[493]['count']; ?>");


     var distroDates = [
        
                '06/2013',
                '12/2012',
                '06/2012',
                '12/2011',
                '06/2011',
                '12/2010',
                '06/2010',
                '12/2009',
                '06/2009',
                '12/2008',
                '06/2008',
                '12/2007',
                '12/2006',
                '12/2005',
                '12/2004',
                '12/2003',
                '12/2002',
            ]

    $('#container').highcharts({
        chart: {
            type: 'bar'
        },
        title: {
            text: 'Candidates Met 01.05.2016 to Present'
        },
        xAxis: {
            categories: distroDates
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
            data: [user_130_count, user_276_count, user_314_count, user_343_count, user_347_count,user_354_count,user_374_count,user_388_count,user_431_count,user_443_count,user_459_count,user_466_count,user_481_count,user_493_count]
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