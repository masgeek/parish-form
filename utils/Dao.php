<?php


$root_dir = dirname(dirname(__FILE__));

require_once $root_dir . '/vendor/autoload.php';
require_once $root_dir . '/config/config.php';

if (!defined('MyConst')) {
    die('Direct access not permitted');
}

/**
 * Class Dao
 */
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

        if ($data == false) {
            return [];
        }
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
            'mass_schedule_date' => $massDate,
            "GROUP" => ["outstations.outstation_name"],
            "ORDER" => ["outstations.outstation_name" => 'ASC'],
        ]);

        return $data;
    }

    /**
     * @param $outstation_id
     * @param $scheduleDate
     * @return array|bool
     */
    public function getActiveScheduledMasses($outstation_id, $scheduleDate)
    {
        $data = $this->database->select('v_mass_schedule', [
            'mass_schedule_date',
            'id',
            'capacity',
            'choir_capacity',
            'lector_seat_no',
            'mass_id',
            'mass_title',
            'time_from',
            'time_to',
            'mass_status_id',
            'mass_status',
            'status_description',
            'outstation_id',
            'schedule_master_id'
        ], [
            'outstation_id' => $outstation_id,
            'mass_schedule_date' => $scheduleDate,
            "ORDER" => ["time_to" => 'ASC'],
        ]);

        if ($data == false) {
            return [];
        }
        return $data;
    }

    /**
     * @param $massId
     * @param $capacity
     * @return bool|int|mixed|string
     */
    public function getChoirSeatsLeft($massId, $capacity)
    {
        $seatCount = $this->database->count("mass_registration", [
            'mass_schedule_id' => $massId,
            'is_choir' => 1
        ]);

        return $capacity - $seatCount;
    }

    /**
     * @param $massId
     * @param $capacity
     * @return bool
     */
    public function isLectorSeatAssigned($massId, $capacity)
    {
        $seatCount = $this->database->count("mass_registration", [
            'mass_schedule_id' => $massId,
            'is_lector' => 1
        ]);

        return $seatCount > 0;
    }


    /**
     * @param $massScheduleId
     * @return int|mixed
     */
    public function getMassScheduleChoirCapacity($massScheduleId)
    {
        $capacity = $this->database->select("mass_schedule", [
            'choir_capacity'
        ], [
            'id' => $massScheduleId
        ]);
        if ($capacity != false) {
            return ($capacity[0]['choir_capacity']);
        }
        return 0;
    }

    public function getMassScheduleCapacity($massScheduleId)
    {
        $capacity = $this->database->select("mass_schedule", [
            'capacity'
        ], [
            'id' => $massScheduleId
        ]);
        if ($capacity != false) {
            return ($capacity[0]['capacity']);
        }
        return 0;
    }

    /**
     * @param $scheduleId
     * @param $capacity
     * @return bool|int|mixed|string
     */
    public function getSeatsLeft($scheduleId, $capacity)
    {
        $seatCount = $this->database->count("mass_registration", [
            'mass_schedule_id' => $scheduleId
        ]);

        return $capacity - $seatCount;
    }

    /**
     * @param $scheduleId
     * @param $surname
     * @param $otherNames
     * @param $phoneNumber
     * @return array|bool
     */
    public function isAlreadyRegistered($scheduleId, $surname, $otherNames, $phoneNumber)
    {
        $regCount = $this->database->count('mass_registration', [
            'id',
        ], [
            'mass_schedule_id' => $scheduleId,
            'surname' => $surname,
            'other_names' => $otherNames,
            'mobile' => $phoneNumber,
        ]);

        return $regCount > 0;

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

    public function getSeatsArray($massCapacity)
    {
        $seats = [];
        for ($x = 1; $x <= $massCapacity; $x++) {
            $seats[] = $x;
        }
        return $seats;
    }

    /**
     * @param $massScheduleId
     * @param int $choirSeats
     * @return mixed
     */
    public function getAllocatedSeats($massScheduleId, $choirSeats = 0)
    {
        $data = $this->database->select("mass_registration", [
            'seat_no'
        ], [
            'mass_schedule_id' => $massScheduleId,
            'is_choir' => $choirSeats
        ]);

        $seats = [];
        foreach ($data as $key => $item) {
            $seats[] = (int)$item['seat_no'];
        }
        return $seats;
    }

    /**
     * @param $tableName
     * @param array $fields fields to fetch
     * @param array $condition query conditions
     * @return array|bool
     */
    public function selectData($tableName, array $fields, array $condition)
    {
        return $this->database->select($tableName, $fields, $condition);
    }

    public function executeQuery($query)
    {
        return $this->database->query($query)->fetchAll();
    }
}