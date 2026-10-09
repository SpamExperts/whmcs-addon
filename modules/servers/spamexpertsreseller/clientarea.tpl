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
<div class="buttons">
    <h3 style="text-align:left;margin-bottom: 5px;">{$lang.management}</h3>
    {if $api_error}
    <div class="alert alert-danger">{$api_error|escape}</div>
    {else}
        <a href="clientarea.php?action=productdetails&id={$serviceid}&modop=custom&a=management&page=managedomains&token={$token}" class="btn"><i class="icon-globe"></i> {$lang.manage_domains}</a>
{*        <button class="btn" onclick="window.location.href='clientarea.php?action=productdetails&id={$serviceid}&modop=custom&a=management&page=stats';return false;"><i class="icon-th-list"></i> {$lang.stats}</button>*}
        <a href="{$api_url|escape}" target="_blank" rel="noopener" class="btn"><i class="icon-user"></i> {$lang.login}</a>
    {/if}
</div> 
