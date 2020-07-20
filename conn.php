<?php
require_once 'vendor/autoload.php';

class conn
{
    public $database;

    public function __construct()
    {
        $this->database = new Medoo\Medoo([
            'database_type' => 'mysql',
            'database_name' => DB_NAME,
            'server' => DB_URL,
            'username' => DB_USER,
            'password' => DB_PASS
        ]);

    }

    public function getOutStations()
    {
        $data = $this->database->select('outstations', [
            'outstation_id',
            'outstation_name',
            'description'
        ]);

        return $data;
    }

    public function getBookingDetails($id, $name)
    {
        return [];
    }

    public function getGroups($outstation_id)
    {
        $data = $this->database->select('groups', [
            'group_id',
            'group_name',
            'estate_id'
        ], [
            'outstation_id' => $outstation_id
        ]);

        return $data;
    }

    public function getEstates($estateId)
    {
        $data = $this->database->select('estates', [
            'estate_id',
            'estate_name',
        ], [
            'estate_id' => $estateId
        ]);

        return $data;
    }

    public function getOutstationMass($outstation_id)
    {
        $data = $this->database->select('mass_schedule_master', [
            '[>]mass_schedule' => ['schedule_master_id' => 'id']
        ], [
            'group_id',
            'group_name',
            'estate_id'
        ], [
            'outstation_id' => $outstation_id
        ]);

        return $data;
    }
}