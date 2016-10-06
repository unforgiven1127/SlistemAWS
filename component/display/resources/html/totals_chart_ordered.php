<script>
var nextloop = <?php echo $nextloop; ?>;
var loopFlag = false;
if(nextloop < 666)
{
	var loopFlag = true;
}

alert(loopFlag);
if(loopFlag)
{
	$('#getKpiForm').hide();
	$('#closeThis').hide();
	document.getElementById('componentContainerId').setAttribute("style","margin-top:-40px");

	//document.getElementById('footerId').remove();
	//$('#componentContainerId').css('margin-top','-48px;');
	//
	alert(nextloop);
	setTimeout(function()
	{
		var url = '/index.php5?uid=555-006&ppa=pprev&ppt=revenue&ppk=0&watercooler=1&nextloop='+nextloop;
		//alert('test');
		window.location.replace(url);
	}, 30000);
}

$(document).ready(function(){
    $('[data-toggle="tooltip"]').tooltip();
});
</script>

<form id="getKpiForm" action="" method="post">
	<div id="closeThis" class="general_form_row" style="font-size: 16px;">
		<div class="general_form_column">Start date: </div>
		<div class="general_form_column">
			<input id="start_date" style="width: 90px" type="text" name="start_date"
				value="<?php echo $start_date_original; ?>" />
		</div>
		<div class="general_form_column add_margin_left_20">End date: </div>
		<div class="general_form_column">
			<input id="end_date" style="width: 90px" type="text" name="end_date"
				value="<?php echo $end_date_original; ?>" />
		</div>
		<div class="general_form_column add_margin_left_10">
			<input type="submit" name="submit_totals" value="Get totals" />
		</div>
	</div>
</form>

<?php foreach ($stats_data as $key => $stat): $arrayPosition = $key ?>
	<?php
 	$total_ncm = 0;
	$total_ncip = 0;
	$total_npip = 0;
	$total_o = 0;
	$total_p = 0;
	 ?>
