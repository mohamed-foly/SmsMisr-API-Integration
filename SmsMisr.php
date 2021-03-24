<?php

class SmsMisr {

    private $username, $password, $sender_name, $phone_number, $message;
    private $languages = ['en'=>1,'ar'=>2,'unicode'=>3];
    private $language = 1;

    public function __construct($username, $password, $sender_name){
        $this->username = $username;
        $this->password = $password;
        $this->sender_name = $sender_name;
    }

    public function setPhoneNumbers($phones = []){
        $this->phone_number = is_array($phones) ? implode(',',$phones):$phones;
        return $this;
    }

    public function setMessage($message){
        $this->message = $message;
        return $this;
    }

    public function setLanguage($language){
        if(isset($this->languages[$language])) $this->language = $this->languages[$language];
        return $this;
    }
    
    public function send(){
        $result = $this->curl_request(
            "https://smsmisr.com/api/v2/",
            [
                'username'=> $this->username,
                'password'=> $this->password,
                'language'=> $this->language,
                'sender'=> $this->sender_name,
                'mobile'=> $this->phone_number,
                'message'=> $this->message
            ],
            [
                'Content-Type: application/json',
                'Accept: application/json',
                'Accept-Language: en-US'
            ]);

        $result = json_decode($result,true);
        if (isset($result['code']) && in_array($result['code'],[1901,6000]) ) return true;
        return false;
    }


    private function curl_request ($url, $fields, $headers = []){
        $payload = json_encode($fields);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLINFO_HEADER_OUT, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_VERBOSE, true);
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }
}