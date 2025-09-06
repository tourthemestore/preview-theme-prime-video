<?php
include "../../model/model.php";
?>
<div class="app_panel_content Filter-panel">
    <div class="row">
        <div class="col-md-4 col-md-offset-4">
            <select id="role_id" name="role_id" onchange="assign_user_roles_reflect()" class="form-control" title="Select Role">
                <option value="">Select Role</option>
                <?php
                    $sq = mysqlQuery("select * from role_master where active_flag='Active'");
                    while($row = mysqli_fetch_assoc($sq))
                    {
                    ?>
                        <option value="<?php echo $row['role_id'] ?>"><?php echo $row['role_name'] ?></option>
                    <?php       
                    }
                ?>
            </select>
            <small class="note mg_tp_10">Note : Set permission for individual Role</small>
        </div>
    </div>
</div>

<div id="div_user_roles" class="main_block"></div>    
<script>
$('#role_id').select2();
function assign_user_roles_reflect()
{
    var role_id = $("#role_id").val();
    var role_name = $('#role_id').select2('data')[0].text;
    $.post( "../role_mgt/assign_user_roles_reflect.php" , { role_id : role_id, role_name : role_name  } , function (data) {
        $ ("#div_user_roles").html( data ) ;
    });
}
</script>