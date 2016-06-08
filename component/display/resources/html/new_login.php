<!DOCTYPE html>
<html >
  <head>
    <meta charset="UTF-8">
    <title>Sl[i]stem by Slate</title>

  <link rel="stylesheet" href="/common/style/login_style.css">
  <!--<link rel="stylesheet" type="text/css" href="common/lib/verticalSlider/css/style.css">-->
  <link href='https://fonts.googleapis.com/css?family=Merriweather' rel='stylesheet' type='text/css'>

  <link rel="stylesheet" type="text/css" href="common/lib/bootstrap/css/bootstrap.css">
  <!--<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.1/css/bootstrap.min.css">-->
  <!-- jQuery library -->

  <!-- Latest compiled JavaScript -->
  <!--<script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>-->
  <script src="common/lib/bootstrap/js/bootstrap.min.js"></script>
  <!--<script type="text/javascript" src="/common/lib/verticalSlider/js/jquery.totemticker.js"></script>-->


<script type="text/javascript">

var url = document.URL;
var search = "/?";

if(url.indexOf(search)>-1)
{
  url = url.substring(0, url.length - 1);
  window.location.href = url;
  //alert();
}


//location.reload(); // sayfayi tekrar yukler

//document.getElementByClassName("userBloc").style.visibility='hidden';

function closeExtra()
{
  var divsToHide = document.getElementsByClassName("closeAll");

  for(var i = 0; i < divsToHide.length; i++)
    {
    divsToHide[i].style.display="none";
    }
}

function openExtra(open)
{

    var divsToHide = document.getElementsByClassName("closeAll");

    var td_ = open+"_";

    for(var i = 0; i < divsToHide.length; i++)
    {
    divsToHide[i].style.display="none";
    }
    document.getElementById(open).style.display = "table-row";
}

