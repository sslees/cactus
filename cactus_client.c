/*
 * File: cactus_client.c
 * Author: Samuel Lees (sslees)
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
#include <unistd.h>

#include "cactus.h"
#include "coap.h"

double measure() {
   return 3.14159L;
}

int main() {
   struct sockaddr_in server_addr;
   int socket_fd, server_len = sizeof server_addr;
   struct hostent *server = gethostbyname(HOSTNAME);
   char ok = 1, packets = 2, ackCt = 0, response[BUFF_LEN], code;
   time_t timestamp = time(NULL);
   double measurement = measure();
   u_char payload[PAYLOAD_LEN];
   buffer_t data, request, ack;
   u_short messageID;

   // create UDP socket
   socket_fd = socket(PF_INET, SOCK_DGRAM, IPPROTO_UDP);
   memset((char *) &server_addr, 0, sizeof server_addr);
   server_addr.sin_family = AF_INET;
   memcpy((char *) &server_addr.sin_addr.s_addr, (char *) server->h_addr,
    server->h_length);
   server_addr.sin_port = htons(PORT);

   // periodically POST data
   while (ok) {
      // build packet
      timestamp = time(NULL);
      measurement = measure();
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
         // recieve packet
         memset(response, 0, BUFF_LEN);
         recvfrom(socket_fd, response, BUFF_LEN, 0, (struct sockaddr *)
          &server_addr, (socklen_t *) &server_len);

         // process packet
         switch (parse_type((u_char *) response)) {
         case T_ACK:
            // process ACK
            if (!packets && ackCt) {
               printf("Recieved unexpected ACK. Terminating.\n");
               ok = 0;
            } else {
               printf("Recieved ACK.\n");
               ackCt++;
            }
         break;
         case T_CON:
            // process response
            if (!packets && !ackCt) {
               printf("Expected ACK. Terminating.\n");
               ok = 0;
            } else {
               code = parse_code((u_char *) response);
               if (code == RC_VALID) {
                  printf("Recieved response (0x%X, %s).\n", code,
                   response_decrip(code));
                  messageID = parse_message_id((u_char *) response);

                  // send ACK
                  ack = build_ack(messageID);
                  sendto(socket_fd, ack.data, ack.len, 0, (struct sockaddr *)
                   &server_addr, server_len);
                  free(ack.data);
                  printf("Sent ACK.\n");
               } else {
                  printf("Recieved unexpected response code. Terminating.\n");
                  ok = 0;
               }
            }
         break;
         default:
            printf("Recieved unexpected packet type. Terminating.\n");
            ok = 0;
         }
      }

      if (ok) sleep(5);
   }

   close(socket_fd);

   return 1;
}
