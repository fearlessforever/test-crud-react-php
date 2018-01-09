<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

function my_error_handler($errno, $errstr, $errfile, $errline)
{
    if (!(error_reporting() & $errno))
    {
        // This error code is not included in error_reporting
        return;
    }
    log_message('error', "$errstr @$errfile::$errline($errno)" );
    throw new ErrorException( $errstr, $errno, 0, $errfile, $errline );
}

set_error_handler("my_error_handler");

function my_exception_handler($exception)
{
    header( "HTTP/1.0 500 Internal Server Error" );
    echo '<pre>'; print_r($exception); echo '</pre>';
    
}
set_exception_handler("my_exception_handler");


function my_fatal_handler()
{
    $errfile = "unknown file";
    $errstr  = "Fatal error";
    $errno   = E_CORE_ERROR;
    $errline = 0;
    $error = error_get_last();
    if ( $error !== NULL )
    {
	header( "HTTP/1.0 500 Internal Server Error" );
	echo '<pre>'; print_r($error); echo '</pre>';
    }
}
register_shutdown_function("my_fatal_handler");

function my_assert_handler($file, $line, $code)
{
    log_message('debug', "assertion failed @$file::$line($code)" );
    throw new Exception( "assertion failed @$file::$line($code)" );
}
assert_options(ASSERT_ACTIVE,     1);
assert_options(ASSERT_WARNING,    0);
assert_options(ASSERT_BAIL,       0);
assert_options(ASSERT_QUIET_EVAL, 0);
assert_options(ASSERT_CALLBACK, 'my_assert_handler');