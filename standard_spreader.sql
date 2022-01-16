CREATE TABLE spreader_configs (
  designation varchar(100) NOT NULL,
  content varchar(5000) NOT NULL,
  UNIQUE (designation)
);
