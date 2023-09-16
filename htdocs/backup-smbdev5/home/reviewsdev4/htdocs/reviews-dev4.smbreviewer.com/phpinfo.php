<?php

$apcuAvailabe = function_exists('apcu_enabled') && apcu_enabled();
$memcachedAvailabe = function_exists('memcached_get') && memcached_get('foo');

if($apcuAvailabe)
{
  echo " apcu enabled";
} else {
  echo "acpu disabled";
}
echo "<br>";

if($memcachedAvailabe)
{
  echo " memcached enabled";
} else {
  echo "memcached disabled";
}
// header('Content-Type: application/json; charset=utf-8');
//
// echo json_encode(opcache_get_status());
// echo __DIR__;
//error_log('bar');

// Show all information, defaults to INFO_ALL
phpinfo();

?>
