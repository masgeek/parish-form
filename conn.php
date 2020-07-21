<?php
require_once 'vendor/autoload.php';
require_once 'config.php';

class conn
{
    // [>] == LEFT JOIN
// [<] == RIGH JOIN
// [<>] == FULL JOIN
// [><] == INNER JOIN

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

    public function getActiveMassDates()
    {
        $data = $this->database->select('mass_schedule_master', [
            'mass_schedule_date',
        ], [
            'mass_schedule_date[>=]' => Medoo\Medoo::raw('CURDATE()'),
            "ORDER" => ["mass_schedule_date" => 'ASC'],
            "GROUP" => ["mass_schedule_date"],
        ]);

        return $data;
    }

    public function getMassStations($massDate)
    {
        $data = $this->database->select('mass_schedule_master', [
            '[><]outstations' => ['outstation_id' => 'outstation_id']
        ], [
            'mass_schedule_master.id',
            'mass_schedule_master.mass_schedule_date',
            'outstations.outstation_name',
            'outstations.outstation_id'
        ], [
            'mass_schedule_date[>=]' => $massDate,
            "ORDER" => ["outstations.outstation_name" => 'ASC'],
        ]);

        return $data;
    }

    public function getActiveScheduledMasses($schedule_id)
    {
        $data = $this->database->select('mass_schedule', [
            '[><]masses' => ['mass_id' => 'mass_id']
        ], [
            'mass_schedule.id',
            'mass_schedule.mass_id',
            'mass_schedule.schedule_master_id',
            'mass_schedule.mass_status_id',
            'mass_schedule.capacity',
            'masses.mass_title',
            'masses.time_from',
            'masses.time_to'
        ], [
            'mass_schedule.schedule_master_id' => $schedule_id,
            "ORDER" => ["masses.time_from" => 'ASC'],
        ]);

        return $data;
    }
}