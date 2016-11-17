/*
 * File: cactus_sql.h
 * Author: Samuel Lees (sslees)
 * Date: 11/07/16
 * Class: CPE 458-01
 * Assignment: Final Project
 */

#ifndef CACTUS_SQL_H
#define CACTUS_SQL_H

#define DATABASE "test.db"

void sql_open(char *dbName);
void sql_cmd(char *cmd, int (*callback)(void *, int, char **, char **));
void sql_store_data(time_t timestamp, double measurement);
int sql_print(void *notUsed, int argc, char **argv, char **colName);
void sql_close();

#endif
