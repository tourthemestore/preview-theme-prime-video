<?php
include "../../../model/model.php";
?>


<style>
textarea.form-control {
    height: 120 !important;
}


</style>
<form id="itinerary_frm_save">

<div class="modal fade" id="itinerary_save_modal" role="dialog" aria-labelledby="myModalLabel" data-backdrop="static" data-keyboard="false">

  <div class="modal-dialog modal-lg" role="document" style="width:95% !important;">

    <div class="modal-content">

      <div class="modal-header">

        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>

        <h4 class="modal-title" id="myModalLabel">Add Itinerary</h4>

      </div>

      <div class="modal-body">
        <div class="row">
          <div class="text-left col-md-3 col-sm-6">
            <select id="dest_ids"  name="dest_names" title="Select Destination" class="form-control" onchange="check_dest_validation(this.id)" style="width:100%"> 
              <option value="">*Destination</option>
              <?php
              $sq_query = mysqlQuery("select * from destination_master where status != 'Inactive'"); 
              while($row_dest = mysqli_fetch_assoc($sq_query)){ ?>
              <option value="<?php echo $row_dest['dest_id']; ?>"><?php echo $row_dest['dest_name']; ?></option>
              <?php } ?>
            </select>
          </div>
          <div class="col-sm-6 text-left">
            <button type="button" class="btn btn-info btn-sm ico_left pull-left" style="margin-right:10px" onclick="display_format_modal();"><i class="fa fa-download"></i>&nbsp;&nbsp;CSV Format</button>
            <div class="div-upload  mg_bt_20" id="div_upload_button">
                  <div id="itinerary_csv_upload" class="upload-button1"><span  id="vendor_status1">CSV</span></div>
                  <span id="vendor_status"></span>
                  <ul id="files" ></ul>
                  <input type="hidden" id="txt_itinerary_csv_upload_dir" name="txt_itinerary_csv_upload_dir">
            </div>
            <button type="button" data-toggle="tooltip" class="btn btn-excel" title="Character limit for Special attraction is 85 characters, for Day-wise program is 2000 characters and for Overnight stay is 30 characters."><i class="fa fa-question-circle"></i></button> 
          </div>
          
        
          <div class="col-xs-3 text-right text_center_xs">
            <button type="button" class="btn btn-excel" title="Add Row" onclick="addRow('default_program_list')"><i class="fa fa-plus"></i></button>
          </div>
        </div>
        <div class="row">
          <div class="col-sm-12"> 
		        <!-- <button type="button" data-toggle="tooltip" class="btn btn-excel" title="Character limit for Special attraction is 85 characters, for Day-wise program is 2000 characters and for Overnight stay is 30 characters."><i class="fa fa-question-circle"></i></button> 
          <span style="color: red;" class="note" data-original-title="" title="">For saving daywise program keep checkbox selected!</span> -->
          </div>
        </div>
        <div class="row">
            <div class="col-md-12 col-sm-6 col-xs-12 mg_bt_10">
            <table style="width:100%" id="default_program_list" name="default_program_list" class="table table-bordered table-hover table-striped no-marg pd_bt_51 mg_bt_0">
                <tbody>
                  <tr>
                    <td width="27px;" style="padding-right: 10px !important;"><input class="css-checkbox labelauty" id="chk_programd1" type="checkbox" checked style="display: none;"><label for="chk_programd1" style="margin-top:55px;"><span class="labelauty-unchecked-image"></span><span class="labelauty-checked-image"></span></label></td>
                    <td width="20px;"><input maxlength="15" value="1" type="text" name="username" placeholder="Sr. No." class="form-control" disabled="" style="margin-top:35px;"></td>
                    <td class="col-md-3 no-pad" style="padding-left: 5px !important;"><input type="text" id="special_attaraction" onchange="validate_spaces(this.id);validate_spattration(this.id);" name="special_attaraction" class="form-control" placeholder="*Special Attraction" title="Special Attraction" style="margin-top:35px;"></td>
                    <td class="col-md-5 no-pad" style="padding-left: 5px !important;max-width: 594px;overflow: hidden;position: relative;" ><textarea id="day_program" name="day_program" class="form-control day_program" rows="2" placeholder="*Day-wise Program" onchange="validate_spaces(this.id);validate_dayprogram(this.id);" title="Day-wise Program"   style="overflow:hidden;resize:none;height:900px;"  
    rows="1"></textarea><span class="style_text" style="position: absolute !important; right: 15px !important; display: flex !important; gap: 15px; background: #f5f5f5 !important; padding: 0px 14px !important; top: 0px !important;"><span class="style_text_b" data-wrapper="**" style="font-weight: bold; cursor: pointer;" title="Bold text">B</span><span class="style_text_u" data-wrapper="__" style="cursor: pointer;" title="Underline text"><u>U</u></span></span></td>
                    <td class="col-md-2 no-pad" style="padding-left: 5px !important;"><input type="text" id="overnight_stay" name="overnight_stay" style="margin-top:35px;"  onchange="validate_spaces(this.id);validate_onstay(this.id);" class="form-control" placeholder="*Overnight Stay" title="Overnight Stay"></td>
                  </tr>
                </tbody>
            </table>
            </div>
        </div>
        <div class="row mg_tp_10">
          <div class="col-xs-12 text-center">
            <button class="btn btn-sm btn-success" id="btn_save"><i class="fa fa-floppy-o"></i>&nbsp;&nbsp;Save</button>
          </div>
        </div>
        <div id="itinerary_html">
        <div>
      </div>      

    </div>

  </div>

