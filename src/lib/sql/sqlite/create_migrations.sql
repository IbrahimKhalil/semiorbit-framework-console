CREATE TABLE IF NOT EXISTS "migrations" (
    "id" INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
    "migration" TEXT NOT NULL UNIQUE,
    "batch" INTEGER NOT NULL,
    "last_update" DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
);