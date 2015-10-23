# news-site-for-phpoopsecond.local
Spec4
Структура приложения "Лента новостей"
news.php.\n
основной файл новостной ленты
INewsDB.class.php
○ интерфейс INewsDB с декларациями методов
для новостной ленты
 NewsDB.class.php
○ класс NewsDB реализующий интерфейс
INewsDB
 save_news.php
○ php-код обработки данных для добавления
записи в таблицу БД
 delete_news.php
php-код обработки данных для удаления
записи из таблицы БД
 get_news.php
○ вывод списка записей из таблицы БД
