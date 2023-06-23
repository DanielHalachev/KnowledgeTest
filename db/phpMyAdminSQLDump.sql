-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 23, 2023 at 07:52 AM
-- Server version: 10.4.27-MariaDB
-- PHP Version: 8.2.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `tests`
--

-- --------------------------------------------------------

--
-- Table structure for table `answers`
--

CREATE TABLE `answers` (
  `id` int(11) NOT NULL,
  `questionId` int(11) NOT NULL,
  `uploaderId` int(11) DEFAULT NULL,
  `label` varchar(255) NOT NULL,
  `isCorrect` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `answers`
--

INSERT INTO `answers` (`id`, `questionId`, `uploaderId`, `label`, `isCorrect`) VALUES
(1, 1, 1, 'Фреймуърк за разработка на десктоп приложения', 0),
(2, 1, 1, 'Библиотека за манипулиране на DOM елементи', 0),
(3, 1, 1, 'Фреймуърк за създаване на уеб приложения', 1),
(4, 1, 1, 'Програмен език за създаване на уеб страници', 0),
(5, 2, 1, 'npm install angular-cli', 0),
(6, 2, 1, 'ng install angular-cli', 0),
(7, 2, 1, 'npm install -g @angular/cli', 1),
(8, 2, 1, 'ng add @angular/cli', 0),
(9, 3, 1, 'Част от Angular модул', 0),
(10, 3, 1, 'Код за валидация на форми', 0),
(11, 3, 1, 'HTML шаблон за потребителски интерфейс', 1),
(12, 3, 1, 'Сървиз за извличане на данни от база данни', 0),
(13, 4, 1, ' [(ngModel)]', 1),
(14, 4, 1, '{{ngModel}}', 0),
(15, 4, 1, ' [ngModel]', 0),
(16, 4, 1, '(ngModel)', 0),
(17, 5, 1, '*ngFor', 1),
(18, 5, 1, '*ngIf', 0),
(19, 5, 1, '*ngSwitch', 0),
(20, 5, 1, '*ngWhile', 0),
(21, 6, 1, 'Манипулиране на URL адресите на уеб приложението', 1),
(22, 6, 1, 'Управление на HTTP заявки и отговори', 0),
(23, 6, 1, 'Интеграция със социални мрежи', 0),
(24, 6, 1, 'Валидация на форми в реално време', 0);

-- --------------------------------------------------------

--
-- Table structure for table `feedbacks`
--

CREATE TABLE `feedbacks` (
  `id` int(11) NOT NULL,
  `questionId` int(11) NOT NULL,
  `complexity` int(11) DEFAULT NULL,
  `feedback` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `feedbacks`
--

INSERT INTO `feedbacks` (`id`, `questionId`, `complexity`, `feedback`) VALUES
(1, 1, 6, 'Well-written!'),
(2, 2, 7, 'Excellent!'),
(3, 3, 8, 'Excellent but kind of misleading!'),
(4, 4, 9, 'Very good!'),
(5, 5, 10, 'Excellent!'),
(6, 6, 6, 'Well-written!');

-- --------------------------------------------------------

--
-- Table structure for table `questions`
--

CREATE TABLE `questions` (
  `id` int(11) NOT NULL,
  `testId` int(11) DEFAULT NULL,
  `uploaderId` int(11) DEFAULT NULL,
  `aim` varchar(100) DEFAULT NULL,
  `questionType` int(11) NOT NULL,
  `isMultipleChoice` tinyint(1) NOT NULL DEFAULT 0,
  `label` varchar(200) NOT NULL,
  `correctFeedback` varchar(200) DEFAULT NULL,
  `incorrectFeedback` varchar(200) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `questions`
--

INSERT INTO `questions` (`id`, `testId`, `uploaderId`, `aim`, `questionType`, `isMultipleChoice`, `label`, `correctFeedback`, `incorrectFeedback`) VALUES
(1, 1, 1, 'Проверка на знанията за основите на Angular и неговата функционалност', 3, 1, 'Какво представлява Angular?', 'Правилно! Angular е фреймуърк за създаване на уеб приложения.', 'Грешка! Angular е фреймуърк за създаване на уеб приложения.'),
(2, 1, 1, 'Проверка на познанията за инсталацията на Angular CLI.', 3, 1, 'Каква команда се използва за инсталиране на Angular CLI?', 'Правилно! За инсталация на Angular CLI се използва командата npm install -g @angular/cli', 'Грешка! За инсталация на Angular CLI трябва да използвате командата npm install -g @angular/cli.'),
(3, 1, 1, 'Проверка на разбирането за понятието на компонент в Angular.', 3, 1, 'Какво представлява компонентът в Angular?', 'Правилно! Компонентът в Angular представлява HTML шаблон за потребителски интерфейс.', 'Грешка! В компонента в Angular е включен HTML шаблон за потребителски интерфейс.'),
(4, 1, 1, 'Проверка на познанията за двустранното данни свързване в Angular.', 3, 1, 'Как се постига двустранно данни свързване (two-way data binding) в Angular?', 'Правилно! Двустранното данни свързване се постига с помощта на [(ngModel)] директива.', 'Грешка! За двустранното данни свързване в Angular се използва [(ngModel)] директива.'),
(5, 1, 1, 'Проверка на познанията за итерацията през елементи в Angular.', 3, 1, 'Коя директива се използва за итерация през елементи в Angular?', 'Правилно! За итерация през елементи в Angular се използва директивата *ngFor.', 'Грешка! Директивата *ngFor се използва за итерация през елементи в Angular.'),
(6, 1, 1, 'Проверка на разбирането за целта на рутирането в Angular.', 3, 1, 'Каква е целта на Angular рутирането (routing)?', 'Правилно! Целта на Angular рутирането е манипулиране на URL адресите на уеб приложението.', 'Грешка! Целта на Angular рутирането е манипулиране на URL адресите на уеб приложението.');

-- --------------------------------------------------------

--
-- Table structure for table `questiontypes`
--

CREATE TABLE `questiontypes` (
  `id` int(11) NOT NULL,
  `description` varchar(30) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `questiontypes`
--

INSERT INTO `questiontypes` (`id`, `description`) VALUES
(1, 'Тип 1'),
(2, 'Тип 2'),
(3, 'Тип 3');

-- --------------------------------------------------------

--
-- Table structure for table `tests`
--

CREATE TABLE `tests` (
  `id` int(11) NOT NULL,
  `uploaderId` int(11) NOT NULL,
  `author` varchar(100) DEFAULT NULL,
  `topic` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tests`
--

INSERT INTO `tests` (`id`, `uploaderId`, `author`, `topic`) VALUES
(1, 1, '66666', 'Angular');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `googleId` varchar(255) DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `password` varbinary(255) NOT NULL,
  `firstName` varchar(50) NOT NULL,
  `lastName` varchar(50) NOT NULL,
  `profilePicture` varchar(25) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `googleId`, `email`, `password`, `firstName`, `lastName`, `profilePicture`) VALUES
(1, NULL, 'ivan.ivanov@gmail.com', 0x24327924313024336578534c68375578593533386e30736f6c6a48414f6f6c6f533864477836655839765a2e4f3733552f5058524d327a53394f432e, 'Иван', 'Иванов', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `answers`
--
ALTER TABLE `answers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `uploaderId` (`uploaderId`),
  ADD KEY `questionId` (`questionId`);

--
-- Indexes for table `feedbacks`
--
ALTER TABLE `feedbacks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `questionId` (`questionId`);

--
-- Indexes for table `questions`
--
ALTER TABLE `questions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `uploaderId` (`uploaderId`),
  ADD KEY `testId` (`testId`),
  ADD KEY `questionType` (`questionType`);

--
-- Indexes for table `questiontypes`
--
ALTER TABLE `questiontypes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tests`
--
ALTER TABLE `tests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `uploaderId` (`uploaderId`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `googleId` (`googleId`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `answers`
--
ALTER TABLE `answers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `feedbacks`
--
ALTER TABLE `feedbacks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `questions`
--
ALTER TABLE `questions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `questiontypes`
--
ALTER TABLE `questiontypes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `tests`
--
ALTER TABLE `tests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `answers`
--
ALTER TABLE `answers`
  ADD CONSTRAINT `answers_ibfk_1` FOREIGN KEY (`uploaderId`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `answers_ibfk_2` FOREIGN KEY (`questionId`) REFERENCES `questions` (`id`);

--
-- Constraints for table `feedbacks`
--
ALTER TABLE `feedbacks`
  ADD CONSTRAINT `feedbacks_ibfk_1` FOREIGN KEY (`questionId`) REFERENCES `questions` (`id`);

--
-- Constraints for table `questions`
--
ALTER TABLE `questions`
  ADD CONSTRAINT `questions_ibfk_1` FOREIGN KEY (`uploaderId`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `questions_ibfk_2` FOREIGN KEY (`testId`) REFERENCES `tests` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `questions_ibfk_3` FOREIGN KEY (`questionType`) REFERENCES `questiontypes` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `tests`
--
ALTER TABLE `tests`
  ADD CONSTRAINT `tests_ibfk_1` FOREIGN KEY (`uploaderId`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
