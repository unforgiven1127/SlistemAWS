<form name="addcandidate" enctype="multipart/form-data" submitAjax="1"
  action="<?php echo $form_url; ?>" class="candiAddForm" ajaxTarget="candi_duplicate"


  <div id="candi_container">
    <div id="candi_data" class="add_margin_top_10">
      <div class="general_form_row">
        Candidate details
      </div>
      <div class="gray_section extended_select extended_input">


      </div>
      <div class="general_form_row">
        Occupation
      </div>
      <div class="gray_section">
        <div class="general_form_row extended_input">
          <div class="general_form_label">Company</div>
          <div class="general_form_column" style="width: 183px;">
            <input id="company" type="text" name="companypk" value="<?php echo $company; ?>" />
          </div>
          <div class="general_form_column add_margin_left_30" style="width: 278px;">
            <a href="javascript:;"
            onclick="var oConf = goPopup.getConfig(); oConf.height = 600;
            oConf.width = 900;goPopup.setLayerFromAjax(oConf, '<?php echo $add_company_url ?>');">
              + add a new company
            </a>
          </div>
          <div class="general_form_label add_margin_left_30">Title</div>
          <div class="general_form_column">
            <input type="text" name="title" value="<?php echo $title; ?>" />
          </div>
        </div>

        <div class="general_form_row">
          
        </div>

      </div>
    </div>

  </div>

</form>

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