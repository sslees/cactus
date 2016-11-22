/*
 * File: util.h
 * Author: Samuel Lees (sslees)
 * Date: 11/02/16
 * Class: CPE 458-01
 * Assignment: Final Project
 */

#ifndef UTIL_H
#define UTIL_H

#define PYTHON_EXE "python3"

typedef unsigned char u_char;
typedef unsigned short u_short;
typedef unsigned int u_int;

typedef struct buffer_t {
   u_char *data;
   u_int len;
} buffer_t;

#endif
