/*
 * File: cactus.h
 * Author: Samuel Lees (sslees)
 * Date: 11/02/16
 * Class: CPE 458-01
 * Assignment: Final Project
 */

#ifndef CACTUS_H
#define CACTUS_H

#include <time.h>

#include "util.h"

#define HOST "localhost"
#define PORT 5683
#define BUFF_LEN 255
#define PAYLOAD_LEN 16

void build_payload(u_char *payload, time_t timestamp, double measurement);
time_t parse_timestamp(u_char *packet);
double parse_measurement(u_char *packet);

#endif
