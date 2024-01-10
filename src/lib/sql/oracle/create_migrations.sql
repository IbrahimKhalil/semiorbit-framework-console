DECLARE
table_count NUMBER;
BEGIN
SELECT COUNT(*)
INTO table_count
FROM user_tables
WHERE table_name = 'MIGRATIONS';

IF table_count = 0 THEN
        EXECUTE IMMEDIATE '
            CREATE TABLE migrations (
                id NUMBER GENERATED BY DEFAULT ON NULL AS IDENTITY PRIMARY KEY,
                migration NVARCHAR2(255) NOT NULL UNIQUE,
                batch INT NOT NULL,
                last_update TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL
            )';
END IF;
END;
/