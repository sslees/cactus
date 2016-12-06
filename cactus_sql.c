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
#include "cactus.h"
#include "sqlite3.h"

#define COMPARE_VALS 12
#define NOTIFICATOIN_INTERVAL_S 480 // 8 hrs.

static sqlite3 *db;
static long tempCt;
static double latestAvg;

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
   sql_cmd("CREATE TABLE IF NOT EXISTS temps(timestamp INTEGER NOT NULL "
    "UNIQUE, measurement REAL NOT NULL);", NULL);
   sql_cmd("CREATE TABLE IF NOT EXISTS stats(parameter TEXT NOT NULL UNIQUE, "
    "timestamp INTEGER NOT NULL, measurement REAL NOT NULL);", NULL);
}

void sql_cmd(char *cmd, int (*callback)(void *, int, char **, char **)) {
   char *errMsg;

   if (sqlite3_exec(db, cmd, callback, 0, &errMsg) != SQLITE_OK) {
      fprintf(stderr, "SQL error: %s\nTerminating.\n", errMsg);
      sqlite3_free(errMsg);

      exit(1);
   }
}

void sql_process_data(time_t timestamp, double measurement) {
   static char dry = 0;
   static time_t lastNotified = 0;

   sqlite3_stmt *stmt;

   sqlite3_prepare_v2(db, "REPLACE INTO stats VALUES('current', ?1, ?2);", -1,
    &stmt, NULL);
   sqlite3_bind_int(stmt, 1, timestamp);
   sqlite3_bind_double(stmt, 2, measurement);
   if (sqlite3_step(stmt) != SQLITE_DONE) {
      fprintf(stderr, "SQL error: %s\nTerminating.\n", sqlite3_errmsg(db));

      exit(1);
   }
   sqlite3_finalize(stmt);

   sql_cmd("DELETE FROM stats WHERE timestamp < "
    "strftime('%s', 'now', '-1 day');", NULL);

   sql_cmd("REPLACE INTO stats VALUES("
    "   'minimum',"
    "   (SELECT timestamp FROM stats WHERE measurement ="
    "    (SELECT min(measurement) FROM stats)),"
    "   (SELECT min(measurement) FROM stats)"
    ");", NULL);

   sql_cmd("REPLACE INTO stats VALUES("
    "   'maximum',"
    "   (SELECT timestamp FROM stats WHERE measurement ="
    "    (SELECT max(measurement) FROM stats)),"
    "   (SELECT max(measurement) FROM stats)"
    ");", NULL);

   sql_cmd("DELETE FROM temps WHERE timestamp < "
    "strftime('%s', 'now', '-3 minute');", NULL);

   sql_cmd("INSERT INTO temps VALUES("
    "(SELECT timestamp FROM stats WHERE parameter = 'current'), "
    "(SELECT measurement FROM stats WHERE parameter = 'current'));", NULL);

   sql_cmd("SELECT count(*) FROM temps;", sql_count_temps);
   if (tempCt == SAMPLE_SIZE) {
      sql_cmd("INSERT INTO measurements VALUES("
       "(SELECT avg(timestamp) FROM temps), "
       "(SELECT avg(measurement) FROM temps));", NULL);

      sql_cmd("DELETE FROM temps;", NULL);

      sql_cmd("SELECT measurement FROM measurements WHERE rowid = "
       "last_insert_rowid();", sql_check_latest_avg);
      if (latestAvg < DRY_THRESHOLD &&
       (!dry | (dry && time(NULL) - lastNotified > NOTIFICATOIN_INTERVAL_S))) {
         dry = 1;
         lastNotified = timestamp;
         notify_dry();
      } else if (latestAvg > WATERED_THRESHOLD &&
       (dry | (!dry && time(NULL) - lastNotified > NOTIFICATOIN_INTERVAL_S))) {
         dry = 0;
         lastNotified = timestamp;
         notify_watered();
      }
   }
}

int sql_count_temps(void *notUsed, int argc, char **argv, char **colName) {
   tempCt = strtol(*argv, NULL, 10);

   return 0;
}

int sql_check_latest_avg(void *notUsed, int argc, char **argv, char **colName) {
   latestAvg = strtod(*argv, NULL);

   return 0;
}

void sql_close() {
   sqlite3_close(db);
}
