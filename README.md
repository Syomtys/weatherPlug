# EN
## Weather plugin for Wordpress

This Wordpress plugin performs the following functions:
- Determining the site visitor's city through the Dadata service.
- Request weather data for the received city from OpenWeatherMap.
- Enable/disable the plugin.
- Output of weather data in the basement of each user page.
- A widget showing the current weather in the user's city and the last 5 requests in the format: city, time, temperature.
- Record user data in a database with the following columns:
    - Record ID
- temperature (in the user's city at the time of the request)
    - city (User's city)
- recorded_at (Recording time)
- IP
    - active (Plugin activity (1 or 0))
- data_json (Detailed weather forecast in the user's city for 5 days (in JSON format))

### Getting started:

It is assumed that you already have a Wordpress CMS installed.

- Create a user database:

    ```sql
    CREATE TABLE wp_weather_data (
        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        temperature FLOAT,
        city VARCHAR(255),
        recorded_at DATETIME,
        Ip VARCHAR(20),
        active BOOLEAN,
        data_json TEXT
    );
    ```

- Copy this project to the working wordpress directory along the path wp-content/plugins/weatherPlug, weatherPlug is the directory of the project that needs to be created.

- Register on dadata(1 key) and openweathermap(2 keys) services to receive api keys. the received keys must be registered in the file config.php
 
# RU
## Плагин погоды для Wordpress

Данный плагин для Wordpress выполняет следующие функции:
- Определение города посетителя сайта через сервис Dadata.
- Запрос данных о погоде для полученного города из OpenWeatherMap.
- Включение/выключение плагина.
- Вывод данных о погоде в подвале каждой пользовательской страницы.
- Виджет, показывающий текущую погоду в городе пользователя и последние 5 запросов в формате: город, время, температура.
- Запись данных пользователей в базу данных с следующими столбцами:
    - ID записи
    - temperature (в городе пользователя на момент запроса)
    - city (Город пользователя)
    - recorded_at (Время записи)
    - IP
    - active (Активность плагина (1 или 0))
    - data_json (Подробный прогноз погоды в городе пользователя на 5 дней (в формате JSON))

### Начало работы:

Предполагается, что у вас уже установлена CMS Wordpress.

- Создайте базу данных пользователей:

    ```sql
    CREATE TABLE wp_weather_data (
        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        temperature FLOAT,
        city VARCHAR(255),
        recorded_at DATETIME,
        Ip VARCHAR(20),
        active BOOLEAN,
        data_json TEXT
    );
    ```

- скопируйте данный проект в рабочую wordpress директорию по пути wp-content/plugins/weatherPlug, weatherPlug директория проекта которую необходимо создать.

- зарегистрируйтесь на сервисах dadata(1 ключ) и openweathermap(2ключа) для получения api ключей. полученные ключи необходимо прописать в файле config.php
