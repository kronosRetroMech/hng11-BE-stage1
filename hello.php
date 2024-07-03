<?php

// Function to get the client IP address
function get_client_ip()
{
    $ipaddress = '';
    if (isset($_SERVER['HTTP_CLIENT_IP']))
        $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
    else if (isset($_SERVER['HTTP_X_FORWARDED_FOR']))
        $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
    else if (isset($_SERVER['HTTP_X_FORWARDED']))
        $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
    else if (isset($_SERVER['HTTP_FORWARDED_FOR']))
        $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
    else if (isset($_SERVER['HTTP_FORWARDED']))
        $ipaddress = $_SERVER['HTTP_FORWARDED'];
    else if (isset($_SERVER['REMOTE_ADDR']))
        $ipaddress = $_SERVER['REMOTE_ADDR'];
    else
        $ipaddress = 'UNKNOWN';
    return $ipaddress;
}



//JSON file for the user's ip address
// $ip_test = "104.28.219.121"; //to test on Localhost
$ip_test = get_client_ip();

$loc = file_get_contents("http://ip-api.com/json/$ip_test");

$loc2 = json_decode($loc);



// Variables extracted from the ip JSON file
$location = $loc2->city;
$lat = $loc2->lat;
$lon = $loc2->lon;
$temperature = "";



// Parameters for the Weather (API key)
$api_key = "d94f65b94eedbb9e5eca6373f2e20e95";
$api_url = "http://api.openweathermap.org/data/2.5/weather?lat={$lat}&lon={$lon}&units=metric&appid={$api_key}";

$ch = curl_init();

curl_setopt($ch, CURLOPT_HEADER, 0);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_URL, $api_url);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
curl_setopt($ch, CURLOPT_VERBOSE, 0);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
$response = curl_exec($ch);

curl_close($ch);
$data = json_decode($response, true);

$temperature = $data["main"]["temp"];



// Setting the user name if provided, else set to default 'Guest'
if (isset($_GET['visitor_name'])) {
    $visitor_name = htmlspecialchars($_GET['visitor_name']);
} else {
    $visitor_name = 'Guest';
};



//Main API call for the greeting message
$greeting = [
    "client_ip" => $ip_test,
    "location" => $location,
    "greeting" => "Hello, {$visitor_name}!, the temperature is {$temperature} degrees Celcius in {$location}"
];

header("Content-type: JSON");
echo json_encode($greeting, JSON_PRETTY_PRINT);
