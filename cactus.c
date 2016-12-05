/*
 * File: cactus.c
 * Author: Samuel Lees (sslees)
 * Date: 11/05/16
 * Class: CPE 458-01
 * Assignment: Final Project
 */

#include <string.h>
#include <sys/wait.h>
#include <unistd.h>

#include "cactus.h"

void build_payload(u_char *payload, time_t timestamp, double measurement) {
   memcpy(payload, &timestamp, PAYLOAD_LEN / 2);
   memcpy(payload + PAYLOAD_LEN / 2, &measurement, PAYLOAD_LEN / 2);
}

time_t parse_timestamp(u_char *packet) {
   return *(time_t *) packet;
}

double parse_measurement(u_char *packet) {
   return *(double *) (packet + PAYLOAD_LEN / 2);
}

void notify_dry() {
   if (!fork()) execlp(PYTHON_EXE, PYTHON_EXE, NOTIFY_DRY_SCRIPT, NULL);
   wait(NULL);
}

void notify_watered() {
   if (!fork()) execlp(PYTHON_EXE, PYTHON_EXE, NOTIFY_WATERED_SCRIPT, NULL);
   wait(NULL);
}
