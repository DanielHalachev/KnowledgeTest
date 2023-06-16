CREATE TABLE tests (
  id INT NOT NULL AUTO_INCREMENT,
  uploaderId INT NOT NULL,
  authorId INT NULL,
  topicId INT NULL,
  PRIMARY KEY (id),
  FOREIGN KEY (uploaderId) REFERENCES users (id),
  FOREIGN KEY (authorId) REFERENCES authors (id),
  FOREIGN KEY (topicId) REFERENCES topics (id)
);

