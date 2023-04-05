<?php


namespace app\core\db;


use app\core\Application;
use app\core\Model;

abstract class DatabaseModel extends Model {
    public static  $db;
    public function __construct(){
        self::$db = Application::$app->writeDB;
    }

    abstract public static function tableName() : string;
    abstract public function columns(): array;
    abstract public static function primaryKey(): string;

    public function save(){
        $tableName = $this->tableName();
        $columns = $this->columns();
        $columnNames = implode(',',$columns);

        $params = implode(',', array_map(fn($cn)=>'?', $columns));
        $values = array_map(fn($c)=>$this->{$c},$columns);
        $types = implode("",array_map(fn($c)=>$this->typeShorten($this->{$c}),$columns));

        $statement = self::prepare("INSERT INTO $tableName($columnNames) VALUES($params)");
        $statement->bind_param($types,...$values);
        $statement->execute();
        return true;

    }

    public static function findOne($where)/**[email=>xxx,name=>xxx**/{
        $tablename = static::tableName();
        $attributes = array_keys($where);
        $values = array_map(fn($val)=>self::escape($val),array_values($where));
        //SELECT * FROM $TABLENAME WHERE KEY = VALUE AND KEY = VALUE;
        $query = array_map(fn($key)=>"$key = ?",$attributes);
        $types = implode("",array_map(fn($attr)=>self::typeShorten($attr),$attributes));
        $whereQueryString = count($where)>1?implode(" AND ",$query):implode("",$query);
        $statement = self::prepare("SELECT * FROM $tablename WHERE $whereQueryString");
        $statement->bind_Param($types,...$values);
        $statement->execute();
        $result = $statement->get_result();
        return $result->fetch_object(static::class);
    }


    public function emailExists(){
        $sql = self::prepare("SELECT * FROM users WHERE email = ?");
        $sql->bind_param('s',$this->email);
        $sql->execute();
        return $sql->get_result()->num_rows > 0;
    }

    public static function prepare($sql){
        return self::$db->prepare($sql);
    }

    public static function escape($value){
        self::$db = self::$db ?? Application::$app->readDB;
        return self::$db->real_escape_string($value);
    }

    public static function typeShorten($value){
        if(gettype($value)==="string"){return 's';}
        if(gettype($value)==="integer"){return 'i';}
        if(gettype($value)==="boolean"){return 'i';}
        if(gettype($value)==="blob"){return 'b';}
        if(gettype($value)==="double"){return 'd';}
        return 's';
    }

}