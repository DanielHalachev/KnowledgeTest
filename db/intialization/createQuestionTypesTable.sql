CREATE TABLE questionTypes (
  id INT NOT NULL,
  description VARCHAR(30),
  PRIMARY KEY(id)
);

INSERT INTO questionTypes (id, description) VALUES (1, "Тип 1");
INSERT INTO questionTypes (id, description) VALUES (2, "Тип 2");
INSERT INTO questionTypes (id, description) VALUES (3, "Тип 3");
