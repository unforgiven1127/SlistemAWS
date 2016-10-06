

<?php $total_consultant_count = 0 ?>
<table style="width:100%;" valign="top">
	<tr>
		<td style="width:100%;" valign="top">
		<table style="width:100%;" valign="top">
		<tr >
			<td style="width:50%;" valign="top" >
				<table class="revenue_table">
					<tr style="width:100%;">
						<th style="overflow:hidden; width:100%; font-size:390%; white-space: nowrap;" class="text_center" colspan="6"><?php echo ucfirst($location); ?> - Individual Revenue Consultants <?php echo $year; ?></th>
					</tr>
					<tr>
						<th style="overflow:hidden;height: 50px; font-size: 350%;" class="text_center">Rank</th>
						<th style="overflow:hidden;height: 50px; font-size: 350%;" class="text_center">Flag</th>
						<th style="overflow:hidden;height: 50px; font-size: 350%;" class="text_center">Name</th>
						<th style="overflow:hidden;height: 50px; font-size: 350%;" class="text_center">Signed</th>
						<th style="overflow:hidden;height: 50px; font-size: 350%;" class="text_center">Paid</th>
						<th style="overflow:hidden;height: 50px; font-size: 350%;" class="text_center">Placed</th>
						<!--<th class="text_center">Team</th>-->
					</tr>

					<?php
						foreach ($revenue_data['Consultant'] as $key => $value):
							if ($key == 'former' && empty($value['consultant']['signed']))
								continue;
							if($value['consultant']['signed'] == 0 && $value['consultant']['paid'] == 0)
							{
								$value['Consultant']['placed'] = 0;
							}
							if ($row_number_rank % 2 === 0)
								$even = ' even_row';
							else
								$even = '';

							if (empty($value['Consultant']['nationality']))
								$flag_pic = 'world_32.png';
							else
								$flag_pic = $value['Consultant']['nationality'].'_32.png';
					?>
					<?php if(($value['Consultant']['userPosition'] == "Consultant" || $value['userPosition'] == "Not defined")){ $total_consultant_count++;?>
						<tr class="hover_row<?php echo $even; ?>">
							<td style="height: 10%; font-size: 300%;" class="text_center"><?php echo $row_number_rank; ?></td>
							<td style="height: 10%; font-size: 300%;" class="text_center"><?php echo $display_object->getPicture('/common/pictures/flags/'.$flag_pic); ?></td>
							<td style="height: 10%; font-size: 300%;" class="text_center"><?php echo $value['Consultant']['name']; ?></td>
							<td style="height: 10%; font-size: 300%;" class="text_right">&yen;<?php echo number_format($value['consultant']['signed'], $decimals, '.', ','); ?></td>
							<td style="height: 10%; font-size: 300%;" class="text_right">&yen;<?php echo number_format($value['consultant']['paid'], $decimals, '.', ','); ?></td>
							<td style="height: 10%; font-size: 300%;" class="text_center"><?php echo $value['Consultant']['placed']; ?></td>
							<!--<td class="text_center"><?php echo $value['team']; ?></td>-->
						</tr>

						<?php
							$row_number_rank += 1;

							$total_paid += $value['consultant']['paid'];
							$total_signed += $value['consultant']['signed'];
							$total_placed += $value['Consultant']['placed'];
					}
						endforeach;
					?>

					<tr class="revenue_table_footer">
						<td style="height: 40px; font-size: 300%;" class="text_center" colspan="3">Total</td>
						<td style="height: 40px; font-size: 300%;" class="text_right">&yen;<?php echo number_format($total_signed, $decimals, '.', ','); ?></td>
						<td style="height: 40px; font-size: 300%;" class="text_right">&yen;<?php echo number_format($total_paid, $decimals, '.', ','); ?></td>
						<td style="height: 40px; font-size: 300%;" class="text_center"><?php echo $total_placed; ?></td>
					</tr>
				</table>
			</td>
	</table>
		</td>
	</tr>
</table>


<script>
	//var url = '<?php echo $url; ?>';
	//var swap_time = <?php echo $swap_time; ?>;

	//$('.scrollingContainer').css('overflow', 'auto');
	//document.getElementById('componentContainerId').setAttribute("style","margin-top:-40px");

	//document.getElementById('footerId').remove();
	//$('#componentContainerId').css('margin-top','-48px;');
	setTimeout(function() {
		alert('test');
		//window.location.replace(url);
	}, 5000);


</script>
