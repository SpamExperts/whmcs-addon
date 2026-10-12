<div class="buttons">
    <h3 style="text-align:left;margin-bottom: 5px;">{$lang.management}</h3>
    {if $api_error}
    <div class="alert alert-danger">{$api_error|escape}</div>
    {else}
    {*<button class="btn" onclick="window.location.href='clientarea.php?action=productdetails&id={$serviceid}&modop=custom&a=management&page=logintopanel';return false">{$lang.logintopanel}</button>*}
    {if !$disable_edit_contact}
        <a href="clientarea.php?action=productdetails&id={$serviceid}&modop=custom&a=management&page=EditEmail&token={$token}" class="btn"><i class="icon-edit"></i> {$lang.editcontactemail}</a>
    {/if}

    {if !$disable_manage_routes}
        <a href="clientarea.php?action=productdetails&id={$serviceid}&modop=custom&a=management&page=ManageRoutes&token={$token}" class="btn"><i class="icon-retweet"></i> {$lang.manageroutes}</a>
    {/if}

    <a href="clientarea.php?action=productdetails&id={$serviceid}&modop=custom&a=management&page=ManageAliases&token={$token}" class="btn"><i class="icon-globe"></i> {$lang.managealiases}</a>

    <a href="{$api_url|escape}/?authticket={$api_auth|escape:'url'}" target="_blank" rel="noopener" class="btn">
        <i class="icon-user"></i> {$lang.logintopanel}
    </a>
    {/if}
</div>

<style>
    {literal}
        .buttons .btn {margin-bottom:5px;}
    {/literal}
</style>
