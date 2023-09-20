<?php
 $arr= array();
 foreach($_GET as $index=>$val)
 {
    array_push($arr, $index."=".$val);
 }
 $str= implode('&', $arr);
 echo "<script>window.location=`../../../../index.php?page=do_payment_execute&".$str."`;</script>";
?>