</div>

</form>

<script>
$('#dest_ids').select2();
$('#itinerary_save_modal').modal('show');



$(document).on("click", ".style_text_b, .style_text_u", function() {
        var wrapper = $(this).data("wrapper");

        var textarea = $(this).parents('.style_text').siblings('.day_program')[0];
        console.log(textarea);
        // Ensure textarea exists and selectionStart/selectionEnd are supported
        var start = textarea.selectionStart;
        var end = textarea.selectionEnd;

        // Get the selected text
        var selectedText = textarea.value.substring(start, end);

        // Wrap the selected text with the wrapper (e.g., ** for bold, __ for underline)
        var wrappedText = wrapper + selectedText + wrapper;

        // Insert the wrapped text back into the textarea
        textarea.value = textarea.value.substring(0, start) + wrappedText + textarea.value.substring(end);

        // Adjust the cursor position after wrapping
        textarea.selectionStart = start;
        textarea.selectionEnd = end + wrapper.length * 2;
        var text = textarea.value;
        var content = text.replace(/\*\*(.*?)\*\*/g, '<b>$1</b>');

        // Replace markdown-style underline (__text__) with <u> tags
        content = content.replace(/__(.*?)__/g, '<u>$1</u>');
        textarea.value = content;
        //console.log(content);    
    });

function display_format_modal(){
    var base_url = $('#base_url').val();
    window.location = base_url+"images/csv_format/itinerary.csv";
}

function check_dest_validation(dest_id){

  var dest_id = $('#'+dest_id).val();
  $.post('itinerary/check_dest_validation.php', {dest_id:dest_id}, function(data){
    if(data != ''){
      error_msg_alert(data);
    }
	});
}

