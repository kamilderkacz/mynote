    DROP TABLE IF EXISTS guestbook;
     
    CREATE TABLE guestbook (
        id int NOT NULL AUTO_INCREMENT,
        email varchar(32) NOT NULL DEFAULT 'noemail@example.com',
        comment TEXT NULL,
        created DATETIME NOT NULL,
        PRIMARY KEY (id)
    ) ENGINE=MyISAM DEFAULT CHARSET=utf8;
     
    CREATE INDEX id ON guestbook(id);