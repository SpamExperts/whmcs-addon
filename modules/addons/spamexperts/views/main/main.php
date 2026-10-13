<?php

/**
 * Settings view, loaded by core.php after pages/main/main.php.
 *
 * @var array $data API settings, set by the page
 */
if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}
?>
<form action="" method="post">
    <table class="form" width="100%" cellspacing="2" cellpadding="3">
        <tr>
            <td width="30%" class="fieldlabel">API URL</td>
            <td class="fieldarea">
                <input type="text" name="conf[url]" class="form-control input-400"
                    value="<?php echo htmlentities($data['url'], ENT_QUOTES, 'UTF-8'); ?>" />
            </td>
        </tr>
        <tr>
            <td class="fieldlabel">API Username</td>
            <td class="fieldarea">
                <input type="text" name="conf[user]" class="form-control input-400"
                    value="<?php echo htmlentities($data['user'], ENT_QUOTES, 'UTF-8'); ?>" />
            </td>
        </tr>
        <tr>
            <td class="fieldlabel">API Password</td>
            <td class="fieldarea">
                <input type="password" name="conf[password]" class="form-control input-400"
                    value="<?php echo htmlentities($data['password'], ENT_QUOTES, 'UTF-8'); ?>" />
            </td>
        </tr>
        <tr>
            <td class="fieldlabel">Disable user access to manage routes page</td>
            <td class="fieldarea">
                <input type="checkbox" name="conf[disable_manage_routes]" value="1"
                    <?php echo ($data['disable_manage_routes'] ?? '') === '1' ? 'checked' : ''; ?>/>
            </td>
        </tr>
        <tr>
            <td class="fieldlabel">Disable user access to edit contact e-mail page</td>
            <td class="fieldarea">
                <input type="checkbox" name="conf[disable_edit_contact]" value="1"
                    <?php echo ($data['disable_edit_contact'] ?? '') === '1' ? 'checked' : ''; ?>/>
            </td>
        </tr>
    </table>
    <div class="btn-container">
        <input type="hidden" name="action" value="savechanges" />
        <input type="submit" value="Save Changes" class="btn btn-primary" />
        <input type="submit" value="Test Connection" id="testConnection" class="btn btn-default" />
        <span id="ajax_response"></span>
    </div>
</form>


<script type="text/javascript">
    jQuery(document).ready(function() {
        jQuery("#testConnection").click(function(){
           jQuery.post(window.location.href,{
               'ajax'       : 1,
               'user'       : jQuery("input[name='conf[user]']").val(),
               'password'   : jQuery("input[name='conf[password]']").val(),
               'url'        : jQuery("input[name='conf[url]']").val(),
               'action'     : 'testconnection',
               'token'      : jQuery(this).closest('form').find("input[name='token']").val()
           }, function (data) {
               if(data === 'success'){
                   jQuery("#ajax_response").html("<span class='text-success'>Success</span>");
               } else {
                   jQuery("#ajax_response").html("<span class='text-danger'>"+data+'</span>');
               }
           });
           return false;
        });
        
        jQuery( document ).ajaxStart(function() {
            jQuery( "#ajax_response" ).html("<img src='images/loading.gif' alt='' />");
        });
    });
</script>