itinerary_csv_upload();
function itinerary_csv_upload()
{   
    var type="id_proof";
    var btnUpload=$('#itinerary_csv_upload');
    var status=$('#vendor_status');
    new AjaxUpload(btnUpload, {
      action: 'itinerary/upload_itinerary_csv_file.php',
      name: 'uploadfile',
      onSubmit: function(file, ext){

        if(!confirm('Do you want to import this file?')){
          return false;
        }
        if (! (ext && /^(csv)$/.test(ext))){ 
          error_msg_alert('Only CSV files are allowed');
          return false;
        }
        status.text('Uploading...');
      },
      onComplete: function(file, response){
        status.text('');
        if(response==="error"){          
          alert("File is not uploaded.");           
        } else{
          document.getElementById("txt_itinerary_csv_upload_dir").value = response;
          itinerary_form_csv_save();
        }
      }
    });
}
function itinerary_form_csv_save(){
  
    var itinerary_csv_dir = document.getElementById("txt_itinerary_csv_upload_dir").value;
    var base_url = $('#base_url').val();
    $.ajax({
        type:'post',
        url: base_url+'controller/other_masters/itinerary/csv_save.php',
        data:{itinerary_csv_dir : itinerary_csv_dir },
        success:function(result){
            var table = document.getElementById("default_program_list");
            $('#itinerary_html').html(result);
            var itinerary_arr = JSON.parse($('#itinerary_arr').val());

            if(itinerary_arr.length == 0){
              error_msg_alert('Improper itinerary details entered!');
              return false;
            }
            else{

              for(var i=0; i<itinerary_arr.length; i++){

                  var row = table.rows[i];
                  itinerary_arr[i]['spa'] = itinerary_arr[i]['spa'].replace(/\\/g, '');
                  itinerary_arr[i]['dwp'] = itinerary_arr[i]['dwp'].replace(/\\/g, '');
                  itinerary_arr[i]['os'] = itinerary_arr[i]['os'].replace(/\\/g, '');
                  row.cells[2].childNodes[0].value = itinerary_arr[i]['spa'];
                  row.cells[3].childNodes[0].value = itinerary_arr[i]['dwp'];
                  row.cells[4].childNodes[0].value = itinerary_arr[i]['os'];
                  if(i!=itinerary_arr.length-1){
                      if(table.rows[i+1]==undefined){
                          addRow('default_program_list');
                      }
                  }
              }
            }
        }
    });
}
$('#itinerary_frm_save').validate({

    rules:{
      dest_names : { required : true }
    },

    submitHandler:function(form){

      var dest_id = $('#dest_ids').val();
      var table = document.getElementById("default_program_list");
      var rowCount = table.rows.length;
      //Atleast one row validation
      var count = 0;
      for(var i=0; i<rowCount; i++){
          var row = table.rows[i];
          if(row.cells[0].childNodes[0].checked){
              count++;
          }
      }
      if(parseInt(count) == 0){
          error_msg_alert("Please select atleast one day itinerary!");
          return false;
      }
      var sp_arr = new Array();
      var dwp_arr = new Array();
      var os_arr = new Array();
      for(var i=0; i<rowCount; i++){
        
        var row = table.rows[i];
        if(row.cells[0].childNodes[0].checked){

          var sp = row.cells[2].childNodes[0].value;
          var dwp = row.cells[3].childNodes[0].value;
          var os = row.cells[4].childNodes[0].value;
          if(sp==""){
              error_msg_alert('Special attraction is mandatory in row'+(i+1));
              return false;
          }
          if(dwp==""){
              error_msg_alert('Daywise program is mandatory in row'+(i+1));
              return false;
          }
          if(os==""){
              error_msg_alert('Overnight stay is mandatory in row'+(i+1));
              return false;
          }
          var flag1 = validate_spattration(row.cells[2].childNodes[0].id);
          var flag2 = validate_dayprogram(row.cells[3].childNodes[0].id);
          var flag3 = validate_onstay(row.cells[4].childNodes[0].id);         
          if(!flag1 || !flag2 || !flag3){
              return false;
          }
          sp_arr.push(sp);
          dwp_arr.push(dwp);
          os_arr.push(os);
        }
      }

      $('#btn_save').button('loading');
      $.ajax({
      type:'post',
      url:base_url()+'controller/other_masters/itinerary/itinerary_save.php',
      data:{ dest_id : dest_id, sp_arr : sp_arr, dwp_arr : dwp_arr, os_arr : os_arr},
      success:function(result){

          $('#btn_save').button('reset');
          var msg = result.split('--');
          if(msg[0]!="error"){
            $('#itinerary_save_modal').modal('hide');
            msg_alert(result);
            list_reflect();
          }
          else{
            error_msg_alert(msg[1]);
            $('#btn_save').button('reset');
          }
      }
      });
    }
});
</script>
<script src="<?= BASE_URL ?>js/app/footer_scripts.js"></script>