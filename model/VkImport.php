<?php

namespace studyqa\model;

use Exception;

error_reporting(E_ALL);
ini_set("display_errors","On");

class VkImport {

    var $headerOptions;
    var $lang=0;
    var $countryId = 1; // Russia

    function __construct(){
        $this->headerOptions = array(
            'http' => array(
                'method' => "GET",
                'header' => "Accept-language: en\r\n" .
                    "Cookie: remixlang=$this->lang\r\n"
            )
        );
    }

    /**
     * Return array   response: {
     *                           count: 85,
     *                           items: [{
     *                                   id: 1121483,
     *                                   title: 'Агинский Бурятский АО'
     *                                  }
     *                          }
     */
    public function getRegions($countryId,$offset,$count){
        $methodUrlGetRegions = 'http://api.vk.com/method/database.getRegions?v=5.27&offset='.$offset.'&count='.$count.'&country_id=' . $countryId;
        try{
            $streamContext = stream_context_create($this->headerOptions);
            $json = file_get_contents($methodUrlGetRegions, false, $streamContext);
        }catch (Exception $e){
            echo('ERROR '.$e);
            exit();
        }

        $regions=json_decode($json, true);
        return $regions;

    }


    public function getCities($countryId,$offset,$count,$regionId=null,$need_all){
        if($regionId){
            $region='&region_id='.$regionId;
        }else{
            $region='';
        }
        try{
            $methodUrlGetCities= 'http://api.vk.com/method/database.getCities?v=5.27&need_all='.$need_all.'&offset='.$offset.'&count='.$count.'&country_id=' . $countryId.$region;
            echo $methodUrlGetCities;
            $streamContext = stream_context_create($this->headerOptions);
        }catch (Exception $e){
            echo('ERROR '.$e);
            exit();
        }

        $json = file_get_contents($methodUrlGetCities, false, $streamContext);
        $cities=json_decode($json, true);
        return $cities;

    }

} 