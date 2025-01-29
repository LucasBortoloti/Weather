<?php
// Configurações do banco de dados
$host = 'localhost';
$dbname = 'weather_data';
$user = 'Marcelo';
$password = 'prefeiturajaragua';

try {
    // Conexão com o banco de dados
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Data padrão (hoje) ou a data selecionada pelo usuário
    $selectedDate = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d');

    // Consulta para obter os dados filtrados pela data
    $stmt = $pdo->prepare("
        SELECT * 
        FROM clima 
        WHERE DATE(observation_time) = :selectedDate
        ORDER BY observation_time ASC
    ");
    $stmt->execute([':selectedDate' => $selectedDate]);
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Filtrar apenas os dados de 2 em 2 horas
    $filteredData = [];
    $lastHour = null;
    foreach ($data as $row) {
        $currentHour = date('H', strtotime($row['observation_time'])); // Extrai apenas a hora
        if ($currentHour % 2 === 0 && $currentHour !== $lastHour) { // Apenas horas múltiplas de 2
            $filteredData[] = $row;
            $lastHour = $currentHour;
        }
    }
} catch (Exception $e) {
    die("Erro ao conectar ou consultar o banco: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Previsão do Tempo</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        /* Estilo simplificado */
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f0f5;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 1200px;
            margin: 20px auto;
            padding: 10px;
        }

        h1 {
            text-align: center;
        }

        canvas {
            margin: 20px 0;
        }

        .icons-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 20px;
        }

        .icon-item {
            text-align: center;
            font-size: 14px;
        }

        .icon-item img {
            width: 50px;
            height: 50px;
        }

        .date-filter {
            text-align: center;
            margin-bottom: 20px;
        }

        .date-filter form {
            display: inline-block;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>Previsão do Tempo</h1>

        <!-- Filtro de Data -->
        <div class="date-filter">
            <form method="GET" action="">
                <label for="date">Selecione a data:</label>
                <input type="date" id="date" name="date" value="<?php echo htmlspecialchars($selectedDate); ?>">
                <button type="submit">Filtrar</button>
            </form>
        </div>

        <!-- Gráfico -->
        <?php if (!empty($data)): ?>
            <canvas id="weatherChart"></canvas>
        <?php else: ?>
            <p style="text-align: center; color: red;">Nenhum dado encontrado para a data selecionada.</p>
        <?php endif; ?>

        <!-- Ícones do Clima -->
        <div class="icons-container">
            <?php if (!empty($filteredData)): ?>
                <?php foreach ($filteredData as $row): ?>
                    <div class="icon-item">
                        <img src="https:<?php echo $row['icon']; ?>" alt="<?php echo $row['condition']; ?>">
                        <p><?php echo date('H:i', strtotime($row['observation_time'])); ?></p>
                        <p><?php echo $row['condition']; ?></p>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p style="text-align: center; color: red;">Nenhum dado disponível de 2 em 2 horas.</p>
            <?php endif; ?>
        </div>
    </div>

    <script>
        // Dados do PHP para JavaScript
        const weatherData = <?php echo json_encode($filteredData); ?>;

        const times = weatherData.map(row => new Date(row.observation_time).toLocaleTimeString('pt-BR', {
            hour: '2-digit',
            minute: '2-digit'
        }));
        const temperatures = weatherData.map(row => parseFloat(row.temperature));
        const humidities = weatherData.map(row => parseFloat(row.humidity));
        const windSpeeds = weatherData.map(row => parseFloat(row.wind_speed));
        const precipitations = weatherData.map(row => parseFloat(row.precip_mm));
        const uv = weatherData.map(row => parseFloat(row.uv));
        const feelslike_c = weatherData.map(row => parseFloat(row.feelslike_c));
        const cloud = weatherData.map(row => parseFloat(row.cloud));

        // Gráfico
        const ctx = document.getElementById('weatherChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: times,
                datasets: [{
                        label: 'Temperatura (°C)',
                        data: temperatures,
                        borderColor: 'rgb(255, 24, 74)',
                        backgroundColor: 'rgba(255, 99, 132, 0.2)',
                        fill: true,
                        tension: 0.4
                    },
                    {
                        label: 'Umidade (%)',
                        data: humidities,
                        borderColor: 'rgba(54, 162, 235, 1)',
                        backgroundColor: 'rgba(54, 162, 235, 0.2)',
                        fill: true,
                        tension: 0.4
                    },
                    {
                        label: 'Vento (km/h)',
                        data: windSpeeds,
                        borderColor: 'rgb(4, 94, 94)',
                        backgroundColor: 'rgba(7, 170, 170, 0.2)',
                        fill: true,
                        tension: 0.4
                    },
                    {
                        label: 'Chuva (mm)',
                        data: precipitations,
                        borderColor: 'rgb(80, 35, 168)',
                        backgroundColor: 'rgba(153, 102, 255, 0.2)',
                        fill: true,
                        tension: 0.4
                    },
                    {
                        label: 'Raios UV',
                        data: uv,
                        borderColor: 'rgb(0, 17, 255)',
                        backgroundColor: 'rgba(144, 91, 248, 0.2)',
                        fill: true,
                        tension: 0.4
                    },
                    {
                        label: 'Sensação Térmica (°C)',
                        data: feelslike_c,
                        borderColor: 'rgb(255, 73, 112)',
                        backgroundColor: 'rgba(255, 99, 132, 0.2)',
                        fill: true,
                        tension: 0.4
                    },
                    {
                        label: 'Nuvens',
                        data: cloud,
                        borderColor: 'rgb(6, 115, 187)',
                        backgroundColor: 'rgba(54, 162, 235, 0.2)',
                        fill: true,
                        tension: 0.4
                    }
                ]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top'
                    }
                },
                scales: {
                    x: {
                        title: {
                            display: true,
                            text: 'Horários'
                        }
                    },
                    y: {
                        title: {
                            display: true,
                            text: 'Valores'
                        }
                    }
                }
            }
        });
    </script>
</body>

</html>