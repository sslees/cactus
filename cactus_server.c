/*
 * File: cactus_server.c
 * Author: Samuel Lees (sslees)
 * Date: 11/02/16
 * Class: CPE 458-01
 * Assignment: Final Project
 * References:
 *    http://www.binarytides.com/programming-udp-sockets-c-linux
 *       by Silver Moon (m00n.silv3r@gmail.com)
 */

#include <netdb.h>
#include <signal.h>
#include <stdio.h>
#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <unistd.h>

#include "cactus.h"
#include "cactus_sql.h"
#include "coap.h"

static int socket_fd;

void interrupt(int signal) {
   printf("\n");
   sql_cmd("SELECT * FROM measurements;", sql_print);
   close(socket_fd);
   sql_close();

   exit(0);
}

int main() {
   struct sockaddr_in server_addr, client_addr;
   int client_len = sizeof client_addr;
   char ok = 1, packets = 1, ackCt = 1;

   // prepare database
   sql_open(DATABASE);
   sql_prep_table(DATABASE);

   // set up interrupt handler
   signal(SIGINT, interrupt);

   // create UDP socket
   socket_fd = socket(AF_INET, SOCK_DGRAM, IPPROTO_UDP);
   memset((char *) &server_addr, 0, sizeof server_addr);
   server_addr.sin_family = AF_INET;
   server_addr.sin_addr.s_addr = htonl(INADDR_ANY);
   server_addr.sin_port = htons(PORT);
   bind(socket_fd, (struct sockaddr *) &server_addr, sizeof server_addr);

   // continuously recieve and process data
   while (ok) {
      buffer_t empty = {.data = (u_char *) "", .len = 0}, response;

      // recieve and process ACK and data (1 loop each)
      while (packets-- && ok) {
         char request[BUFF_LEN];

         // recieve packet
         memset(request, 0, BUFF_LEN);
         recvfrom(socket_fd, request, BUFF_LEN, 0, (struct sockaddr *)
          &client_addr, (socklen_t *) &client_len);

         // process packet
         switch (parse_type((u_char *) request)) {
         case T_ACK:
            // process ACK
            if (!packets && ackCt) {
               ok = 0;
               printf("Recieved unexpected ACK. Terminating.\n");
            } else {
               printf("Recieved ACK.\n");
               ackCt++;
            }
         break;
         case T_CON:
            // process request
            if (!packets && !ackCt) {
               ok = 0;
               printf("Expected ACK. Terminating.\n");
            } else {
               int code = parse_code((u_char *) request);

               if (code == MC_POST) {
                  buffer_t ack = build_ack(parse_message_id((u_char *)
                   request));

                  if (!strcmp(parse_path((u_char *) request), "/data")) {
                     time_t timestamp =
                      parse_timestamp(parse_payload((u_char *) request));
                     double measurement =
                      parse_measurement(parse_payload((u_char *) request));

                     sql_store_data(timestamp, measurement);
                     printf("timestamp: %ld, measurement: %lf\n", timestamp,
                      measurement);

                     response = build_packet(RC_VALID, "/data", empty);
                  } else {
                     response = build_packet(RC_NOT_FOUND, "", empty);
                     printf("Recieved unexpected URI-path.\n");
                  }

                  // send ACK
                  sendto(socket_fd, ack.data, ack.len, 0, (struct sockaddr*)
                   &client_addr, client_len);
                  free(ack.data);
                  printf("Sent ACK.\n");
               } else {
                  response = build_packet(RC_METHOD_NOT_ALLOWED, "", empty);
                  printf("Recieved unexpected method code.\n");
               }
            }
         break;
         default:
            ok = 0;
            printf("Recieved unexpected packet type. Terminating.\n");
         }
      }

      if (ok) {
         // send response
         sendto(socket_fd, response.data, response.len, 0, (struct sockaddr*)
          &client_addr, client_len);
         free(response.data);
         printf("Sent response.\n");

         // set up next cycle
         packets = 2;
         ackCt = 0;
      }
   }

   close(socket_fd);
   sql_close();

   return 1;
}
