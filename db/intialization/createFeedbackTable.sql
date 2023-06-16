CREATE TABLE feedbacks (
  id INT NOT NULL AUTO_INCREMENT,
  questionId INT NOT NULL,
  complexity INT NULL,
  feedback TEXT NULL,
  PRIMARY KEY(id),
  FOREIGN KEY (questionId) REFERENCES questions(id)
);
