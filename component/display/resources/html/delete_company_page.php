<!DOCTYPE html>
<html >
  <head>
    <meta charset="UTF-8">
    <title>Sl[i]stem by Slate</title>


<style>

  .alert {
    text-shadow: 0 1px 0 rgba(255, 255, 255, .2);
    -webkit-box-shadow: inset 0 1px 0 rgba(255, 255, 255, .25), 0 1px 2px rgba(0, 0, 0, .05);
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, .25), 0 1px 2px rgba(0, 0, 0, .05);
  }

  .alert-danger {
    background-image: -webkit-linear-gradient(top, #f2dede 0%, #e7c3c3 100%);
    background-image:      -o-linear-gradient(top, #f2dede 0%, #e7c3c3 100%);
    background-image: -webkit-gradient(linear, left top, left bottom, from(#f2dede), to(#e7c3c3));
    background-image:         linear-gradient(to bottom, #f2dede 0%, #e7c3c3 100%);
    filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#fff2dede', endColorstr='#ffe7c3c3', GradientType=0);
    background-repeat: repeat-x;
    border-color: #dca7a7;
  }


</style>

<script type="text/javascript">


</script>

  </head>


  <body>

  <script language="javascript">

  </script>

  <?php if(isset($header)){ echo $header; } ?>
  <table style='width:100%; margin-left: -20px;' >
    <tr>
      <td style='margin-top: 10px; ' valign="top">
        <div style=" font-size:15px; width: 500px; color:#585858; font-weight: bold; " class="alert alert-danger" role="alert">
          <p style='margin-left: 20px; padding-top: 10px; padding-bottom: 10px;'>
            Warning you are about to delete company #123456!
          </p>
        </div>
      </td>
    </tr>
  </table>
  <table>
    <tr>
      <td style='font-weight: bold; margin-top: 10px;' >
        Confirm deleting company #123456
      </td>
    </tr>
    <tr>
      <td style='font-weight: bold; margin-top: 10px;'>
        Move employees to:[company id]
      </td>
      <td>
        <input type="text" name="company_id"  id="company_id">
      </td>
    </tr>
  </table>


  </body>

</html>