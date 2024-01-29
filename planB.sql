/* If the table is not created automatically, then run this code. */
CREATE TABLE hleb_spreader_conf (
  designation varchar(255) NOT NULL,
  content varchar(5000) NOT NULL,
  UNIQUE (designation)
);
