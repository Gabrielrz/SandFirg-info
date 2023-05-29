<?php

require __DIR__ . '/../../vendor/autoload.php';
use Dotenv\Dotenv;
class C_Output_Inicio{

  function __construct()
  {
    $dotenv = Dotenv::createImmutable(__DIR__.'/../');
    $dotenv->safeLoad();

  }
}
$c_o_inicio=new C_Output_Inicio();

 ?>
