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
<p style="margin-bottom: 8px;"><button onclick="window.location.href = 'clientarea.php?action=productdetails&id={$serviceid}&modop=custom&a=management';
        return false" class="btn btn-small"><i class="icon-arrow-left"></i> {$main_lang.back}</button></p>
{if $_status}
    <div class="alert {if $_status.code==1}alert-success{else}alert-danger{/if}">{$_status.msg}</div>
{/if}

<form action="" method="post">
    <input type="hidden" name="_action" value="unbind" />
    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>{$lang.domain}</th>
                <th style="width:60px;">{$lang.action}</th>
            </tr>
        </thead>
        {foreach from=$domains_list item="entry" key="k"}
            <tr>
                <td><a href="http://{$entry}" taget="_blank">{$entry}</a></td>
                <td><button name="unbind-domain" value="{$entry}" class="btn btn-danger">{$lang.unbind}</button></td>
            </tr>
        {foreachelse}
            <tr>
                <td colspan="2" style="text-align:center;">{$lang.no_domains}</td>
            </tr>
        {/foreach}
    </table>
</form>
    

<form action="" method="post">
    <fieldset>
        <legend style="margin-bottom:17px;">{$lang.bind_domain}</legend>
        <input type="text" size="30" name="bind[domain]" value="" placeholder="{$lang.example}" /> 
        <input type="submit" class="btn btn-success" name="submit"  value="{$lang.bind}" style="margin-top:-10px;" />
        <input type="hidden" name="_action" value="bind" />
    </fieldset>

    
</form>    