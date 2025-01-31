<?php
// Configurações do banco de dados
$host = 'localhost';
$dbname = 'weather_data';
$user = 'Marcelo';
$password = 'prefeiturajaragua';

// URL da API WeatherAPI
$apiKey = 'coloque a key do WeatherAPI';
$latitude = '-26.4853';
$longitude = '-49.0672';
$url = "http://api.weatherapi.com/v1/current.json?key=$apiKey&q=$latitude,$longitude&lang=pt";

try {
    // Conexão com o banco de dados
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Consulta a API
    $response = file_get_contents($url);
    if ($response === false) {
        throw new Exception("Falha ao acessar a API.");
    }

    $data = json_decode($response, true);

    // Extrai os dados necessários
    $city = $data['location']['name'];
    $region = $data['location']['region'];
    $temperature = $data['current']['temp_c'];
    $feelslike_c = $data['current']['feelslike_c'];
    $heatindex_c = $data['current']['heatindex_c'];
    $dewpoint_c = $data['current']['dewpoint_c'];
    $condition = $data['current']['condition']['text'];
    $humidity = $data['current']['humidity'];
    $cloud = $data['current']['cloud'];
    $precip_mm = $data['current']['precip_mm'];
    $windSpeed = $data['current']['wind_kph'];
    $wind_degree = $data['current']['wind_degree'];
    $wind_dir = $data['current']['wind_dir'];
    $windchill_c = $data['current']['windchill_c'];
    $gust_kph = $data['current']['gust_kph'];
    $vis_km = $data['current']['vis_km'];
    $pressure_mb = $data['current']['pressure_mb'];
    $uv = $data['current']['uv'];
    $observationTime = $data['current']['last_updated'];
    $icon = $data['current']['condition']['icon'];

    // Insere os dados no banco
    $stmt = $pdo->prepare("
    INSERT INTO clima (city, region, temperature, feelslike_c, heatindex_c, dewpoint_c ,`condition`, humidity, cloud, precip_mm, wind_speed, wind_degree, wind_dir, windchill_c, gust_kph, vis_km, pressure_mb, uv, observation_time, icon)
    VALUES (:city, :region, :temperature, :feelslike_c, :heatindex_c, :dewpoint_c, :condition, :humidity, :cloud, :precip_mm, :wind_speed, :wind_degree, :wind_dir, :windchill_c, :gust_kph, :vis_km, :pressure_mb, :uv, :observation_time, :icon)
    ");

    $stmt->execute([
        ':city' => $city,
        ':region' => $region,
        ':temperature' => $temperature,
        ':feelslike_c' => $feelslike_c,
        ':heatindex_c' => $heatindex_c,
        ':dewpoint_c' => $dewpoint_c,
        ':condition' => $condition,
        ':humidity' => $humidity,
        ':cloud' => $cloud,
        ':precip_mm' => $precip_mm,
        ':wind_speed' => $windSpeed,
        ':wind_degree' => $wind_degree,
        ':wind_dir' => $wind_dir,
        ':windchill_c' => $windchill_c,
        ':gust_kph' => $gust_kph,
        ':vis_km' => $vis_km,
        ':pressure_mb' => $pressure_mb,
        ':uv' => $uv,
        ':observation_time' => $observationTime,
        ':icon' => $icon
    ]);

    echo "Dados do tempo salvos com sucesso!";
} catch (Exception $e) {
    echo "Erro: " . $e->getMessage();
}
