/*
 * File: cactus_sql.c
 * Author: Samuel Lees (sslees)
 * Date: 11/07/16
 * Class: CPE 458-01
 * Assignment: Final Project
 * References:
 *    https://www.sqlite.org/cintro.html
 */

#include <stdio.h>
#include <stdlib.h>
#include <time.h>

#include "cactus_sql.h"
#include "sqlite3.h"

static sqlite3 *db;

void sql_open(char *dbName) {
   if (sqlite3_open(dbName, &db)) {
      fprintf(stderr, "Can't open database: %s\nTerminating.\n",
       sqlite3_errmsg(db));
      sqlite3_close(db);

      exit(1);
   }
}

void sql_prep_table(char *dbName) {
   sql_cmd("CREATE TABLE IF NOT EXISTS measurements(timestamp INTEGER NOT NULL "
    "UNIQUE, measurement REAL NOT NULL);", NULL);
}

void sql_cmd(char *cmd, int (*callback)(void *, int, char **, char **)) {
   char *errMsg;

   if (sqlite3_exec(db, cmd, callback, 0, &errMsg) != SQLITE_OK) {
      fprintf(stderr, "SQL error: %s\nTerminating.\n", errMsg);
      sqlite3_free(errMsg);

      exit(1);
   }
}

void sql_store_data(time_t timestamp, double measurement) {
   sqlite3_stmt *stmt;

   sqlite3_prepare_v2(db, "INSERT INTO measurements VALUES(?1, ?2);", -1,
    &stmt, NULL);
   sqlite3_bind_int(stmt, 1, timestamp);
   sqlite3_bind_double(stmt, 2, measurement);
   if (sqlite3_step(stmt) != SQLITE_DONE) {
      fprintf(stderr, "SQL error: %s\nTerminating.\n", sqlite3_errmsg(db));

      exit(1);
   }
   sqlite3_finalize(stmt);
}

int sql_print(void *notUsed, int argc, char **argv, char **colName) {
   int i;

   for (i = 0; i < argc - 1; i++)
      printf("%s: %s, ", colName[i], argv[i] ? argv[i] : "NULL");
   printf("%s: %s\n", colName[i], argv[i] ? argv[i] : "NULL");

   return 0;
}

void sql_close() {
   sqlite3_close(db);
}
