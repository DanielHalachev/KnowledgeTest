<?php
// SERVER CONFIGURATION VARIABLES

// Site Variables
define('SITE_NAME', 'KnowledgeTest');
define('SITE_CREATOR_NAME_1', 'Даниел Халачев');
define('SITE_CREATOR_FN_1', '62547');
define('SITE_CREATOR_EMAIL_1', 'dihalachev@uni-sofia.bg');
define('SITE_CREATOR_NAME_2', 'Стефан Велев');
define('SITE_CREATOR_FN_2', '62537');
define('SITE_CREATOR_EMAIL_2', 'sdvelev@uni-sofia.bg');
define('SITE_INFO', 'This project was created during 2023 year, on Web Technologies course at FMI, Sofia University, lead by: prof. Milen Petrov');
define('SITE_DESCRIPTION', 'Software system for generating, importing, exporting and solving tests');

// Database Variables
define('DB_HOST', 'localhost');
define('DB_NAME', 'tests');
define('DB_USER', 'root');
define('DB_PASSWORD', '');

// Default number of items in GET collection response
define('DEFAULT_QUERY_SIZE', 10);

// CSV Variables
define('DELIMITER', ';');
define('MAXIMUM_LINE_LENGTH', 10000);
define('FROM_ENCODING', 'CP866');
define('TO_ENCODING', 'UTF-8');
define('OPENING_MODE', 'r');
define('INDEX_COLUMN_TIME', 0);
define('INDEX_COLUMN_FACULTY_NUMBER', 1);
define('INDEX_COLUMN_QUESTION_NUMBER', 2);
define('INDEX_COLUMN_QUESTION_AIM', 3);
define('INDEX_COLUMN_QUESTION_TYPE', 4);
define('INDEX_COLUMN_QUESTION_TEXT', 5);
define('INDEX_COLUMN_QUESTION_FIRST_OPTION', 6);
define('INDEX__COLUMN_QUESTION_SECOND_OPTION', 7);
define('INDEX_COLUMN_QUESTION_THIRD_OPTION', 8);
define('INDEX_COLUMN_QUESTION_FOURTH_OPTION', 9);
define('INDEX_COLUMN_QUESTION_CORRECT_ANSWER', 10);
define('INDEX_COLUMN_QUESTION_COMPLEXITY', 11);
define('INDEX_COLUMN_CORRECT_FEEDBACK', 12);
define('INDEX_COLUMN_INCORRECT_FEEDBACK', 13);
define('INDEX_COLUMN_NOTES', 14);
?>
