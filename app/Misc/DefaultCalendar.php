<?php

namespace App\Misc;

use Carbon\Carbon;

class DefaultCalendar
{
    private function GetCalendarUrlByCountry(String $code, String $lang='en')
    {
        $calendars = [
            'AU' => 'australian',
            'UT' => 'austrian',
            'BR' => 'brazilian',
            'BY' => 'by',
            'CA' => 'canadian',
            'CN' => 'china',
            'DK' => 'danish',
            'NL' => 'dutch',
            'FI' => 'finnish',
            'FR' => 'french',
            'DE' => 'german',
            'GR' => 'greek',
            'HK' => 'hong_kong',
            'IN' => 'indian',
            'ID' => 'indonesian',
            'IR' => 'iranian',
            'IE' => 'irish',
            'IT' => 'italian',
            'JP' => 'japanese',
            'MY' => 'malaysia',
            'MX' => 'mexican',
            'NZ' => 'new_zealand',
            'NO' => 'norwegian',
            'PH' => 'philippines',
            'PL' => 'polish',
            'PT' => 'portuguese',
            'RU' => 'russian',
            'SG' => 'singapore',
            'ZA' => 'sa',
            'KR' => 'south_korea',
            'ES' => 'spain',
            'SE' => 'swedish',
            'TW' => 'taiwan',
            'TH' => 'thai',
            'UA' => 'ukrainian',
            'GB' => 'uk',
            'US' => 'usa',
            'VN' => 'vietnamese',
            'christian' => 'christian',
            'islamic' => 'islamic',
            'jewish' => 'jewish'];
        if (isset($calendars, $code)) {
            $country = $calendars[$code];
            return "$lang.$country%23holiday@group.v.calendar.google.com";
        }
    }

    private function GetLocationByIp(String $ip)
    {
        $xml = file_get_contents("https://geolocation-db.com/json/$ip");
        $json = json_decode($xml, true);
        return $json['country_code'];
    }

    public function GetEvents(String $ip)
    {
        // TODO remove, just for test
        if ($ip == '127.0.0.1')
            $ip = '193.107.172.106';
        $country = $this->GetLocationByIp($ip);
        $calendar = $this->GetCalendarUrlByCountry($country);
        $key = env('GOOGLE_APP_API_KEY');
        $xml = file_get_contents("https://www.googleapis.com/calendar/v3/calendars/$calendar/events?key=$key");
        $json = json_decode($xml, true);
        $map_events = function($data) {
            return ['title' => $data['summary'],
                    'start_dt' => Carbon::createFromFormat('Y-m-d', $data['start']['date'])
                    ->hour(12)->minute(0)->second(0)];
        };
        $events = array_map($map_events, $json['items']);
        return $events;
    }
}
