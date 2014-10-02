<?php
/*
 * Optional include to activate FirePHP debugger for Firefox
 * 
 * */
?>

<?php
require_once('FirePHPCore/FirePHP.class.php');
ob_start();

$firephp = FirePHP::getInstance(true);
$firephp->registerErrorHandler(
            $throwErrorExceptions=false);
$firephp->registerExceptionHandler();
$firephp->registerAssertionHandler(
            $convertAssertionErrorsToExceptions=true,
            $throwAssertionExceptions=false);
 
try {
  //throw new Exception('Test Exception');
} catch(Exception $e) {
  $firephp->error($e);  // or FB::
}
?>
