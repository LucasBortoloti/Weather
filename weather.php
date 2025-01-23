<?php
// Configurações do banco de dados
$host = 'localhost';
$dbname = 'weather_data';
$user = 'Marcelo';
$password = 'prefeiturajaragua';

// URL da API WeatherAPI
$apiKey = 'af9c22a542c04e7582a195448252001';
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
    $condition = $data['current']['condition']['text'];
    $humidity = $data['current']['humidity'];
    $precip_mm = $data['current']['precip_mm'];
    $windSpeed = $data['current']['wind_kph'];
    $observationTime = $data['current']['last_updated'];
    $icon = $data['current']['condition']['icon'];

    // Insere os dados no banco
    $stmt = $pdo->prepare("
    INSERT INTO clima (city, region, temperature, `condition`, humidity, precip_mm, wind_speed, observation_time, icon)
    VALUES (:city, :region, :temperature, :condition, :humidity, :precip_mm, :wind_speed, :observation_time, :icon)
    ");

    $stmt->execute([
        ':city' => $city,
        ':region' => $region,
        ':temperature' => $temperature,
        ':condition' => $condition,
        ':humidity' => $humidity,
        ':precip_mm' => $precip_mm,
        ':wind_speed' => $windSpeed,
        ':observation_time' => $observationTime,
        ':icon' => $icon
    ]);

    echo "Dados do tempo salvos com sucesso!";
} catch (Exception $e) {
    echo "Erro: " . $e->getMessage();
}