<table class="totals_table">
	<tr>
		<th colspan="15"><?php echo ucfirst($key); ?> totals - <?php echo date('M Y', strtotime($start_date)); ?></th>
	</tr>
	<tr id="head">
		<th class="name_column">Name</th>
		<th>Set</th>
		<th>Met</th>
		<th>Resumes sent</th>
		<th>CCM1 set</th>
		<th>CCM1 done</th>
		<th>CCM2 set</th>
		<th>CCM2 done</th>
		<th>MCCM set</th>
		<th>MCCM done</th>
		<th>New candidates met</th>
		<th>New candidates<br>in play</th>
		<th>New positions<br>in play</th>
		<th>Offer</th>
		<th>Placement</th>
	</tr>

	<?php $row_number_rank = 1; ?>

	<?php foreach ($stat as $key => $value): ?>
	<?php
	$flag = true;
	if($arrayPosition == "researcher")
	{
		if($value['position'] != "Researcher")
		{
			$flag = false;
		}
	}
	if($arrayPosition == "consultant")
	{
		if($value['position'] != "Consultant")
		{
			$flag = false;
		}
	}
	if($value['promoteFlag'] == "true")
	{
		$flag = true;
	}
	if($value['kpi_flag'] == "p")
	{
		$flag = false;
	}
	if ($row_number_rank % 2 === 0)
		$even = ' even_row';
	else
		$even = '';
	?>
	<?php if($flag){ ?>
	<?php $row_number_rank += 1; ?>
	<tr class="hover_row<?php echo $even; ?>">
	<!--<td><?php echo $arrayPosition ?></td>-->
	<!--<td><?php echo $value['position'] ?></td>-->
		<td class="name_column"><?php echo $value['name']; ?></td>
		<td>
			<div class="stat_holder" id="<?php echo $value['user_id']; ?>">
			<?php echo $value['set']; ?>
			</div>
			<div class="stat_candi_info <?php echo $value['user_id']; ?>">
			<?php
			$line = 1;
			 foreach($allCanidatesArray[$arrayPosition][$value['user_id']] as $candidate_id => $data):
			 	if ($line % 2 === 0)
			 	{
			 		if($even == '')
			 		{
			 			$colored_row = ' colored_row';
			 		}
					else
					{
						$colored_row = ' colored_row2';
					}
			 	}
				else
					$colored_row = '';
				$line ++;
			 	?>
				<div class="hover_row <?php echo $colored_row; ?>">
				<?php
					if(isset($data['setFlag']))
					{
						$url = $page_obj->getAjaxUrl('555-001', CONST_ACTION_VIEW, CONST_CANDIDATE_TYPE_CANDI, (int)$candidate_id);
						?>
						<a onmouseover=" $(this).tooltip({content: function(){ return $(this).attr('title'); }}).mouseenter();" onmouseout="$('.ui-tooltip-content').parents('div').remove();" data-toggle="tooltip" title="<?php echo $data['hoverTooltip']; ?>"  href="javascript: view_candi('<?php echo $url; ?>')"><?php echo $candidate_id; ?></a>
						<?php
					}
					else
					{
						$url = '';
						echo "<a href='javascript:'><center>-</center></a>";
					}
				?>
				</div>
			<?php endforeach ?>
			</div>
		</td>
		<td>
			<div class="stat_holder" id="<?php echo $value['user_id']; ?>">
			<?php echo $value['met']; ?>
			</div>
			<div class="stat_candi_info <?php echo $value['user_id']; ?>">
			<?php
			$line = 1;
			 foreach($allCanidatesArray[$arrayPosition][$value['user_id']] as $candidate_id => $data):
			 	if ($line % 2 === 0)
			 	{
			 		if($even == '')
			 		{
			 			$colored_row = ' colored_row';
			 		}
					else
					{
						$colored_row = ' colored_row2';
					}
			 	}
				else
					$colored_row = '';
				$line ++;?>
				<div class="hover_row<?php echo $colored_row; ?>">
				<?php
					if(isset($data['metFlag']))
					{
						$url = $page_obj->getAjaxUrl('555-001', CONST_ACTION_VIEW, CONST_CANDIDATE_TYPE_CANDI, (int)$candidate_id);
						?>
						<a onmouseover=" $(this).tooltip({content: function(){ return $(this).attr('title'); }}).mouseenter();" onmouseout="$('.ui-tooltip-content').parents('div').remove();" data-toggle="tooltip" title="<?php echo $data['hoverTooltip']; ?>" href="javascript: view_candi('<?php echo $url; ?>')"><?php echo $candidate_id; ?></a>
						<?php
					}
					else
					{
						$url = '';
						echo "<a href='javascript:'><center>-</center></a>";
					}
				?>
				</div>
			<?php endforeach ?>
			</div>
		</td>
		<td>
			<div class="stat_holder" id="<?php echo $value['user_id']; ?>">
			<?php echo $value['resumes_sent']; ?>
			</div>
			<div class="stat_candi_info <?php echo $value['user_id']; ?>">
			<?php
			$line = 1;
			 foreach($allCanidatesArray[$arrayPosition][$value['user_id']] as $candidate_id => $data):
			 	if ($line % 2 === 0)
			 	{
			 		if($even == '')
			 		{
			 			$colored_row = ' colored_row';
			 		}
					else
					{
						$colored_row = ' colored_row2';
					}
			 	}
				else
					$colored_row = '';
				$line ++;?>
				<div class="hover_row<?php echo $colored_row; ?>">
				<?php
					if(isset($data['resumeSentFlag']))
					{
						$url = $page_obj->getAjaxUrl('555-001', CONST_ACTION_VIEW, CONST_CANDIDATE_TYPE_CANDI, (int)$candidate_id);
						?>
						<a onmouseover=" $(this).tooltip({content: function(){ return $(this).attr('title'); }}).mouseenter();" onmouseout="$('.ui-tooltip-content').parents('div').remove();" data-toggle="tooltip" title="<?php echo $data['hoverTooltip']; ?>" href="javascript: view_candi('<?php echo $url; ?>')"><?php echo $candidate_id; ?></a>
						<?php
					}
					else
					{
						$url = '';
						echo "<a href='javascript:'><center>-</center></a>";
					}
				?>
				</div>
			<?php endforeach ?>
			</div>
		</td>
		<td>
			<div class="stat_holder" id="<?php echo $value['user_id']; ?>">
			<?php echo $value['ccm1']; ?>
			</div>
			<div class="stat_candi_info <?php echo $value['user_id']; ?>">
			<?php
			$line = 1;
			 foreach($allCanidatesArray[$arrayPosition][$value['user_id']] as $candidate_id => $data):
			 	if ($line % 2 === 0)
			 	{
			 		if($even == '')
			 		{
			 			$colored_row = ' colored_row';
			 		}
					else
					{
						$colored_row = ' colored_row2';
					}
			 	}
				else
					$colored_row = '';
				$line ++;?>
				<div class="hover_row<?php echo $colored_row; ?>">
				<?php
					if(isset($data['ccm1SetFlag']))
					{
						$url = $page_obj->getAjaxUrl('555-001', CONST_ACTION_VIEW, CONST_CANDIDATE_TYPE_CANDI, (int)$candidate_id);
						?>
						<a onmouseover=" $(this).tooltip({content: function(){ return $(this).attr('title'); }}).mouseenter();" onmouseout="$('.ui-tooltip-content').parents('div').remove();" data-toggle="tooltip" title="<?php echo $data['hoverTooltip']; ?>" href="javascript: view_candi('<?php echo $url; ?>')"><?php echo $candidate_id; ?></a>
						<?php
					}
					else
					{
						$url = '';
						echo "<a href='javascript:'><center>-</center></a>";
					}
				?>
				</div>
			<?php endforeach ?>
			</div>
		</td>
		<td>
			<div class="stat_holder" id="<?php echo $value['user_id']; ?>">
			<?php echo $value['ccm1_done']; ?>
			</div>
			<div class="stat_candi_info <?php echo $value['user_id']; ?>">
			<?php
			$line = 1;
			 foreach($allCanidatesArray[$arrayPosition][$value['user_id']] as $candidate_id => $data):
			 	if ($line % 2 === 0)
			 	{
			 		if($even == '')
			 		{
			 			$colored_row = ' colored_row';
			 		}
					else
					{
						$colored_row = ' colored_row2';
					}
			 	}
				else
					$colored_row = '';
				$line ++;
			 	?>
				<div class="hover_row <?php echo $colored_row; ?>">
				<?php
					if(isset($data['ccm1DoneFlag']))
					{
						$url = $page_obj->getAjaxUrl('555-001', CONST_ACTION_VIEW, CONST_CANDIDATE_TYPE_CANDI, (int)$candidate_id);
						?>
						<a onmouseover=" $(this).tooltip({content: function(){ return $(this).attr('title'); }}).mouseenter();" onmouseout="$('.ui-tooltip-content').parents('div').remove();" data-toggle="tooltip" title="<?php echo $data['hoverTooltip']; ?>" href="javascript: view_candi('<?php echo $url; ?>')"><?php echo $candidate_id; ?></a>
						<?php
					}
					else
					{
						$url = '';
						echo "<a href='javascript:'><center>-</center></a>";
					}
				?>
				</div>
			<?php endforeach ?>
			</div>
		</td>
		<td>
			<div class="stat_holder" id="<?php echo $value['user_id']; ?>">
			<?php echo $value['ccm2']; ?>
			</div>
			<div class="stat_candi_info <?php echo $value['user_id']; ?>">
			<?php
			$line = 1;
			 foreach($allCanidatesArray[$arrayPosition][$value['user_id']] as $candidate_id => $data):
			 	if ($line % 2 === 0)
			 	{
			 		if($even == '')
			 		{
			 			$colored_row = ' colored_row';
			 		}
					else
					{
						$colored_row = ' colored_row2';
					}
			 	}
				else
					$colored_row = '';
				$line ++;
			 	?>
				<div class="hover_row <?php echo $colored_row; ?>">
				<?php
					if(isset($data['ccm2SetFlag']))
					{
						$url = $page_obj->getAjaxUrl('555-001', CONST_ACTION_VIEW, CONST_CANDIDATE_TYPE_CANDI, (int)$candidate_id);
						?>
						<a onmouseover=" $(this).tooltip({content: function(){ return $(this).attr('title'); }}).mouseenter();" onmouseout="$('.ui-tooltip-content').parents('div').remove();" data-toggle="tooltip" title="<?php echo $data['hoverTooltip']; ?>" href="javascript: view_candi('<?php echo $url; ?>')"><?php echo $candidate_id; ?></a>
						<?php
					}
					else
					{
						$url = '';
						echo "<a href='javascript:'><center>-</center></a>";
					}
				?>
				</div>
			<?php endforeach ?>
			</div>
		</td>
		<td>
			<div class="stat_holder" id="<?php echo $value['user_id']; ?>">
			<?php echo $value['ccm2_done']; ?>
			</div>
			<div class="stat_candi_info <?php echo $value['user_id']; ?>">
			<?php
			$line = 1;
			 foreach($allCanidatesArray[$arrayPosition][$value['user_id']] as $candidate_id => $data):
			 	if ($line % 2 === 0)
			 	{
			 		if($even == '')
			 		{
			 			$colored_row = ' colored_row';
			 		}
					else
					{
						$colored_row = ' colored_row2';
					}
			 	}
				else
					$colored_row = '';
				$line ++;
			 	?>
				<div class="hover_row <?php echo $colored_row; ?>">
				<?php
					if(isset($data['ccm2DoneFlag']))
					{
						$url = $page_obj->getAjaxUrl('555-001', CONST_ACTION_VIEW, CONST_CANDIDATE_TYPE_CANDI, (int)$candidate_id);
						?>
						<a onmouseover=" $(this).tooltip({content: function(){ return $(this).attr('title'); }}).mouseenter();" onmouseout="$('.ui-tooltip-content').parents('div').remove();" data-toggle="tooltip" title="<?php echo $data['hoverTooltip']; ?>" href="javascript: view_candi('<?php echo $url; ?>')"><?php echo $candidate_id; ?></a>
						<?php
					}
					else
					{
						$url = '';
						echo "<a href='javascript:'><center>-</center></a>";
					}
				?>
				</div>
			<?php endforeach ?>
			</div>
		</td>
		<td>
			<div class="stat_holder" id="<?php echo $value['user_id']; ?>">
			<?php echo $value['mccm']; ?>
			</div>
			<div class="stat_candi_info <?php echo $value['user_id']; ?>">
			<?php
			$line = 1;
			 foreach($allCanidatesArray[$arrayPosition][$value['user_id']] as $candidate_id => $data):
			 	if ($line % 2 === 0)
			 	{
			 		if($even == '')
			 		{
			 			$colored_row = ' colored_row';
			 		}
					else
					{
						$colored_row = ' colored_row2';
					}
			 	}
				else
					$colored_row = '';
				$line ++;
			 	?>
				<div class="hover_row <?php echo $colored_row; ?>">
				<?php
					if(isset($data['mccmSetFlag']))
					{
						$url = $page_obj->getAjaxUrl('555-001', CONST_ACTION_VIEW, CONST_CANDIDATE_TYPE_CANDI, (int)$candidate_id);
						?>
						<a onmouseover=" $(this).tooltip({content: function(){ return $(this).attr('title'); }}).mouseenter();" onmouseout="$('.ui-tooltip-content').parents('div').remove();" data-toggle="tooltip" title="<?php echo $data['hoverTooltip']; ?>" href="javascript: view_candi('<?php echo $url; ?>')"><?php echo $candidate_id; ?></a>
						<?php
					}
					else
					{
						$url = '';
						echo "<a href='javascript:'><center>-</center></a>";
					}
				?>
				</div>
			<?php endforeach ?>
			</div>
		</td>
		<td>
			<div class="stat_holder" id="<?php echo $value['user_id']; ?>">
			<?php echo $value['mccm_done']; ?>
			</div>
			<div class="stat_candi_info <?php echo $value['user_id']; ?>">
			<?php
			$line = 1;
			 foreach($allCanidatesArray[$arrayPosition][$value['user_id']] as $candidate_id => $data):
			 	if ($line % 2 === 0)
			 	{
			 		if($even == '')
			 		{
			 			$colored_row = ' colored_row';
			 		}
					else
					{
						$colored_row = ' colored_row2';
					}
			 	}
				else
					$colored_row = '';
				$line ++;
			 	?>
				<div class="hover_row <?php echo $colored_row; ?>">
				<?php
					if(isset($data['mccmDoneFlag']))
					{
						$url = $page_obj->getAjaxUrl('555-001', CONST_ACTION_VIEW, CONST_CANDIDATE_TYPE_CANDI, (int)$candidate_id);
						?>
						<a onmouseover=" $(this).tooltip({content: function(){ return $(this).attr('title'); }}).mouseenter();" onmouseout="$('.ui-tooltip-content').parents('div').remove();" data-toggle="tooltip" title="<?php echo $data['hoverTooltip']; ?>" href="javascript: view_candi('<?php echo $url; ?>')"><?php echo $candidate_id; ?></a>
						<?php
					}
					else
					{
						$url = '';
						echo "<a href='javascript:'><center>-</center></a>";
					}
				?>
				</div>
			<?php endforeach ?>
			</div>
		</td>
		<td>
			<div class="stat_holder" id="<?php echo $value['user_id']; ?>">
			<?php
				$total_ncm  = $total_ncm + $value['new_candidate_met_count'];
				echo $value['new_candidate_met_count'];
			?>
			</div>
			<div class="stat_candi_info <?php echo $value['user_id']; ?>">
			<?php
			$line = 1;
			 foreach($allCanidatesArray[$arrayPosition][$value['user_id']] as $candidate_id => $data):
			 	if ($line % 2 === 0)
			 	{
			 		if($even == '')
			 		{
			 			$colored_row = ' colored_row';
			 		}
					else
					{
						$colored_row = ' colored_row2';
					}
			 	}
				else
					$colored_row = '';
				$line ++;
			 	?>
				<div class="hover_row <?php echo $colored_row; ?>">
				<?php
					if(isset($data['newCandiMetFlag']))
					{
						$url = $page_obj->getAjaxUrl('555-001', CONST_ACTION_VIEW, CONST_CANDIDATE_TYPE_CANDI, (int)$candidate_id);
						?>
						<a onmouseover=" $(this).tooltip({content: function(){ return $(this).attr('title'); }}).mouseenter();" onmouseout="$('.ui-tooltip-content').parents('div').remove();" data-toggle="tooltip" title="<?php echo $data['hoverTooltip']; ?>" href="javascript: view_candi('<?php echo $url; ?>')"><?php echo $candidate_id; ?></a>
						<?php
					}
					else
					{
						$url = '';
						echo "<a href='javascript:'><center>-</center></a>";
					}
				?>
				</div>
			<?php endforeach ?>
			</div>
		</td>
		<td>
			<div class="stat_holder" id="<?php echo $value['user_id']; ?>">
			<?php
				$total_ncip = $total_ncip + $value['new_candidates'];
				echo $value['new_candidates'];
			?>
			</div>
			<div class="stat_candi_info <?php echo $value['user_id']; ?>">
			<?php
			$line = 1;
			 foreach($allCanidatesArray[$arrayPosition][$value['user_id']] as $candidate_id => $data):
			 	if ($line % 2 === 0)
			 	{
			 		if($even == '')
			 		{
			 			$colored_row = ' colored_row';
			 		}
					else
					{
						$colored_row = ' colored_row2';
					}
			 	}
				else
					$colored_row = '';
				$line ++;
			 	?>
				<div class="hover_row <?php echo $colored_row; ?>">
				<?php
					if(isset($data['newCandiPlayFlag']))
					{
						$url = $page_obj->getAjaxUrl('555-001', CONST_ACTION_VIEW, CONST_CANDIDATE_TYPE_CANDI, (int)$candidate_id);
						?>
						<a onmouseover=" $(this).tooltip({content: function(){ return $(this).attr('title'); }}).mouseenter();" onmouseout="$('.ui-tooltip-content').parents('div').remove();" data-toggle="tooltip" title="<?php echo $data['hoverTooltip']; ?>" href="javascript: view_candi('<?php echo $url; ?>')"><?php echo $candidate_id; ?></a>
						<?php
					}
					else
					{
						$url = '';
						echo "<a href='javascript:'><center>-</center></a>";
					}
				?>
				</div>
			<?php endforeach ?>
			</div>
		</td>
		<td>
			<div class="stat_holder" id="<?php echo $value['user_id']; ?>">
			<?php
				$total_npip = $total_npip + $value['new_positions'];
				echo $value['new_positions'];
			?>
			</div>
			<div class="stat_candi_info <?php echo $value['user_id']; ?>">
			<?php
			$line = 1;
			 foreach($allCanidatesArray[$arrayPosition][$value['user_id']] as $candidate_id => $data):
			 	if ($line % 2 === 0)
			 	{
			 		if($even == '')
			 		{
			 			$colored_row = ' colored_row';
			 		}
					else
					{
						$colored_row = ' colored_row2';
					}
			 	}
				else
					$colored_row = '';
				$line ++;
			 	?>
				<div class="hover_row <?php echo $colored_row; ?>">
				<?php
					if(isset($data['newPositionPlayFlag']))
					{
						$url = $page_obj->getAjaxUrl('555-005',CONST_ACTION_VIEW,CONST_POSITION_TYPE_JD,(int)$data['newPositionPlayFlag']);
						//echo "<a href='javascript: view_candi(".$url.")'>".$data['newPositionPlayFlag']."</a>";
						?>
						<a data-toggle="tooltip" title="Candidate ID: <?php echo $candidate_id; ?>" href="javascript: view_candi('<?php echo $url; ?>')"><?php echo $data['newPositionPlayFlag']; ?></a>
						<?php
					}
					else
					{
						$url = '';
						echo "<a href='javascript:'><center>-</center></a>";
					}
				?>
				</div>
			<?php endforeach ?>
			</div>
		</td>
		<td>
			<div class="stat_holder" id="<?php echo $value['user_id']; ?>">
			<?php
				$total_o = $total_o + $value['offers_sent'];
				echo $value['offers_sent'];
			?>
			</div>
			<div class="stat_candi_info <?php echo $value['user_id']; ?>">
			<?php
			$line = 1;
			 foreach($allCanidatesArray[$arrayPosition][$value['user_id']] as $candidate_id => $data):
			 	if ($line % 2 === 0)
			 	{
			 		if($even == '')
			 		{
			 			$colored_row = ' colored_row';
			 		}
					else
					{
						$colored_row = ' colored_row2';
					}
			 	}
				else
					$colored_row = '';
				$line ++;
			 	?>
				<div class="hover_row <?php echo $colored_row; ?>">
				<?php
					if(isset($data['offerFlag']))
					{
						$url = $page_obj->getAjaxUrl('555-001', CONST_ACTION_VIEW, CONST_CANDIDATE_TYPE_CANDI, (int)$candidate_id);
						?>
						<a onmouseover=" $(this).tooltip({content: function(){ return $(this).attr('title'); }}).mouseenter();" onmouseout="$('.ui-tooltip-content').parents('div').remove();" data-toggle="tooltip" title="<?php echo $data['hoverTooltip']; ?>" href="javascript: view_candi('<?php echo $url; ?>')"><?php echo $candidate_id; ?></a>
						<?php
					}
					else
					{
						$url = '';
						echo "<a href='javascript:'><center>-</center></a>";
					}
				?>
				</div>
			<?php endforeach ?>
			</div>
		</td>
		<td>
			<div class="stat_holder" id="<?php echo $value['user_id']; ?>">
			<?php
				if($value['position'] == "Researcher")
				{
					$total_p = $total_p + $value['placedRevenue'];
					echo $value['placedRevenue']; }
				else
				{
					$total_p = $total_p + $value['placed'];
					echo $value['placed'];
				}
			 ?>
			</div>
			<div class="stat_candi_info <?php echo $value['user_id']; ?>">
			<?php
			$line = 1;
			 foreach($allCanidatesArray[$arrayPosition][$value['user_id']] as $candidate_id => $data):
			 	if ($line % 2 === 0)
			 	{
			 		if($even == '')
			 		{
			 			$colored_row = ' colored_row';
			 		}
					else
					{
						$colored_row = ' colored_row2';
					}
			 	}
				else
					$colored_row = '';
				$line ++;
			 	?>
				<div class="hover_row <?php echo $colored_row; ?>">
				<?php
					if(isset($data['placedFlag']))
					{
						$url = $page_obj->getAjaxUrl('555-001', CONST_ACTION_VIEW, CONST_CANDIDATE_TYPE_CANDI, (int)$candidate_id);
						?>
						<a onmouseover=" $(this).tooltip({content: function(){ return $(this).attr('title'); }}).mouseenter();" onmouseout="$('.ui-tooltip-content').parents('div').remove();" data-toggle="tooltip" title="<?php echo $data['hoverTooltip']; ?>" href="javascript: view_candi('<?php echo $url; ?>')"><?php echo $candidate_id; ?></a>
						<?php
					}
					else
					{
						$url = '';
						echo "<a href='javascript:'><center>-</center></a>";
					}
				?>
				</div>
			<?php endforeach ?>
			</div>
		</td>
	</tr>
	<?php } ?>

	<?php endforeach ?>
	<tr class="totals_table_footer">
	<!--<td colspan="15">&nbsp;</td></tr>-->
	<!--<tr bgcolor="#58FAAC"> -->
		<td colspan="10" class="text_right">
			Total :
		</td>
		<td>
			<?php echo $total_ncm; ?>
		</td>
		<td>
			<?php echo $total_ncip; ?>
		</td>
		<td>
			<?php echo $total_npip; ?>
		</td>
		<td>
			<?php echo $total_o; ?>
		</td>
		<td>
			<?php echo $total_p; ?>
		</td>
	</tr>
</table>

<div class="general_form_row" style="height: 20px;"></div>
<?php endforeach ?>

<script>
	$(function() {
		$("#start_date, #end_date").datepicker({
			showButtonPanel: true,
			changeYear: true,
			dateFormat: 'yy-mm-dd'
		});

		$('.stat_holder').click(function() {
			var newClass = $(this).attr("id");
			//alert(newClass);
			//$('.'+newClass).toggle(500,"linear");
			var options = {};
			$('.'+newClass).toggle( 'blind', options, 500 );
			//var sibling_obj_size = $($(this).siblings().get(0)).children().length;

			//if (sibling_obj_size > 0)
				//$(this).siblings().toggle();
		});
	});
</script>