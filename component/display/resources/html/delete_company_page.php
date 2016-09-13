<!DOCTYPE html>
<html >
  <head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="/component/form/resources/css/token-input-mac.css" type="text/css">
    <link rel="stylesheet" href="/component/form/resources/css/form.css" type="text/css">
    <link rel="stylesheet" href="/component/form/resources/css/jquery.bsmselect.css" type="text/css">
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
      
        <!-- <input type="text" name="company_id"  id="company_id"> -->
        <div class="gray_section">
        <div class="general_form_row extended_input">
          <div class="general_form_label">Company</div>
          <div class="general_form_column" style="width: 183px;">
            <input id="company" type="text" name="companypk"  />
          </div>
        </div>
      </div>
      
    </tr>
  </table>
  <table style='width:100%;'>
    <tr>
      <td align="right" style='padding-top: 30px; padding-right: 55px;'>
        <button onclick="$('.ui-dialog').remove();" type="button" class="log-btn_" >No</button>
        <button onclick="
          var selctedCompany = document.getElementById('company_id');
          $('.ui-dialog').remove();
          var oConf = goPopup.getConfig();
          oConf.width = 400;
          oConf.height = 200;
          goPopup.setLayerFromAjax(oConf, <?php echo "'".$delete_url."'"; ?>+'&newId='+selctedCompany.value);"
        style='margin-right: 10px !important;' type="button" class="log-btn_" >Yes</button>
      </td>
    </tr>
  </table>


  </body>

  <script>
  var company_token = '';
  var alt_occupation_token = '';
  var alt_industry_token = '';

  <?php if (!empty($company_token)) { ?>
  company_token = <?php echo $company_token; ?>
  <?php } ?>

  <?php if (!empty($alt_occupation_token)) { ?>
  alt_occupation_token = <?php echo $alt_occupation_token; ?>
  <?php } ?>

  <?php if (!empty($alt_industry_token)) { ?>
  alt_industry_token = <?php echo $alt_industry_token; ?>
  <?php } ?>

  $(function()
  {
    $('#birth_date').datepicker({
      defaultDate: '<?php echo $default_date; ?>',
      yearRange: '<?php echo $year_range; ?>',
      showButtonPanel: true,
      changeYear: true,
      numberOfMonths: 2,
      showOn: 'both',
      buttonImage: '<?php echo $calendar_icon; ?>',
      buttonImageOnly: true,
      dateFormat: 'yy-mm-dd'
    });

    $('#company').tokenInput('<?php echo $company_token_url; ?>',
    {
      noResultsText: "no results found",
      tokenLimit: 1,
      prePopulate: company_token
    });

    $('#alt_occupation').tokenInput('<?php echo $alt_occupation_token_url; ?>',
    {
      noResultsText: "no results found",
      tokenLimit: 5,
      prePopulate: alt_occupation_token
    });

    $('#alt_industry').tokenInput('<?php echo $alt_industry_token_url; ?>',
    {
      noResultsText: "no results found",
      tokenLimit: 5,
      prePopulate: alt_industry_token
    });

    $('.gray_section .skill_field input').spinner(
    {
      min:-1, max: 10,
      spin: function(event, ui)
      {
        if(ui.value > 9)
        {
          $(this).spinner("value", 0); return false;
        }
        else if (ui.value < 0)
        {
          $(this).spinner("value", 9); return false;
        }
      }
    });

    $('.gray_section .skill_field input').focus(function()
    {
      if($(this).hasClass('empty_spinner'))
      {
        $(this).val(5).removeClass('empty_spinner').unbind('focus');
      }
    });

    $('#alt_language').bsmSelect(
    {
      animate: true,
      highlight: true,
      showEffect: function(jQueryel)
      {
        var sText = jQueryel.text();
        sText = sText.substr(0, sText.length-1).trim();

        var oOriginal = $('#alt_language option:contains('+sText+')');
        if(oOriginal)
          jQueryel.addClass(oOriginal.attr('class'));

        jQueryel.fadeIn();
      },
      hideEffect: function(jQueryel){ jQueryel.fadeOut(function(){ $(this).remove(); }); },
      removeLabel: '<strong>X</strong>'
    }).change();

    $('.gray_section').find('textarea').each(function()
    {
      initMce($(this).attr('name'), '', 700);
    });

    linkCurrencyFields('salary_unit', 'bonus', 'salary');
    linkCurrencyFields('salary_unit', 'target_low', 'salary');
    linkCurrencyFields('salary_unit', 'target_high', 'salary');

    $('.salary_field').focusout(function() {
      var formated_value = format_currency($(this).val());

      $(this).val(formated_value);
    });

    check_dom_change();
  });

  function toggle_tabs(menu_dom, tab_id)
  {
    var menu_obj = $(menu_dom);
    var menu_siblings = menu_obj.siblings();

    var tab_obj = $('#candi_container #'+tab_id);
    var tab_siblings = tab_obj.siblings();

    menu_siblings.removeClass('selected');
    menu_obj.addClass('selected');

    tab_siblings.hide();
    tab_obj.show();
  }

  function change_date_field(date_field_id)
  {
    var date_field_obj = $('.general_form_column #'+date_field_id);
    var date_field_siblings = date_field_obj.siblings();

    date_field_siblings.hide();
    if (date_field_id == 'birth_date')
      $('.general_form_column .ui-datepicker-trigger').show();
    date_field_obj.show();
  }

  $('form[name=addcandidate]').submit(function(event){
    event.preventDefault();

    var sURL = $('form[name=addcandidate]').attr('action');
    var sFormId = $('form[name=addcandidate]').attr('id');
    var sAjaxTarget = 'candi_duplicate';
    setTimeout(" AjaxRequest('"+sURL+"', '.body.', '"+sFormId+"', '"+sAjaxTarget+"', '', '', 'setCoverScreen(false);  '); ", 350);

    return false;
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

</html>