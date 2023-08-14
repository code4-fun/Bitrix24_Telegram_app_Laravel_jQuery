<?php

namespace App\Services;

use App\Models\Lead;

/**
 * Class BitrixService to interact with Bitrix24 API
 * @package App\Services
 */
class BitrixService
{
  private string $uri;

  public function __construct($uri)
  {
    $this->uri = $uri;
  }

  /**
   * Calls Bitrix24 method (for example crm.lead.add)
   * @param $method Bitrix24 method
   * @param $data data provided for the method
   * @return mixed result of method invocation
   */
  public function invokeBitrixMethod($method, $data)
  {
    $webhook_uri = $this->uri.$method;
    $query_params = http_build_query($data);
    $curl = curl_init();
    curl_setopt_array($curl, array(
      CURLOPT_POST => 1,
      CURLOPT_HEADER => 0,
      CURLOPT_SSL_VERIFYPEER => 0,
      CURLOPT_RETURNTRANSFER => 1,
      CURLOPT_URL => $webhook_uri,
      CURLOPT_POSTFIELDS => $query_params,
    ));
    $result = curl_exec($curl);
    curl_close($curl);
    return json_decode($result, 1);
  }

  /**
   * Creates contact in Bitrix24
   * @param $data data provided by user through form
   * @return mixed|null id of created or existing contact or null in case of error
   */
  public function createContact($data)
  {
    $checkPhone = $this->checkContactPhone($data);
    if ($checkPhone['total'] != 0) {
      return $checkPhone['result'][0]['ID'];
    }

    $checkEmail = $this->checkContactEmail($data);
    if ($checkEmail['total'] != 0) {
      return $checkEmail['result'][0]['ID'];
    }

    $result = $this->invokeBitrixMethod('crm.contact.add.json', [
      'fields' => [
        'NAME' => $data['NAME'],
        'SECOND_NAME' => $data['SECOND_NAME'],
        'LAST_NAME' => $data['LAST_NAME'],
        'BIRTHDATE' => $data['BIRTHDATE'],
        'EMAIL' => [['VALUE' => $data['EMAIL'], 'VALUE_TYPE' => 'WORK']],
        'PHONE' => [['VALUE' => $data['PHONE'], 'VALUE_TYPE' => 'WORK']],
        'COMMENTS' => $data['COMMENTS'],
      ], 'params' => [
        'REGISTER_SONET_EVENT' => 'Y'
      ]
    ]);

    if (isset($result['error'])) {
      return null;
    }
    return $result['result'];
  }

  /**
   * Creates lead in Bitrix24
   * @param $data data provided by user through form
   * @return array id of created or existing lead or throws an error
   */
  public function createLead($data)
  {
    $check = $this->checkLead($data['FULL_NAME']);
    if ($check['total'] != 0) {
      $message = 'Lead was not created. Such lead already exists';
      $tg = new TelegramService(config('custom.TG_TOKEN'), config('custom.TG_CHAT_ID'));
      $data['message'] = $message;
      $data['lead_id'] = $check['result'][0]['ID'];
      $tg->sendMessage($data);
      return [
        'message' => $message,
        'statusCode' => 400
      ];
    }

    $result = $this->invokeBitrixMethod('crm.lead.add.json', [
      'fields' => [
        'TITLE' => $data['FULL_NAME'],
        'NAME' => $data['NAME'],
        'SECOND_NAME' => $data['SECOND_NAME'],
        'LAST_NAME' => $data['LAST_NAME'],
        'BIRTHDATE' => $data['BIRTHDATE'],
        'EMAIL' => [['VALUE' => $data['EMAIL'], 'VALUE_TYPE' => 'WORK']],
        'PHONE' => [['VALUE' => $data['PHONE'], 'VALUE_TYPE' => 'WORK']],
        'COMMENTS' => $data['COMMENTS'],
        'CONTACT_ID' => $data['CONTACT_ID'] ?? '',
      ], 'params' => [
        'REGISTER_SONET_EVENT' => 'Y'
      ]
    ]);

    if (isset($result['error'])) {
      $message = 'Lead was not created. Error occurred';
      $tg = new TelegramService(config('custom.TG_TOKEN'), config('custom.TG_CHAT_ID'));
      $data['error'] = $message;
      $tg->sendMessage($data);
      abort(500, $message);
    }
    $message = 'Lead has been successfully created';
    $tg = new TelegramService(config('custom.TG_TOKEN'), config('custom.TG_CHAT_ID'));
    $data['message'] = $message;
    $data['lead_id'] = $result['result'];
    $tg->sendMessage($data);
    $this->saveInDB($data);
    return [
      'id' => $result['result'],
      'message' => $message,
      'statusCode' => 201
    ];
  }

  /**
   * Checks if contact exists by phone
   * @param $data phone number
   * @return array ids of contacts if exist
   */
  private function checkContactPhone($data)
  {
    return $this->invokeBitrixMethod('crm.contact.list.json', [
      'filter' => ['PHONE' => $data['PHONE']],
      'select' => ['ID'],
    ]);
  }
  /**
   * Checks if contact exists by email
   * @param $data email
   * @return array ids of contacts if exist
   */
  private function checkContactEmail($data)
  {
    return $this->invokeBitrixMethod('crm.contact.list.json', [
      'filter' => ['EMAIL' => $data['EMAIL']],
      'select' => ['ID'],
    ]);
  }

  /**
   * Checks if lead exists by its title
   * @param $data title
   * @return array ids of leads if exist
   */
  private function checkLead($data)
  {
    $filter = $data ?? '';
    return $this->invokeBitrixMethod('crm.lead.list', [
      'filter' => ['TITLE' => $filter],
      'select' => ['ID'],
    ]);
  }

  /**
   * Saves lead in DB
   * @param $data lead parameters
   */
  private function saveInDB($data)
  {
    $db_lead = new Lead();
    $db_lead->title = $data['FULL_NAME'];
    $db_lead->name = $data['NAME'];
    $db_lead->second_name = $data['SECOND_NAME'];
    $db_lead->last_name = $data['LAST_NAME'];
    $db_lead->birthdate = $data['BIRTHDATE'] === '' ? null : $data['BIRTHDATE'];
    $db_lead->phone = $data['PHONE'];
    $db_lead->email = $data['EMAIL'];
    $db_lead->comment = $data['COMMENTS'];
    $db_lead->save();
  }
}
