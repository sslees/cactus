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
#include <sys/wait.h>
#include <time.h>
#include <unistd.h>

#include "cactus_sql.h"
#include "cactus.h"
#include "sqlite3.h"

#define COMPARE_VALS 12

static sqlite3 *db;
static time_t latest_timestamp;

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
   static double recent[] =
    {-1.0, 0.0, 0.0, 0.0, 0.0, 0.0, 0.0, 0.0, 0.0, 0.0, 0.0, 0.0};

   sqlite3_stmt *stmt;
   int i;
   double sum = 0.0;

   sql_cmd("SELECT timestamp FROM measurements WHERE rowid = "
    "(SELECT max(rowid) FROM measurements);", sql_update_latest);
   if (timestamp >= latest_timestamp + S_BETWEEN_STORES) {
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
   if (recent[0] == -1.0)
      for (i = 0; i < COMPARE_VALS; i++) recent[i] = measurement;
   else {
      for (i = 0; i < COMPARE_VALS - 1; i++) recent[i] = recent[i + 1];
      recent[COMPARE_VALS - 1] = measurement;
   }
   for (i = 0; i < COMPARE_VALS; i++) sum += recent[i];
   if (sum / COMPARE_VALS < DRY_THRESHOLD) notify();
}

void notify() {
   int pipeFDs[2];

   pipe(pipeFDs);
   if (!fork()) { // if child
      close(pipeFDs[1]); // close child pipe write
      dup2(pipeFDs[0], 0); // forward child stdin to child pipe read
      close(pipeFDs[0]); // close child pipe read
      execlp(PYTHON_EXE, PYTHON_EXE, NOTIFY_SCRIPT, NULL); // run script
   }
   close(pipeFDs[0]); // close parent pipe read
   wait(NULL); // wait for child to terminate
   write(pipeFDs[1], /**data*/NULL, /*sizeof data*/0); // write data to parent pipe write
   close(pipeFDs[1]); // close parent pipe write
}

int sql_update_latest(void *notUsed, int argc, char **argv, char **colName) {
   latest_timestamp = *argv ? strtol(*argv, NULL, 10) : 0;

   return 0;
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
