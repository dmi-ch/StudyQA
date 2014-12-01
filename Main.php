<?php

namespace studyqa;

use studyqa\model\City;
use studyqa\model\Region;
use SplClassLoader;
use studyqa\model\VkImport;

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



    /**
     * import all regions with check existing records in table `region`
     */
    public function importRegions($countryId,$offset,$queryCount,$limit){
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
            $resultImportRegions=$this->vkImportModel->getRegions($countryId,$offset,$queryCount);

            $importedRegions=$resultImportRegions['response']['items'];
            foreach($importedRegions as $i){
                $existRegion=$this->regionModel->getById($i['id']);
//                echo json_encode($existRegion).'  $i'.json_encode($i['id']);
                if(!($i['id']==$existRegion['region_id'])){
                    $this->regionModel->setOne($countryId,$i['id'],$i['title']);
                    $affected_rows++;
                }
            }
            $offset+=$queryCount;
        }
        echo 'Script finished successfully. Imported rows: '.$affected_rows;
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
        while($offset<=$limit){

            $resultImportCities=$this->vkImportModel->getCities($countryId,$offset,$queryCount,null,$needAll);
            $importedCities=$resultImportCities['response']['items'];
            foreach($importedCities as $i){

                    $existCity=$this->cityModel->getById($i['id']);
                    if(!($i['id']==$existCity['city_id'])){
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
                        $this->cityModel->setOne($countryId,$region,$area,$i['id'],$i['title']);
                        $affected_rows++;
                    }


            }
            $offset+=$queryCount;

        }
        echo 'Script finished successfully. Imported rows: '.$affected_rows;
    }


}


$test=new Main();

//$test->importRegions($test::COUNTRY_RUSSIA_ID,1,5,20);
$test->importCitiesAll($test::COUNTRY_RUSSIA_ID,1,100,700,0);





