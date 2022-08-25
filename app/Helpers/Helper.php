<?php

namespace App\Helpers;

class Helper
{
    public static function generatePassword()
    {
        $array = [8, 9, 10, 11, 12];
        $k = array_rand($array);
        $length = $array[$k];
        $password = substr(str_shuffle(str_repeat($x = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ&*@_+!', ceil($length / strlen($x)))), 1, $length);
        return $password;
    }

    public static function sendNotification($reference_id, $device_type, $device_token, $message, $type)
    {
        // echo $device_type .'<br><br>'. $device_token .'<br><br>'. $message; die;
        $serverKey = env('FCM_KEY', 'AAAAUnEI1VU:APA91bH4uN1FneWk2u8dQUQPtKnNlM1TruojA_YZcM9mOwaofrsEt86Rb8WuGDyPJjgEWeGI1kQbhIRhug1Wo5JmUjFwVVQlfWdieq9glSJwBNIqSp0bmiqI8szG8ixvMXTZ8lZduFBz');

        $url = "https://fcm.googleapis.com/fcm/send";
        $token = $device_token;

        $notification = [
            'title' => "Suthar Enterprises",
            'body' => $message,
            'sound' => 'default',
            'badge' => 1,
            'content-available' => 1,
            'type' => $type,
            'type_id' => $reference_id
        ];

        $arrayToSend = array(
            'to' => $token,
            'notification' => $notification,
            'priority' => 'high',
            'data' => [
                'type' => $type,
                'type_id' => $reference_id
            ]
        );
        $json = json_encode($arrayToSend);
        $headers = array();
        $headers[] = 'Content-Type: application/json';
        $headers[] = 'Authorization: key=' . $serverKey;
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 60);
        $response = curl_exec($ch);

        if ($response == false) {
            $result_noti = 0;
        } else {
            $result_noti = 1;
        }
        curl_close($ch);
        // dd($response);
        return $result_noti;
    }

    public static function points_to_rupees($points = 0)
    {
        $point_1_value = config('constants.point') ?? 1;
        return round($points * $point_1_value, 2);
    }
}
