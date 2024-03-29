<?php

require_once __DIR__.'/../vendor/autoload.php';
require_once __DIR__.'/conectar.php';
use Rakit\Validation\Rule;
class UniqueRule extends Rule
{
    protected $message = ":attribute :value ya esta en uso, intente con otro :attribute";
    protected $fillableParams = ['table', 'column', 'except'];
    protected $pdo;

    public function __construct()
    {
        $this->pdo = Conectar::conexion();
    }

    public function check($value): bool
    {
        // make sure required parameters exists
        $this->requireParameters(['table', 'column']);
        // getting parameters
        $column = $this->parameter('column');
        $table = $this->parameter('table');
        $except = $this->parameter('except');
        if ($except AND $except == $value) {
            return true;
        }
        // do query
        $stmt = $this->pdo->prepare("select count(*) as count from `{$table}` where `{$column}` = :value");
        $stmt->bindParam(':value', $value);
        $stmt->execute();
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        // true for valid, false for invalid
        return intval($data['count']) === 0;
    }
}
