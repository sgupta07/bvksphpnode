<?php
require 'vendor/autoload.php';
ini_set('memory_limit', "-1");
ini_set('max_execution_time', 0); 

global $api;
$api = new RestClient([
    'base_url' => "https://bvks.com/wp-json/wp/v2/", 
    
]);

/*================== code to update json file ==============*/

  $string = file_get_contents("results.json");
  $json_a = json_decode($string);
  //var_dump($json_a);
//echo count($json_a->lectures);

  foreach($json_a->lectures as $key=>$value){
    $result1= $api->get("lecture/".$value->id, ['per_page' => "100", "filter[orderby]" => "created_date", "order" => "asc", "page"=> $i]);
    $result1 = $result1->decode_response();
    $resources = new stdClass();
    $audioObject = new stdClass();
    $audioObject ->url = $result1->custom_parameters->attachment_url;
    $audioObject ->views = 0;
    $audioObject ->downloads = 0;
    $audioObject ->creationTimestamp = $date = date('m/d/Y h:i:s a', time());;
    $audioObject ->lastModifiedTimestamp = $date = date('m/d/Y h:i:s a', time());;

   
    $resources->audios = [$audioObject];
    $resources->videos = [];
    $resources->transcriptions = [];

    $json_a->lectures[$key]->resources = $resources;
    
    

  }
  $fp = fopen('results2.json', 'w');
  fwrite($fp, json_encode($json_a));
  fclose($fp);
  

  exit;



/*$i=1;
$result = [];
while($i < 46){
    $result1= $api->get("lecture", ['per_page' => "100", "filter[orderby]" => "created_date", "order" => "asc", "page"=> $i]);
    $result =array_merge($result,$result1->decode_response());
$i++;
}*/
//$result = $api->get("lecture", ['per_page' => "100", "filter[orderby]" => "created_date", "order" => "asc", "page"=> 45]);
// GET http://api.twitter.com/1.1/search/tweets.json?q=%23php

if($result->info->http_code == 200)
    //var_dump($result->decode_response());


$response = array();
$posts = array();

echo "count-------->" .count($result) ."<br>";

foreach($result as $key => $value){
    //echo $value->title;
    echo "Lecture ID-------->" .$value->id."<br>";
    $id = $value->id;
    /*$title = $pieces = explode(" ", $value->custom_parameters->lecture_title);

    $description= $pieces = explode(" ", $value->excerpt->rendered);
    $language = new stdClass();
    $language->main = $value->custom_parameters->languages;   

    $language->translations = taxnomies('translation', $id);
    $category = taxnomies('lectures', $id);
    $places = taxnomies('place', $id);

    

    $country = $value->custom_parameters->country;
    $city = '';
    $state = '';
    $location = array('country' => $country, 'city' => $city, 'state' => $state); 

    if (preg_match('#^(\d{4})(\d{2})(\d{2})$#', $value->metadata->created_date[0], $matches)) {
        $month = $matches[2];
        $day   = $matches[3];
        $year  = $matches[1];
    } else {
        echo 'invalid format';
    }
    
    $dateOfRecording =  array('year' => $year, 'month' => $month, 'day' => $day); 
    if($value->custom_parameters->meta_data->length > 1500){
        $lengthType = 'standard'; 
    }else{
        $lengthType = 'short'; 
    }

    $length = $value->custom_parameters->meta_data->length;
    $thumbnail = $value->custom_parameters->thumbnail; */

    $resources = new stdClass();
    $audioObject = new stdClass();
    $audioObject ->url = $value->custom_parameters->thumbnail->attachment_url;
    $audioObject ->views = 0;
    $audioObject ->downloads = 0;
    $audioObject ->creationTimestamp = $date = date('m/d/Y h:i:s a', time());;
    $audioObject ->lastModifiedTimestamp = $date = date('m/d/Y h:i:s a', time());;

   
    $resources->audios = [$audioObject];
    $resources->videos = [];
    $resources->transcriptions = [];

    /*$legacyData = new stdClass();
    $legacyData->wpid = $id;
    $legacyData->lectureCode = $value->metadata->lecture_code[0];
    $legacyData->verse = taxnomies('verse', $id);
    $legacyData->verse = $legacyData->verse[0];
    $legacyData->slug = $value->slug;
    $creationTimestamp = $value->date;
*/


    

  
    $posts[] = array('id'=> $id, 'title'=> $title, 
    'description' => $description, 'language' => $language, 'category' => $category, 'place' => $places,
    'location' => $location,
    'dateOfRecording' => $dateOfRecording,
    "tags" => [], //todo
    "lengthType" => [$lengthType],
    "length" => $length,
    'thumbnail' => $thumbnail,
    'legacyData' => $legacyData,
    'resources' => $resources,
    "creationTimestamp" => $creationTimestamp,
    "lastModifiedTimestamp" => ""


);
} 

$response = new stdClass();
$response->lectures = $posts;

$fp = fopen('results2.json', 'w');
fwrite($fp, json_encode($response));
fclose($fp);

function taxnomies($val,$id){ 
    global $api;
             
    $translation = $api->get($val, ['post' => $id])->decode_response();
    //var_dump($translation);
    if(!empty($translation)){
        foreach($translation as $key=>$value){
            $arr[] = $value->name;
         }
         return $arr;
    }
    
    return null;
   
}

?>