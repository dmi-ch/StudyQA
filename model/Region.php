<?php

namespace studyqa\model;

use mysqli;
use studyqa\Db;


error_reporting(E_ALL);
ini_set("display_errors","On");

/**
 * This is the model class for table "region".
 * @property mysqli  $mysqli
 * @property integer $region_id
 * @property string  $region_name
 * @property array   $region_country_id
 */

class Region
{
    var $mysqli;
    var $i=0;

    public function getAll(){
        //сообщение об ошибки
        $this->mysqli=$mysqli=Db::init();
        if(!($this->mysqli instanceof mysqli)){
            exit ($this->mysqli);
        }


        $stmt =$this->mysqli->prepare("SELECT * FROM region");
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

        $result=$this->mysqli->query("SELECT  * FROM region WHERE region_id=$id");
        $row=$result->fetch_assoc();
        $this->mysqli->close();

        return $row;
    }

    public function getByFieldValue($field,$value){


        $this->mysqli=Db::init();
        if(!($this->mysqli instanceof mysqli)){
            exit ($this->mysqli);
        }
        //'Белгородская область'
        $result=$this->mysqli->query("SELECT * FROM region WHERE $field='$value'");
        echo $this->mysqli->error;
        if (!empty($result)) {
            $row=$result->fetch_assoc();
        }else{
            $row=null;
        }
        $this->mysqli->close();

        return $row;
    }

    public function countRecords(){
        if(!(isset($this->mysqli) AND $this->mysqli!==null)){

            $this->mysqli=$mysqli=Db::init();
            if(!($mysqli instanceof \mysqli)){
                exit ($mysqli);
            }
        }
        $result=$this->mysqli->query("SELECT COUNT(*) FROM region");
        $row = $result->fetch_row();
        $this->mysqli->close();

        return $row[0];

    }


    public function insertRow($countryId,$region_id,$region_name){
        $this->mysqli=$mysqli=Db::init();
        if(!($this->mysqli instanceof mysqli)){
            exit ($this->mysqli);
        }
         $stmt = $this->mysqli->prepare("INSERT INTO region (region_id, region_name, region_country_id) VALUES(?,?,?) ");
         $stmt->bind_param('isi',$region_id,$region_name,$countryId);
        try{
            if (!empty($stmt)) {
                $r=$stmt->execute();
                if($r===false){
                    echo $this->mysqli->error;
                }else{
                    return  date("Y-m-d H:i:s").'Imported successfuly.</p>';

                }
            }
        }catch(Exception $e){
            echo $e;
        }
         $stmt->execute();
         $stmt->free_result();
         $this->mysqli->close();
    }

    public function updateRow($region_id,$field,$value){
        $this->mysqli=$mysqli=Db::init();
        if(!($this->mysqli instanceof mysqli)){
            exit ($this->mysqli);
        }
        $stmt = $this->mysqli->prepare("UPDATE region SET $field=$value WHERE region_id=$region_id");
        try{
            if (!empty($stmt)) {
                $r=$stmt->execute();
                if($r===false){
                    echo $this->mysqli->error;
                }else{
                    return  date("Y-m-d H:i:s").'Updated successfuly.</p>';

                }
            }
        }catch(Exception $e){
            echo $e;
        }
        $stmt->execute();
        $stmt->free_result();
        $this->mysqli->close();
    }


}
