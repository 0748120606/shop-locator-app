<?php
function geocodeLocation($location) {
    $apiKey = "YOUR_GOOGLE_API_KEY";
    $address = urlencode($location);
    $url = "https://maps.googleapis.com/maps/api/geocode/json?address={$address}&key={$apiKey}";

    $response = file_get_contents($url);
    $data = json_decode($response, true);

    if ($data['status'] === 'OK') {
        return [
            'lat' => $data['results'][0]['geometry']['location']['lat'],
            'lng' => $data['results'][0]['geometry']['location']['lng']
        ];
    }
    return null;
}
?>
