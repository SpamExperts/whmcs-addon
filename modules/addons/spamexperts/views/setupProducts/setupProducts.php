<?php

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}
?>

<form action="" method="post">
    <table class="form" width="100%" cellspacing="2" cellpadding="3">
        <tr>
            <td width="30%" class="fieldlabel">Product</td>
            <td class="fieldarea">
                <input type="text" name="product[name]" value="" class="form-control input-400" />
            </td>
        </tr>
        <tr>
            <td class="fieldlabel">Product Type</td>
            <td class="fieldarea">
                <select name="product[type]" class="form-control input-200">
                    <option>Incoming</option>
                    <option>Outgoing</option>
                    <option>Both</option>
                </select>
            </td>
        </tr>
    </table>
    <div class="btn-container">
        <input type="hidden" name="action" value="addProduct" />
        <input type="submit" value="Add Product" class="btn btn-primary" />
    </div>
</form>
