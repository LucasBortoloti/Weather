Para fazer com que os dados sejam registrados no banco de dados sem precisar ficar atualizando a página, é possível utilizar o:

Cron Job

Utilizando os comandos:

crontab -e

E dentro do arquivo colocar esse comando:

*/15 * * * * /usr/bin/php /var/www/html/weather/weather.php

Nesse exemplo eu coloquei para ele repetir a cada 15 min, e na segunda parte está o diretório do arquivo PHP

A Api utilizada para essa aplicação foi a WeatherAPI

Print do projeto:

![printclima](https://github.com/user-attachments/assets/e89fb35d-8253-4332-bce8-5e0a2b4548ab)

:)
