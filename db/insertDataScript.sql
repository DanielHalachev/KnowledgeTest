USE tests;

INSERT INTO `users` (id, email, password, firstName, lastName)
VALUES (1, "ivan.ivanov@gmail.com", "$2y$10$3exSLh7UxY538n0soljHAOoloS8dGx6eX9vZ.O73U/PXRM2zS9OC.", "Иван", "Иванов");

INSERT INTO `tests` (id, uploaderId, author, topic)
VALUES (1, 1, "66666", "Angular");

INSERT INTO `questions` (id, testId, uploaderId, aim, questionType, isMultipleChoice, label, correctFeedback, incorrectFeedback)
VALUES (1, 1, 1, "Проверка на знанията за основите на Angular и неговата функционалност", 3, 1, "Какво представлява Angular?",
"Правилно! Angular е фреймуърк за създаване на уеб приложения.", "Грешка! Angular е фреймуърк за създаване на уеб приложения.");

INSERT INTO `answers` (id, questionId, uploaderId, label, isCorrect)
VALUES (1, 1, 1, "Фреймуърк за разработка на десктоп приложения", 0);

INSERT INTO `answers` (id, questionId, uploaderId, label, isCorrect)
VALUES (2, 1, 1, "Библиотека за манипулиране на DOM елементи", 0);

INSERT INTO `answers` (id, questionId, uploaderId, label, isCorrect)
VALUES (3, 1, 1, "Фреймуърк за създаване на уеб приложения", 1);

INSERT INTO `answers` (id, questionId, uploaderId, label, isCorrect)
VALUES (4, 1, 1, "Програмен език за създаване на уеб страници", 0);

INSERT INTO `questions` (id, testId, uploaderId, aim, questionType, isMultipleChoice, label, correctFeedback, incorrectFeedback)
VALUES (2, 1, 1, "Проверка на познанията за инсталацията на Angular CLI.", 3, 1, "Каква команда се използва за инсталиране на Angular CLI?",
"Правилно! За инсталация на Angular CLI се използва командата npm install -g @angular/cli", "Грешка! За инсталация на Angular CLI трябва да използвате командата npm install -g @angular/cli.");

INSERT INTO `answers` (id, questionId, uploaderId, label, isCorrect)
VALUES (5, 2, 1, "npm install angular-cli", 0);

INSERT INTO `answers` (id, questionId, uploaderId, label, isCorrect)
VALUES (6, 2, 1, "ng install angular-cli", 0);

INSERT INTO `answers` (id, questionId, uploaderId, label, isCorrect)
VALUES (7, 2, 1, "npm install -g @angular/cli", 1);

INSERT INTO `answers` (id, questionId, uploaderId, label, isCorrect)
VALUES (8, 2, 1, "ng add @angular/cli", 0);

INSERT INTO `questions` (id, testId, uploaderId, aim, questionType, isMultipleChoice, label, correctFeedback, incorrectFeedback)
VALUES (3, 1, 1, "Проверка на разбирането за понятието на компонент в Angular.", 3, 1, "Какво представлява компонентът в Angular?",
"Правилно! Компонентът в Angular представлява HTML шаблон за потребителски интерфейс.", "Грешка! В компонента в Angular е включен HTML шаблон за потребителски интерфейс.");

INSERT INTO `answers` (id, questionId, uploaderId, label, isCorrect)
VALUES (9, 3, 1, "Част от Angular модул", 0);

INSERT INTO `answers` (id, questionId, uploaderId, label, isCorrect)
VALUES (10, 3, 1, "Код за валидация на форми", 0);

INSERT INTO `answers` (id, questionId, uploaderId, label, isCorrect)
VALUES (11, 3, 1, "HTML шаблон за потребителски интерфейс", 1);

INSERT INTO `answers` (id, questionId, uploaderId, label, isCorrect)
VALUES (12, 3, 1, "Сървиз за извличане на данни от база данни", 0);

