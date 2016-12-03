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

#define HOSTNAME "cactus.sslees.com"
#define PORT 5683
#define MEASURE_SCRIPT "test_util/random_measure.py"
#define NOTIFY_SCRIPT "notify.py"
#define PAYLOAD_LEN 16
#define BUFF_LEN 255
#define S_BETWEEN_UPDATES 5
#define SAMPLE_SIZE 12
#define DRY_THRESHOLD 30.0

void build_payload(u_char *payload, time_t timestamp, double measurement);
time_t parse_timestamp(u_char *packet);
double parse_measurement(u_char *packet);
void notify();

#endif
