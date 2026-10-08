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
<h1>{$lang.header}</h1>
<p><a href="clientarea.php?action=productdetails&id={$serviceid}" class="btn btn-small"><i class="icon-arrow-left"></i> {$main_lang.back}</a></p>
{if $_result}
    <div class="alert {if $_result.code==1}alert-success{else}alert-danger{/if}">{$_result.msg}</div>
{/if}
{if $_status && $_status.code==2}
    <div class="alert alert-danger">{$_status.msg}</div>
{else}
    {if $_status}
        <div class="alert {if $_status.code==1}alert-success{else}alert-danger{/if}">{$_status.msg}</div>
    {/if}
    <p>{$lang.description}</p>
    <form action="clientarea.php?action=productdetails&id={$serviceid}" method="post">
        <input type="hidden" name="id" value="{$serviceid}" />
        <input type="hidden" name="modop" value="custom" />
        <input type="hidden" name="a" value="management" />
        <input type="hidden" name="page" value="EditEmail" />
        <input type="hidden" name="token" value="{$token}" />
        {$lang.cotactemail}
        <table class="se-email-table">
            <tr>
                <td>{$lang.primarycontact}</td>
                <td><input type="text" size="30" name="primaryemail" value="{$primarycontact}" /></td>
            </tr>
            <tr>
                <td>{$lang.admincontact}</td>
                <td><input type="text" size="30" name="adminemail" value="{$admincontact}" /></td>
            </tr>
            <tr>
                <td></td>
                <td><input type="submit" name="submit" class="btn btn-success" value="{$lang.save}" /></td>
            </tr>
        </table>
    </form>

    {literal}
    <style type="text/css">
        .se-email-table td{padding: 5px;}
    </style>
    {/literal}
{/if} 
