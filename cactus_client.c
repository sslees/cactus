/*
 * File: cactus_client.c
 * Authora: Samuel Lees (sslees) and Matthew Lindly (mlindly)
 * Date: 11/02/16
 * Class: CPE 458-01
 * Assignment: Final Project
 * References:
 *    http://www.binarytides.com/programming-udp-sockets-c-linux
 *       by Silver Moon (m00n.silv3r@gmail.com)
 */

#include <netdb.h>
#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <sys/wait.h>
#include <unistd.h>

#include "cactus.h"
#include "coap.h"

#define ADC_LEN 5
#define ADC_MAX 1023

double measure() {
   int pipeFDs[2];
   char buffer[ADC_LEN];

   pipe(pipeFDs);
   if (!fork()) { // if child
      close(pipeFDs[0]); // close child pipe read
      dup2(pipeFDs[1], 1); // forward child stdout to child pipe write
      close(pipeFDs[1]); // close child pipe write
      execlp(PYTHON_EXE, PYTHON_EXE, MEASURE_SCRIPT, NULL); // run script
   }
   close(pipeFDs[1]); // close parent pipe write
   wait(NULL); // wait for child to terminate
   read(pipeFDs[0], buffer, ADC_LEN); // read measurement from parent pipe read
   close(pipeFDs[0]); // close parent pipe read
   buffer[ADC_LEN - 1] = '\0'; // replace newline with NULL terminator

   return (double) strtol(buffer, NULL, 10) / ADC_MAX; // return percentage
}

int main() {
   struct sockaddr_in server_addr;
   int socket_fd, server_len = sizeof server_addr;
   struct hostent *server = gethostbyname(HOSTNAME);
   char ok = 1;

   // create UDP socket
   socket_fd = socket(PF_INET, SOCK_DGRAM, IPPROTO_UDP);
   setsockopt(socket_fd, SOL_SOCKET, SO_RCVTIMEO, &((struct timeval)
    {.tv_sec = 1, .tv_usec = 500000}), sizeof (struct timeval));
   memset((char *) &server_addr, 0, sizeof server_addr);
   server_addr.sin_family = AF_INET;
   memcpy((char *) &server_addr.sin_addr.s_addr, (char *) server->h_addr,
    server->h_length);
   server_addr.sin_port = htons(PORT);

   // periodically POST data
   while (ok) {
      time_t timestamp = time(NULL);
      double measurement = measure();
      u_char payload[PAYLOAD_LEN];
      buffer_t data, request;
      char packets = 2, ackCt = 0;

      // build packet
      build_payload(payload, timestamp, measurement);
      data = (buffer_t) {.data = payload, .len = PAYLOAD_LEN};
      request = build_packet(MC_POST, "/data", data);

      // POST data
      sendto(socket_fd, request.data, request.len, 0,
       (struct sockaddr *) &server_addr, server_len);
      free(request.data);
      printf("POST coap://%s:%d/data (timestamp: %ld, measurement: %lf)\n",
       HOSTNAME, PORT, timestamp, measurement);

      // recieve and process ACK and response (1 loop each)
      while (packets-- && ok) {
         char response[BUFF_LEN];

         // recieve packet
         memset(response, 0, BUFF_LEN);
         if (recvfrom(socket_fd, response, BUFF_LEN, 0, (struct sockaddr *)
          &server_addr, (socklen_t *) &server_len) == -1) {
            ok = 0;
            printf("Request timed out. Terminating.\n");
            break;
         }

         // process packet
         switch (parse_type((u_char *) response)) {
         case T_ACK:
            // process ACK
            if (!packets && ackCt) {
               ok = 0;
               printf("Recieved unexpected ACK. Terminating.\n");
            } else {
               ackCt++;
               printf("Recieved ACK.\n");
            }
         break;
         case T_CON:
            // process response
            if (!packets && !ackCt) {
               ok = 0;
               printf("Expected ACK. Terminating.\n");
            } else {
               int code = parse_code((u_char *) response);
               buffer_t ack = build_ack(parse_message_id((u_char *) response));

               printf("Recieved response (0x%X: %s).\n", code,
                response_decrip(code));

               // send ACK
               sendto(socket_fd, ack.data, ack.len, 0, (struct sockaddr *)
                &server_addr, server_len);
               free(ack.data);
               printf("Sent ACK.\n");

               if (code != RC_VALID) {
                  ok = 0;
                  printf("Unexpected response code. Terminating.\n");
               }
            }
         break;
         default:
            ok = 0;
            printf("Recieved unexpected packet type. Terminating.\n");
         }
      }

      if (ok) sleep(3);
   }

   close(socket_fd);

   return 1;
}
