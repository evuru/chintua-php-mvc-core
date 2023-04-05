<?php


namespace evuru\chintuaphpmvc;


class Migration{
    public $db;
    public function __construct(){
        $this->db = Application::$app->writeDB;
    }
    public function table_exists($table_name){
        $sql = $this->db->prepare("SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name ='$table_name'");
        $sql->execute();
        $result = $sql->get_result();
        $row = $result->fetch_assoc();
        //ßprint_r($row);
        return $row['COUNT(*)'];
    }


}