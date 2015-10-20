<?php
if (!defined("WHMCS")) 
{
  die("This file cannot be accessed directly");
}
?>
<form action="" method="post">
    <table class="table table-bordered table-striped configtable">
        <tr>
            <td>API URL:</td>
            <td><input type="text" name="conf[url]" style="width:304px;" value="<?php echo $data['url']; ?>" /></td>
        </tr>
        <tr>
            <td>API Username</td>
            <td><input type="text" name="conf[user]" style="width:304px;" value="<?php echo $data['user']; ?>" /></td>
        </tr>
        <tr>
            <td>API Password</td>
            <td><input type="password" name="conf[password]" value="<?php echo $data['password']; ?>" /></td>
        </tr>
        <tr>
            <td>Disable user access to manage routes page</td>
            <td><input type="checkbox" name="conf[disable_manage_routes]" value="1" <?php if($data['disable_manage_routes'] == '1') echo 'checked'; ?>/></td>
        </tr>
        <tr>
            <td>Disable user access to edit contact e-mail page</td>
            <td><input type="checkbox" name="conf[disable_edit_contact]" value="1" <?php if($data['disable_edit_contact'] == '1') echo 'checked'; ?>/></td>
        </tr>
        <tr>
            <td colspan="2">
                <input type="hidden" name="action" value="savechanges" />
                <input type="submit" value="Save Changes" class="btn btn-success" style="height:32px;" />
                <input type="submit" value="Test Connection" id="testConnection" class="btn btn-info" style="height:32px;" />
                <span id="ajax_response"></span>
            </td>
        </tr>
    </table>
</form>


<script type="text/javascript">
    jQuery(document).ready(function() {
        jQuery("#testConnection").click(function(){
           jQuery.post(window.location.href,{
               'ajax'       : 1,
               'user'       : jQuery("input[name='conf[user]']").val(),
               'password'   : jQuery("input[name='conf[password]']").val(),
               'url'        : jQuery("input[name='conf[url]']").val(),
               'action'     : 'testconnection'
           }, function (data) {
               if(data == 'success'){
                   jQuery("#ajax_response").html("<span style='color:green'>Success</span>");
               } else {
                   jQuery("#ajax_response").html("<span style='color:red'>"+data+'</span>');
               }
           });
           return false;
        });
        
        jQuery( document ).ajaxStart(function() {
            jQuery( "#ajax_response" ).html("<img src='images/loading.gif' alt='' />");
        });
    });
</script>
