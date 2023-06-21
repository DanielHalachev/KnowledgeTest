CREATE DATABASE tests;

USE tests;

CREATE TABLE users (
                       id INT NOT NULL AUTO_INCREMENT,
                       googleId VARCHAR(255) UNIQUE,
                       email VARCHAR(255) UNIQUE NOT NULL,
                       password VARBINARY(255) NOT NULL,
                       firstName VARCHAR(50) NOT NULL,
                       lastName VARCHAR(50) NOT NULL,
                       profilePicture VARCHAR(25) NULL,
                       PRIMARY KEY(id)
);

CREATE TABLE tests (
                       id INT NOT NULL AUTO_INCREMENT,
                       uploaderId INT NOT NULL,
                       author VARCHAR(100) NULL,
                       topic VARCHAR(50) NULL,
                       PRIMARY KEY (id),
                       FOREIGN KEY (uploaderId) REFERENCES users (id)
);

CREATE TABLE questionTypes (
                               id INT NOT NULL AUTO_INCREMENT,
                               description VARCHAR(30),
                               PRIMARY KEY(id)
);

INSERT INTO questionTypes (id, description) VALUES (1, "Тип 1");
INSERT INTO questionTypes (id, description) VALUES (2, "Тип 2");
INSERT INTO questionTypes (id, description) VALUES (3, "Тип 3");

CREATE TABLE questions (
                           id INT NOT NULL AUTO_INCREMENT,
                           testId INT NULL,
                           uploaderId INT NULL,
                           aim VARCHAR(100) NULL,
                           questionType INT NOT NULL,
                           isMultipleChoice BOOLEAN NOT NULL DEFAULT FALSE,
                           label VARCHAR(200) NOT NULL,
                           correctFeedback VARCHAR(200) NULL,
                           incorrectFeedback VARCHAR(200) NULL,
                           PRIMARY KEY (id),
                           FOREIGN KEY (uploaderId) REFERENCES users (id) ON DELETE CASCADE,
                           FOREIGN KEY (testId) REFERENCES tests(id) ON DELETE CASCADE,
                           FOREIGN KEY(questionType) REFERENCES questionTypes(id) ON DELETE CASCADE
);

CREATE TABLE answers (
                         id INT NOT NULL AUTO_INCREMENT,
                         questionId INT NOT NULL,
                         uploaderId INT NULL,
                         label VARCHAR(255) NOT NULL,
                         isCorrect BOOLEAN NOT NULL,
                         PRIMARY KEY (id),
                         FOREIGN KEY (uploaderId) REFERENCES users (id) ON DELETE CASCADE,
                         FOREIGN KEY (questionId) REFERENCES questions(id)
);

CREATE TABLE feedbacks (
                           id INT NOT NULL AUTO_INCREMENT,
                           questionId INT NOT NULL,
                           complexity INT NULL,
                           feedback TEXT NULL,
                           PRIMARY KEY(id),
                           FOREIGN KEY (questionId) REFERENCES questions(id)
);