<form action="" method="post">
	<div class="general_form_row" style="font-size: 16px;">
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
	<tr>
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
	<tr class="hover_row<?php echo $even; ?>">
	<!--<td><?php echo $arrayPosition ?></td>-->
	<!--<td><?php echo $value['position'] ?></td>-->
		<td class="name_column"><?php echo $value['name']; ?></td>
		<td>
			<div class="stat_holder">
			<?php echo $value['set']; ?>
			</div>
			<div class="stat_candi_info">
			<?php foreach ($value['set_meeting_info'] as $stat_info): ?>
				<div>
				<?php $url = $page_obj->getAjaxUrl('555-001', CONST_ACTION_VIEW, CONST_CANDIDATE_TYPE_CANDI, (int)$stat_info['candidate']); ?>
					<a href="javascript: view_candi('<?php echo $url; ?>')"><?php echo $stat_info['candidate']; ?></a>
				</div>
			<?php endforeach ?>
			</div>
		</td>
		<td>
			<div class="stat_holder">
			<?php echo $value['met']; ?>
			</div>
			<div class="stat_candi_info">
			<?php foreach ($value['met_meeting_info'] as $stat_info): ?>
				<div>
				<?php if(isset($stat_info['candidate'])){$candidate = $stat_info['candidate'];} else {$candidate = $stat_info['candidatefk'];}
				$url = $page_obj->getAjaxUrl('555-001', CONST_ACTION_VIEW, CONST_CANDIDATE_TYPE_CANDI, (int)$candidate); ?>
					<a href="javascript: view_candi('<?php echo $url; ?>')"><?php if(isset($stat_info['candidate'])){echo $stat_info['candidate'];}
					else if(isset($stat_info['candidatefk'])){echo $stat_info['candidatefk'];}
					else {echo $stat_info['candidate'];} ?></a>
				</div>
			<?php endforeach ?>
			</div>
		</td>
		<td>
			<div class="stat_holder">
			<?php echo $value['resumes_sent']; ?>
			</div>
			<div class="stat_candi_info">
			<?php foreach ($value['resumes_sent_info'] as $stat_info): ?>
				<div>
				<?php $url = $page_obj->getAjaxUrl('555-001', CONST_ACTION_VIEW, CONST_CANDIDATE_TYPE_CANDI, (int)$stat_info['candidate']); ?>
					<a href="javascript: view_candi('<?php echo $url; ?>')"><?php echo $stat_info['candidate']; ?></a>
				</div>
			<?php endforeach ?>
			</div>
		</td>
		<td>
			<div class="stat_holder">
			<?php echo $value['ccm1']; ?>
			</div>
			<div class="stat_candi_info">
			<?php foreach ($value['ccm1_info'] as $stat_info): if (empty($stat_info['candidate'])) continue; ?>
				<div>
				<?php $url = $page_obj->getAjaxUrl('555-001', CONST_ACTION_VIEW, CONST_CANDIDATE_TYPE_CANDI, (int)$stat_info['candidate']); ?>
					<a href="javascript: view_candi('<?php echo $url; ?>')"><?php echo $stat_info['candidate']; ?></a>
				</div>
			<?php endforeach ?>
			</div>
		</td>
		<td>
			<div class="stat_holder">
			<?php echo $value['ccm1_done']; ?>
			</div>
			<div class="stat_candi_info">
			<?php foreach ($value['ccm1_info'] as $stat_info): if (empty($stat_info['ccm_done_candidate'])) continue; ?>
				<div>
				<?php $url = $page_obj->getAjaxUrl('555-001', CONST_ACTION_VIEW, CONST_CANDIDATE_TYPE_CANDI, (int)$stat_info['ccm_done_candidate']); ?>
					<a href="javascript: view_candi('<?php echo $url; ?>')"><?php echo $stat_info['ccm_done_candidate']; ?></a>
				</div>
			<?php endforeach ?>
			</div>
		</td>
		<td>
			<div class="stat_holder">
			<?php echo $value['ccm2']; ?>
			</div>
			<div class="stat_candi_info">
			<?php foreach ($value['ccm2_info'] as $stat_info): if (empty($stat_info['candidate'])) continue; ?>
				<div>
				<?php $url = $page_obj->getAjaxUrl('555-001', CONST_ACTION_VIEW, CONST_CANDIDATE_TYPE_CANDI, (int)$stat_info['candidate']); ?>
					<a href="javascript: view_candi('<?php echo $url; ?>')"><?php echo $stat_info['candidate']; ?></a>
				</div>
			<?php endforeach ?>
			</div>
		</td>
		<td>
			<div class="stat_holder">
			<?php echo $value['ccm2_done']; ?>
			</div>
			<div class="stat_candi_info">
			<?php foreach ($value['ccm2_info'] as $stat_info): if (empty($stat_info['ccm_done_candidate'])) continue; ?>
				<div>
				<?php $url = $page_obj->getAjaxUrl('555-001', CONST_ACTION_VIEW, CONST_CANDIDATE_TYPE_CANDI, (int)$stat_info['ccm_done_candidate']); ?>
					<a href="javascript: view_candi('<?php echo $url; ?>')"><?php echo $stat_info['ccm_done_candidate']; ?></a>
				</div>
			<?php endforeach ?>
			</div>
		</td>
		<td>
			<div class="stat_holder">
			<?php echo $value['mccm']; ?>
			</div>
			<div class="stat_candi_info">
			<?php foreach ($value['mccm_info'] as $stat_info): if (empty($stat_info['candidate'])) continue; ?>
				<div>
				<?php $url = $page_obj->getAjaxUrl('555-001', CONST_ACTION_VIEW, CONST_CANDIDATE_TYPE_CANDI, (int)$stat_info['candidate']); ?>
					<a href="javascript: view_candi('<?php echo $url; ?>')"><?php echo $stat_info['candidate']; ?></a>
				</div>
			<?php endforeach ?>
			</div>
		</td>
		<td>
			<div class="stat_holder">
			<?php echo $value['mccm_done']; ?>
			</div>
			<div class="stat_candi_info">
			<?php
				foreach ($value['mccm_info'] as $stat_info) {
					if (empty($stat_info['ccm_done_candidate'])) continue;
					foreach ($stat_info['ccm_done_candidate'] as $candidate) {
			?>
				<div>
				<?php $url = $page_obj->getAjaxUrl('555-001', CONST_ACTION_VIEW, CONST_CANDIDATE_TYPE_CANDI, (int)$candidate); ?>
					<a href="javascript: view_candi('<?php echo $url; ?>')"><?php echo $candidate; ?></a>
				</div>
			<?php
					}
				}
			?>
			</div>
		</td>
		<td>
			<div class="stat_holder">
			<?php
				$total_ncm  = $total_ncm + $value['new_candidate_met_count'];
				echo $value['new_candidate_met_count'];
			?>
			</div>
			<div class="stat_candi_info">
			<?php foreach ($value['new_candidate_met_info'] as $stat_info): ?>
				<div>
				<?php $url = $page_obj->getAjaxUrl('555-001', CONST_ACTION_VIEW, CONST_CANDIDATE_TYPE_CANDI, (int)$stat_info['candidatefk']); ?>
					<a href="javascript: view_candi('<?php echo $url; ?>')"><?php echo $stat_info['candidatefk']; ?></a>
				</div>
			<?php endforeach ?>
			</div>
		</td>
		<td>
			<div class="stat_holder">
			<?php
				$total_ncip = $total_ncip + $value['new_candidates'];
				echo $value['new_candidates'];
			?>
			</div>
			<div class="stat_candi_info">
			<?php foreach ($value['new_candidate_info'] as $stat_info): ?>
				<div>
				<?php $url = $page_obj->getAjaxUrl('555-001', CONST_ACTION_VIEW, CONST_CANDIDATE_TYPE_CANDI, (int)$stat_info['candidatefk']); ?>
					<a href="javascript: view_candi('<?php echo $url; ?>')"><?php echo $stat_info['candidatefk']; ?></a>
				</div>
			<?php endforeach ?>
			</div>
		</td>
		<td>
			<div class="stat_holder">
			<?php
				$total_npip = $total_npip + $value['new_positions'];
				echo $value['new_positions'];
			?>
			</div>
			<div class="stat_candi_info">
			<?php foreach ($value['new_position_info'] as $stat_info): ?>
				<div>
				<?php $url = $page_obj->getAjaxUrl('555-005', CONST_ACTION_VIEW, CONST_POSITION_TYPE_JD, (int)$stat_info['positionfk']); ?>
					<a href="javascript: view_position('<?php echo $url; ?>')"><?php echo $stat_info['positionfk']; ?></a>
				</div>
			<?php endforeach ?>
			</div>
		</td>
		<td>
			<div class="stat_holder">
			<?php
				$total_o = $total_o + $value['offers_sent'];
				echo $value['offers_sent'];
			?>
			</div>
			<div class="stat_candi_info">
			<?php foreach ($value['offer_info'] as $stat_info): ?>
				<div>
				<?php $url = $page_obj->getAjaxUrl('555-001', CONST_ACTION_VIEW, CONST_CANDIDATE_TYPE_CANDI, (int)$stat_info['candidate']); ?>
					<a href="javascript: view_candi('<?php echo $url; ?>')"><?php echo $stat_info['candidate']; ?></a>
				</div>
			<?php endforeach ?>
			</div>
		</td>
		<td>
			<div class="stat_holder">
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
			<div class="stat_candi_info">
			<?php
			if($value['position'] == "Researcher")
			{$foreachValue = $value['placedRevenue_info'];}
			else
			{$foreachValue = $value['placed_info'];}
			foreach ($foreachValue as $stat_info): ?>
				<div>
				<?php $url = $page_obj->getAjaxUrl('555-001', CONST_ACTION_VIEW, CONST_CANDIDATE_TYPE_CANDI, (int)$stat_info['candidate']); ?>
					<a href="javascript: view_candi('<?php echo $url; ?>')"><?php echo $stat_info['candidate']; ?></a>
				</div>
			<?php endforeach ?>
			</div>
		</td>
	</tr>
	<?php } ?>
	<?php $row_number_rank += 1; ?>
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
			var sibling_obj_size = $($(this).siblings().get(0)).children().length;

			if (sibling_obj_size > 0)
				$(this).siblings().toggle();
		});
	});
</script>