<?php
/**
 * Created by PhpStorm.
 * User: imran
 * Date: 11/27/14
 * Time: 2:46 PM
 */

namespace Mci\Bundle\PatientBundle\Lib;


class Utility {

     public static function getJsonData($fileName)
    {
        $filePath =  'assets/json/'.$fileName;
        return  json_decode(file_get_contents($filePath), true);
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
                case 1006:
                    $SystemAPiError[] = "Invalid Search Parameter";
                    break;

                case 1002:
                    $SystemAPiError[] = "Invalid Pattern";
                    break;

                case 1005:
                    $SystemAPiError[] = "Invalid Marital Status";
                    break;

                case 1004:
                    $SystemAPiError[] = "Invalid Relational Status";
                    break;

                case 2001:
                    $SystemAPiError[] = "Invalid json";
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
    public  static function filterRelations($postData)
    {

        if (!empty($postData['relation']['type'])) {

            foreach ($postData['relation']['type'] as $key => $val) {

                if ($val) {
                    $postData['relations'][$key]['type'] = $val;
                }
                if (!empty($postData['relation']['bin_brn'][$key])) {
                    $postData['relations'][$key]['bin_brn'] = $postData['relation']['bin_brn'][$key];
                }
                if (!empty($postData['relation']['id'][$key])) {
                    $postData['relations'][$key]['id'] = $postData['relation']['id'][$key];
                }
                if (!empty($postData['relation']['uid'][$key])) {
                    $postData['relations'][$key]['uid'] = $postData['relation']['uid'][$key];
                }
                if (!empty($postData['relation']['nid'][$key])) {
                    $postData['relations'][$key]['nid'] = $postData['relation']['nid'][$key];
                }
                if (!empty($postData['relation']['name_bangla'][$key])) {
                    $postData['relations'][$key]['name_bangla'] = $postData['relation']['name_bangla'][$key];
                }
                if (!empty($postData['relation']['given_name'][$key])) {
                    $postData['relations'][$key]['given_name'] = $postData['relation']['given_name'][$key];
                }
                if (!empty($postData['relation']['sur_name'][$key])) {
                    $postData['relations'][$key]['sur_name'] = $postData['relation']['sur_name'][$key];
                }
                if (!empty($postData['relation']['marriage_id'][$key])) {
                    $postData['relations'][$key]['marriage_id'] = $postData['relation']['marriage_id'][$key];
                }
                if (!empty($postData['relation']['relational_status'][$key])) {
                    $postData['relations'][$key]['relational_status'] = $postData['relation']['relational_status'][$key];
                }
            }
            return $postData;
        }
        return $postData;
    }

    /**
     * @param $postData
     * @return mixed
     */
    public static  function filterPhoneNumber($postData)
    {
        if (empty($postData['phone_number']['number'])) {
            unset($postData['phone_number']);
        } else {
            $postData['phone_number'] = array_filter($postData['phone_number']);
        }

        if (empty($postData['primary_contact_number']['number'])) {
            unset($postData['primary_contact_number']);
            return $postData;
        } else {
            $postData['primary_contact_number'] = array_filter($postData['primary_contact_number']);
            return $postData;
        }
    }

    /**
     * @param $postData
     * @return mixed
     */
    public static  function filterAddress($postData)
    {
        if (empty($postData['permanent_address']['division_id'])) {
            unset($postData['permanent_address']);
        } else {
            $postData['permanent_address'] = array_filter($postData['permanent_address']);
        }

        if (empty($postData['present_address']['division_id'])) {
            unset($postData['present_address']);
            return $postData;
        } else {
            $postData['present_address'] = array_filter($postData['present_address']);
            return $postData;
        }
    }

    /**
     * @param $postData
     */
    public static  function unsetUnessaryData($postData)
    {
        unset($postData['relation']);
        unset($postData['save']);
        unset($postData['_token']);
        return $postData;
    }



} 