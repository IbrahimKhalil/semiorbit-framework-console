IF NOT EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'migrations') AND type in (N'U'))
BEGIN
CREATE TABLE migrations (
                            id INT IDENTITY(1,1) NOT NULL PRIMARY KEY,
                            migration NVARCHAR(255) NOT NULL UNIQUE,
                            batch INT NOT NULL,
                            last_update DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
);
END
