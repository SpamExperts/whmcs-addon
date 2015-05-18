<?php

/* * ********************************************************************
 * Customization Services by ModulesGarden.com
 * Copyright  ModulesGarden, INBS Group Brand, All Rights Reserved 
 * (2014-01-08, 10:10:02)
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
if($logs){
    foreach($logs as $key=>$value){

        echo '<tr>
                <td><textarea style="width:99%;height: 150px;overflow-y: scroll;cursor: default!important;line-height: 15px;" readonly>'.htmlspecialchars($value['data']).'</textarea></td>
              </tr>';
    }
} else {
    echo '<tr><td colspan="2">Nothing to display!</td></tr>';
}