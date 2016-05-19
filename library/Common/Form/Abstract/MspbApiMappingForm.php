<?php

abstract class Common_Form_Abstract_MspbApiMappingForm extends Common_Form_Abstract_MspbApiForm
{
    public function getColumns(){
        return array_keys($this->getColumnMapping());
    }

    public function getColumnsValues(){
        return array_values($this->getColumnMapping());
    }

    public function getOptimaPopulate($response){
        $arr = array();
        foreach($this->getColumnMapping() as $column => $values){
            //Zmiana formatu daty ponieważ daty z optimy przychodzą w formacie innym niż formularz z datą przyjmuje
            if (DateTime::createFromFormat('Y-m-d H:i:s', $response[$values]) !== FALSE) {
                $arr[$column] = date("Y-m-d", strtotime($response[$values]));
            }
            else{
                $arr[$column] = $response[$values];
            }
        }
        return $arr;
    }



    public function localColumnToOptima($sLocalColumn){
        $aMap = $this->getColumnMapping();
        if(isset($aMap[$sLocalColumn])){
            return $aMap[$sLocalColumn];
        }
        return $sLocalColumn;
    }
}