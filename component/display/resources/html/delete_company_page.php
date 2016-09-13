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

  .log-btn_ {
    /*background: #892828;*/
    background: #e6e6e6;
    color:black;
    display: block;
    margin: auto;
    width: 100px;
    font-size: 14px;
    height: 20px;
    /*color: #fff;*/
    text-decoration: none;
    border: 1px grey solid;
    -moz-border-radius: 4px;
    -webkit-border-radius: 4px;
    border-radius: 4px;
    float:right;
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
            Warning you are about to delete company #<?php if(isset($company_id)){echo $company_id;} ?> !
          </p>
        </div>
      </td>
    </tr>
  </table>
  <table>
    <tr>
      <td colspan="2" style='font-weight: bold; padding-top: 10px;' >
        Confirm deleting company <?php if(isset($company_name)){echo $company_name;} ?> (#<?php if(isset($company_id)){echo $company_id;} ?>)
      </td>
    </tr>
    <tr>
      <td style='font-weight: bold; padding-top: 10px;'>
        Move employees to: [company id]
      </td>
      <td style='padding-top: 11px; padding-left: 13px;'>
        <!-- <input type="text" name="company_id"  id="company_id"> -->

        <input id="company" type="text" name="company_id"  />

      </td>
    </tr>
  </table>
  <table style='width:100%;'>
    <tr>
      <td align="right" style='padding-top: 30px; padding-right: 55px;'>
        <button onclick="$('.ui-dialog').remove();" type="button" class="log-btn_" >No</button>
        <button onclick="
          var selctedCompany = document.getElementsByClassName('autocomp_lvl_undefined');
          var test = getElementsByTagName('p')[0].innerHTML;
          console.log(test);
          $('.ui-dialog').remove();
          var oConf = goPopup.getConfig();
          oConf.width = 400;
          oConf.height = 200;
          goPopup.setLayerFromAjax(oConf, <?php echo "'".$delete_url."'"; ?>+'&newId='+selctedCompany.html);"
        style='margin-right: 10px !important;' type="button" class="log-btn_" >Yes</button>
      </td>
    </tr>
  </table>

  </body>

</html>


<script>
  var company_token = '';

  <?php if (!empty($company_token)) { ?>
  company_token = <?php echo $company_token; ?>
  <?php } ?>

  $(function()
  {

    $('#company').tokenInput('<?php echo $company_token_url; ?>',
    {
      noResultsText: "no results found",
      tokenLimit: 1,
      prePopulate: company_token
    });


    check_dom_change();
  });


  function check_dom_change()
  {
    if ($('.token-input-list-mac .token-input-token-mac p').is(":visible"))
    {
      var element_height = $('.token-input-list-mac .token-input-token-mac').height();
      var element_text_length = $('.token-input-list-mac .token-input-token-mac p').text().length;

      if (element_text_length > 29 && element_height < 33)
        $('.token-input-list-mac .token-input-token-mac').css('height', '33');
    }

    setTimeout(check_dom_change, 1000);
  }
</script>