</script>
<style>
      .test {
        font-family: 'Merriweather', serif;

      }
      .borderlist {
        list-style-position:inside;
        border: 1px solid black;
        -moz-border-radius: 4px;
        -webkit-border-radius: 4px;
        border-radius: 4px;
        margin-bottom: 10px;
      }

      .jobs {
          border-collapse: collapse;
          width: 80%;
          margin-top: 20px;
      }

      td {
          padding: 8px;
          font-size: 12pt;
          border-bottom: solid 1px #892828;
      }

      tr:nth-child(even){background-color: #f2f2f2}

      /*th {
          background-color: #4CAF50;
          color: white;
      }*/

</style>

  </head>


  <body>

  <table style="width: 100%; margin-left: -10px;">
    <tr>
      <td valign="middle" align="middle" class="half" style=" width: 50%;">
        <div class="login-form">
        <form name="loginFormData" enctype="multipart/form-data" submitajax="1" action=<?php echo "'".$login."'"; ?> method="POST" id="loginFormDataId" onbeforesubmit onsubmit>
          <div style="width: 300px;"><center><img style="text-align: center; width: 300px; margin-bottom: 20px;" src="/common/pictures/slate_logo.png" /></center></div>
         <div style="width: 300px;" class="form-group ">
           <input type="text" name="login" class="form-control" placeholder="Username " id="UserName">
           <i class="fa fa-user"></i>
         </div>
         <div style="width: 300px;" class="form-group log-status">
           <input type="password" name="password" class="form-control" placeholder="Password" id="Passwod">
           <i class="fa fa-lock"></i>
         </div>
         <div style="width: 300px;">
            <span class="alert">Invalid Credentials</span>
            <a class="link" href=<?php echo "'".$lost."'"; ?> >Lost your password?</a>
            <button type="submit" class="log-btn" >Log in</button>
         </div>
        </form>

       </div>
      </td>

      <td  style="width: 50%;">
        <div class="login-form2">
          <center><p class="test" style="color: #892828; font-size: 48px;">LATEST JOBS</p></center>

          <table class="jobs" align="center">
          <?php for($i=0 ; $i<5 ; $i++) { ?>
            <tr>
              <td>
                <p style="font-size: 12pt;" class="test"><b>Company: </b><?php echo $firstFive[$i]['name']; ?></p>
                <p style="font-size: 12pt;" class="test"><b>Title: </b><?php echo $firstFive[$i]['title']; ?> (#<?php echo $firstFive[$i]['sl_positionpk']; ?>)</p>
                <p style="font-size: 12pt;" class="test"><b>Salary range: </b>&yen;<?php echo $firstFive[$i]['salary_from']; ?> - &yen;<?php echo $firstFive[$i]['salary_to']; ?></p>
                <p style="font-size: 12pt;" class="test"><b>Consultant: </b><?php echo $firstFive[$i]['firstname']; ?> <?php echo $firstFive[$i]['lastname']; ?></p>
                <div style="margin-top: 5px;">
                  <p style="font-size: 11pt; " class="test"><b>More: </b><img style="cursor:pointer; width: 20px; vertical-align: text-bottom;" src="common/pictures/plus.png" onclick="openExtra(<?php echo "'firstFive_".$i."'" ?>)"></p>
                </div>
              </td>
              <td>
                <p style="font-size: 12pt;" class="test"><b>Company: </b><?php echo $lastFive[$i]['name']; ?></p>
                <p style="font-size: 12pt;" class="test"><b>Title: </b><?php echo $lastFive[$i]['title']; ?> (#<?php echo $lastFive[$i]['sl_positionpk']; ?>)</p>
                <p style="font-size: 12pt;" class="test"><b>Salary range: </b>&yen;<?php echo $lastFive[$i]['salary_from']; ?> - &yen;<?php echo $lastFive[$i]['salary_to']; ?></p>
                <p style="font-size: 12pt;" class="test"><b>Consultant: </b><?php echo $lastFive[$i]['firstname']; ?> <?php echo $lastFive[$i]['lastname']; ?></p>
                <div style="margin-top: 5px;">
                  <p style="font-size: 11pt; " class="test"><b>More: </b><img style="cursor:pointer; width: 20px; vertical-align: text-bottom;" src="common/pictures/plus.png" onclick="openExtra(<?php echo "'lastFive_".$i."'" ?>)"></p>
                </div>
              </td>
            </tr>
            <tr class="closeAll" style="display: none;"  id=<?php echo "'firstFive_".$i."'" ?> >
              <th class="extra " id=<?php echo "'firstFive_".$i."_'" ?>  style=" background-color: rgba(138, 40, 40, 0.15);" colspan="2">
                <p style="font-size: 12pt;" class="test"><b>Company: </b><?php echo $firstFive[$i]['name']; ?></p>
                <p style="font-size: 12pt;" class="test"><b>Title: </b><?php echo $firstFive[$i]['long_title']; ?> (#<?php echo $firstFive[$i]['sl_positionpk']; ?>)</p>
                <p style="font-size: 12pt;" class="test"><b>Salary range: </b>&yen;<?php echo $firstFive[$i]['salary_from']; ?> - &yen;<?php echo $firstFive[$i]['salary_to']; ?></p>
                <p style="font-size: 12pt;" class="test"><b>Consultant: </b><?php echo $firstFive[$i]['firstname']; ?> <?php echo $firstFive[$i]['lastname']; ?></p>
                <p style="font-size: 12pt;" class="test"><b>Description: </b><?php echo $firstFive[$i]['description']; ?></p>
                <p style="font-size: 12pt;" class="test"><b>Requirements: </b><?php echo $firstFive[$i]['requirements']; ?></p>
                <div style="margin-top: 5px;">
                  <p style="font-size: 11pt; " class="test"><b>Less: </b><img style="cursor:pointer; width: 20px; vertical-align: text-bottom;" src="common/pictures/minus.png" onclick="closeExtra()"></p>
                </div>
              </th>
            </tr>
            <tr class="closeAll" style="display: none;" id=<?php echo "'lastFive_".$i."'" ?> >
              <th class="extra " id=<?php echo "'lastFive_".$i."_'" ?>  style=" background-color: rgba(138, 40, 40, 0.15);" colspan="2">
                <p style="font-size: 12pt;" class="test"><b>Company: </b><?php echo $lastFive[$i]['name']; ?></p>
                <p style="font-size: 12pt;" class="test"><b>Title: </b><?php echo $lastFive[$i]['long_title']; ?> (#<?php echo $lastFive[$i]['sl_positionpk']; ?>)</p>
                <p style="font-size: 12pt;" class="test"><b>Salary range: </b>&yen;<?php echo $lastFive[$i]['salary_from']; ?> - &yen;<?php echo $lastFive[$i]['salary_to']; ?></p>
                <p style="font-size: 12pt;" class="test"><b>Consultant: </b><?php echo $lastFive[$i]['firstname']; ?> <?php echo $lastFive[$i]['lastname']; ?></p>
                <p style="font-size: 12pt;" class="test"><b>Description: </b><?php echo $lastFive[$i]['description']; ?></p>
                <p style="font-size: 12pt;" class="test"><b>Requirements: </b><?php echo $lastFive[$i]['requirements']; ?></p>
                <div style="margin-top: 5px;">
                  <p style="font-size: 11pt; " class="test"><b>Less: </b><img style="cursor:pointer; width: 20px; vertical-align: text-bottom;" src="common/pictures/minus.png" onclick="closeExtra()"></p>
                </div>
              </th>
            </tr>

            <?php } ?>
          </table>

        </div>
      </td>
    </tr>
  </table>


  </body>

</html>