INSERT INTO `questions` (id, testId, uploaderId, aim, questionType, isMultipleChoice, label, correctFeedback, incorrectFeedback)
VALUES (4, 1, 1, "Проверка на познанията за двустранното данни свързване в Angular.", 3, 1, "Как се постига двустранно данни свързване (two-way data binding) в Angular?",
"Правилно! Двустранното данни свързване се постига с помощта на [(ngModel)] директива.", "Грешка! За двустранното данни свързване в Angular се използва [(ngModel)] директива.");

INSERT INTO `answers` (id, questionId, uploaderId, label, isCorrect)
VALUES (13, 4, 1, " [(ngModel)]", 1);

INSERT INTO `answers` (id, questionId, uploaderId, label, isCorrect)
VALUES (14, 4, 1, "{{ngModel}}", 0);

INSERT INTO `answers` (id, questionId, uploaderId, label, isCorrect)
VALUES (15, 4, 1, " [ngModel]", 0);

INSERT INTO `answers` (id, questionId, uploaderId, label, isCorrect)
VALUES (16, 4, 1, "(ngModel)", 0);

INSERT INTO `questions` (id, testId, uploaderId, aim, questionType, isMultipleChoice, label, correctFeedback, incorrectFeedback)
VALUES (5, 1, 1, "Проверка на познанията за итерацията през елементи в Angular.", 3, 1, "Коя директива се използва за итерация през елементи в Angular?",
        "Правилно! За итерация през елементи в Angular се използва директивата *ngFor.", "Грешка! Директивата *ngFor се използва за итерация през елементи в Angular.");

INSERT INTO `answers` (id, questionId, uploaderId, label, isCorrect)
VALUES (17, 5, 1, "*ngFor", 1);

INSERT INTO `answers` (id, questionId, uploaderId, label, isCorrect)
VALUES (18, 5, 1, "*ngIf", 0);

INSERT INTO `answers` (id, questionId, uploaderId, label, isCorrect)
VALUES (19, 5, 1, "*ngSwitch", 0);

INSERT INTO `answers` (id, questionId, uploaderId, label, isCorrect)
VALUES (20, 5, 1, "*ngWhile", 0);

INSERT INTO `questions` (id, testId, uploaderId, aim, questionType, isMultipleChoice, label, correctFeedback, incorrectFeedback)
VALUES (6, 1, 1, "Проверка на разбирането за целта на рутирането в Angular.", 3, 1, "Каква е целта на Angular рутирането (routing)?",
"Правилно! Целта на Angular рутирането е манипулиране на URL адресите на уеб приложението.", "Грешка! Целта на Angular рутирането е манипулиране на URL адресите на уеб приложението.");

INSERT INTO `answers` (id, questionId, uploaderId, label, isCorrect)
VALUES (21, 5, 1, "Манипулиране на URL адресите на уеб приложението", 1);

INSERT INTO `answers` (id, questionId, uploaderId, label, isCorrect)
VALUES (22, 5, 1, "Управление на HTTP заявки и отговори", 0);

INSERT INTO `answers` (id, questionId, uploaderId, label, isCorrect)
VALUES (23, 5, 1, "Интеграция със социални мрежи", 0);

INSERT INTO `answers` (id, questionId, uploaderId, label, isCorrect)
VALUES (24, 5, 1, "Валидация на форми в реално време", 0);

INSERT INTO `feedbacks` (id, questionId, complexity, feedback)
VALUES (1, 1, 6, "Well-written!");

INSERT INTO `feedbacks` (id, questionId, complexity, feedback)
VALUES (2, 2, 7, "Excellent!");

INSERT INTO `feedbacks` (id, questionId, complexity, feedback)
VALUES (3, 3, 8, "Excellent but kind of misleading!");

INSERT INTO `feedbacks` (id, questionId, complexity, feedback)
VALUES (4, 4, 9, "Very good!");

INSERT INTO `feedbacks` (id, questionId, complexity, feedback)
VALUES (5, 5, 10, "Excellent!");

INSERT INTO `feedbacks` (id, questionId, complexity, feedback)
VALUES (6, 6, 6, "Well-written!");