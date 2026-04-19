<?php

# Display server software details
echo 'Web Server : ' . $_SERVER[ 'SERVER_SOFTWARE' ] . '<br>' ;

# Display the current script name
echo 'This Script : ' . $_SERVER[ 'PHP_SELF' ] . '<br>' ;

# Display the host name
echo 'Host Name : ' . $_SERVER[ 'HTTP_HOST' ] . '<br>' ;

# Display the HTTP request method
echo 'Request Method : ' . $_SERVER[ 'REQUEST_METHOD' ] ;

# Display any URL parameters passed via HTTP GET
foreach( $_GET as $key => $value )
{ echo '<hr>HTTP GET : ' . $key . '=' . $value ; }

# Display all global variable names as a bulleted list
foreach( $GLOBALS as $key => $var )
{
    echo "&bull; $key <br>" ;
}

