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
   write(pipeFDs[1], /**data*/NULL, /*sizeof data*/0); // write data to parent pipe write ////
   close(pipeFDs[1]); // close parent pipe write
}
