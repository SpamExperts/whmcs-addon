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
