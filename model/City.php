<?php

namespace studyqa\model;


use Exception;
use mysqli;
use studyqa\Db;

class City {
    var $mysqli;
    var $i=0;

    public function getAll(){
        //сообщение об ошибки
        $this->mysqli=$mysqli=Db::init();
        if(!($this->mysqli instanceof mysqli)){
            exit ($this->mysqli);
        }


        $stmt =$this->mysqli->prepare("SELECT * FROM city");
        $stmt->execute();
        $stmt->store_result();
        $meta = $stmt->result_metadata();
        while ($field = $meta->fetch_field())
        {
            $params[] = &$row[$field->name];
        }

        call_user_func_array(array($stmt, 'bind_result'), $params);

        while ($stmt->fetch()) {
            foreach($row as $key => $val)
            {
                $c[$key] = $val;
            }
            $result[] = $c;
        };
        $stmt->free_result();
        $this->mysqli->close();


        return $result;
    }


    public function getById($id){


        $this->mysqli=Db::init();
        if(!($this->mysqli instanceof mysqli)){
            exit ($this->mysqli);
        }

        $result=$this->mysqli->query("SELECT * FROM city WHERE city_id=$id");
        $row=$result->fetch_assoc();
        $this->mysqli->close();

        return $row;
    }


    public function countRecords(){
        if(!(isset($this->mysqli) AND $this->mysqli!==null)){

            $this->mysqli=Db::init();

        }
        $result=$this->mysqli->query("SELECT COUNT(*) FROM city");
        $row = $result->fetch_row();
        $this->mysqli->close();

        return $row[0];

    }


    public function setOne($countryId,$region,$area,$city_id,$city_name){
        $this->mysqli=Db::init();
        if(!($this->mysqli instanceof mysqli)){
            exit ($this->mysqli);
        }
        print_r($this->mysqli);


        $stmt = $this->mysqli->prepare("INSERT INTO city (city_id, city_name,city_country_id,city_region_name,city_area) VALUES(?,?,?,?,?) ");

        if($stmt==false){
            echo 'Не удалось выполнить prepare:';
            echo $this->mysqli->error;
            exit();
        }

        $stmt->bind_param('isiss',$city_id,$city_name,$countryId,$region,$area);

        try{
            if (!empty($stmt)) {
                $r=$stmt->execute();
                if($r===false){
                    echo $this->mysqli->error;
                }
            }
        }catch(Exception $e){
            echo $e;
        }

        $stmt->free_result();
        $this->mysqli->close();


    }
} 