<?php $total_consultant_count = 0 ?>
<table style="width:100%;" valign="top">
	<tr>
		<td style="width:100%;" valign="top">
		<table style="width:100%;" valign="top">
		<tr >
			<td style="width:50%;" valign="top" >
			<table class="revenue_table">
					<tr style="width:100%;">
						<th style="overflow:hidden;width:100% font-size: 390%; white-space: nowrap;"  class="text_center" colspan="7"><?php echo ucfirst($location); ?> - Individual Revenue Researchers <?php echo $year; ?></th>
					</tr>
					<tr>
						<th style="overflow:hidden;height: 50px; font-size: 350%;" class="text_center">Rank</th>
						<th style="overflow:hidden;height: 50px; font-size: 350%;" class="text_center">Flag</th>
						<th style="overflow:hidden;height: 50px; font-size: 350%;" class="text_center">Name</th>
						<th style="overflow:hidden;height: 50px; font-size: 350%;" class="text_center">Signed</th>
						<th style="overflow:hidden;height: 50px; font-size: 350%;" class="text_center">Placed</th>
						<th style="overflow:hidden;height: 50px; font-size: 350%;" class="text_center">MCCM's</th>
						<th style="overflow:hidden;height: 50px; font-size: 350%;" class="text_center">CCM1</th>
						<!--<th class="text_center">Team</th>-->
						<!--<th style=" height: 45px; font-size: 300%;" class="text_center">Placed</th>-->
					</tr>

					<?php
						$total_paid_researcher = 0;
						$total_signed_researcher = 0;
						$total_placed_researcher = 0;
						$total_ccm1_researcher = 0;
						$total_mccm_researcher = 0;
						$researcher_rank = 0;
						foreach ($revenue_data['Researcher'] as $key => $value):

							if ($key == 'former' && empty($value['researcher']['signed']))
								continue;
							else if($key == 'former' && !empty($value['researcher']['signed']))
							{
								$value['Researcher']['nationality'] = '';
							}

							if ($row_number_rank % 2 === 0)
								$even = ' even_row';
							else
								$even = '';

							if (empty($value['Researcher']['nationality']))
								$flag_pic = 'world_32.png';
							else if($value['Researcher']['nationality'] == "PK")
								$flag_pic = 'MNG_32.png';
							else
								$flag_pic = $value['Researcher']['nationality'].'_32.png';
					?>
					<?php if($value['Researcher']['userPosition'] == "Researcher" || $value['userPosition'] == "Not defined"){
						$researcher_rank ++;
						$total_consultant_count--;?>
						<tr class="hover_row<?php echo $even; ?>">
							<td style="height: 10%; font-size: 300%;" class="text_center"><?php echo $researcher_rank; ?></td>
							<td style="height: 10%; font-size: 300%;" class="text_center"><?php echo $display_object->getPicture('/common/pictures/flags/'.$flag_pic); ?></td>
							<td style="height: 10%; font-size: 300%;" class="text_center"><?php echo $value['Researcher']['name']; ?></td>
							<td style="height: 10%; font-size: 300%;" class="text_right">&yen;<?php echo number_format($value['researcher']['signed'], $decimals, '.', ','); ?></td>
							<td style="height: 10%; font-size: 300%;" class="text_center"><?php echo $value['Researcher']['placedRevenue']; ?></td>
							<td style="height: 10%; font-size: 300%;" class="text_center"><?php echo $value['Researcher']['mccm']; ?></td>
							<td style="height: 10%; font-size: 300%;" class="text_center"><?php echo $value['Researcher']['ccm1']; ?></td>
							<!--<td class="text_center"><?php echo $value['team']; ?></td>-->
							<!--<td style="height: 55px; font-size: 250%;" class="text_right"><?php echo $value['Researcher']['placed']; ?></td>-->
						</tr>

						<?php
							$row_number_rank += 1;

							//$total_paid += $value['paid'];
							$total_paid_researcher += $value['researcher']['paid'];
							//$total_signed += $value['signed'];
							$total_signed_researcher += $value['researcher']['signed'];
							//$total_placed += $value['placed'];
							$total_placed_researcher += $value['Researcher']['placedRevenue'];
							$total_ccm1_researcher += $value['Researcher']['ccm1'];
							$total_mccm_researcher += $value['Researcher']['mccm'];
					}
						endforeach;
					?>
					<?php /*if($total_consultant_count>0)
					{
						$flag_url = "/common/pictures/flags/empty.png";
						$empty_flag = $display_object->getPicture($flag_url);
						for ($i=0; $i < $total_consultant_count ; $i++) {
							echo "
							<tr>
								<td style='height: 10%; font-size: 150%;'><center>-</center></td>
								<td style='height: 10%; font-size: 150%;'><center>".$empty_flag ."</center></td>
								<td style='height: 10%; font-size: 150%;'><center>-</center></td>
								<td style='height: 10%; font-size: 150%;'><center>-</center></td>
								<td style='height: 10%; font-size: 150%;'><center>-</center></td>
								<td style='height: 10%; font-size: 150%;'><center>-</center></td>
								<td style='height: 10%; font-size: 150%;'><center>-</center></td>
							</tr>
							";
						}
					} */?>
					<tr class="revenue_table_footer">
						<td style="height: 40px; font-size: 300%;" class="text_center" colspan="3">Total</td>
						<td style="height: 40px; font-size: 300%;" class="text_right">&yen;<?php echo number_format($total_signed_researcher, $decimals, '.', ','); ?></td>
						<td style="height: 40px; font-size: 300%;" class="text_center"><?php echo $total_placed_researcher?></td>
						<td style="height: 40px; font-size: 300%;" class="text_center"><?php echo $total_mccm_researcher?></td>
						<td style="height: 40px; font-size: 300%;" class="text_center"><?php echo $total_ccm1_researcher?></td>
						<!--<td style="height: 55px; font-size: 250%;" class="text_right">&yen;<?php echo number_format($total_paid_researcher, $decimals, '.', ','); ?></td>-->
						<!--<td style="height: 55px; font-size: 250%;" class="text_right"><?php echo $total_placed_researcher; ?></td>-->
					</tr>
				</table>
			</td>
		</tr>
	</table>
		</td>
	</tr>
</table>


<script>
	//var url = '<?php echo $url; ?>';
	//var swap_time = <?php echo $swap_time; ?>;

	//$('.scrollingContainer').css('overflow', 'auto');
	document.getElementById('componentContainerId').setAttribute("style","margin-top:-40px");

	//document.getElementById('footerId').remove();
	//$('#componentContainerId').css('margin-top','-48px;');
	setTimeout(function() {
		var nextloop = '<?php echo $nextloop; ?>';
		var url = '/index.php5?uid=555-006&ppa=pprev&ppt=revenue&ppk=0&watercooler=1&nextloop='+nextloop;
		//alert('test');
		window.location.replace(url);
	}, 30000);


</script>
