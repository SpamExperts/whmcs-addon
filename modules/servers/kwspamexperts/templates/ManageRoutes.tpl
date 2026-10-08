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
<p style="margin-bottom: 8px;"><a href="clientarea.php?action=productdetails&id={$serviceid}" class="btn btn-small"><i class="icon-arrow-left"></i> {$main_lang.back}</a></p>
{if $_result}
    <div class="alert {if $_result.code==1}alert-success{else}alert-danger{/if}">{$_result.msg}</div>
{/if}
{if $_status && $_status.code==2}
    <div class="alert alert-danger">{$_status.msg}</div>
{else}
    {if $_status}
        <div class="alert {if $_status.code==1}alert-success{else}alert-danger{/if}">{$_status.msg}</div>
    {/if}
    <form action="clientarea.php?action=productdetails&id={$serviceid}" method="post">
        <input type="hidden" name="id" value="{$serviceid}" />
        <input type="hidden" name="modop" value="custom" />
        <input type="hidden" name="a" value="management" />
        <input type="hidden" name="page" value="ManageRoutes" />
        <input type="hidden" name="token" value="{$token}" />
        <input type="hidden" name="_action" value="deleteroute" />
        <table class="table table-bordered table-striped">
            <tr>
                <th style="width:20px;"><input type="checkbox" class="selectall" /></th>
                <th>{$lang.destination}</th>
                <th>{$lang.port}</th>
                <th style="width:50px;">{$lang.action}</th>
            </tr>
            {foreach $domainroutes as $domainroute}
                {if $domainroute.0}
                    <tr>
                        <td><input type="checkbox" class="select-all" name="routes[old_list][]" value="{$domainroute.0}" /></td>
                        <td><a href="https://{$domainroute.0}" target="_blank">{$domainroute.0}</a></td>
                        <td>{$domainroute.2}</td>
                        <td>
                            {if $domainroutes|count ne 1}
                                <a href="#" onclick="onDelete('{$domainroute.0}', '{$domainroute.2}');return false" class="btn btn-danger btn-small">{$lang.delete}</a>
                            {else}
                                <i class="icon-info-sign" title="{$lang.remove_info}"></i>
                            {/if}
                        </td>
                    </tr>
                {/if}
            {foreachelse}
                <tr>
                    <td colspan="3" style="text-align:center;">{$lang.nothing}</td>
                </tr>
            {/foreach}
        </table>
        {if $domainroutes|count > 0}
             <input type="submit" onclick="return confirm('{$lang.confirm_delete}');" name="delete-all" class="btn btn-danger btn-small" value="{$lang.delete}" />
        {/if}
    </form>

    <form action="clientarea.php?action=productdetails&id={$serviceid}" method="post">
        <input type="hidden" name="id" value="{$serviceid}" />
        <input type="hidden" name="modop" value="custom" />
        <input type="hidden" name="a" value="management" />
        <input type="hidden" name="page" value="ManageRoutes" />
        <input type="hidden" name="token" value="{$token}" />
        <fieldset>
            <legend style="margin-bottom:17px;">{$lang.add_route}</legend>
            <input type="text" size="30" name="route[hostname]" value="" placeholder="{$lang.hostname}" /> 
            <input type="text" name="route[port]" value="" placeholder="{$lang.port}" /> 
            <input type="submit" class="btn btn-success" name="submit"  value="{$lang.add}" style="margin-top:-10px;" />
            <input type="hidden" name="_action" value="addroute" />
        </fieldset> 
    </form>

    <form action="clientarea.php?action=productdetails&id={$serviceid}" method="post" id="se-delete-route">
        <input type="hidden" name="id" value="{$serviceid}" />
        <input type="hidden" name="modop" value="custom" />
        <input type="hidden" name="a" value="management" />
        <input type="hidden" name="page" value="ManageRoutes" />
        <input type="hidden" name="token" value="{$token}" />
        <input type="hidden" name="_action" value="deleteroute" />
        <input type="hidden" name="routetodel" value="" />
        <input type="hidden" name="routeporttodel" value="" />
    </form>

    <p style="clear:both;margin-bottom: 10px;"></p>
    <script type="text/javascript">
        {literal}
        function onDelete(route, port) {
                if (confirm("{/literal}{$lang.confirm_delete}{literal}")) {
                    var form = document.getElementById('se-delete-route');
                    form.elements['routetodel'].value = route;
                    form.elements['routeporttodel'].value = port;
                    form.submit();
                }
                return false;
        }    
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
