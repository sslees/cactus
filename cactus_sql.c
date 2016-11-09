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

#include "sqlite3.h"

static int callback(void *NotUsed, int argc, char **argv, char **azColName) {
   for (int i = 0; i < argc; i++)
      printf("%s = %s\n", azColName[i], argv[i] ? argv[i] : "NULL");
   printf("\n");

   return 0;
}

int main(int argc, char **argv) {
   sqlite3 *db;
   char *zErrMsg = 0;
   int rc;

   if (argc != 3) {
      fprintf(stderr, "Usage: %s DATABASE SQL-STATEMENT\n", argv[0]);

      return 1;
   }
   if ((rc = sqlite3_open(argv[1], &db))) {
      fprintf(stderr, "Can't open database: %s\n", sqlite3_errmsg(db));
      sqlite3_close(db);

      return 1;
   }
   if ((rc = sqlite3_exec(db, argv[2], callback, 0, &zErrMsg)) != SQLITE_OK) {
      fprintf(stderr, "SQL error: %s\n", zErrMsg);
      sqlite3_free(zErrMsg);
   }
   sqlite3_close(db);

   return 0;
}

// CREATE TABLE raw_data(timestamp INTEGER PRIMARY KEY ASC, measurement REAL NOT NULL);
// INSERT INTO raw_data VALUES(0, 0.0);
