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
