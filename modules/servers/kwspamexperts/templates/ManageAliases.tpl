{**********************************************************************
* Customization Services by ModulesGarden.com
* Copyright (coffee) ModulesGarden, INBS Group Brand, All Rights Reserved 
* (${date}, ${time})
*
*  CREATED BY MODULESGARDEN       ->        http://modulesgarden.com
*  CONTACT                        ->       contact@modulesgarden.com
*
*
*
*
* This software is furnished under a license and may be used and copied
* only  in  accordance  with  the  terms  of such  license and with the
* inclusion of the above copyright notice.  This software  or any other
* copies thereof may not be provided or otherwise made available to any
* other person.  No title to and  ownership of the  software is  hereby
* transferred.
*
*
**********************************************************************}

{**
* @author Maciej Husak <maciej@modulesgarden.com>
*}

<h1 style="margin-bottom:5px;">{$lang.header}</h1>
<p style="margin-bottom: 10px;"><button onclick="window.location.href = 'clientarea.php?action=productdetails&id={$serviceid}&modop=custom&a=management';
        return false" class="btn btn-small"><i class="icon-arrow-left"></i> {$main_lang.back}</button></p>
{if $_status}
    <div class="alert {if $_status.code==1}alert-success{else}alert-danger{/if}">{$_status.msg}</div>
{/if}
<form action="" method="post">
<input type="hidden" name="_action" value="deletealias" />    
<table class="table table-bordered table-striped">
        <tr>
            <th style="width:20px;"><input type="checkbox" class="selectall" /></th>
            <th>{$lang.alias}</th>
            <th style="width:50px;">{$lang.action}</th>
        </tr>
    {foreach from=$domainaliases|@array_filter key=k item=domainalias}
        <tr>
            <td><input type="checkbox" class="select-all" name="alias[old_list][]" value="{$domainalias}" /></td>
            <td><a href="http://{$domainalias}" target="_blank">{$domainalias}</a></td>
            <td>
                 <input type="submit" name="delete-item[{$domainalias}]" onclick="return confirm('{$lang.confirm_delete}');" class="btn btn-danger btn-small" value="{$lang.delete}" />
            </td>
        </tr>
    {foreachelse}
        <tr>
            <td colspan="3" style="text-align:center;">{$lang.nothing}</td>
        </tr>
    {/foreach}
</table>
   {if $domainaliases|@count > 0}
        <input type="submit" onclick="return confirm('{$lang.confirm_delete}');" name="delete-all" class="btn btn-danger btn-small" value="{$lang.delete}" />
   {/if}
</form>

<form action="" method="post">
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