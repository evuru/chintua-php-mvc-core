<?php


namespace evuru\chintuaphpmvc;


abstract class Model{
    public const RULE_REQUIRED  ='required';
    public const RULE_EMAIL     ='email';
    public const RULE_MAX       ='max';
    public const RULE_MIN       ='min';
    public const RULE_MATCH     ='match';

    public const RULE_UNIQUE = 'unique';
    public const RULE_INVALID = 'invalid';

    public array $errors=[];


    public function loadData($data){
        foreach ($data as $key=>$value){
            if(property_exists($this, $key)){
                $this->{$key}=$value;
            }
        }
    }

    abstract public function rules() : array;
    abstract public function labels(): array ;

    public function validate(){
        $error = [];
        foreach ($this->rules() as $attribute => $rules){
            $value = $this->{$attribute};
            foreach ($rules as $rule){
                $ruleName = $rule;
                if(!is_string($ruleName)){
                    $ruleName = $rule[0];
                }
                if($ruleName==self::RULE_REQUIRED && !$value){
                    $this->addErrors($attribute,self::RULE_REQUIRED);
                }
                if($ruleName==self::RULE_EMAIL && !filter_var($value, FILTER_VALIDATE_EMAIL)){
                    $this->addErrors($attribute,self::RULE_EMAIL);
                }
                if($ruleName==self::RULE_MIN && strlen($value)<$rule['min']){
                    $this->addErrors($attribute,self::RULE_MIN, $rule);
                }
                if($ruleName==self::RULE_MAX && strlen($value)>$rule['max']){
                    $this->addErrors($attribute,self::RULE_MAX, $rule);
                }
                if($ruleName==self::RULE_MATCH && $value!==$this->{$rule['match']}){
                    $this->addErrors($attribute,self::RULE_MATCH,$rule);
                }
//                echo "<pre>";
//                var_dump($value."   ---   ".$ruleName);
//                echo "</pre>";
            }
        }
        return empty($this->errors);
    }


//add errors for rules
    private function addErrors(string $attribute, String $rule, $ruleParams=[]){
        $message = $this->errorMessages($this->labels()[$attribute]??$attribute)[$rule] ?? '';
        foreach ($ruleParams as $key => $value){
           $message = str_replace("{{$key}}", $value, $message);
        }
            $this->errors[$attribute][]= $message;
    }

    public function addError(string $attribute,$rule){
        $message=$this->errorMessages($this->labels()[$attribute])[$rule];
        $this->errors[$attribute][]=$message;

    }







    public function errorMessages($field){
        return[
            self::RULE_REQUIRED=>"{$field} is required ",
            self::RULE_EMAIL=>"{$field} must be a valid email address ",
            self::RULE_MAX=>"Maximum length for {$field} {max} ",
            self::RULE_MIN=>"Minimum length for {$field} {min} ",
            self::RULE_MATCH=>"{$field} must be the same as {match} ",


            self::RULE_UNIQUE=>"{$field} already exists",
            self::RULE_INVALID=>"invalid {$field}",
        ];
    }

    public function hasError($attribute){
        return $this->errors[$attribute]??false;
    }

}