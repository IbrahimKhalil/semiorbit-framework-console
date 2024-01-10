SELECT * FROM migrations AS m
WHERE batch = (SELECT MAX(batch) FROM migrations)
ORDER BY migration DESC