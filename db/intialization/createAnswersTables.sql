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
