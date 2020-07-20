<?php
require_once 'vendor/autoload.php';

class conn
{
    public $database;

    public function __construct()
    {
        $this->database = new Medoo\Medoo([
            'database_type' => 'mysql',
            'database_name' => 'name',
            'server' => 'localhost',
            'username' => 'your_username',
            'password' => 'your_password'
        ]);

    }

    public function getOutStations()
    {
        $data = $this->database->select('oustations', [
            'outstation_name',
            'description'
        ]);

        return json_encode($data);
    }
}