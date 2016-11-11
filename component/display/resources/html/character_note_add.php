
<script type="text/javascript">

	$(function(){
	    $('#personality_note').attr('placeholder', "\u2022What is the candidate's Profession \n\u2022What is the Scope of his Experience -specific or broad \n\u2022list any key words for skills");//  \n\u2022Sections must be filled.  Minimum of 25 characters.

	    $('#personality_noteTD').attr('title', "What is the candidate's Profession <br><br>\u2022What is the Scope of his Experience -specific or broad <br><br>\u2022List any key words for skills <br><br>\u2022Sections must be filled.  Minimum of 25 characters.");

	    $('#career_note').attr('placeholder', "\u2022Is the candidate intelligent and able to solve problems, manage processes? \n\u2022Is the Candidate well presented and professional? \n\u2022Describe specific projects he has successfully overseen");// \n\u2022Sections must be filled.  Minimum of 25 characters.

	    $('#career_noteTD').attr('title', "\u2022Is the candidate intelligent and able to solve problems, manage processes? <br><br>\u2022Is the Candidate well presented and professional? <br><br>\u2022Describe specific projects he has successfully overseen <br><br>\u2022Sections must be filled.  Minimum of 25 characters.");


	    $('#education_note').attr('placeholder', "\u2022Can he manage a team and is he ambitious? \n\u2022List any specific companies or industries he has professional relationships with \n\u2022How does his compensation break down, how much does he want to make if he changes");//\n\u2022What is his educational background \n\u2022Sections must be filled.  Minimum of 15 characters.

	    $('#education_noteTD').attr('title', "\u2022Can he manage a team and is he ambitious? <br><br>\u2022List any specific companies or industries he has professional relationships with <br><br>\u2022How does his compensation break down, how much does he want to make if he changes <br><br>\u2022What is his educational background <br><br>\u2022Sections must be filled.  Minimum of 15 characters.");

	    $('#move_note').attr('placeholder', "\u2022How does he present? \n\u2022Is he confident, articulate, etc? \n\u2022Does the candidate have a powerful presence?");// \n\u2022Sections must be filled.  Minimum of 25 characters.

	    $('#move_noteTD').attr('title', "\u2022How does he present? <br><br>\u2022Is he confident, articulate, etc? <br><br>\u2022What is his motivation for moving <br><br>\u2022Does the candidate have a powerful presence? <br><br>\u2022Sections must be filled.  Minimum of 25 characters.");


	    $('#compensation_note').attr('placeholder', "\u2022What is his vision for his Future?/What is the logical next step in his career evolution? \n\u2022Why does he want to change his job? \n\u2022What is his job change timing/what does he need to accomplish before he moves");// \n\u2022Sections must be filled.  Minimum of 15 characters.

	    $('#compensation_noteTD').attr('title', "\u2022What is his vision for his Future?/What is the logical next step in his career evolution? <br><br>\u2022Why does he want to change his job? <br><br>\u2022What is his job change timing/what does he need to accomplish before he moves <br><br>\u2022Sections must be filled.  Minimum of 15 characters.");

	    $('#past_note').attr('placeholder', "\u2022How many people work in his company \n\u2022How many branches of divisions \n\u2022What's the situation at his company, good, bad, hiring, firing, etc.");

	    $('#past_noteTD').attr('title', "\u2022How many people work in his company <br><br>\u2022How many branches of divisions <br><br>\u2022What's the situation at his company, good, bad, hiring, firing, etc.");
	});

	if($.browser.chrome) {
	   $('.inputs').css({'width':'300px'});
	   $('.inputsSkill').css({'width':'50px'});
	} else if ($.browser.mozilla) {
	   $('.inputs').css({'width':'302px'});
	   $('.inputsSkill').css({'width':'46px'});
	} else if ($.browser.msie) {
	   $('.inputs').css({'width':'300px'});
	}

	$('.formFieldTitle').css({'margin-left':'-90px'});
	$('.formFieldTitle').css({'margin-top':'-20px'});
	$('#topTextP').css({'margin-left':'-90px'});
	$('#topTextP').css({'margin-bottom':'-10px'});
	$('#topTextP2').css({'margin-left':'-90px'});
	$('#notifyBox_ID').parent().css({'margin-left':'-94px'});
	//$('#notifyBox_ID').parent().css({'margin-top':'-40px'});
	$('.fieldNamenotify_meeting_done').css({'margin-top':'-40px'});
	$('.fieldNamenotify_meeting_done').css({'margin-botom':'-40px'});

	$('#meeting_typeId').css({'margin-left':'-90px'});
	$('#meeting_typeId').css({'margin-top':'-20px'});
	$('#meetingDate').css({'margin-left':'-90px'});
	$('#meetingDate').css({'margin-bottom':'10px'});
	$('.formFieldTitle').css({'width':'935px'});
	$('.formFieldTitle').html('Add character assessment');
	$('.formLabel').hide();
	$('.formFieldRequired').hide();
	//$('.formLabel').css({'margin-left':'-156px'});
	$('.fieldNameevent_type').hide();

	$('#formSubmitButton').click(function(){
		var controlFlag = $('#ControlAllAreas').val();
		if(controlFlag == 'true')
		{
			var personality = $('#personality_note').val();
			var career = $('#career_note').val();
			var education = $('#education_note').val();
			var move = $('#move_note').val();
			var compensation = $('#compensation_note').val();

			var personality_length = personality.length;
			var career_length = career.length;
			var education_length = education.length;
			var move_length = move.length;
			var compensation_length = compensation.length;

			if(personality_length < 25)
			{
				$('#personality_note').css({'border-color':'red'});
				$('#personality_note').css({'background':'linear-gradient(-45deg, rgba(255, 255, 255, 0.2) 25%, rgba(0, 0, 0, 0) 25%, rgba(0, 0, 0, 0) 50%, rgba(255, 255, 255, 0.2) 50%, rgba(255, 255, 255, 0.2) 75%, rgba(0, 0, 0, 0) 75%, rgba(0, 0, 0, 0)) repeat scroll 0 0 / 10px 10px #f7dede'});
				$('#personality_note').css({'display':'inline-block'});
			}
			else
			{
				$('#personality_note').css({'border-color':'grey'});
				$('#personality_note').css({'background':''});
				$('#personality_note').css({'background-color':''});
			}

			if(career_length < 25)
			{
				$('#career_note').css({'border-color':'red'});
				$('#career_note').css({'background':'linear-gradient(-45deg, rgba(255, 255, 255, 0.2) 25%, rgba(0, 0, 0, 0) 25%, rgba(0, 0, 0, 0) 50%, rgba(255, 255, 255, 0.2) 50%, rgba(255, 255, 255, 0.2) 75%, rgba(0, 0, 0, 0) 75%, rgba(0, 0, 0, 0)) repeat scroll 0 0 / 10px 10px #f7dede'});
				$('#career_note').css({'display':'inline-block'});
			}
			else
			{
				$('#career_note').css({'border-color':'grey'});
				$('#career_note').css({'background':''});
				$('#career_note').css({'background-color':''});
			}

			if(education_length < 15)
			{
				$('#education_note').css({'border-color':'red'});
				$('#education_note').css({'background':'linear-gradient(-45deg, rgba(255, 255, 255, 0.2) 25%, rgba(0, 0, 0, 0) 25%, rgba(0, 0, 0, 0) 50%, rgba(255, 255, 255, 0.2) 50%, rgba(255, 255, 255, 0.2) 75%, rgba(0, 0, 0, 0) 75%, rgba(0, 0, 0, 0)) repeat scroll 0 0 / 10px 10px #f7dede'});
				$('#education_note').css({'display':'inline-block'});
			}
			else
			{
				$('#education_note').css({'border-color':'grey'});
				$('#education_note').css({'background':''});
				$('#education_note').css({'background-color':''});
			}

			if(move_length < 25)
			{
				$('#move_note').css({'border-color':'red'});
				$('#move_note').css({'background':'linear-gradient(-45deg, rgba(255, 255, 255, 0.2) 25%, rgba(0, 0, 0, 0) 25%, rgba(0, 0, 0, 0) 50%, rgba(255, 255, 255, 0.2) 50%, rgba(255, 255, 255, 0.2) 75%, rgba(0, 0, 0, 0) 75%, rgba(0, 0, 0, 0)) repeat scroll 0 0 / 10px 10px #f7dede'});
				$('#move_note').css({'display':'inline-block'});
			}
			else
			{
				$('#move_note').css({'border-color':'grey'});
				$('#move_note').css({'background':''});
				$('#move_note').css({'background-color':''});
			}

			if(compensation_length < 15)
			{
				$('#compensation_note').css({'border-color':'red'});
				$('#compensation_note').css({'background':'linear-gradient(-45deg, rgba(255, 255, 255, 0.2) 25%, rgba(0, 0, 0, 0) 25%, rgba(0, 0, 0, 0) 50%, rgba(255, 255, 255, 0.2) 50%, rgba(255, 255, 255, 0.2) 75%, rgba(0, 0, 0, 0) 75%, rgba(0, 0, 0, 0)) repeat scroll 0 0 / 10px 10px #f7dede'});
				$('#compensation_note').css({'display':'inline-block'});
			}
			else
			{
				$('#compensation_note').css({'border-color':'grey'});
				$('#compensation_note').css({'background':''});
				$('#compensation_note').css({'background-color':''});
			}
		}
		
	});

	var initialSkills = new Array();
	initialSkills['skill_ag'] = $('#skill_ag').val();
	initialSkills['skill_fx'] = $('#skill_fx').val();
	initialSkills['skill_ap'] = $('#skill_ap').val();
	initialSkills['skill_ch'] = $('#skill_ch').val();
	initialSkills['skill_am'] = $('#skill_am').val();
	initialSkills['skill_ed'] = $('#skill_ed').val();
	initialSkills['skill_mp'] = $('#skill_mp').val();
	initialSkills['skill_pl'] = $('#skill_pl').val();
	initialSkills['skill_in'] = $('#skill_in').val();
	initialSkills['skill_e']  = $('#skill_e').val();
	initialSkills['skill_ex'] = $('#skill_ex').val();

	$pressedKey = '';
	$(".inputsSkill").keypress(function(e){
	    $pressedKey = e.keyCode;
	});

	var first_click = new Array();
	$('.inputsSkill').change(function(e){
		var val = $(this).val();
		var id = e.target.id;

		if(typeof first_click[id] == 'undefined' && initialSkills[id] == '0' && $pressedKey == '')
		{
			first_click[id] = id;
			$('#'+id).val(5);
		}
		$pressedKey = '';

		var controlFlag = $('#ControlAllAreas').val();
		if(controlFlag == 'true')
		{
			if(val < 1 || val > 9)
			{
				alert("All skill areas should have a value between 1 - 9.");
			}
		}

	});

