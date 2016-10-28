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
        margin-top: -30px !important;
    }
</style>
<script type="text/javascript">
$(function () {

    var title = "<?php echo $title; ?>";
    var met_target = "<?php echo $met_target; ?>";
//-----------------------------------------------------------------------------------------//
    var users = "<?php echo $new_candidate_met_json; ?>";
    users = users.split(';');
//-----------------------------------------------------------------------------------------//
    var count = "<?php echo $new_candidate_count; ?>";
    count = count.split(';');

    for(var i=0; i<count.length; i++) { count[i] = parseInt(count[i], 10); }
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
            //categories: ['R.Pedersen |19|', 'P.Thai |13|', 'M.Moir |10|', 'Y.Takagi |4|', 'G.Young |2|',]
        },
        yAxis: [{
            plotLines:[{
                value:27,
                color: 'black',
                width:1,
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
            },
            {
                value:27,
                color: 'black',
                width:1,
                zIndex:4,
                label:{
                        useHTML: true,
                        text:'<img src="/component/sl_candidate/resources/pictures/tabs/doubleflag.png"/>',
                        verticalAlign: 'top',
                        textAlign: 'center',
                        style: {
                            //fontSize: '20px',
                            //fontWeight: 'bold'
                        }
                      }
            },
            {
                value:met_target,
                color: 'grey',
                dashStyle: 'shortdash',
                width:1,
                zIndex:4,
                label:{
                        useHTML: true,
                        text:'<img src="/component/sl_candidate/resources/pictures/tabs/rabbit_40.png"/>',
                        verticalAlign: 'top',
                        textAlign: 'center',
                        style: {
                            fontSize: '20px',
                            color: 'grey',
                        }
                      }
            },
            {
                value:met_target,
                color: 'grey',
                dashStyle: 'shortdash',
                width:1,
                zIndex:4,
                label:{
                        text:'DAILY TARGET',
                        verticalAlign: 'middle',
                        textAlign: 'center',
                        style: {
                            fontSize: '20px',
                            color: 'grey',
                        }
                      }
            }],
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
            reversed: true,
            verticalAlign: 'top',
            itemMarginTop: 50,
            //itemMarginBottom: 50
        },
        plotOptions: {
            series: {
                stacking: 'normal'
            }
        },
        series: [ {
                name: ' ',
                        type: 'scatter',
                        marker: {
                    enabled: false
                },
                data: [27]
            },{
            name: 'Candidate met',
            color: 'rgba(0, 32, 194,0.8)',//blue
            opacity: '.4',
            data: count
        }/*,
        {
                name: ' ',
                        type: 'scatter',
                        marker: {
                    enabled: false
                },
                data: [27]
            }*/]

    });
});
		</script>
	</head>
	<body>


<script src="/common/lib/highcharts5/js/highcharts.js" ></script>
<script src="/common/lib/highcharts5/js/modules/exporting.js" ></script>
<script>

    setTimeout(function() {
        var nextloop = '<?php echo $nextloop; ?>';
        var url = '/index.php5?uid=555-006&ppa=pprev&ppt=revenue&ppk=0&watercooler=1&nextloop='+nextloop;
        //alert('test');
        window.location.replace(url);
    }, 30000);


</script>
<div id="container" style="width:99%; height: 99%; margin: 0 auto"></div>


	</body>
</html>