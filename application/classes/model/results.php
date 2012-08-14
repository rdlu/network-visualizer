<?php
/**
 * Model Results: Lógica de Dados para os resultados em sql server
 * @author Rodrigo Dlugokenski
 * @depends Model_Profile, Model_Metric
 */
class Model_Results
{

    private $config = 'results';
    private $db;
    private $tableName = null;

    private $profile = null;
    private $metric = null;
    private static $instances = array();

    private function __construct($profileID, $metricID)
    {
        $this->profile = ORM::factory('profile', $profileID);
        $this->metric = ORM::factory('metric', $metricID);
        $this->db = Database::instance($this->config);
        if (!$this->tableName) {
            $this->tableName = "res_" . $this->metric->name . "_" . $this->profile->id;
        }
    }

    /**
     * @static
     * @param $profileID
     * @param $metricID
     * @return Model_Results
     */
    public static function factory($profileID, $metricID)
    {
        if (!isset(Model_Results::$instances[$profileID . ':' . $metricID])) {
            Model_Results::$instances[$profileID . ':' . $metricID] = new Model_Results($profileID, $metricID);
        }

        return Model_Results::$instances[$profileID . ':' . $metricID];
    }

    public function createDB()
    {
        $fields =
            "`id` INT(32) UNSIGNED NOT NULL AUTO_INCREMENT,
            `process_id` INT(3),
            `timestamp` TIMESTAMP NOT NULL,
            `dsavg` DOUBLE,
            `sdavg` DOUBLE,
            `dsmin` DOUBLE,
            `sdmin` DOUBLE,
            `dsmax` DOUBLE,
            `sdmax` DOUBLE,
            `stored` TIMESTAMP,
            `source_name` VARCHAR(127),
            `destination_name` VARCHAR(127),
              PRIMARY KEY (`id`),
              UNIQUE INDEX `PROF` (`process_id`, `timestamp`),
              INDEX `Times` (`timestamp`) USING BTREE";
        $result = DB::query(NULL, "CREATE TABLE IF NOT EXISTS `" . $this->tableName . "` ($fields)")->execute($this->db);
        DB::query(NULL, "CREATE TABLE IF NOT EXISTS `" . $this->tableName . "_quarter` ($fields)")->execute($this->db);
        return $result;
    }

    /**
     * @param int $processId
     * @param array $values
     */
    public function insert($processId, array $values)
    {
        $fields = array("process_id", "dsavg", "sdavg", "dsmin", "sdmin", "dsmax", "sdmax", "timestamp", "source_name", "destination_name", "stored");
        if ($this->validate($values)) {
            $values['timestamp'] = date("Y-m-d H:i:s", $values['timestamp']);
            $values['stored'] = date("Y-m-d H:i:s", $values['stored']);
            $query = DB::insert($this->tableName, $fields)->values($values);
            /*foreach($values as $k => $result) {
                Kohana_Log::instance()->add(Kohana_Log::DEBUG,'@SQL '.$k.': '.$result);
            }*/
        } else return null;
        return $this->db->query(Database::INSERT, $query);
    }

    public function query($processID, $timestampMin, $timestampMax)
    {
        $query = DB::select()->from($this->tableName)->where("process_id", "=", $processID)->where('timestamp', 'BETWEEN', array($timestampMin, $timestampMax));
        return $this->db->query(Database::SELECT, $query);
    }

    private function optimize($processID)
    {
        //marcado para 1h am do dia atual
        $now = $quarter[4] = date("U", mktime(1, 0, 0, date("m"), date("d"), date("Y")));
        //19h pm
        $quarter[3] = $now - 21600;
        //13h pm
        $quarter[2] = $now - 43200;
        $quarter[1] = $now - 64800;
        $quarter[0] = $now - 86400;

        for ($i = 3; $i > 0; $i--) {
            $results = $this->query($processID, $quarter[$i], $quarter[$i + 1]);
        }

    }

    protected function validate(array $values)
    {
        $validation = Validation::factory($values);
        $validation->rule("process_id", "digit")
            ->rule("timestamp", "digit");

        $return = $validation->check();

        if (!$return) {
            foreach ($validation->errors() as $k => $error) Kohana_Log::instance()->add(Kohana_Log::DEBUG, '@Validation ' . $k . ': ' . $error);
            throw new Validation_Exception($validation, "Validation on SQL results failed.", $values);
        }

        return $return;
    }


}