<form name="addcandidate" enctype="multipart/form-data" submitAjax="1"
  action="<?php echo $form_url; ?>" class="candiAddForm" ajaxTarget="candi_duplicate"


  <div id="candi_container">
    <div id="candi_data" class="add_margin_top_10">

      <div class="general_form_row">
        Occupation
      </div>
      <div class="gray_section">
        <div class="general_form_row extended_input">
          <div class="general_form_label">Company</div>
          <div class="general_form_column" style="width: 183px;">
            <input id="company" type="text" name="companypk"  />
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