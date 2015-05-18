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

<table class="table table-striped table-bordered">
    <tbody>
        <tr>
            <th>{$lang.domain}</th>
            <th>{$lang.incoming_bandwidth_daily}</th>
            <th>{$lang.incoming_bandwidth_yearly}</th>
            <th>{$lang.outgoing_bandwidth_daily}</th>
            <th>{$lang.outgoing_bandwidth_yearly}</th>
        </tr>
    </tbody>
    {foreach from=$data key="k" item="entry"}
        <tr>
            <td>{$entry[0]}</td>
            <td>{$entry[2]} B</td>
            <td>{$entry[3]} B</td>
            <td>{$entry[4]} B</td>
            <td>{$entry[5]} B</td>
        </tr>
    {foreachelse}
        <tr>
            <td colspan="5" style="text-align:center;">{$lang.nothing_to_display}</td>
        </tr>
    {/foreach}
</table>