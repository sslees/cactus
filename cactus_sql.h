/*
 * File: cactus_sql.h
 * Author: Samuel Lees (sslees)
 * Date: 11/07/16
 * Class: CPE 458-01
 * Assignment: Final Project
 */

#ifndef CACTUS_SQL_H
#define CACTUS_SQL_H

#define DATABASE "data.sqlite3"

void sql_open(char *dbName);
void sql_prep_table(char *dbName);
void sql_cmd(char *cmd, int (*callback)(void *, int, char **, char **));
void sql_process_data(time_t timestamp, double measurement);
int sql_count_temps(void *notUsed, int argc, char **argv, char **colName);
int sql_check_latest_avg(void *notUsed, int argc, char **argv, char **colName);
void sql_close();

#endif
