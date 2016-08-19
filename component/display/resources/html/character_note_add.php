<form name="evtAddForm" enctype="multipart/form-data" submitAjax="1"  action="https://beta1.slate.co.jp/index.php5?uid=555-004&ppa=ppasa&ppt=event&ppk=0&pg=ajx"  class="fullPageForm"  method="POST"  id="evtAddFormId"  onBeforeSubmit=""  onsubmit="">
	<div id='evtAddFormInnerId' class="innerForm" >
		<div class="formFieldContainer fieldNamecp_uid formFieldWidth1  formFieldHidden " >
			<div class="formField">
				<input type="hidden" name="cp_uid"  value="555-001"  inajax=""  id="fldid_57b66cff5736a"  />
			</div>
			<div class="floatHack" ></div>
		</div>
		<div class="formFieldContainer fieldNamecp_action formFieldWidth1  formFieldHidden " >
			<div class="formField">
				<input type="hidden" name="cp_action"  value="ppav"  inajax=""  id="fldid_57b66cff5743a"  />
			</div>
			<div class="floatHack" ></div>
		</div>
		<div class="formFieldContainer fieldNamecp_type formFieldWidth1  formFieldHidden " >
			<div class="formField">
				<input type="hidden" name="cp_type"  value="candi"  inajax=""  id="fldid_57b66cff57500"  />
			</div>
			<div class="floatHack" ></div>
		</div>
		<div class="formFieldContainer fieldNamecp_pk formFieldWidth1  formFieldHidden " >
			<div class="formField">
				<input type="hidden" name="cp_pk"  value="410873"  inajax=""  id="fldid_57b66cff575c4"  />
			</div>
			<div class="floatHack" ></div>
		</div>
		<div class="formFieldContainer fieldNameno_candi_refresh formFieldWidth1  formFieldHidden " >
			<div class="formField">
				<input type="hidden" name="no_candi_refresh"  value="0"  inajax=""  id="fldid_57b66cff57688"  />
			</div>
			<div class="floatHack" ></div>
		</div>
		<div class="formFieldContainer fieldName57b66cff56985 formFieldWidth1 " >
			<div inajax=""  id="57b66cff56985Id"  class=" formFieldTitle" >Add a note</div>
			<div class="floatHack" ></div>
		</div>
		<div class="formFieldContainer fieldNameevent_type formFieldWidth1 " >
			<div class="formLabel">Note type</div>
			<div class="formField">
				<select name="event_type"  jsControl="jsFieldNotEmpty@|"  label="Note type"  onchange="if($(this).val() == 'character'){ $(this).closest('.ui-dialog').find('.note_tip_container').show(); } else { $(this).closest('.ui-dialog').find('.note_tip_container').hide(); } "  inajax=""  id="event_typeId"  class="formFieldRequired"  title=" Field required" >
					<option  value="character"  selected="selected" >Character note</option>
				</select>
			</div>
			<div class="floatHack" ></div>
		</div>
		<div class="formFieldContainer fieldNamedate_event formFieldWidth1  formFieldHidden " >
			<div class="formLabel">Date</div>
			<div class="formField">
				<input type="hidden" name="date_event"  value="2016-08-19 11:20"  inajax=""  id="fldid_57b66cff57786"  />
			</div>
			<div class="floatHack" ></div>
		</div>
		<div class="formFieldContainer fieldNamepersonality_note formFieldWidth1 " >
			<div class="formLabel">Personality & Communication</div>
			<div class="formField">
				<textarea name="personality_note"  placeholder="test"  isTinymce="1"  inajax=""  id="personality_noteId"  class=" tinymce hidden "  jsControl="jsFieldMinSize@2|jsFieldMaxSize@9000|"  ></textarea>
			</div>
			<div class="floatHack" ></div>
		</div>
		<div class="formFieldContainer fieldNamecurrent_podition_note formFieldWidth1 " >
			<div class="formLabel">Current Position & Responsibilities</div>
			<div class="formField">
				<script>initMce("current_podition_noteId"); </script>
				<textarea name="current_podition_note"  isTinymce="1"  inajax=""  id="current_podition_noteId"  class=" tinymce hidden "  jsControl="jsFieldMinSize@2|jsFieldMaxSize@9000|"  ></textarea>
			</div>
			<div class="floatHack" ></div>
		</div>
		<div class="formFieldContainer fieldNamecareer_note formFieldWidth1 " >
			<div class="formLabel">Career History</div>
			<div class="formField">
				<script>initMce("career_noteId"); </script>
				<textarea name="career_note"  isTinymce="1"  inajax=""  id="career_noteId"  class=" tinymce hidden "  jsControl="jsFieldMinSize@2|jsFieldMaxSize@9000|"  ></textarea>
			</div>
			<div class="floatHack" ></div>
		</div>
		<div class="formFieldContainer fieldNameproduct_exp_note formFieldWidth1 " >
			<div class="formLabel">Product or Technical Expertise</div>
			<div class="formField">
				<script>initMce("product_exp_noteId"); </script>
				<textarea name="product_exp_note"  isTinymce="1"  inajax=""  id="product_exp_noteId"  class=" tinymce hidden "  jsControl="jsFieldMinSize@2|jsFieldMaxSize@9000|"  ></textarea>
			</div>
			<div class="floatHack" ></div>
		</div>
		<div class="formFieldContainer fieldNameeducation_note formFieldWidth1 " >
			<div class="formLabel">Education & Training</div>
			<div class="formField">
				<script>initMce("education_noteId"); </script>
				<textarea name="education_note"  isTinymce="1"  inajax=""  id="education_noteId"  class=" tinymce hidden "  jsControl="jsFieldMinSize@2|jsFieldMaxSize@9000|"  ></textarea>
			</div>
			<div class="floatHack" ></div>
		</div>
		<div class="formFieldContainer fieldNamemove_note formFieldWidth1 " >
			<div class="formLabel">Reason for moving</div>
			<div class="formField">
				<script>initMce("move_noteId"); </script>
				<textarea name="move_note"  isTinymce="1"  inajax=""  id="move_noteId"  class=" tinymce hidden "  jsControl="jsFieldMinSize@2|jsFieldMaxSize@9000|"  ></textarea>
			</div>
			<div class="floatHack" ></div>
		</div>
		<div class="formFieldContainer fieldNametimeline_note formFieldWidth1 " >
			<div class="formLabel">Move timeline</div>
			<div class="formField">
				<script>initMce("timeline_noteId"); </script>
				<textarea name="timeline_note"  isTinymce="1"  inajax=""  id="timeline_noteId"  class=" tinymce hidden "  jsControl="jsFieldMinSize@2|jsFieldMaxSize@9000|"  ></textarea>
			</div>
			<div class="floatHack" ></div>
		</div>
		<div class="formFieldContainer fieldNamecompensation_note formFieldWidth1 " >
			<div class="formLabel">Compensation Breakdown</div>
			<div class="formField">
				<script>initMce("compensation_noteId"); </script>
				<textarea name="compensation_note"  isTinymce="1"  inajax=""  id="compensation_noteId"  class=" tinymce hidden "  jsControl="jsFieldMinSize@2|jsFieldMaxSize@9000|"  ></textarea>
			</div>
			<div class="floatHack" ></div>
		</div>
		<div class="formFieldContainer fieldNamekeywants_note formFieldWidth1 " >
			<div class="formLabel">Key Wants</div>
			<div class="formField">
				<script>initMce("keywants_noteId"); </script>
				<textarea name="keywants_note"  isTinymce="1"  inajax=""  id="keywants_noteId"  class=" tinymce hidden "  jsControl="jsFieldMinSize@2|jsFieldMaxSize@9000|"  ></textarea>
			</div>
			<div class="floatHack" ></div>
		</div>
		<div class="formFieldContainer fieldNamepast_note formFieldWidth1 " >
			<div class="formLabel">Companies – introduced recently / bing pitched</div>
			<div class="formField">
				<script>initMce("past_noteId"); </script>
				<textarea name="past_note"  isTinymce="1"  inajax=""  id="past_noteId"  class=" tinymce hidden "  jsControl="jsFieldMinSize@2|jsFieldMaxSize@9000|"  ></textarea>
			</div>
			<div class="floatHack" ></div>
		</div>
		<div class='floatHack' >
			<div style='margin-left:150px; margim-top:10px;'>
				<table>
					<tr>
						<td style='width:30px !important;' >
							<p class='spinner_label2'>AG</p>
						</td>
						<td style='width:30px !important;' >
							<p class='spinner_label2'>AP</p>
						</td>
						<td style='width:30px !important;' >
							<p class='spinner_label2'>AM</p>
						</td>
						<td style='width:30px !important;' >
							<p class='spinner_label2'>MP</p>
						</td>
						<td style='width:30px !important;' >
							<p class='spinner_label2'>IN</p>
						</td>
						<td style='width:30px !important;' >
							<p class='spinner_label2'>EX</p>
						</td>
						<td style='width:30px !important;' >
							<p class='spinner_label2'>FX</p>
						</td>
						<td style='width:30px !important;' >
							<p class='spinner_label2'>CH</p>
						</td>
						<td style='width:30px !important;' >
							<p class='spinner_label2'>ED</p>
						</td>
						<td style='width:30px !important;' >
							<p class='spinner_label2'>PL</p>
						</td>
						<td style='width:30px !important;' >
							<p class='spinner_label2'>E</p>
						</td>
					</tr>
					<tr>
						<td>
							<input type='text' style='width:30px;text-align: center;' name='skill_ag' value='0'/>
						</td>
						<td>
							<input type='text' style='width:30px;text-align: center;' name='skill_ap' value='0'/>
						</td>
						<td>
							<input type='text' style='width:30px;text-align: center;' name='skill_am' value='0'/>
						</td>
						<td>
							<input type='text' style='width:30px;text-align: center;' name='skill_mp' value='0'/>
						</td>
						<td>
							<input type='text' style='width:30px;text-align: center;' name='skill_in' value='0'/>
						</td>
						<td>
							<input type='text' style='width:30px;text-align: center;' name='skill_ex' value='0'/>
						</td>
						<td>
							<input type='text' style='width:30px;text-align: center;' name='skill_fx' value='0'/>
						</td>
						<td>
							<input type='text' style='width:30px;text-align: center;' name='skill_ch' value='0'/>
						</td>
						<td>
							<input type='text' style='width:30px;text-align: center;' name='skill_ed' value='0'/>
						</td>
						<td>
							<input type='text' style='width:30px;text-align: center;' name='skill_pl' value='0'/>
						</td>
						<td>
							<input type='text' style='width:30px;text-align: center;' name='skill_e' value='0'/>
						</td>
					</tr>
				</table>
			</div>
		</div>
		<div class="formFieldLinebreaker formFieldWidth1" >&nbsp;</div>
		<div class="submitBtnClass formFieldWidth1">
			<input type="submit" value="Save" onclick="" />
			<div class="floatHack" ></div>
		</div>
		<div class="floatHack" ></div>
	</div>
	<div class="floatHack" ></div>
</form>
<script>  $('form[name=evtAddForm]').submit(function(event)   {  event.preventDefault();       if(checkForm('evtAddForm'))     {       var sURL = $('form[name=evtAddForm]').attr('action');       var sFormId = $('form[name=evtAddForm]').attr('id');       var sAjaxTarget = '';       setTimeout(" AjaxRequest('"+sURL+"', '.body.', '"+sFormId+"', '"+sAjaxTarget+"', '', '', 'setCoverScreen(false);  '); ", 350);     }     return false;  }); </script>