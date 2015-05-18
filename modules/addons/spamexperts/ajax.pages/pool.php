<?php


if($_REQUEST['parent'] == 'mg_pagination')
{
    $p = new MG_Pagination();
   // $p->resetFilter();
    $row = mysql_get_row("SELECT count(*) as `count` FROM nicit_logs".$p->query(false, false, false));
    $p->setAmount($row['count']);
    $logs = mysql_get_array("SELECT * FROM nicit_logs ORDER BY date DESC".$p->query(false, false));

}
