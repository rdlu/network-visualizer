<?php
/**
 * Model_Kpi: guarda os resultados os indicadores auxiliares de performance
 * KPI: Key Performance Indicator
 */
class Model_Kpi
{
    private $config = 'results';
    private $db;
    private $tableName = null;
    private $entityID;

    private static $instances = array();

    private function __construct($entityID)
    {
        $this->db = Database::instance($this->config);
        if (!$this->tableName) {
            $this->tableName = "res_kpi";
        }
        $this->entityID = (int)$entityID;

        return $this;
    }

    /**
     * @static
     * @param $profileID
     * @param $metricID
     * @return Model_Kpi
     */
    public static function factory($entityID)
    {
        if (!isset(Model_Kpi::$instances[$entityID])) {
            Model_Kpi::$instances[$entityID] = new Model_Kpi($entityID);
        }

        return Model_Kpi::$instances[$entityID];
    }

    public function createDB()
    {
        $fields =
            "`id` INT(32) UNSIGNED NOT NULL AUTO_INCREMENT,
            `destination_id` INT(3),
            `timestamp` TIMESTAMP NOT NULL,
            `cell_id` VARCHAR(8),
            `brand` VARCHAR(20),
            `model` VARCHAR(20),
            `conn_type` VARCHAR(10),
            `conn_tech` VARCHAR(10),
            `signal` VARCHAR(10),
            `error_rate` VARCHAR(10),
            `number_of_ips` INT(4),
            `mtu` INT(4),
            `dnsLatency` VARCHAR(10),
            `lac` VARCHAR(10),
            `polling` INT(3),
            `route` TEXT,
            `stored` TIMESTAMP,
            `source_name` VARCHAR(127),
            `destination_name` VARCHAR(127),
              PRIMARY KEY (`id`),
              UNIQUE INDEX `PROF` (`destination_id`, `timestamp`),
              INDEX `Times` (`timestamp`) USING BTREE";
        $result = DB::query(NULL, "CREATE TABLE IF NOT EXISTS `" . $this->tableName . "` ($fields)")->execute($this->db);
        DB::query(NULL, "CREATE TABLE IF NOT EXISTS `" . $this->tableName . "_quarter` ($fields)")->execute($this->db);
        return $result;
    }

    /**
     * @param int $processId
     * @param array $values
     */
    public function insert(array $values)
    {
        $fields = array("cell_id", "brand", "model", "conn_type", "conn_tech", "signal", "error_rate", "number_of_ips", "mtu", "dns_latency", "lac", "route", "polling", "timestamp", "source_name", "destination_name", "stored");
        $newarr = array();

        if ($this->validate($values)) {
            $values['destination_id'] = $this->entityID;
            $values['timestamp'] = date("Y-m-d H:i:s", $values['timestamp']);
            $values['stored'] = date("Y-m-d H:i:s");
            foreach ($fields as $field) {
                $newarr[$field] = $values[$field];
            }
            $query = DB::insert($this->tableName, $fields)->values($newarr);
            /*foreach($values as $k => $result) {
                Kohana_Log::instance()->add(Kohana_Log::DEBUG,'@SQL '.$k.': '.$result);
            }*/
        } else return null;
        return $this->db->query(Database::INSERT, $query);
    }

    public function query($timestampMin, $timestampMax)
    {
        $query = DB::select()->from($this->tableName)->where("destination_id", "=", $this->entityID)->where('timestamp', 'BETWEEN', array($timestampMin, $timestampMax));
        return $this->db->query(Database::SELECT, $query);
    }

    protected function validate(array $values)
    {
        $validation = Validation::factory($values);
        $validation
            ->rule("timestamp", "digit");

        $return = $validation->check();

        if (!$return) {
            foreach ($validation->errors() as $k => $error) Kohana_Log::instance()->add(Kohana_Log::DEBUG, '@Validation ' . $k . ': ' . $error);
            throw new Validation_Exception($validation, "Validation on SQL results failed.", $values);
        }

        return $return;
    }
}
