<?php


namespace app\core\db;


use Cassandra\Date;
use DateTimeZone;

class Database{
    private \mysqli $mysql;
    private static $writeDBConnection;
    private static $readDBConnection;

    public static string $dsn;
    public static string $user;
    public static string $password;
    public static string $dbName;

    public array $newMigrations=[];
    /**
     * Database constructor.
     * @param string $dsn
     * @param string $user
     * @param string $password
     * @param string $dbName
     */
    public function __construct(array $config){
        self::$dsn = $config['dsn'] ?? '';
        self::$user = $config['user'] ?? '';
        self::$password = $config['password'] ?? '';
        self::$dbName = $config['dbName'] ?? '';
    }


    public static function connectWriteDB(){
        if(self::$writeDBConnection === null){
            self::$writeDBConnection = new \mysqli(self::$dsn,self::$user,self::$password,self::$dbName) or die("error");
            if (self::$writeDBConnection->connect_error) {
                throw new Exception("Database connection error_m");
                die("connection failed:" . self::$writeDBConnection->connect_error . self::$writeDBConnection->connect_errno);
            }
        }
        return self::$writeDBConnection;
    }

    public static function connectReadDB(){
        if(self::$readDBConnection === null){
            self::$readDBConnection = new \mysqli(self::$dsn,self::$user,self::$password,self::$dbName) or die("error");
            if (self::$readDBConnection->connect_error) {
                throw new Exception("Database connection error_s");
                die("connection failed:" . self::$readDBConnection->connect_error);
            }
        }
        return self::$readDBConnection;
    }

    public function applyMigrations(){
        $this->createMigrationsTable();
        $appliedMigrations = $this->getAppliedMigrations();
        $files = scandir(Application::$ROOT_DIR."/migrations");
        $toApplyMigrations = array_diff($files, $appliedMigrations);

        foreach ($toApplyMigrations as $migration){
            if($migration==='.'||$migration==='..'){continue;}

            require_once Application::$ROOT_DIR.'/migrations/'.$migration;
            $className = pathinfo($migration, PATHINFO_FILENAME);
            $this->log("applying migration $className");
            //call_user_func([new$className,'up']);
            $instance = new $className;
            $instance->up();
            $this->log("applied migration $migration".PHP_EOL);
            $this->newMigrations[] = $migration;

        }

        if(!empty($this->newMigrations)){
            $this->saveMigrations($this->newMigrations);
        }else {

            $this->log("All Migrations are applied");
        }


    }

    public function createMigrationsTable(){
        $this->connectWriteDB()->prepare(
            "CREATE TABLE IF NOT EXISTS migrations
            ( 
            id INT AUTO_INCREMENT PRIMARY KEY,
            migration varchar(255),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP 
            ) ENGINE=INNODB;"
        )->execute();
    }


    public function getAppliedMigrations(){

        $sql = $this->connectReadDB()->prepare("SELECT migration FROM migrations");
        $sql->execute();
        $result = $sql->get_result();
        //$rowcount = $result->num_rows;
        $row = $result->fetch_all(MYSQLI_ASSOC);
        $allarr =array_map(fn($m)=>$m['migration'],$row);
        //$row = $result->fetch_array(MYSQLI_NUM);
        return $allarr;
    }


    public function saveMigrations(array $migrations){
        //$str = implode(",",array_map(fn($m)=>"('$m')",$migrations));//add quotes and comma
        $migrations = array_map(fn($m)=>"'".$this::connectReadDB()->real_escape_string($m)."'",$migrations);//add quotes and comma
        foreach ($migrations as $str) {
            $sql = $this->connectWriteDB()->prepare("INSERT INTO migrations (migration) VALUES($str)");
            $sql->execute();
        }

    }

    protected function log($maessage){
//        $date = date('Y-m-d H:i:s');
//        echo $date.PHP_EOL;
//        $date =  date_create($date);
//        echo date_timezone_get($date)->getName().PHP_EOL;
//
//        $date->setTimezone(new DateTimeZone('Africa/Lagos'));
//        echo date_timezone_get($date)->getName().PHP_EOL;
//        echo $date->format('Y-m-d H:i:s');

        echo '['.date('Y-m-d H:i:s').']-'.$maessage.PHP_EOL;
    }


}