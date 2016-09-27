

    <input type="hidden" name="userfk" value="<?php echo $user_id; ?>" />
    <input id="dup_checked" type="hidden" name="check_duplicate" value="0" />

    <div class="formFieldTitle">
        Add/edit contact details
    </div>
    <?php if ($display_all_tabs) { ?>
    <div class="general_form_row add_margin_top_10">
        <ul class="candidate_form_tabs">
            <li onclick="toggle_tabs(this, 'candi_data');" class="selected">
                <div>Candidate data</div>
            </li>
            <li onclick="toggle_tabs(this, 'candi_contact');">
                <div>Contact details</div>
            </li>
            <li onclick="toggle_tabs(this, 'candi_note');">
                <div>Notes</div>
            </li>
            <li onclick="toggle_tabs(this, 'candi_resume');">
                <div>Resume</div>
            </li>
            <li onclick="toggle_tabs(this, 'candi_duplicate');" class="hidden tab_duplicate">
                <div>Duplicates</div>
            </li>
        </ul>
    </div>
    <?php } ?>
    <div id="candi_container">
        <div id="candi_data" class="add_margin_top_10">
            <div class="general_form_row">
                Candidate details
            </div>
            <div class="gray_section extended_select extended_input">
                <div class="general_form_row">
                    <div class="general_form_label">Gender</div>
                    <div class="general_form_column">
                        <select id="sex_id" name="sex" onchange="toggleGenderPic(this);">
                            <option value="2">female</option>
                            <option value="1" <?php echo (($user_sex == 1)? 'selected':''); ?>>male</option>
                        </select>
                    </div>
                    <div class="general_form_column">
                        <span class="woman" href="javascript:;" onclick="toggleGenderPic(false, 1);"
                            style="<?php echo (($user_sex != 1)? '':'display: none'); ?>">
                            <img src="/common/pictures/slistem/woman_16.png"/>
                        </span>
                        <span class="man" href="javascript:;" onclick="toggleGenderPic(false, 2);"
                            style="<?php echo (($user_sex == 1)? '':'display: none'); ?>">
                            <img src="/common/pictures/slistem/man_16.png"/>
                        </span>
                    </div>
                </div>
                <div class="general_form_row">
                    <div class="general_form_label">Lastname</div>
                    <div class="general_form_column">
                        <input <?php echo $readonly_name; ?> type="text" name="lastname" value="<?php echo $lastname; ?>" />
                    </div>
                    <div class="general_form_label add_margin_left_30">Firstname</div>
                    <div class="general_form_column">
                        <input <?php echo $readonly_name; ?> type="text" name="firstname" value="<?php echo $firstname; ?>" />
                    </div>
                    <div class="general_form_label add_margin_left_30">
                        <a href="javascript:;" onclick="change_date_field('birth_date');">Birth</a> /
                        <a href="javascript:;" onclick="change_date_field('estimated_age');">age</a>
                    </div>
                    <div class="general_form_column">
                        <input id="birth_date" type="text" name="birth_date" value="<?php echo $birth_date; ?>" />
                        <input id="estimated_age" style="display: none;" type="text" name="age" value="<?php echo $estimated_age; ?>" />
                    </div>
                </div>
                <div class="general_form_row">
                    <div class="general_form_label">Language</div>
                    <div class="general_form_column">
                        <select name="language">
                        <?php echo $language; ?>
                        </select>
                    </div>
                    <div class="general_form_label add_margin_left_30">Nationality</div>
                    <div class="general_form_column">
                        <select name="nationality">
                        <?php echo $nationality; ?>
                        </select>
                    </div>
                    <div class="general_form_label add_margin_left_30">Location</div>
                    <div class="general_form_column">
                        <select name="location" >
                        <?php echo $location; ?>
                        </select>
                    </div>
                </div>
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
                <div class="general_form_row extended_input">
                    <div class="general_form_label">Occupation</div>
                    <div class="general_form_column" style="width: 183px;">
                    <?php echo $occupation_tree; ?>
                    </div>
                    <div class="general_form_label add_margin_left_30">Industry</div>
                    <div class="general_form_column" style="width: 184px;">
                    <?php echo $industry_tree; ?>
                    </div>
                    <div class="general_form_label add_margin_left_30">Department</div>
                    <div class="general_form_column">
                        <input type="text" name="department" value="<?php echo $department; ?>" />
                    </div>
                </div>
                <div class="general_form_row">
                    <div class="general_form_label">Salary</div>
                    <div class="general_form_column">
                        <input class="salary_field" type="text" name="salary" value="<?php echo $candidate_salary; ?>" />
                        <select id="salary_unit" class="salary_manipulation" name="salary_unit">
                            <option value=""></option>
                            <option value="K" <?php if ($money_unit == 'K') echo 'selected'; ?>>K</option>
                            <option value="M" <?php if ($money_unit == 'M') echo 'selected'; ?>>M</option>
                        </select>
                        <select id="salary_currency" class="salary_manipulation" name="salary_currency">
                        <?php
                        $list = array('aud','cad','eur','hkd','jpy','php','usd');

                        //foreach ($currency_list as $currency => $rate)
                        foreach ($list as $key => $value)
                        {
                            $currency = $value;
                            $rate = $currency_list[$value];

                            if ($currency == $currencyCode)
                            {
                                $selected = ' selected ';
                            }
                            else
                            {
                                $selected = '';
                            }

                            $rateNew = 1/$rate;
                            echo "<option value='".$currency."' ";
                            echo $selected;
                            echo "title='Rate: 1 ".$currency." = ".$rateNew." &yen'>";
                            echo $currency;
                            echo "</option>";
                        } ?>
                        </select>
                    </div>
                    <div class="general_form_label add_margin_left_30">Bonus</div>
                    <div class="general_form_column">
                        <input class="salary_field" type="text" name="bonus" value="<?php echo $candidate_salary_bonus; ?>" />
                        <input id="bonus_unit" class="salary_manipulation_small read_only_field" type="text" name="bonus_unit"
                        value="" readonly />
                        <input id="bonus_currency" class="salary_manipulation_small read_only_field" type="text" name="bonus_currency"
                        value="" readonly />
                    </div>
                </div>
                <div class="general_form_row">
                    <div class="general_form_label">Target sal. from</div>
                    <div class="general_form_column">
                        <input class="salary_field" type="text" name="target_low" value="<?php echo $target_low; ?>" />
                        <input id="target_low_unit" class="salary_manipulation_small read_only_field" type="text" name="target_low_unit"
                            value="" readonly />
                        <input id="target_low_currency" class="salary_manipulation_small read_only_field" type="text" name="target_low_currency"
                            value="" readonly />
                    </div>
                    <div class="general_form_label add_margin_left_30">To</div>
                    <div class="general_form_column">
                        <input class="salary_field" type="text" name="target_high" value="<?php echo $target_high; ?>" />
                        <input id="target_high_unit" class="salary_manipulation_small read_only_field" type="text" name="target_high_unit"
                            value="" readonly />
                        <input id="target_high_currency" class="salary_manipulation_small read_only_field" type="text" name="target_high_currency"
                            value="" readonly />
                    </div>
                </div>
            </div>
            <div class="general_form_row">
                Profile
            </div>
            <div class="gray_section">
                <div class="general_form_row  extended_select">
                    <div class="general_form_label">Grade</div>
                    <div class="general_form_column">
                        <select name="grade" >
                        <?php echo $grade; ?>
                        </select>
                    </div>
                    <div class="general_form_label add_margin_left_30">Status</div>
                    <div class="general_form_column">
                        <select name="status" onchange="manageFormStatus(this, <?php echo $candidate_id; ?>);">
                        <?php echo $status_options; ?>
                        </select>
                    </div>
                    <div class="general_form_label add_margin_left_30">MBA/CPA</div>
                    <div class="general_form_column">
                        <select name="diploma">
                            <option value="">none</option>
                            <?php echo $diploma_options; ?>
                        </select>
                    </div>
                </div>
                <div class="general_form_row">
                    <div class="general_form_label">Keyword</div>
                    <div class="general_form_column extended_input">
                        <input type="text" name="keyword" value="<?php echo $keyword; ?>" />
                    </div>
                    <div class="general_form_label add_margin_left_30">Is client</div>
                    <div class="general_form_column">
                        <input id="is_client" class="css-checkbox" type="checkbox" name="client"
                            <?php if (!empty($is_client)) echo 'checked'; ?> />
                        <label for="is_client" class="css-label">&nbsp;</label>
                    </div>
                </div>
                <div class="general_form_row add_margin_top_10">
                    <div class="spinner_holder skill_field">
                        <div class="spinner_label">
                            AG
                        </div>
                        <input class="<?php echo $spinner_class; ?>" type="text" name="skill_ag" value="<?php echo $skill_ag; ?>" />
                    </div>
                    <div class="spinner_holder skill_field add_margin_left_20">
                        <div class="spinner_label">
                            AP
                        </div>
                        <input class="<?php echo $spinner_class; ?>" type="text" name="skill_ap" value="<?php echo $skill_ap; ?>" />
                    </div>
                    <div class="spinner_holder skill_field add_margin_left_20">
                        <div class="spinner_label">
                            AM
                        </div>
                        <input class="<?php echo $spinner_class; ?>" type="text" name="skill_am" value="<?php echo $skill_am; ?>" />
                    </div>
                    <div class="spinner_holder skill_field add_margin_left_20">
                        <div class="spinner_label">
                            MP
                        </div>
                        <input class="<?php echo $spinner_class; ?>" type="text" name="skill_mp" value="<?php echo $skill_mp; ?>" />
                    </div>
                    <div class="spinner_holder skill_field add_margin_left_20">
                        <div class="spinner_label">
                            IN
                        </div>
                        <input class="<?php echo $spinner_class; ?>" type="text" name="skill_in" value="<?php echo $skill_in; ?>" />
                    </div>
                    <div class="spinner_holder skill_field add_margin_left_20">
                        <div class="spinner_label">
                            EX
                        </div>
                        <input class="<?php echo $spinner_class; ?>" type="text" name="skill_ex" value="<?php echo $skill_ex; ?>" />
                    </div>
                    <div class="spinner_holder skill_field add_margin_left_20">
                        <div class="spinner_label">
                            FX
                        </div>
                        <input class="<?php echo $spinner_class; ?>" type="text" name="skill_fx" value="<?php echo $skill_fx; ?>" />
                    </div>
                    <div class="spinner_holder skill_field add_margin_left_20">
                        <div class="spinner_label">
                            CH
                        </div>
                        <input class="<?php echo $spinner_class; ?>" type="text" name="skill_ch" value="<?php echo $skill_ch; ?>" />
                    </div>
                    <div class="spinner_holder skill_field add_margin_left_20">
                        <div class="spinner_label">
                            ED
                        </div>
                        <input class="<?php echo $spinner_class; ?>" type="text" name="skill_ed" value="<?php echo $skill_ed; ?>" />
                    </div>
                    <div class="spinner_holder skill_field add_margin_left_20">
                        <div class="spinner_label">
                            PL
                        </div>
                        <input class="<?php echo $spinner_class; ?>" type="text" name="skill_pl" value="<?php echo $skill_pl; ?>" />
                    </div>
                    <div class="spinner_holder skill_field add_margin_left_20">
                        <div class="spinner_label">
                            E
                        </div>
                        <input class="<?php echo $spinner_class; ?>" type="text" name="skill_e" value="<?php echo $skill_e; ?>" />
                    </div>
                </div>
            </div>


            <div class="general_form_row">
                <div style="margin-top: 5px; cursor: pointer;" class="bold italic"
                onclick="$('#additional_candidate_info').fadeToggle(function(){ $(this).closest('.ui-dialog-content').scrollTop(5000); });">
                    Additional data ?
                </div>
            </div>
            <div id="additional_candidate_info" class="gray_section hidden">
                <div class="general_form_row">
                    Multiple industries ? Speak different languages? Fully and accuratly describing the candidates is a key for Sl[i]stem.
                    <br>
                    It will improve the search functions and increase the candidate profile quality. Use this section to add alternative /
                    secondary information about the candidate.
                </div>
                <div class="general_form_row extended_select extended_input">
                    <div class="general_form_label">alt. occupation</div>
                    <div class="general_form_column">
                        <input id="alt_occupation" type="text" name="alt_occupationpk" value="<?php if(isset($alt_occupationpk)){echo $alt_occupationpk;}  ?>" />
                    </div>
                    <div class="general_form_label add_margin_left_30">alt. industry</div>
                    <div class="general_form_column">
                        <input id="alt_industry" type="text" name="alt_industrypk" value="<?php if(isset($alt_industrypk)){echo $alt_industrypk;} ?>" />
                    </div>
                    <div class="general_form_label add_margin_left_30">language</div>
                    <div class="general_form_column">
                        <select id="alt_language" name="alt_language[]" multiple>
                        <?php echo $alt_language; ?>
                        </select>
                    </div>
                </div>
                <?php if ($candidate_sys_status > 0 && $is_admin) { ?>
                <div class="general_form_row" style="color: #077AC1; font-weight: bold;">
                    DBA
                </div>
                <div class="general_form_row">
                    <div class="general_form_label">Deleted ?</div>
                    <div class="general_form_column extended_select">
                        <select name="_sys_status">
                            <option value="<?php echo $candidate_sys_status; ?>">Keep deleted</option>
                            <option value="0">Restore candidate</option>
                        </select>
                    </div>
                    <div class="general_form_label add_margin_left_30">Merged with</div>
                    <div class="general_form_column extended_input">
                        <input type="text" name="_sys_redirect" value="<?php echo $candidate_sys_redirect; ?>" />
                    </div>
                </div>
                <?php } ?>
            </div>
        </div>

        <?php if ($display_all_tabs) { ?>
        <div id="candi_contact" class="add_margin_top_10 hidden">
        <?php echo $contact_details_form; ?>
        </div>

        <div id="candi_note" class="add_margin_top_10 hidden">
            <div class="gray_section">
                <div class="general_form_row">
                    <span style="font-size: 10px; color: blue;">
                        * If the candidate has been "assessed", the character note is required.<br/>
                        * In the other case, one of those fields is required.<br/>
                    </span>
                </div>
                <div class="general_form_row add_margin_top_10">
                    <div class="general_form_label">Character Note</div>
                    <div class="general_form_column">
                        <textarea id="character_note" name="character_note" ></textarea>
                    </div>
                </div>
                <!--<div class="general_form_row">
                    <div class="general_form_label">** Current Position & Responsibilities</div>
                    <div class="general_form_column">
                        <textarea id="current_podition_note" name="current_podition_note"></textarea>
                    </div>
                </div>
                <div class="general_form_row">
                    <div class="general_form_label">** Product or Technical Expertise</div>
                    <div class="general_form_column">
                        <textarea id="product_exp_note" name="product_exp_note"></textarea>
                    </div>
                </div>
                <div class="general_form_row">
                    <div class="general_form_label">** Compensation Breakdown</div>
                    <div class="general_form_column">
                        <textarea id="compensation_note" name="compensation_note"></textarea>
                    </div>
                </div>
                <div class="general_form_row">
                    <div class="general_form_label">** Reason for moving</div>
                    <div class="general_form_column">
                        <textarea id="move_note" name="move_note"></textarea>
                    </div>
                </div>
                <div class="general_form_row">
                    <div class="general_form_label">** Information on earlier career</div>
                    <div class="general_form_column">
                        <textarea id="career_note" name="career_note"></textarea>
                    </div>
                </div>
                <div class="general_form_row">
                    <div class="general_form_label">** Move timeline</div>
                    <div class="general_form_column">
                        <textarea id="timeline_note" name="timeline_note"></textarea>
                    </div>
                </div>
                <div class="general_form_row">
                    <div class="general_form_label">** Key Wants</div>
                    <div class="general_form_column">
                        <textarea id="keywants_note" name="keywants_note"></textarea>
                    </div>
                </div>
                <div class="general_form_row">
                    <div class="general_form_label">** Companies introduced within past 6 – 12 months</div>
                    <div class="general_form_column">
                        <textarea id="past_note" name="past_note"></textarea>
                    </div>
                </div>
                <div class="general_form_row">
                    <div class="general_form_label">** Education – Higher Educations</div>
                    <div class="general_form_column">
                        <textarea id="education_note" name="education_note"></textarea>
                    </div>
                </div>-->
                <div class="general_form_row">
                    <div class="general_form_label">Note</div>
                    <div class="general_form_column">
                        <textarea id="note" name="note"></textarea>
                    </div>
                </div>
            </div>
        </div>

        <div id="candi_resume" class="add_margin_top_10 hidden">
            <div class="gray_section">
                <div class="general_form_row">
                    <div class="general_form_label">Document title</div>
                    <div class="general_form_column">
                        <input type="text" name="doc_title" value="" />
                    </div>
                </div>
                <div class="general_form_row add_margin_top_10">
                    <div class="general_form_label">HTML resume</div>
                    <div class="general_form_column">
                        <textarea id="doc_description" name="doc_description"></textarea>
                    </div>
                </div>
                <div class="general_form_row add_margin_top_10">
                    <div class="general_form_label">Upload document</div>
                    <div class="general_form_column">
                        <input type="file" maxfilesize="<?php echo CONST_SS_MAX_DOCUMENT_SIZE; ?>" name="document" />
                        <input type="hidden" name="MAX_FILE_SIZE" value="<?php echo CONST_SS_MAX_DOCUMENT_SIZE; ?>" />
                    </div>
                </div>
            </div>
        </div>
        <?php } ?>
        <div id="candi_duplicate" class="add_margin_top_10 hidden">
        </div>
    </div>


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

        $('#meeting_date').datepicker({
            defaultDate: '<?php echo $todaysDate; ?>',
            yearRange: '<?php echo $sYearRangeToday; ?>',
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

<div id="57e9d82c90630" class="candidate_inner_section formSection" type="open" inajax="">
    <div class="formFieldContainer fieldNamecontact_type[0] formFieldWidth1 ">
        <div class="formLabel">Type</div>
        <div class="formField">
            <select name="contact_type[0]" label="Type" inajax="" id="contact_type0Id">
                <option value="1">Home</option>
                <option value="2" selected="selected">Work</option>
                <option value="3">Web</option>
                <option value="4">Fax</option>
                <option value="5">Email</option>
                <option value="6">Mobile</option>
                <option value="7">Facebook</option>
                <option value="8">LinkedIn</option>
                <option value="9">Info</option>
                <option value="10">Skype address</option>
            </select>
        </div>
        <div class="floatHack"></div>
    </div>
    <div class="formFieldContainer fieldNamecontact_value[0] formFieldWidth1 ">
        <div class="formLabel">Value</div>
        <div class="formField">
            <input type="text" name="contact_value[0]" style="padding-left:-500px" value="" inajax="" id="fldid_57e9d82c93e17">
        </div>
        <div class="floatHack"></div>
    </div>
    <div class="formFieldContainer fieldNamecontact_visibility[0] formFieldWidth1 ">
        <div class="formField">
            <select name="contact_visibility[0]" class="hidden" visibility="hidden" type="hidden" onchange="if($(this).val() == 4){ $('.custom_vis0').fadeIn(); }else { $('.custom_vis0:visible').fadeOut(); } " inajax="" id="contact_visibility0Id">
                <option style="width:5px" value="1">Public</option>
                <option style="width:5px" value="2">Private</option>
                <option style="width:5px" value="3">My team</option>
                <option style="width:5px" value="4">Custom</option>
            </select>
        </div>
        <div class="floatHack"></div>
    </div>
    <div class="formFieldContainer fieldNamesl_contactpk[0] formFieldWidth1  formFieldHidden ">
        <div class="formField">
            <input type="hidden" name="sl_contactpk[0]" value="0" inajax="" id="fldid_57e9d82c93f08">
        </div>
        <div class="floatHack"></div>
    </div>
    <div class="formFieldContainer fieldNamegroupfk0 formFieldWidth1  custom_vis0 hidden ">
        <div class="formLabel">Quick select</div>
        <div class="formField">
            <select name="groupfk0" label="Quick select" onchange="

            $('#contact_userfk0Id').tokenInput('clear');
            $('#contact_userfk0Id').css('color', 'red');

            var asCons = $(this).val().split('||');
            //console.log(asCons);
            $(asCons).each(function(nIndex, sValue)
            {
              var asValue = sValue.split('@@');
              if(asValue.length == 2)
              {
                //console.log('adding user '+asValue[1]);
                $('#contact_userfk0Id').tokenInput('add', {id: asValue[0], name: asValue[1]});
              }
            });  " inajax="" id="groupfk0Id">
                <option value="101@@administrator">-</option>
                <option value="477@@||498@@||478@@||482@@respool||499@@||2@@watercooler"></option>
                <option value="442@@helpdesk||241@@fastars||240@@FATokyo||388@@lfry||448@@management||314@@mmoir||85@@msoni||99@@rkamo||312@@src||347@@ytakagi||199@@ykuwabara">Active user</option>
                <option value="388@@lfry||314@@mmoir||85@@msoni||99@@rkamo||347@@ytakagi||199@@ykuwabara">Active users</option>
                <option value="260@@dba||309@@nicholas||343@@rkiyamu||199@@ykuwabara">Administration</option>
                <option value="382@@akramer||481@@Cici||407@@it.group||443@@jkovaliovas||274@@lifescience||466@@ryago||490@@Sarah||315@@agent">Analyst</option>
                <option value="481@@Cici||462@@fhan||473@@||443@@jkovaliovas||431@@kkapur||388@@lfry||314@@mmoir||85@@msoni||493@@||276@@pthai||374@@pmiles||466@@ryago||354@@rpedersen||459@@ray||99@@rkamo||457@@saltanbagana||315@@agent||130@@vmaslyuk||347@@ytakagi">Consultant</option>
                <option value="442@@helpdesk||241@@fastars||240@@FATokyo||407@@it.group||274@@lifescience||448@@management||406@@mr||312@@src">Mailing list</option>
                <option value="314@@mmoir||85@@msoni||276@@pthai||459@@ray||354@@rpedersen||186@@ryan||99@@rkamo">Management</option>
                <option value="382@@akramer||481@@Cici||489@@||492@@||480@@pgreeff||466@@ryago||490@@Sarah||457@@saltanbagana||315@@agent||501@@srose">Researcher</option>
                <option value="489@@||276@@pthai||480@@pgreeff||459@@ray||354@@rpedersen||501@@srose">Slate Canada</option>
                <option value="186@@ryan">Slate Hong Kong</option>
                <option value="382@@akramer||186@@ryan||312@@src">Slate Manila</option>
                <option value="481@@Cici||462@@fhan||473@@||443@@jkovaliovas||431@@kkapur||388@@lfry||492@@||314@@mmoir||85@@msoni||406@@mr||494@@||309@@nicholas||276@@pthai||374@@pmiles||466@@ryago||459@@ray||354@@rpedersen||343@@rkiyamu||99@@rkamo||490@@Sarah||457@@saltanbagana||215@@tokyo||130@@vmaslyuk||347@@ytakagi||199@@ykuwabara">Slate Tokyo</option>
                <option value="101@@administrator||309@@nicholas">Support</option>
                <option value="442@@helpdesk||101@@administrator||300@@arinestine||260@@dba||241@@fastars||343@@rkiyamu||215@@tokyo||199@@ykuwabara">Team admin</option>
                <option value="374@@pmiles||130@@vmaslyuk">Team CNS</option>
                <option value="186@@ryan">Team finance</option>
                <option value="101@@administrator||473@@||274@@lifescience||314@@mmoir||466@@ryago||99@@rkamo||490@@Sarah||457@@saltanbagana||347@@ytakagi">Team healthcare</option>
                <option value="489@@||443@@jkovaliovas||492@@||459@@ray||354@@rpedersen">Team Industrial</option>
                <option value="481@@Cici||462@@fhan||407@@it.group||431@@kkapur||388@@lfry||85@@msoni||493@@||276@@pthai">Team IT</option>
                <option value="448@@management">Team the others</option>
            </select>
        </div>
        <div class="floatHack"></div>
    </div>
    <div class="formFieldContainer fieldNamecontact_description[0] formFieldWidth1 ">
        <div class="formLabel">Notes</div>
        <div class="formField">
            <input type="text" name="contact_description[0]" value="" style="width:510px" inajax="" id="fldid_57e9d82c9400e">
        </div>
        <div class="floatHack"></div>
    </div>
    <div class="formFieldContainer fieldNamecontact_userfk[0] formFieldWidth1  custom_vis0 hidden " id="user_block_0">
        <script language="javascript">
            $(document).ready(function() {
                $("#contact_userfk0Id").tokenInput("https://beta.slate.co.jp/index.php5?uid=579-704&ppa=ppasea&ppt=usr&ppk=0&show_id=0&friendly=1&active_only=1&pg=ajx", {
                    noResultsText: "no results found",
                    onResult: function(oResult) {
                        if (oResult.length == 0)
                            return [{
                                id: "token_clear",
                                name: "no result found"
                            }]

                        var oLast = $(oResult).last();
                        if (oLast && oLast[0] && oLast[0].callback)
                            eval(oLast[0].callback);

                        return oResult;
                    },
                    onAdd: function(oItem) {
                        // console.log(oItem);
                        if (oItem.id == "token_clear")
                            $(this).tokenInput("clear");


                    },
                    tokenFormatter: function(item) {
                        if (item.label)
                            return "<li class=test1 'contact_userfk[0]_item' title='" + item.title + "'><p>" + item.label + "</p></li>";
                        else
                            return "<li class=test2 'contact_userfk[0]_item' title='" + item.title + "'><p>" + item.name + "</p></li>";
                    },

                    tokenLimit: "10"
                });
            });
            $("#contact_userfk0Id").on("remove", function() {
                $("#contact_userfk0Id .token-input-dropdown-mac").remove();
            });
        </script>
        <div class="formLabel">Users</div>
        <div class="formField formAutocompleteContainer ">
            <ul class="token-input-list-mac">
                <li class="token-input-input-token-mac">
                    <input type="text" autocomplete="off" id="token-input-contact_userfk0Id" style="outline: none;">
                    <tester style="position: absolute; top: -9999px; left: -9999px; width: auto; font-size: 12px; font-family: Verdana, Arial, sans-serif; font-weight: 400; letter-spacing: 0px; white-space: nowrap;"></tester>
                </li>
            </ul>
            <input type="text" name="contact_userfk[0]" nbresult="10" url="https://beta.slate.co.jp/index.php5?uid=579-704&amp;ppa=ppasea&amp;ppt=usr&amp;ppk=0&amp;show_id=0&amp;friendly=1&amp;active_only=1&amp;pg=ajx" inajax="" id="contact_userfk0Id" value="" class="autocompleteField" onadd="" style="display: none;">
        </div>
        <div class="floatHack"></div>
    </div>
    <div class="formFieldContainer fieldName57e9d82c930bc formFieldWidth1  formFieldHidden "><span inajax="" id="57e9d82c930bcId"></span>
        <div class="floatHack"></div>
    </div>
    <div class="formFieldLineBreaker formFieldWidth1">&nbsp;</div>
    <div class="formFieldSeparator formFieldWidth1">&nbsp;</div>
    <div class="floatHack"></div>
    <div class="formFieldContainer fieldNamecontact_type[1] formFieldWidth1 ">
        <div class="formLabel">Type</div>
        <div class="formField">
            <select name="contact_type[1]" label="Type" inajax="" id="contact_type1Id">
                <option value="1">Home</option>
                <option value="2">Work</option>
                <option value="3">Web</option>
                <option value="4">Fax</option>
                <option value="5" selected="selected">Email</option>
                <option value="6">Mobile</option>
                <option value="7">Facebook</option>
                <option value="8">LinkedIn</option>
                <option value="9">Info</option>
                <option value="10">Skype address</option>
            </select>
        </div>
        <div class="floatHack"></div>
    </div>
    <div class="formFieldContainer fieldNamecontact_value[1] formFieldWidth1 ">
        <div class="formLabel">Value</div>
        <div class="formField">
            <input type="text" name="contact_value[1]" style="padding-left:-500px" value="" inajax="" id="fldid_57e9d82c94177">
        </div>
        <div class="floatHack"></div>
    </div>
    <div class="formFieldContainer fieldNamecontact_visibility[1] formFieldWidth1 ">
        <div class="formField">
            <select name="contact_visibility[1]" class="hidden" visibility="hidden" type="hidden" onchange="if($(this).val() == 4){ $('.custom_vis1').fadeIn(); }else { $('.custom_vis1:visible').fadeOut(); } " inajax="" id="contact_visibility1Id">
                <option style="width:5px" value="1">Public</option>
                <option style="width:5px" value="2">Private</option>
                <option style="width:5px" value="3">My team</option>
                <option style="width:5px" value="4">Custom</option>
            </select>
        </div>
        <div class="floatHack"></div>
    </div>
    <div class="formFieldContainer fieldNamesl_contactpk[1] formFieldWidth1  formFieldHidden ">
        <div class="formField">
            <input type="hidden" name="sl_contactpk[1]" value="0" inajax="" id="fldid_57e9d82c9425d">
        </div>
        <div class="floatHack"></div>
    </div>
    <div class="formFieldContainer fieldNamegroupfk1 formFieldWidth1  custom_vis1 hidden ">
        <div class="formLabel">Quick select</div>
        <div class="formField">
            <select name="groupfk1" label="Quick select" onchange="

            $('#contact_userfk1Id').tokenInput('clear');
            $('#contact_userfk1Id').css('color', 'red');

            var asCons = $(this).val().split('||');
            //console.log(asCons);
            $(asCons).each(function(nIndex, sValue)
            {
              var asValue = sValue.split('@@');
              if(asValue.length == 2)
              {
                //console.log('adding user '+asValue[1]);
                $('#contact_userfk1Id').tokenInput('add', {id: asValue[0], name: asValue[1]});
              }
            });  " inajax="" id="groupfk1Id">
                <option value="101@@administrator">-</option>
                <option value="477@@||498@@||478@@||482@@respool||499@@||2@@watercooler"></option>
                <option value="442@@helpdesk||241@@fastars||240@@FATokyo||388@@lfry||448@@management||314@@mmoir||85@@msoni||99@@rkamo||312@@src||347@@ytakagi||199@@ykuwabara">Active user</option>
                <option value="388@@lfry||314@@mmoir||85@@msoni||99@@rkamo||347@@ytakagi||199@@ykuwabara">Active users</option>
                <option value="260@@dba||309@@nicholas||343@@rkiyamu||199@@ykuwabara">Administration</option>
                <option value="382@@akramer||481@@Cici||407@@it.group||443@@jkovaliovas||274@@lifescience||466@@ryago||490@@Sarah||315@@agent">Analyst</option>
                <option value="481@@Cici||462@@fhan||473@@||443@@jkovaliovas||431@@kkapur||388@@lfry||314@@mmoir||85@@msoni||493@@||276@@pthai||374@@pmiles||466@@ryago||354@@rpedersen||459@@ray||99@@rkamo||457@@saltanbagana||315@@agent||130@@vmaslyuk||347@@ytakagi">Consultant</option>
                <option value="442@@helpdesk||241@@fastars||240@@FATokyo||407@@it.group||274@@lifescience||448@@management||406@@mr||312@@src">Mailing list</option>
                <option value="314@@mmoir||85@@msoni||276@@pthai||459@@ray||354@@rpedersen||186@@ryan||99@@rkamo">Management</option>
                <option value="382@@akramer||481@@Cici||489@@||492@@||480@@pgreeff||466@@ryago||490@@Sarah||457@@saltanbagana||315@@agent||501@@srose">Researcher</option>
                <option value="489@@||276@@pthai||480@@pgreeff||459@@ray||354@@rpedersen||501@@srose">Slate Canada</option>
                <option value="186@@ryan">Slate Hong Kong</option>
                <option value="382@@akramer||186@@ryan||312@@src">Slate Manila</option>
                <option value="481@@Cici||462@@fhan||473@@||443@@jkovaliovas||431@@kkapur||388@@lfry||492@@||314@@mmoir||85@@msoni||406@@mr||494@@||309@@nicholas||276@@pthai||374@@pmiles||466@@ryago||459@@ray||354@@rpedersen||343@@rkiyamu||99@@rkamo||490@@Sarah||457@@saltanbagana||215@@tokyo||130@@vmaslyuk||347@@ytakagi||199@@ykuwabara">Slate Tokyo</option>
                <option value="101@@administrator||309@@nicholas">Support</option>
                <option value="442@@helpdesk||101@@administrator||300@@arinestine||260@@dba||241@@fastars||343@@rkiyamu||215@@tokyo||199@@ykuwabara">Team admin</option>
                <option value="374@@pmiles||130@@vmaslyuk">Team CNS</option>
                <option value="186@@ryan">Team finance</option>
                <option value="101@@administrator||473@@||274@@lifescience||314@@mmoir||466@@ryago||99@@rkamo||490@@Sarah||457@@saltanbagana||347@@ytakagi">Team healthcare</option>
                <option value="489@@||443@@jkovaliovas||492@@||459@@ray||354@@rpedersen">Team Industrial</option>
                <option value="481@@Cici||462@@fhan||407@@it.group||431@@kkapur||388@@lfry||85@@msoni||493@@||276@@pthai">Team IT</option>
                <option value="448@@management">Team the others</option>
            </select>
        </div>
        <div class="floatHack"></div>
    </div>
    <div class="formFieldContainer fieldNamecontact_description[1] formFieldWidth1 ">
        <div class="formLabel">Notes</div>
        <div class="formField">
            <input type="text" name="contact_description[1]" value="" style="width:510px" inajax="" id="fldid_57e9d82c9435c">
        </div>
        <div class="floatHack"></div>
    </div>
    <div class="formFieldContainer fieldNamecontact_userfk[1] formFieldWidth1  custom_vis1 hidden " id="user_block_1">
        <script language="javascript">
            $(document).ready(function() {
                $("#contact_userfk1Id").tokenInput("https://beta.slate.co.jp/index.php5?uid=579-704&ppa=ppasea&ppt=usr&ppk=0&show_id=0&friendly=1&active_only=1&pg=ajx", {
                    noResultsText: "no results found",
                    onResult: function(oResult) {
                        if (oResult.length == 0)
                            return [{
                                id: "token_clear",
                                name: "no result found"
                            }]

                        var oLast = $(oResult).last();
                        if (oLast && oLast[0] && oLast[0].callback)
                            eval(oLast[0].callback);

                        return oResult;
                    },
                    onAdd: function(oItem) {
                        // console.log(oItem);
                        if (oItem.id == "token_clear")
                            $(this).tokenInput("clear");


                    },
                    tokenFormatter: function(item) {
                        if (item.label)
                            return "<li class=test1 'contact_userfk[1]_item' title='" + item.title + "'><p>" + item.label + "</p></li>";
                        else
                            return "<li class=test2 'contact_userfk[1]_item' title='" + item.title + "'><p>" + item.name + "</p></li>";
                    },

                    tokenLimit: "10"
                });
            });
            $("#contact_userfk1Id").on("remove", function() {
                $("#contact_userfk1Id .token-input-dropdown-mac").remove();
            });
        </script>
        <div class="formLabel">Users</div>
        <div class="formField formAutocompleteContainer ">
            <ul class="token-input-list-mac">
                <li class="token-input-input-token-mac">
                    <input type="text" autocomplete="off" id="token-input-contact_userfk1Id" style="outline: none;">
                    <tester style="position: absolute; top: -9999px; left: -9999px; width: auto; font-size: 12px; font-family: Verdana, Arial, sans-serif; font-weight: 400; letter-spacing: 0px; white-space: nowrap;"></tester>
                </li>
            </ul>
            <input type="text" name="contact_userfk[1]" nbresult="10" url="https://beta.slate.co.jp/index.php5?uid=579-704&amp;ppa=ppasea&amp;ppt=usr&amp;ppk=0&amp;show_id=0&amp;friendly=1&amp;active_only=1&amp;pg=ajx" inajax="" id="contact_userfk1Id" value="" class="autocompleteField" onadd="" style="display: none;">
        </div>
        <div class="floatHack"></div>
    </div>
    <div class="formFieldContainer fieldName57e9d82c93481 formFieldWidth1  formFieldHidden "><span inajax="" id="57e9d82c93481Id"></span>
        <div class="floatHack"></div>
    </div>
    <div class="formFieldLineBreaker formFieldWidth1">&nbsp;</div>
    <div class="formFieldSeparator formFieldWidth1">&nbsp;</div>
    <div class="floatHack"></div>
    <div class="formFieldContainer fieldNamecontact_type[2] formFieldWidth1 ">
        <div class="formLabel">Type</div>
        <div class="formField">
            <select name="contact_type[2]" label="Type" inajax="" id="contact_type2Id">
                <option value="1">Home</option>
                <option value="2">Work</option>
                <option value="3">Web</option>
                <option value="4">Fax</option>
                <option value="5">Email</option>
                <option value="6" selected="selected">Mobile</option>
                <option value="7">Facebook</option>
                <option value="8">LinkedIn</option>
                <option value="9">Info</option>
                <option value="10">Skype address</option>
            </select>
        </div>
        <div class="floatHack"></div>
    </div>
    <div class="formFieldContainer fieldNamecontact_value[2] formFieldWidth1 ">
        <div class="formLabel">Value</div>
        <div class="formField">
            <input type="text" name="contact_value[2]" style="padding-left:-500px" value="" inajax="" id="fldid_57e9d82c9448e">
        </div>
        <div class="floatHack"></div>
    </div>
    <div class="formFieldContainer fieldNamecontact_visibility[2] formFieldWidth1 ">
        <div class="formField">
            <select name="contact_visibility[2]" class="hidden" visibility="hidden" type="hidden" onchange="if($(this).val() == 4){ $('.custom_vis2').fadeIn(); }else { $('.custom_vis2:visible').fadeOut(); } " inajax="" id="contact_visibility2Id">
                <option style="width:5px" value="1">Public</option>
                <option style="width:5px" value="2">Private</option>
                <option style="width:5px" value="3">My team</option>
                <option style="width:5px" value="4">Custom</option>
            </select>
        </div>
        <div class="floatHack"></div>
    </div>
    <div class="formFieldContainer fieldNamesl_contactpk[2] formFieldWidth1  formFieldHidden ">
        <div class="formField">
            <input type="hidden" name="sl_contactpk[2]" value="0" inajax="" id="fldid_57e9d82c9456f">
        </div>
        <div class="floatHack"></div>
    </div>
    <div class="formFieldContainer fieldNamegroupfk2 formFieldWidth1  custom_vis2 hidden ">
        <div class="formLabel">Quick select</div>
        <div class="formField">
            <select name="groupfk2" label="Quick select" onchange="

            $('#contact_userfk2Id').tokenInput('clear');
            $('#contact_userfk2Id').css('color', 'red');

            var asCons = $(this).val().split('||');
            //console.log(asCons);
            $(asCons).each(function(nIndex, sValue)
            {
              var asValue = sValue.split('@@');
              if(asValue.length == 2)
              {
                //console.log('adding user '+asValue[1]);
                $('#contact_userfk2Id').tokenInput('add', {id: asValue[0], name: asValue[1]});
              }
            });  " inajax="" id="groupfk2Id">
                <option value="101@@administrator">-</option>
                <option value="477@@||498@@||478@@||482@@respool||499@@||2@@watercooler"></option>
                <option value="442@@helpdesk||241@@fastars||240@@FATokyo||388@@lfry||448@@management||314@@mmoir||85@@msoni||99@@rkamo||312@@src||347@@ytakagi||199@@ykuwabara">Active user</option>
                <option value="388@@lfry||314@@mmoir||85@@msoni||99@@rkamo||347@@ytakagi||199@@ykuwabara">Active users</option>
                <option value="260@@dba||309@@nicholas||343@@rkiyamu||199@@ykuwabara">Administration</option>
                <option value="382@@akramer||481@@Cici||407@@it.group||443@@jkovaliovas||274@@lifescience||466@@ryago||490@@Sarah||315@@agent">Analyst</option>
                <option value="481@@Cici||462@@fhan||473@@||443@@jkovaliovas||431@@kkapur||388@@lfry||314@@mmoir||85@@msoni||493@@||276@@pthai||374@@pmiles||466@@ryago||354@@rpedersen||459@@ray||99@@rkamo||457@@saltanbagana||315@@agent||130@@vmaslyuk||347@@ytakagi">Consultant</option>
                <option value="442@@helpdesk||241@@fastars||240@@FATokyo||407@@it.group||274@@lifescience||448@@management||406@@mr||312@@src">Mailing list</option>
                <option value="314@@mmoir||85@@msoni||276@@pthai||459@@ray||354@@rpedersen||186@@ryan||99@@rkamo">Management</option>
                <option value="382@@akramer||481@@Cici||489@@||492@@||480@@pgreeff||466@@ryago||490@@Sarah||457@@saltanbagana||315@@agent||501@@srose">Researcher</option>
                <option value="489@@||276@@pthai||480@@pgreeff||459@@ray||354@@rpedersen||501@@srose">Slate Canada</option>
                <option value="186@@ryan">Slate Hong Kong</option>
                <option value="382@@akramer||186@@ryan||312@@src">Slate Manila</option>
                <option value="481@@Cici||462@@fhan||473@@||443@@jkovaliovas||431@@kkapur||388@@lfry||492@@||314@@mmoir||85@@msoni||406@@mr||494@@||309@@nicholas||276@@pthai||374@@pmiles||466@@ryago||459@@ray||354@@rpedersen||343@@rkiyamu||99@@rkamo||490@@Sarah||457@@saltanbagana||215@@tokyo||130@@vmaslyuk||347@@ytakagi||199@@ykuwabara">Slate Tokyo</option>
                <option value="101@@administrator||309@@nicholas">Support</option>
                <option value="442@@helpdesk||101@@administrator||300@@arinestine||260@@dba||241@@fastars||343@@rkiyamu||215@@tokyo||199@@ykuwabara">Team admin</option>
                <option value="374@@pmiles||130@@vmaslyuk">Team CNS</option>
                <option value="186@@ryan">Team finance</option>
                <option value="101@@administrator||473@@||274@@lifescience||314@@mmoir||466@@ryago||99@@rkamo||490@@Sarah||457@@saltanbagana||347@@ytakagi">Team healthcare</option>
                <option value="489@@||443@@jkovaliovas||492@@||459@@ray||354@@rpedersen">Team Industrial</option>
                <option value="481@@Cici||462@@fhan||407@@it.group||431@@kkapur||388@@lfry||85@@msoni||493@@||276@@pthai">Team IT</option>
                <option value="448@@management">Team the others</option>
            </select>
        </div>
        <div class="floatHack"></div>
    </div>
    <div class="formFieldContainer fieldNamecontact_description[2] formFieldWidth1 ">
        <div class="formLabel">Notes</div>
        <div class="formField">
            <input type="text" name="contact_description[2]" value="" style="width:510px" inajax="" id="fldid_57e9d82c9466c">
        </div>
        <div class="floatHack"></div>
    </div>
    <div class="formFieldContainer fieldNamecontact_userfk[2] formFieldWidth1  custom_vis2 hidden " id="user_block_2">
        <script language="javascript">
            $(document).ready(function() {
                $("#contact_userfk2Id").tokenInput("https://beta.slate.co.jp/index.php5?uid=579-704&ppa=ppasea&ppt=usr&ppk=0&show_id=0&friendly=1&active_only=1&pg=ajx", {
                    noResultsText: "no results found",
                    onResult: function(oResult) {
                        if (oResult.length == 0)
                            return [{
                                id: "token_clear",
                                name: "no result found"
                            }]

                        var oLast = $(oResult).last();
                        if (oLast && oLast[0] && oLast[0].callback)
                            eval(oLast[0].callback);

                        return oResult;
                    },
                    onAdd: function(oItem) {
                        // console.log(oItem);
                        if (oItem.id == "token_clear")
                            $(this).tokenInput("clear");


                    },
                    tokenFormatter: function(item) {
                        if (item.label)
                            return "<li class=test1 'contact_userfk[2]_item' title='" + item.title + "'><p>" + item.label + "</p></li>";
                        else
                            return "<li class=test2 'contact_userfk[2]_item' title='" + item.title + "'><p>" + item.name + "</p></li>";
                    },

                    tokenLimit: "10"
                });
            });
            $("#contact_userfk2Id").on("remove", function() {
                $("#contact_userfk2Id .token-input-dropdown-mac").remove();
            });
        </script>
        <div class="formLabel">Users</div>
        <div class="formField formAutocompleteContainer ">
            <ul class="token-input-list-mac">
                <li class="token-input-input-token-mac">
                    <input type="text" autocomplete="off" id="token-input-contact_userfk2Id" style="outline: none;">
                    <tester style="position: absolute; top: -9999px; left: -9999px; width: auto; font-size: 12px; font-family: Verdana, Arial, sans-serif; font-weight: 400; letter-spacing: 0px; white-space: nowrap;"></tester>
                </li>
            </ul>
            <input type="text" name="contact_userfk[2]" nbresult="10" url="https://beta.slate.co.jp/index.php5?uid=579-704&amp;ppa=ppasea&amp;ppt=usr&amp;ppk=0&amp;show_id=0&amp;friendly=1&amp;active_only=1&amp;pg=ajx" inajax="" id="contact_userfk2Id" value="" class="autocompleteField" onadd="" style="display: none;">
        </div>
        <div class="floatHack"></div>
    </div>
    <div class="formFieldContainer fieldName57e9d82c93843 formFieldWidth1  formFieldHidden "><span inajax="" id="57e9d82c93843Id"></span>
        <div class="floatHack"></div>
    </div>
    <div class="formFieldLineBreaker formFieldWidth1">&nbsp;</div>
    <div class="formFieldSeparator formFieldWidth1">&nbsp;</div>
    <div class="floatHack"></div>
    <div class="formFieldContainer fieldNamecontact_type[3] formFieldWidth1 ">
        <div class="formLabel">Type</div>
        <div class="formField">
            <select name="contact_type[3]" label="Type" inajax="" id="contact_type3Id">
                <option value="1">Home</option>
                <option value="2">Work</option>
                <option value="3">Web</option>
                <option value="4">Fax</option>
                <option value="5">Email</option>
                <option value="6">Mobile</option>
                <option value="7">Facebook</option>
                <option value="8" selected="selected">LinkedIn</option>
                <option value="9">Info</option>
                <option value="10">Skype address</option>
            </select>
        </div>
        <div class="floatHack"></div>
    </div>
    <div class="formFieldContainer fieldNamecontact_value[3] formFieldWidth1 ">
        <div class="formLabel">Value</div>
        <div class="formField">
            <input type="text" name="contact_value[3]" style="padding-left:-500px" value="" inajax="" id="fldid_57e9d82c94759">
        </div>
        <div class="floatHack"></div>
    </div>
    <div class="formFieldContainer fieldNamecontact_visibility[3] formFieldWidth1 ">
        <div class="formField">
            <select name="contact_visibility[3]" class="hidden" visibility="hidden" type="hidden" onchange="if($(this).val() == 4){ $('.custom_vis3').fadeIn(); }else { $('.custom_vis3:visible').fadeOut(); } " inajax="" id="contact_visibility3Id">
                <option style="width:5px" value="1">Public</option>
                <option style="width:5px" value="2">Private</option>
                <option style="width:5px" value="3">My team</option>
                <option style="width:5px" value="4">Custom</option>
            </select>
        </div>
        <div class="floatHack"></div>
    </div>
    <div class="formFieldContainer fieldNamesl_contactpk[3] formFieldWidth1  formFieldHidden ">
        <div class="formField">
            <input type="hidden" name="sl_contactpk[3]" value="0" inajax="" id="fldid_57e9d82c94850">
        </div>
        <div class="floatHack"></div>
    </div>
    <div class="formFieldContainer fieldNamegroupfk3 formFieldWidth1  custom_vis3 hidden ">
        <div class="formLabel">Quick select</div>
        <div class="formField">
            <select name="groupfk3" label="Quick select" onchange="

            $('#contact_userfk3Id').tokenInput('clear');
            $('#contact_userfk3Id').css('color', 'red');

            var asCons = $(this).val().split('||');
            //console.log(asCons);
            $(asCons).each(function(nIndex, sValue)
            {
              var asValue = sValue.split('@@');
              if(asValue.length == 2)
              {
                //console.log('adding user '+asValue[1]);
                $('#contact_userfk3Id').tokenInput('add', {id: asValue[0], name: asValue[1]});
              }
            });  " inajax="" id="groupfk3Id">
                <option value="101@@administrator">-</option>
                <option value="477@@||498@@||478@@||482@@respool||499@@||2@@watercooler"></option>
                <option value="442@@helpdesk||241@@fastars||240@@FATokyo||388@@lfry||448@@management||314@@mmoir||85@@msoni||99@@rkamo||312@@src||347@@ytakagi||199@@ykuwabara">Active user</option>
                <option value="388@@lfry||314@@mmoir||85@@msoni||99@@rkamo||347@@ytakagi||199@@ykuwabara">Active users</option>
                <option value="260@@dba||309@@nicholas||343@@rkiyamu||199@@ykuwabara">Administration</option>
                <option value="382@@akramer||481@@Cici||407@@it.group||443@@jkovaliovas||274@@lifescience||466@@ryago||490@@Sarah||315@@agent">Analyst</option>
                <option value="481@@Cici||462@@fhan||473@@||443@@jkovaliovas||431@@kkapur||388@@lfry||314@@mmoir||85@@msoni||493@@||276@@pthai||374@@pmiles||466@@ryago||354@@rpedersen||459@@ray||99@@rkamo||457@@saltanbagana||315@@agent||130@@vmaslyuk||347@@ytakagi">Consultant</option>
                <option value="442@@helpdesk||241@@fastars||240@@FATokyo||407@@it.group||274@@lifescience||448@@management||406@@mr||312@@src">Mailing list</option>
                <option value="314@@mmoir||85@@msoni||276@@pthai||459@@ray||354@@rpedersen||186@@ryan||99@@rkamo">Management</option>
                <option value="382@@akramer||481@@Cici||489@@||492@@||480@@pgreeff||466@@ryago||490@@Sarah||457@@saltanbagana||315@@agent||501@@srose">Researcher</option>
                <option value="489@@||276@@pthai||480@@pgreeff||459@@ray||354@@rpedersen||501@@srose">Slate Canada</option>
                <option value="186@@ryan">Slate Hong Kong</option>
                <option value="382@@akramer||186@@ryan||312@@src">Slate Manila</option>
                <option value="481@@Cici||462@@fhan||473@@||443@@jkovaliovas||431@@kkapur||388@@lfry||492@@||314@@mmoir||85@@msoni||406@@mr||494@@||309@@nicholas||276@@pthai||374@@pmiles||466@@ryago||459@@ray||354@@rpedersen||343@@rkiyamu||99@@rkamo||490@@Sarah||457@@saltanbagana||215@@tokyo||130@@vmaslyuk||347@@ytakagi||199@@ykuwabara">Slate Tokyo</option>
                <option value="101@@administrator||309@@nicholas">Support</option>
                <option value="442@@helpdesk||101@@administrator||300@@arinestine||260@@dba||241@@fastars||343@@rkiyamu||215@@tokyo||199@@ykuwabara">Team admin</option>
                <option value="374@@pmiles||130@@vmaslyuk">Team CNS</option>
                <option value="186@@ryan">Team finance</option>
                <option value="101@@administrator||473@@||274@@lifescience||314@@mmoir||466@@ryago||99@@rkamo||490@@Sarah||457@@saltanbagana||347@@ytakagi">Team healthcare</option>
                <option value="489@@||443@@jkovaliovas||492@@||459@@ray||354@@rpedersen">Team Industrial</option>
                <option value="481@@Cici||462@@fhan||407@@it.group||431@@kkapur||388@@lfry||85@@msoni||493@@||276@@pthai">Team IT</option>
                <option value="448@@management">Team the others</option>
            </select>
        </div>
        <div class="floatHack"></div>
    </div>
    <div class="formFieldContainer fieldNamecontact_description[3] formFieldWidth1 ">
        <div class="formLabel">Notes</div>
        <div class="formField">
            <input type="text" name="contact_description[3]" value="" style="width:510px" inajax="" id="fldid_57e9d82c94950">
        </div>
        <div class="floatHack"></div>
    </div>
    <div class="formFieldContainer fieldNamecontact_userfk[3] formFieldWidth1  custom_vis3 hidden " id="user_block_3">
        <script language="javascript">
            $(document).ready(function() {
                $("#contact_userfk3Id").tokenInput("https://beta.slate.co.jp/index.php5?uid=579-704&ppa=ppasea&ppt=usr&ppk=0&show_id=0&friendly=1&active_only=1&pg=ajx", {
                    noResultsText: "no results found",
                    onResult: function(oResult) {
                        if (oResult.length == 0)
                            return [{
                                id: "token_clear",
                                name: "no result found"
                            }]

                        var oLast = $(oResult).last();
                        if (oLast && oLast[0] && oLast[0].callback)
                            eval(oLast[0].callback);

                        return oResult;
                    },
                    onAdd: function(oItem) {
                        // console.log(oItem);
                        if (oItem.id == "token_clear")
                            $(this).tokenInput("clear");


                    },
                    tokenFormatter: function(item) {
                        if (item.label)
                            return "<li class=test1 'contact_userfk[3]_item' title='" + item.title + "'><p>" + item.label + "</p></li>";
                        else
                            return "<li class=test2 'contact_userfk[3]_item' title='" + item.title + "'><p>" + item.name + "</p></li>";
                    },

                    tokenLimit: "10"
                });
            });
            $("#contact_userfk3Id").on("remove", function() {
                $("#contact_userfk3Id .token-input-dropdown-mac").remove();
            });
        </script>
        <div class="formLabel">Users</div>
        <div class="formField formAutocompleteContainer ">
            <ul class="token-input-list-mac">
                <li class="token-input-input-token-mac">
                    <input type="text" autocomplete="off" id="token-input-contact_userfk3Id" style="outline: none;">
                    <tester style="position: absolute; top: -9999px; left: -9999px; width: auto; font-size: 12px; font-family: Verdana, Arial, sans-serif; font-weight: 400; letter-spacing: 0px; white-space: nowrap;"></tester>
                </li>
            </ul>
            <input type="text" name="contact_userfk[3]" nbresult="10" url="https://beta.slate.co.jp/index.php5?uid=579-704&amp;ppa=ppasea&amp;ppt=usr&amp;ppk=0&amp;show_id=0&amp;friendly=1&amp;active_only=1&amp;pg=ajx" inajax="" id="contact_userfk3Id" value="" class="autocompleteField" onadd="" style="display: none;">
        </div>
        <div class="floatHack"></div>
    </div>
    <div class="formFieldContainer fieldName57e9d82c93bfb formFieldWidth1  formFieldHidden "><span inajax="" id="57e9d82c93bfbId"></span>
        <div class="floatHack"></div>
    </div>
    <div class="formFieldLineBreaker formFieldWidth1">&nbsp;</div>
    <div class="formFieldSeparator formFieldWidth1">&nbsp;</div>
    <div class="floatHack"></div>
    <div class="floatHack"></div>
</div>