<?php
// Tell PayFast that this page is reachable by triggering a header 200
header( 'HTTP/1.0 200 OK' );
flush();
define( 'SANDBOX_MODE', true );
$pfHost = SANDBOX_MODE ? 'sandbox.payfast.co.za' : 'www.payfast.co.za';
// Posted variables from ITN
$pfData = $_POST;
// Strip any slashes in data
foreach( $pfData as $key => $val ) {
    $pfData[$key] = stripslashes( $val );
}
// Convert posted variables to a string
foreach( $pfData as $key => $val ) {
    if( $key !== 'signature' ) {
        $pfParamString .= $key .'='. urlencode( $val ) .'&';
    } else {
        break;
    }
}
$pfParamString = substr( $pfParamString, 0, -1 );
$myfile = fopen("newfile.txt", "w+") or die("Unable to open file!");
fwrite($myfile, $pfParamString);
fclose($myfile);
?>