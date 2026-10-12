<h1 style="margin-bottom:5px;">{$lang.header}</h1>
<p style="margin-bottom: 10px;"><a href="clientarea.php?action=productdetails&id={$serviceid}" class="btn btn-small"><i class="icon-arrow-left"></i> {$main_lang.back}</a></p>
{if $_result}
    <div class="alert {if $_result.code==1}alert-success{else}alert-danger{/if}">{$_result.msg|escape}</div>
{/if}
{if $_status && $_status.code==2}
    <div class="alert alert-danger">{$_status.msg|escape}</div>
{else}
{if $_status}
    <div class="alert {if $_status.code==1}alert-success{else}alert-danger{/if}">{$_status.msg|escape}</div>
{/if}
<form action="clientarea.php?action=productdetails&id={$serviceid}" method="post">
<input type="hidden" name="id" value="{$serviceid}" />
<input type="hidden" name="modop" value="custom" />
<input type="hidden" name="a" value="management" />
<input type="hidden" name="page" value="ManageAliases" />
<input type="hidden" name="token" value="{$token}" />
<input type="hidden" name="_action" value="deletealias" />
<table class="table table-bordered table-striped">
        <tr>
            <th style="width:20px;"><input type="checkbox" class="selectall" /></th>
            <th>{$lang.alias}</th>
            <th style="width:50px;">{$lang.action}</th>
        </tr>
    {foreach $domainaliases as $domainalias}
        <tr>
            <td><input type="checkbox" class="select-all" name="alias[old_list][]" value="{$domainalias|escape}" /></td>
            <td><a href="https://{$domainalias|escape}" target="_blank">{$domainalias|escape}</a></td>
            <td>
                 <input type="submit" name="delete-item[{$domainalias|escape}]" onclick="return confirm('{$lang.confirm_delete|escape:'javascript'|escape}');" class="btn btn-danger btn-small" value="{$lang.delete}" />
            </td>
        </tr>
    {foreachelse}
        <tr>
            <td colspan="3" style="text-align:center;">{$lang.nothing}</td>
        </tr>
    {/foreach}
</table>
   {if $domainaliases|count > 0}
        <input type="submit" onclick="return confirm('{$lang.confirm_delete|escape:'javascript'|escape}');" name="delete-all" class="btn btn-danger btn-small" value="{$lang.delete}" />
   {/if}
</form>

<form action="clientarea.php?action=productdetails&id={$serviceid}" method="post">
    <input type="hidden" name="id" value="{$serviceid}" />
    <input type="hidden" name="modop" value="custom" />
    <input type="hidden" name="a" value="management" />
    <input type="hidden" name="page" value="ManageAliases" />
    <input type="hidden" name="token" value="{$token}" />
    <fieldset>
        <legend style="margin-bottom:17px;">{$lang.add_alias}</legend>
        <input type="text" name="alias[new]" value="" placeholder="exampledomain.com" /> 
        <input type="submit" class="btn btn-success" name="submit"  value="{$lang.add}" style="margin-top:-10px;" /> 
        <input type="hidden" name="_action" value="addalias" />
    </fieldset> 
</form>
<p style="clear:both;margin-bottom: 10px;"></p>
<script type="text/javascript">
    {literal}
    jQuery(document).ready(function(){
        jQuery("input.selectall").change(function(){
            if(jQuery(this).is(":checked"))
                jQuery("input.select-all").attr("checked",true);
            else
                jQuery("input.select-all").removeAttr("checked");
        });

    });
    {/literal}
</script>
{/if}
