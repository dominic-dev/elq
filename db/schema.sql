CREATE TABLE publishers(
publisher_id INTEGER PRIMARY KEY NOT NULL,
name varchar(55) not null
);

CREATE TABLE "books"(
book_id INTEGER PRIMARY KEY NOT NULL,
title varchar(55) not null,
featured BIT not null default(0),
author_id INTEGER REFERENCES authors(author_id) ON DELETE CASCADE,
publisher_id INTEGER REFERENCES publishers(publisher_id) ON DELETE CASCADE
);

CREATE TABLE authors(
author_id INTEGER PRIMARY KEY NOT NULL,
first_name varchar(55) not null,
last_name varchar(55) not null
);

