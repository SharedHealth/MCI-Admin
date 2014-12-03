<?php
/**
 * Created by PhpStorm.
 * User: imran
 * Date: 11/27/14
 * Time: 2:46 PM
 */

namespace Mci\Bundle\PatientBundle\Utills;


class Utility {


   /**
     * @param $postData
     * @return mixed
     */
    public  static function filterRelations($postData)
    {

        if (!empty($postData['type'])) {

            foreach ($postData['type'] as $key => $val) {

                if ($val) {
                    $postData['relations'][$key]['type'] = $val;
                }
                if (!empty($postData['bin_brn'][$key])) {
                    $postData['relations'][$key]['bin_brn'] = $postData['bin_brn'][$key];
                }
                if (!empty($postData['id'][$key])) {
                    $postData['relations'][$key]['id'] = $postData['id'][$key];
                }
                if (!empty($postData['uid'][$key])) {
                    $postData['relations'][$key]['uid'] = $postData['uid'][$key];
                }
                if (!empty($postData['nid'][$key])) {
                    $postData['relations'][$key]['nid'] = $postData['nid'][$key];
                }
                 $postData['relations'][$key]['name_bangla'] = $postData['name_bangla'][$key];

                if (!empty($postData['given_name'][$key])) {
                    $postData['relations'][$key]['given_name'] = $postData['given_name'][$key];
                }
                if (!empty($postData['sur_name'][$key])) {
                    $postData['relations'][$key]['sur_name'] = $postData['sur_name'][$key];
                }
                if (!empty($postData['marriage_id'][$key])) {
                    $postData['relations'][$key]['marriage_id'] = $postData['marriage_id'][$key];
                }
                if (!empty($postData['relational_status'][$key])) {
                    $postData['relations'][$key]['relational_status'] = $postData['relational_status'][$key];
                }
            }

        }
        return $postData['relations'];

    }


    /**
     * @param $postData
     * @return mixed
     */
    public static  function filterAddress($postData)
    {
        if(empty($postData['union_id'])){
            unset($postData['union_id']);
        }

        if(empty($postData['ward_id'])){
            unset($postData['ward_id']);
        }

        if(empty($postData['city_corporation_id'])){
            unset($postData['city_corporation_id']);
        }

        if(empty($postData['country_code'])){
            unset($postData['country_code']);
        }
        if(empty($postData['post_code'])){
            unset($postData['post_code']);
        }

        return $postData;
    }



    /**
     * @param $messages
     * @return string
     */
    public static function getErrorMessages($messages)
    {
        $SystemAPiError = array();
        if(!isset($messages->errors)) {
            return array('Please check your configuration');
        }

        foreach ($messages->errors as $value) {

            switch ($value->code) {
                case 1001:
                    $SystemAPiError[] = $value->message;
                    break;

                case 1002:
                    $SystemAPiError[] = $value->message;
                    break;

                case 1004:
                    $SystemAPiError[] = $value->message;
                    break;

                case 1005:
                    $SystemAPiError[] = $value->message;
                    break;

                case 1006:
                    $SystemAPiError[] = "Invalid Search Parameter";
                    break;

                case 2001:
                    $SystemAPiError[] = "Invalid json";
                    break;

                case 2002:
                    $SystemAPiError[] = "Un Recognized Field";
                    break;

                case 500:
                    $SystemAPiError[] = "Server Error";
                    break;

                case 3001:
                    $SystemAPiError[] = "Permission Error";
                    break;

                default:
                    $SystemAPiError[] = "Service Unavailable";
            }
        }

        return $SystemAPiError;
    }

    /**
     * @param $postData
     * @return mixed
     */
    public static  function filterPhoneNumber($postData)
    {
        if($postData['number']){
            return $postData;
        }
        return false;
    }



    /**
     * @param $postData
     */
    public static  function unsetUnessaryData($postData)
    {
        unset($postData['relation']);
        unset($postData['save']);
        unset($postData['_token']);

        if(empty($postData['name_bangla'])){
            $postData['name_bangla'] = "";
        }

        if(empty($postData['primary_contact'])){
            $postData['primary_contact'] = "";
        }
        if(empty($postData['nationality'])){
            $postData['nationality'] = "";
        }
        if(empty($postData['place_of_birth'])){
            $postData['place_of_birth'] = "";
        }

        return $postData;
    }

    public static function getJsonData($fileName)
    {
        $filePath =  'assets/json/'.$fileName;
        return  json_decode(file_get_contents($filePath), true);
    }



}