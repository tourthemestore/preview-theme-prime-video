<?php
include '../../../../model/model.php';
$from_date = $_POST['from_date'];
$to_date = $_POST['to_date'];
$daysLeft = abs(strtotime($from_date) - strtotime($to_date));
$days = $daysLeft/(60 * 60 * 24);
?>
<div class="panel panel-default panel-body app_panel_style feildset-panel mg_tp_30">
<legend>Tour Itinerary</legend>
<table id="dynamic_table_list_group" style="width:100%" name="dynamic_table_list_group">

<?php 
	for($i = 0; $i<=$days; $i++) { 

		$dayNumber = $i + 1;
?>

		<tr>
			<td class='col-md-1 pad_8'><input type="text" id="day<?php echo $i+1; ?>" name="day" class="form-control mg_bt_10" style="margin-top:15px;" placeholder="Day <?php echo $i+1; ?>" title="Day" value="" disabled> </td>

			<td class='col-md-2 pad_8'><input type="text" id="special_attaraction<?php echo $i+1; ?>" name="special_attaraction" class="form-control mg_bt_10" placeholder="*Special Attraction" style="margin-top:15px;" onchange="validate_spaces(this.id);validate_spattration(this.id);" title="Special Attraction" value=""> 
			</td> 

			<!-- <td class='col-md-6 pad_8' style="max-width: 594px;overflow: hidden;"><textarea id="day_program<?php echo $i; ?>" name="day_program" class="form-control mg_bt_10"  onchange="validate_spaces(this.id);validate_dayprogram(this.id);" placeholder="*Day<?php echo $i+1;?> Program" title="Day-wise Program" rows="3" value=""></textarea>
			</td> -->

			<td class='col-md-6 pad_8' style="max-width: 594px;overflow: hidden;position: relative;"><textarea id="day_program<?php echo $i; ?>" name="day_program" class="form-control mg_bt_10 day_program" placeholder="*Day<?php echo $i+1; ?> Program" title="Day-wise Program" onchange="validate_spaces(this.id);validate_dayprogram(this.id);" rows="3" value="" style=" height:900px;"></textarea><span class="style_text"><span class="style_text_b" data-wrapper="**" style="font-weight: bold; cursor: pointer;" title="Bold text">B</span><span class="style_text_u" data-wrapper="__" style="cursor: pointer;" title="Underline text"><u>U</u></span></span>
            </td>

			<td class='col-md-2 pad_8'><input type="text" id="overnight_stay<?php echo $i+1; ?>" name="overnight_stay" class="form-control mg_bt_10" placeholder="*Overnight Stay" onchange="validate_spaces(this.id);validate_onstay(this.id);" title="Overnight Stay"  value="" style="margin-top:15px;"> 
			</td>
			<td class='col-md-1 pad_8'><select id="meal_plan<?php echo $i+1; ?>" title="Meal Plan" name="meal_plan" class="form-control" style="width:125px;margin-top:15px;">
			<?php get_mealplan_dropdown(); ?>
			</select>
			</td>
			<td class='col-md-1 pad_8'><button type="button" class="btn btn-excel" title="Add Itinerary"  style="margin-top:15px;" id="itinerary<?php echo $dayNumber; ?>" onClick="add_itinerary('dest_name_s','special_attaraction<?php echo $i+1; ?>','day_program<?php echo $i; ?>','overnight_stay<?php echo $i+1; ?>','Day-<?=$dayNumber?>')"><i class="fa fa-plus"></i></button>
			</td>
		</tr>

<?php } ?>	

</table>
</div>
<script src="<?php echo BASE_URL ?>js/app/footer_scripts.js"></script>