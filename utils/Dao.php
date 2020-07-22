<?php
$root_dir = dirname(dirname(__FILE__));

require_once $root_dir . '/vendor/autoload.php';

require_once $root_dir . '/config/config.php';

if (!defined('MyConst')) {
    die('Direct access not permitted');
}

class Dao
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
            "GROUP" => ["mass_schedule_date"],
            "ORDER" => ["mass_schedule_date" => 'ASC'],
        ]);

        return $data;
    }

    public function getMassStations($massDate)
    {
        $data = $this->database->select('mass_schedule_master', [
            '[><]outstations' => ['outstation_id' => 'outstation_id']
        ], [
            'mass_schedule_master.mass_schedule_date',
            'outstations.outstation_name',
            'outstations.outstation_id'
        ], [
            'mass_schedule_date[>=]' => $massDate,
            "GROUP" => ["outstations.outstation_name"],
            "ORDER" => ["outstations.outstation_name" => 'ASC'],
        ]);

        return $data;
    }

    /**
     * @param $outstation_id
     * @return array|bool
     */
    public function getActiveScheduledMasses($outstation_id)
    {
        $query = <<<SQL
SELECT
	mass_schedule_master.mass_schedule_date,
	mass_schedule.id,
	mass_schedule.capacity,
	masses.mass_id,
	masses.mass_title,
	masses.time_from,
	masses.time_to,
	mass_schedule.mass_status_id,
	mass_status.`status`,
	mass_status.status_description,
	mass_schedule_master.outstation_id,
	mass_schedule.schedule_master_id 
FROM
	mass_schedule_master
	INNER JOIN mass_schedule ON mass_schedule.schedule_master_id = mass_schedule_master.id
	INNER JOIN masses ON mass_schedule.mass_id = masses.mass_id
	INNER JOIN mass_status ON mass_schedule.mass_status_id = mass_status.mass_status_id 
WHERE
	mass_schedule_master.outstation_id = $outstation_id
ORDER BY
	masses.time_to ASC
SQL;

        /*$data = $this->database->debug()->select('mass_schedule_master', [
            '[><]mass_schedule' => ['schedule_master_id' => 'schedule_master_id'],
            '[><]masses' => ['mass_id' => 'mass_id'],
            '[><]mass_status' => ['mass_status_id' => 'mass_status_id'],
        ], [
           'mass_schedule_master.mass_schedule_date',
            'mass_schedule.id',
            'mass_schedule.capacity',
            'masses.mass_id',
            'masses.mass_title',
            'masses.time_from',
            'masses.time_to',
            'mass_schedule.mass_status_id',
            'mass_status.status',
            'mass_status.status_description',
            'mass_schedule_master.outstation_id',
            'mass_schedule.schedule_master_id'
        ],
            [
                //'mass_schedule.schedule_master_id' => $schedule_id,
                "ORDER" => ["masses.time_from" => 'ASC'],
            ]);*/

        $data = $this->database->query($query);

        return $data->fetchAll();
    }

    /**
     * @param $massId
     * @param $capacity
     * @return bool|int|mixed|string
     */
    public function getSeatsLeft($massId, $capacity, $debug = false)
    {
        $seatCount = $this->database->count("mass_registration", [
            'mass_schedule_id' => $massId
        ]);

        $seatsLeft = $capacity - $seatCount;

        return $seatsLeft;
    }

    /**
     * @param array $data
     * @param $tableName
     * @return int|mixed|string|null
     */
    public function insertIntoDatabase(array $data, $tableName)
    {
        $data = $this->database->insert($tableName, $data);

        $code = (int)$data->errorCode();

        $message = isset($data->errorInfo()[2]) ? $data->errorInfo()[2] : "Unable to save data";
        return [
            'hasError' => $code != 0,
            'errors' => $message
        ];
    }

}