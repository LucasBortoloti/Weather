Para fazer com que os dados sejam registrados no banco de dados sem precisar ficar atualizando a página, é possível utilizar o:

Cron Job

Utilizando os comandos:

crontab -e

E dentro do arquivo colocar esse comando:

*/15 * * * * /usr/bin/php /var/www/html/weather/weather.php

Nesse exemplo eu coloquei para ele repetir a cada 15 min, e na segunda parte está o diretório do arquivo PHP

:)
