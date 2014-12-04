<?php
namespace studyqa;

use studyqa\model\City;
use studyqa\model\Country;
use studyqa\model\Region;
use SplClassLoader;
use studyqa\model\Translate;
use studyqa\model\VkImport;

ini_set("max_execution_time", "360");
error_reporting(E_ALL);
ini_set("display_errors","On");


include 'SplClassLoader.php';
$loader = new SplClassLoader('studyqa', '../');
$loader->register();


class Main{
    const COUNTRY_RUSSIA_ID=1;
    var $regionModel;
    var $vkImportModel;
    var $cityModel;


    function __construct(){
        $this->regionModel=new Region();
        $this->vkImportModel=new VkImport();
        $this->cityModel=new City();
    }

    public function getModel($tableName){
        switch ($tableName){
            case 'city':
                $c=new City();
                return $c;
                break;
            case 'region';
                $c=new Region();
                return $c;
                break;
            case 'country';
                $c=new Country();
                return $c;
                break;
        }
    }

    /**
     * import all regions with check existing records in table `region`
     */
    public function importRegions($countryId,$offset,$queryCount,$limit){
        ob_start();
        //limit count of import rows
        $affected_rows=0;
        if($countryId==0){
            echo 'Error: country id couldn`t be 0';
            exit();
        }else if($limit<$offset+$queryCount){
            echo 'Error: parameters offset+queryCount should be greater than limit';
            exit();
        }
        while($offset<=$limit){
            echo '<p style="font-size: 16px">'.date("Y-m-d H:i:s").'Getting data from vk.com: offset_count:'.$offset.'...        ';
            flush();
            ob_flush();

            $resultImportRegions=$this->vkImportModel->getRegions($countryId,$offset,$queryCount);
            echo date("Y-m-d H:i:s").'Got data successfully</p>';
            echo json_encode($resultImportRegions);
            ob_flush();

            $importedRegions=$resultImportRegions['response']['items'];
            foreach($importedRegions as $i){
                $existRegion=$this->regionModel->getById($i['id']);
//                echo json_encode($existRegion).'  $i'.json_encode($i['id']);

                if(!($i['id']==$existRegion['region_id'])){
                    echo '<p style="font-size: 16px">'.date("Y-m-d H:i:s").'Importing:'.json_encode($i,JSON_UNESCAPED_UNICODE).'...        ';
                    ob_flush();
                    $this->regionModel->insertRow($countryId,$i['id'],$i['title']);
                    $affected_rows++;
                }
            }
            $offset+=$queryCount;
        }
        echo 'Script finished successfully. Imported Region rows: '.$affected_rows;
        ob_end_flush();
    }

    public function importCitiesAll($countryId,$offset,$queryCount,$limit,$needAll){
        $affected_rows=0;
        if($countryId==0){
            echo 'Error: country id couldn`t be 0';
            exit();
        }else if($limit<$offset+$queryCount){
            echo 'Error: parameters offset+queryCount should be greater than limit';
            exit();
        }

        ob_start();

        while($offset<=$limit){
            echo '<p style="font-size: 16px">'.date("Y-m-d H:i:s").'Getting data from vk.com: offset_count:'.$offset.'...        ';
            ob_flush();
            flush();
            $resultImportCities=$this->vkImportModel->getCities($countryId,$offset,$queryCount,null,$needAll);
            echo date("Y-m-d H:i:s").'Got data successfully</p>';
            ob_flush();
            flush();
            $importedCities=$resultImportCities['response']['items'];
            if($resultImportCities['response']!=null){
                foreach($importedCities as $i){

                    $existCity=$this->cityModel->getById($i['id']);
                    if(!($i['id']==$existCity['city_id'])){
                        echo '<p style="font-size: 16px">'.date("Y-m-d H:i:s").'Importing:'.json_encode($i,JSON_UNESCAPED_UNICODE).'...        ';
                        flush();
                        ob_flush();

                        if (array_key_exists('region',$i)) {
                            $region=$i['region'];
                        } else{
                            $region=null;
                        }
                        if (array_key_exists('area',$i)) {
                            $area=$i['area'];
                        } else{
                            $area=null;
                        }
                        //echo 'reg:'.$region.'</br>';
                        $setCity=$this->cityModel->insertRow($countryId,$region,$area,$i['id'],$i['title']);
                        echo $setCity;
                        $affected_rows++;
                    }


                }
            }

            $offset+=$queryCount;

        }
        echo 'Script finished successfully. Imported City rows: '.$affected_rows;
        ob_end_flush();
    }


    public function fillRegionId(){
        $modelCity=$this->getModel('city');
        $allRowsCity=$modelCity->getAll();

        $modelRegion=$this->getModel('region');

        foreach($allRowsCity as $rowCity){
            echo '<p style="font-size: 16px">'.date("Y-m-d H:i:s").'Checking row:'.json_encode($rowCity,JSON_UNESCAPED_UNICODE).'...        ';
            $regionRow=$modelRegion->getByFieldValue('region_name',$rowCity['city_region_name']);
            if (!empty($regionRow)) {
                if($rowCity['city_region_name']==$regionRow['region_name']){
                    $regionId=$regionRow['region_id'];
                    echo 'Region name found, updating region_id'.$regionId;
                    $modelCity->updateRow($rowCity['city_id'],'city_region_id',$regionId);
                }else{
                    $modelCity->updateRow($rowCity['city_id'],'city_region_id',null);
                }
            }
        }

    }

    public function translateCityNames(){
        ob_start();
        $modelCity=$this->getModel('city');
        $allRowsCity=$modelCity->getAll();

        $modelCountry=$this->getModel('country');
        $allRowsCountry=$modelCity->getAll();

        //foreach($allRowsCountry as $rowCountry){
        //      $lang=$rowCountry['country_alfa2'];

        // }
        $langEn='en';
        $modelTranslate=new Translate();
        $stop=0;
        foreach($allRowsCity as $rowCity){
            if ($stop>=1000){
                exit();
            };
            if($rowCity['city_name_en']==null || $rowCity['city_name_en']==''|| $rowCity['city_name_en']=='Array'){
                echo '<p style="font-size: 16px">'.date("Y-m-d H:i:s").' Getting data from yandex api,city:'.json_encode($rowCity,JSON_UNESCAPED_UNICODE).'    ...';
                $tr=$modelTranslate->translate($rowCity['city_name'],$langEn);
                ob_flush();
                echo ' Translated successfuly:'.json_encode($tr).'</br>';
                ob_flush();
                if (!empty($tr)) {
                    echo '<p style="font-size: 16px">'.date("Y-m-d H:i:s").' Starting update city_id:'.$rowCity['city_id'].'   ...';
                    ob_flush();
                    $modelCity->updateRow($rowCity['city_id'],'city_name_en',$tr);
                }
                $stop++;
            }

        }
        ob_end_flush();
    }



}


$test=new Main();
//$test->importCitiesAll($test::COUNTRY_RUSSIA_ID,0,1000,10000000,1);
//$test->importRegions($test::COUNTRY_RUSSIA_ID,0,1000,2000);
//$test->fillRegionId();
//$test->translateCityNames();


