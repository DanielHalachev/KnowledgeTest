<?php

session_start();

spl_autoload_register(function($className) {
  require_once "../libs/$className.php";
});

set_exception_handler(function ($exception) {

  // if ($exception instanceof BadRequestException) {
  //   echo "Bad request: " . $exception->getMessage();
  // } elseif ($exception instanceof InternalServiceException) {
  //   echo "Internal service error, try again later. Error info: " . $exception->getMessage();
  // }  else {
  //   echo "Unexpected error occured " . $exception->getMessage();
  // }

  $responseCode = $exception->getCode() ?: 500;

  http_response_code($responseCode);
});

?>
