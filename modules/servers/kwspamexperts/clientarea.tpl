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
    {*<button class="btn" onclick="window.location.href='clientarea.php?action=productdetails&id={$serviceid}&modop=custom&a=management&page=logintopanel';return false">{$lang.logintopanel}</button>*}
    {if !$disable_edit_contact}
        <button class="btn" onclick="window.location.href='clientarea.php?action=productdetails&id={$serviceid}&modop=custom&a=management&page=EditEmail';return false">
            <i class="icon-edit"></i> {$lang.editcontactemail}
        </button>
    {/if}

    {if !$disable_manage_routes}
        <button class="btn" onclick="window.location.href='clientarea.php?action=productdetails&id={$serviceid}&modop=custom&a=management&page=ManageRoutes';return false">
            <i class="icon-retweet"></i> {$lang.manageroutes}
        </button>
    {/if}

    <button class="btn" onclick="window.location.href='clientarea.php?action=productdetails&id={$serviceid}&modop=custom&a=management&page=ManageAliases';return false">
        <i class="icon-globe"></i> {$lang.managealiases}
    </button>

    <button class="btn" onclick="window.open('{$api_url}/?authticket={$api_auth}');return false;">
        <i class="icon-user"></i> {$lang.logintopanel}
    </button>
</div>

<style>
    {literal}
        .buttons .btn {margin-bottom:5px;}
    {/literal}
</style>