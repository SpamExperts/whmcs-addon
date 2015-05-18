<?php
/* * ********************************************************************
 * Customization Services by ModulesGarden.com
 * Copyright  ModulesGarden, INBS Group Brand, All Rights Reserved 
 * (2014-02-10, 10:56:33)
 * 
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
 * ******************************************************************** */

/**
 * @author Maciej Husak <maciej@modulesgarden.com>
 */
if (!defined("WHMCS")) 
{
  die("This file cannot be accessed directly");
}
?>

<form action="" method="post">
    <table class="table">
        <tr>
            <td>Product</td>
            <td><input type="text" name="product[name]" value="" style="width:300px;" /></td>
        </tr>
        <tr>
            <td>Product Type</td>
            <td>
                <select name="product[type]">
                    <option>Incoming</option>
                    <option>Outgoing</option>
                    <option>Both</option>
                </select>
            </td>
        </tr>
        <tr>
            <td colspan="2">
                <input type="hidden" name="action" value="addProduct" />
                <input type="submit" value="Add Product" class="btn btn-success" style="height: 32px;" />
            </td>
        </tr>
    </table>
</form>