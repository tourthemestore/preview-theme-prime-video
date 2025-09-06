<?php

include_once("../../../model/model.php");

?>

<form id="frm_save">



<div class="modal fade" id="save_modal" role="dialog" aria-labelledby="myModalLabel" data-backdrop="static" data-keyboard="false">

  <div class="modal-dialog" role="document">

    <div class="modal-content">

      <div class="modal-header">

        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>

        <h4 class="modal-title" id="myModalLabel">Add Image</h4>

      </div>

      <div class="modal-body">

        <div class="row mg_bt_10">

            <div class="col-sm-6 col-xs-7 mg_bt_10_sm_xs"> 

              <select id="dest_name"  name="dest_name" title="*Select Destination" class="form-control"  style="width:100%"> 

                <option value="">Destination</option>

                <?php 
                $sq_query = mysqlQuery("select * from destination_master where status != 'Inactive'"); 
                while($row_dest = mysqli_fetch_assoc($sq_query)){ ?>

                    <option value="<?php echo $row_dest['dest_id']; ?>"><?php echo $row_dest['dest_name']; ?></option>

                <?php } ?>

              </select>

            </div>



        <div class="col-sm-6 col-xs-5 mg_bt_10_sm_xs text-right">

          <div class="div-upload">

                <div id="hotel_btn1" class="upload-button1"><span>Upload Image</span></div>

                <span id="id_proof_status" ></span>

                <ul id="files" ></ul>

                <input type="hidden" id="gallary_url" name="gallary_url">

          </div>
		      <button type="button" data-toggle="tooltip" class="btn btn-excel" title="Note : Upload Image size below 300KB, resolution : 900X450.save images with continuous name only, space/ special characters (except underscore) are not allowed. ( e.g. goa.jpg , goa_beach.jpg )"><i class="fa fa-question-circle"></i></button>


          <!-- <button type="button" 
    data-toggle="tooltip" 
    data-html="true" 
    class="btn btn-excel" 
    title="<div style='width: 200px; word-wrap: break-word; white-space: normal;'>Note: Upload Image size below 300KB, resolution: 900X450.save images with continuous name only, space/ special characters (except underscore) are not allowed. ( e.g. goa.jpg , goa_beach.jpg )</div>">
    <i class="fa fa-question-circle"></i>
</button> -->
          

        </div>

        </div>

        <div class="row mg_bt_10">
          <div class="col-xs-12"> 
            <textarea id="description" onchange="fname_validate(this.id);" name="description" placeholder="*Description" title="Description" rows="4"></textarea>
          </div>
        </div>

        <div class="row mg_tp_10">

          <div class="col-md-12 text-center">

            <button class="btn btn-sm btn-success" id="btn_gsave"><i class="fa fa-floppy-o"></i>&nbsp;&nbsp;Save</button>

          </div>

        </div>

      </div>      

    </div>

  </div>

</div>



</form>

<script src="<?= BASE_URL ?>js/ajaxupload.3.5.js"></script>

<script>

$('#save_modal').modal('show');

$('#dest_name').select2();



upload_pic_attch();

function upload_pic_attch()

{

    var btnUpload=$('#hotel_btn1');

    $(btnUpload).find('span').text('Image');

    $("#gallary_url").val('');

    new AjaxUpload(btnUpload, {

      action: 'gallery/upload_ticket.php',

      name: 'uploadfile',

      onSubmit: function(file, ext){  



        if (! (ext && /^(jpg|png|jpeg)$/.test(ext))){ 

         error_msg_alert('Only JPG, PNG files are allowed');

         return false;

        }
         
        $(btnUpload).find('span').text('Uploading...');

      },

      onComplete: function(file, response){
        if(response==="error"){          
          error_msg_alert("File is not uploaded.");           
          $(btnUpload).find('span').text('Upload Image');
        }else{
          if(response=="error1"){
            $(btnUpload).find('span').text('Upload Image');
            error_msg_alert('Maximum size exceeds');
            return false;
          }
          if(response=="error2"){
            $(btnUpload).find('span').text('Upload Image');
            error_msg_alert('Incorrect resolution of image');
            return false;
          }else{
            $(btnUpload).find('span').text('Uploaded');
            $("#gallary_url").val(response);
          }
        }

      }

    });

}



$('#frm_save').validate({

  rules :

  {

    dest_name : { required : true },

    description : { maxlength : 100, required : true}

  },

    submitHandler:function(form){

      
      $('#btn_gsave').prop('disabled',true);
      var base_url = $("#base_url").val();

      var dest_id = $('#dest_name').val();

      var description = $('#description').val();

      var gallary_url = $('#gallary_url').val();

      if(gallary_url == '') {
        error_msg_alert("Select image to Upload"); 
        $('#btn_gsave').prop('disabled',false);
        return false;
      }

      $('#btn_gsave').button('loading');

      $.ajax({

        type:'post',

        url:base_url+'controller/other_masters/gallary/gallary_img_save.php',

        data:{dest_id : dest_id, description : description, gallary_url : gallary_url },

        success:function(result){

          var msg = result.split('--');
          msg_alert(result);               
          $('#btn_gsave').button('reset');
          $('#btn_gsave').prop('disabled',false);
          $('#save_modal').modal('hide');
          update_b2c_cache();
        }
      });
    }
});

</script>

<script src="<?= BASE_URL ?>js/app/footer_scripts.js"></script>