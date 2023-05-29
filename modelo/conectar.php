<?php
require_once __DIR__.'/../vendor/autoload.php';
use Dotenv\Dotenv;
class Conectar{

    public function __construct(){

    }


    public static  function conexion(){

        $dotenv = Dotenv::createImmutable(__DIR__.'/../');
        $dotenv->safeLoad();
        try{//'mysql:host=localhost;dbname=6598182_linksringtones','root',''
          $conexion=new PDO($_ENV['DB_CONNECTION']
                            .':host='.$_ENV['DB_HOST']
                            .';dbname='.$_ENV['DB_DATABASE'],
                             $_ENV['DB_USERNAME'],
                             $_ENV['DB_PASSWORD']);
          // $conexion->setAttribute(PDO::MYSQL_ATTR_INIT_COMMAND,'SET session sql_mode=""');
          $conexion->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
          $conexion->exec("SET CHARACTER SET UTF8");
        }catch(\Exception $e){
            die("Error!!!".$e->getMessage());
            echo "Linea de error:".$e->getLine();
        }
        return $conexion;

    }


}





?>
