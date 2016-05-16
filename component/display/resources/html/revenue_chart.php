<!DOCTYPE html>
<html>
<head>

</head>
<body>

<?php $total_consultant_count = 0 ?>
<div style="text-align: center;">
	<table style="margin: 0 auto; text-align: left;">
		<tr>
			<td>
				<table class="revenue_table">
					<tr valign="top">
						<th style="font-size:400%; white-space: nowrap;" class="text_center" colspan="6"><?php echo ucfirst($location); ?> - Individual Revenue Consultants <?php echo $year; ?></th>
					</tr>
					<tr>
						<th style="height: 39px; font-size: 300%;" class="text_center">Rank</th>
						<th style="height: 39px; font-size: 300%;" class="text_center">Name</th>
						<th style="height: 39px;" class="text_center"></th>
						<th style="height: 39px; font-size: 300%;" class="text_center">Signed</th>
						<th style="height: 39px; font-size: 300%;" class="text_center">Paid</th>
						<!--<th class="text_center">Team</th>-->
						<th style="height: 39px; font-size: 300%;" class="text_center">Placed</th>
					</tr>

					<?php
						foreach ($revenue_data as $key => $value):

							if ($key == 'former' && empty($value['signed']))
								continue;

							if ($row_number_rank % 2 === 0)
								$even = ' even_row';
							else
								$even = '';

							if (empty($value['nationality']))
								$flag_pic = 'world_32.png';
							else if($value['nationality'] == "PK")
								$flag_pic = 'MNG_32.png';
							else
								$flag_pic = $value['nationality'].'_32.png';
					?>
					<?php if($value['userPosition'] == "Consultant" || $value['name'] == "Former"){ $total_consultant_count++;?>
						<tr class="hover_row<?php echo $even; ?>">
							<td style="font-size: 250%;" class="text_right"><?php echo $row_number_rank; ?></td>
							<td style="font-size: 250%;" class="text_center"><?php echo $value['name']; ?></td>
							<td class="text_center"><?php echo $display_object->getPicture('/common/pictures/flags/'.$flag_pic); ?></td>
							<td style="font-size: 250%;" class="text_right">&yen;<?php echo number_format($value['signed'], $decimals, '.', ','); ?></td>
							<td style="font-size: 250%;" class="text_right">&yen;<?php echo number_format($value['paid'], $decimals, '.', ','); ?></td>
							<!--<td class="text_center"><?php echo $value['team']; ?></td>-->
							<td style="font-size: 250%;" class="text_right"><?php echo $value['placed']; ?></td>
						</tr>

						<?php
							$row_number_rank += 1;

							$total_paid += $value['paid'];
							$total_signed += $value['signed'];
							$total_placed += $value['placed'];
					}
						endforeach;
					?>

					<tr class="revenue_table_footer">
						<td style="font-size: 250%;" class="text_center" colspan="3">Total</td>
						<td style="font-size: 250%;" class="text_right">&yen;<?php echo number_format($total_signed, $decimals, '.', ','); ?></td>
						<td style="font-size: 250%;" class="text_right">&yen;<?php echo number_format($total_paid, $decimals, '.', ','); ?></td>
						<td style="font-size: 250%;" class="text_right"><?php echo $total_placed; ?></td>
					</tr>
				</table>
			</td>
			<td>
			<table class="revenue_table">
					<tr valign="top">
						<th style="font-size: 400%; white-space: nowrap;"  class="text_center" colspan="6"><?php echo ucfirst($location); ?> - Individual Revenue Researchers <?php echo $year; ?></th>
					</tr>
					<tr>
						<th style="height: 39px; font-size: 300%;" class="text_center">Rank</th>
						<th style="height: 39px; font-size: 300%;" class="text_center">Name</th>
						<th style="height: 39px; font-size: 300%;" class="text_center"></th>
						<th style="height: 39px; font-size: 300%;" class="text_center">Signed</th>
						<th style="height: 39px; font-size: 300%;" class="text_center">Paid</th>
						<!--<th class="text_center">Team</th>-->
						<th style=" height: 39px; font-size: 300%;" class="text_center">Placed</th>
					</tr>

					<?php
						$total_paid_researcher = 0;
						$total_signed_researcher = 0;
						$total_placed_researcher = 0;
						$researcher_rank = 0;
						foreach ($revenue_data as $key => $value):

							if ($key == 'former' && empty($value['signed']))
								continue;

							if ($row_number_rank % 2 === 0)
								$even = ' even_row';
							else
								$even = '';

							if (empty($value['nationality']))
								$flag_pic = 'world_32.png';
							else if($value['nationality'] == "PK")
								$flag_pic = 'MNG_32.png';
							else
								$flag_pic = $value['nationality'].'_32.png';
					?>
					<?php if($value['userPosition'] == "Researcher"){
						$researcher_rank ++;
						$total_consultant_count--;?>
						<tr class="hover_row<?php echo $even; ?>">
							<td style="font-size: 250%;" class="text_right"><?php echo $researcher_rank; ?></td>
							<td style="font-size: 250%;" class="text_center"><?php echo $value['name']; ?></td>
							<td style="font-size: 250%;" class="text_center"><?php echo $display_object->getPicture('/common/pictures/flags/'.$flag_pic); ?></td>
							<td style="font-size: 250%;" class="text_right">&yen;<?php echo number_format($value['signed'], $decimals, '.', ','); ?></td>
							<td style="font-size: 250%;" class="text_right">&yen;<?php echo number_format($value['paid'], $decimals, '.', ','); ?></td>
							<!--<td class="text_center"><?php echo $value['team']; ?></td>-->
							<td style="font-size: 250%;" class="text_right"><?php echo $value['placed']; ?></td>
						</tr>

						<?php
							$row_number_rank += 1;

							//$total_paid += $value['paid'];
							$total_paid_researcher += $value['paid'];
							//$total_signed += $value['signed'];
							$total_signed_researcher += $value['signed'];
							//$total_placed += $value['placed'];
							$total_placed_researcher += $value['placed'];
					}
						endforeach;
					?>
					<?php if($total_consultant_count>0)
					{
						for ($i=0; $i < $total_consultant_count ; $i++) { 
							echo "
							<tr>
								<td align='right' style='height: 39px;'>-</td>
								<td style='height: 39px;'><center>-</center></td>
								<td style='height: 39px;'><center>-</center></td>
								<td style='height: 39px;'><center>-</center></td>
								<td style='height: 39px;'><center>-</center></td>
								<td align='right' style='height: 39px;'>-</td>
							</tr>
							";
						}
					} ?>
					<tr class="revenue_table_footer">
						<td style="font-size: 250%;" class="text_center" colspan="3">Total</td>
						<td style="font-size: 250%;" class="text_right">&yen;<?php echo number_format($total_signed_researcher, $decimals, '.', ','); ?></td>
						<td style="font-size: 250%;" class="text_right">&yen;<?php echo number_format($total_paid_researcher, $decimals, '.', ','); ?></td>
						<td style="font-size: 250%;" class="text_right"><?php echo $total_placed_researcher; ?></td>
					</tr>
				</table>
			</td>
		</tr>
	</table>
</div>
</body>

<script>
	var url = '<?php echo $url; ?>';
	var swap_time = <?php echo $swap_time; ?>;

	$('.scrollingContainer').css('overflow', 'auto');
	/*setTimeout(function() {
		window.location.replace(url);
	}, (swap_time));*/
</script>

</html>