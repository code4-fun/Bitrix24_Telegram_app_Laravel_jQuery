<?php

namespace App\Services;

/**
 * Class TelegramService to interact with Telegram API
 * @package App\Services
 */
class TelegramService
{
  private string $token;
  private string $chat_id;

  public function __construct($token, $chat_id)
  {
    $this->token = $token;
    $this->chat_id = $chat_id;
  }

  /**
   * Constructs a message to send to telegram chat
   * @param $data data to create message of
   * @return string message
   */
  private function createMessage($data)
  {
    $html = '';
    if (isset($data['error'])) {
      $html .= $data['error'];
    } else {
      $html .= '<b>'.$data['message'].'</b>';
      $html .= chr(10);
      $html .= '<b><a href="https://b24-zcwbq2.bitrix24.ru/crm/lead/show/'.$data['lead_id'].'/">Go</a></b>';
      if($data['FULL_NAME']) {
        $html .= chr(10);
        $html .= '<b>Name: </b>'.$data['FULL_NAME'];
      }
      if ($data['BIRTHDATE']) {
        $html .= chr(10);
        $html .= '<b>Birthday: </b>'.$data['BIRTHDATE'];
      }
      if (isset($data['PHONE'])) {
        $html .= chr(10);
        $html .= '<b>Phone: </b>'.$data['PHONE'];
      }
      if ($data['EMAIL']) {
        $html .= chr(10);
        $html .= '<b>Email: </b>'.$data['EMAIL'];
      }
      if ($data['COMMENTS']) {
        $html .= chr(10);
        $html .= '<b>Comment: </b>'.$data['COMMENTS'];
      }
    }
    return $html;
  }

  /**
   * Sends message to telegram chat
   * @param $data data to create message of
   */
  public function sendMessage($data)
  {
    $uri = 'https://api.telegram.org/bot'.$this->token.'/sendMessage';
    $query_params = http_build_query([
      'chat_id' => $this->chat_id,
      'parse_mode' => 'html',
      'text' => $this->createMessage($data)
    ]);
    $curl = curl_init();
    curl_setopt_array($curl, array(
      CURLOPT_POST => 1,
      CURLOPT_HEADER => 0,
      CURLOPT_SSL_VERIFYPEER => 0,
      CURLOPT_RETURNTRANSFER => 1,
      CURLOPT_URL => $uri,
      CURLOPT_POSTFIELDS => $query_params,
    ));
    curl_exec($curl);
    curl_close($curl);
  }
}
