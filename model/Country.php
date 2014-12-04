<?php

namespace studyqa\model;


class Country {


    public function getAll(){
        //сообщение об ошибки
        $this->$mysqli=Db::init();
        if(!($this->mysqli instanceof mysqli)){
            exit ($this->mysqli);
        }


        $stmt =$this->mysqli->prepare("SELECT * FROM country");
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

        $result=$this->mysqli->query("SELECT * FROM country WHERE country_id=$id");
        $row=$result->fetch_assoc();
        $this->mysqli->close();

        return $row;
    }
    public function getByFieldValue($field,$value){


        $this->mysqli=Db::init();
        if(!($this->mysqli instanceof mysqli)){
            exit ($this->mysqli);
        }

        $result=$this->mysqli->query("SELECT * FROM country WHERE $field=$value");
        $row=$result->fetch_assoc();
        $this->mysqli->close();

        return $row;
    }

} 