</script>

<style>
	.box {
	    /*margin: 0 auto !important;*/
	    /*margin-top:15px !important;*/
	    margin-left: -100px !important;
	    border-collapse: collapse;
	}

	.box2 {
	    /*margin: 0 auto !important;*/
	    /*margin-top:15px !important;*/
	    /*margin-left: -50px !important;*/
	    border-collapse: collapse;
	}

	.box3 {
	    /*margin: 0 auto !important;*/
	    /*margin-top:15px !important;*/
	    margin-left: -100px !important;
	    border-collapse: collapse;
	    /*margin-top: -50px;*/
	    margin-bottom: 10px;
	}

	.titles{
		font-size: 9pt;
		text-align: center !important;
		background-color: #EEEEEE !important;
		width:304px !important;
		height:30px !important;
		font-weight: bold !important;
		padding-top: 12px;
	    border-left: 1px solid grey;
		border-right: 1px solid grey;
		border-top: 1px solid grey;
		vertical-align: middle;

	}

	.titlesSkill{
		text-align: center !important;
		background-color: #EEEEEE !important;
		width:52px !important;
		height:15px !important;
		font-weight: bold !important;
		/*padding-top: 10px;*/
	    border-left: 1px solid grey;
		border-right: 1px solid grey;
		border-top: 1px solid grey;
		border-bottom: 1px solid grey;

	}

	.inputsSkill{
		/*width:50px !important;*/
		margin-top: -50px;
		text-align: center !important;

	}
	input[type='number']::-webkit-inner-spin-button,
	input[type='number']::-webkit-outer-spin-button {
		text-align: center!important;
    	opacity: 1;
	}

	.inputs{
		/*width:300px !important;*/
		margin-top: -10px;
		height: 110px;
	}

	.tdTitle{
	    padding-top: 30px !important;
	}

	.tdTitleSkill{
		padding-top: 50px !important;
		z-index: 999;
	}

	.inputsSkillTd{
		/*margin-top: -10px !important;*/
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

	.alert-success {
	  background-image: -webkit-linear-gradient(top, #dff0d8 0%, #c8e5bc 100%);
	  background-image:      -o-linear-gradient(top, #dff0d8 0%, #c8e5bc 100%);
	  background-image: -webkit-gradient(linear, left top, left bottom, from(#dff0d8), to(#c8e5bc));
	  background-image:         linear-gradient(to bottom, #dff0d8 0%, #c8e5bc 100%);
	  filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#ffdff0d8', endColorstr='#ffc8e5bc', GradientType=0);
	  background-repeat: repeat-x;
	  border-color: #b2dba1;
	}

	.alert {
	  text-shadow: 0 1px 0 rgba(255, 255, 255, .2);
	  -webkit-box-shadow: inset 0 1px 0 rgba(255, 255, 255, .25), 0 1px 2px rgba(0, 0, 0, .05);
	          box-shadow: inset 0 1px 0 rgba(255, 255, 255, .25), 0 1px 2px rgba(0, 0, 0, .05);
	}

	.info{
		/*padding-left: 20px !important;*/
		padding-top: 5px !important;
	}

	::-webkit-input-placeholder
	{
		padding-left:3px;
		font-size: 9pt;
		color :black;
		font-style: italic;
	}
	:-moz-placeholder { /* older Firefox*/
		padding-left:3px;
		font-size: 9pt;
		color :black;
		font-style: italic;
	}
	::-moz-placeholder { /* Firefox 19+ */
		padding-left:3px;
		font-size: 9pt;
		color :black;
		font-style: italic;
	}
	:-ms-input-placeholder {
		padding-left:3px;
		font-size: 9pt;
		color :black;
		font-style: italic;
	}


</style>

<script>
	$(document).ready(function(){
	    $('[data-toggle="tooltip"]').tooltip();
	});
</script>

<table class="box3" align="center">
	<tr>
		<td class="info"><div style='text-align: center; width: 950px; height:24px; padding-top:7px !important; font-weight: bold; font-size:12pt !important;' class="alert alert-danger">Focus on your candidate's career process and placability when you are writing comments.</div></td>
	</tr>

</table>
<input type="hidden" id='hiddenCharacter' name="hiddenCharacter" value="newForm">
<input type="hidden" id='ControlAllAreas' name="ControlAllAreas" value="<?php if(isset($ControlAllAreas)){echo $ControlAllAreas;} ?>">
<input type="hidden" id='EditTheNotes' name="EditTheNotes" value="<?php if(isset($EditTheNotes)){echo $EditTheNotes;} ?>">
<table class="box" align="center">
	<tr>
		<td
		style='padding-top: 0px !important;' class='tdTitle'><p id='personality_noteTD' onmouseover=" $(this).tooltip({content: function(){ return $(this).attr('title'); }}).mouseenter();" onmouseout="$('.ui-tooltip-content').parents('div').remove();" data-toggle="tooltip" title="" class='titles'>What Does he/she do/Skills?</p>
		</td>

		<td style='padding-top: 0px !important;' class='tdTitle'><p  id='career_noteTD' onmouseover=" $(this).tooltip({content: function(){ return $(this).attr('title'); }}).mouseenter();" onmouseout="$('.ui-tooltip-content').parents('div').remove();" data-toggle="tooltip" title="" class='titles'>Management and Leadership</p></td>
		<td style='padding-top: 0px !important;' class='tdTitle'><p id='education_noteTD' onmouseover=" $(this).tooltip({content: function(){ return $(this).attr('title'); }}).mouseenter();" onmouseout="$('.ui-tooltip-content').parents('div').remove();" data-toggle="tooltip" title="" class='titles'>Major career accomplishments/Education</p></td>
	<tr>
	<tr>
		<td><textarea  id='personality_note' name='personality_note' class='inputs'><?php if(isset($personality_note)){echo $personality_note;} ?></textarea></td>
		<td><textarea id='career_note' name='career_note' class='inputs'><?php if(isset($career_note)){echo $career_note;} ?></textarea></td>
		<td><textarea id='education_note' name='education_note' class='inputs'><?php if(isset($education_note)){echo $education_note;} ?></textarea></td>
	</tr>
</table>

<table class="box" align="center">
	<tr>
		<td class='tdTitle'><p id='move_noteTD' onmouseover=" $(this).tooltip({content: function(){ return $(this).attr('title'); }}).mouseenter();" onmouseout="$('.ui-tooltip-content').parents('div').remove();" data-toggle="tooltip" title="" class='titles'>Presence and Communication</p></td>
		<td class='tdTitle'><p id='compensation_noteTD' onmouseover=" $(this).tooltip({content: function(){ return $(this).attr('title'); }}).mouseenter();" onmouseout="$('.ui-tooltip-content').parents('div').remove();" data-toggle="tooltip" title=""  class='titles'>Career Plan and Compensation</p></td>
		<td class='tdTitle'><p id='past_noteTD' onmouseover=" $(this).tooltip({content: function(){ return $(this).attr('title'); }}).mouseenter();" onmouseout="$('.ui-tooltip-content').parents('div').remove();" data-toggle="tooltip" title=""  class='titles'>Current Company Description/Recent Intros</p></td>
	<tr>
	<tr>
		<td><textarea id='move_note' name='move_note' class='inputs'><?php if(isset($move_note)){echo $move_note;} ?></textarea></td>
		<td><textarea placeholder="&#149;Sections must be filled.  Minimum of 15 characters." id='compensation_note' name='compensation_note' class='inputs'><?php if(isset($compensation_note)){echo $compensation_note;} ?></textarea></td>
		<td><textarea placeholder="" id='past_note' name='past_note' class='inputs'><?php if(isset($past_note)){echo $past_note;} ?></textarea></td>
	</tr>
</table>

<table class="box" align="center">
	<tr>
		<td>Salary</td>
		<td><div class="general_form_column">
						<input class="salary_field" type="text" name="salary" value="<?php if(isset($candidate_salary)){ echo $candidate_salary; }?>" />
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
					</div></td>
	</tr>
</table>

<table class="box2" align="center">
	<tr>
		<td class='tdTitleSkill'><p class='titlesSkill'>AG</p></td>
		<td class='tdTitleSkill'><p class='titlesSkill'>FX</p></td>
		<td class='tdTitleSkill'><p class='titlesSkill'>AP</p></td>
		<td class='tdTitleSkill'><p class='titlesSkill'>CH</p></td>
		<td class='tdTitleSkill'><p class='titlesSkill'>AM</p></td>
		<td class='tdTitleSkill'><p class='titlesSkill'>ED</p></td>
		<td class='tdTitleSkill'><p class='titlesSkill'>MP</p></td>
		<td class='tdTitleSkill'><p class='titlesSkill'>PL</p></td>
		<td class='tdTitleSkill'><p class='titlesSkill'>IN</p></td>
		<td class='tdTitleSkill'><p class='titlesSkill'>ENG</p></td>
		<td class='tdTitleSkill'><p class='titlesSkill'>EX</p></td>
	<tr>
	<tr>
		<td class='inputsSkillTd'><input min="0" max="9" id='skill_ag' name='skill_ag' value='<?php echo $skillArray['skill_ag']; ?>' type='number' class='inputsSkill'></input></td>
		<td class='inputsSkillTd'><input min="0" max="9" id='skill_fx' name='skill_fx' value='<?php echo $skillArray['skill_fx']; ?>' type='number' class='inputsSkill'></input></td>
		<td class='inputsSkillTd'><input min="0" max="9" id='skill_ap' name='skill_ap' value='<?php echo $skillArray['skill_ap']; ?>' type='number' class='inputsSkill'></input></td>
		<td class='inputsSkillTd'><input min="0" max="9" id='skill_ch' name='skill_ch' value='<?php echo $skillArray['skill_ch']; ?>' type='number' class='inputsSkill'></input></td>
		<td class='inputsSkillTd'><input min="0" max="9" id='skill_am' name='skill_am' value='<?php echo $skillArray['skill_am']; ?>' type='number' class='inputsSkill'></input></td>
		<td class='inputsSkillTd'><input min="0" max="9" id='skill_ed' name='skill_ed' value='<?php echo $skillArray['skill_ed']; ?>' type='number' class='inputsSkill'></input></td>
		<td class='inputsSkillTd'><input min="0" max="9" id='skill_mp' name='skill_mp' value='<?php echo $skillArray['skill_mp']; ?>' type='number' class='inputsSkill'></input></td>
		<td class='inputsSkillTd'><input min="0" max="9" id='skill_pl' name='skill_pl' value='<?php echo $skillArray['skill_pl']; ?>' type='number' class='inputsSkill'></input></td>
		<td class='inputsSkillTd'><input min="0" max="9" id='skill_in' name='skill_in' value='<?php echo $skillArray['skill_in']; ?>' type='number' class='inputsSkill'></input></td>
		<td class='inputsSkillTd'><input min="0" max="9" id='skill_e' name='skill_e' value='<?php echo $skillArray['skill_e']; ?>' type='number' class='inputsSkill'></input></td>
		<td class='inputsSkillTd'><input min="0" max="9" id='skill_ex' name='skill_ex' value='<?php echo $skillArray['skill_ex']; ?>' type='number' class='inputsSkill'></input></td>
	</tr>
</table>