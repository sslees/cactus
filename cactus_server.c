/*
 * File: cactus_server.c
 * Author: Samuel Lees (sslees)
 * Date: 11/02/16
 * Class: CPE 458-01
 * Assignment: Final Project
 * Resources:
 *    http://www.binarytides.com/programming-udp-sockets-c-linux
 *       by Silver Moon (m00n.silv3r@gmail.com)
 */

#include <arpa/inet.h>
#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <unistd.h>

#include "cactus.h"
#include "coap.h"

int main() {
   struct sockaddr_in server_addr, client_addr;
   int socket_fd, client_len = sizeof client_addr;
   char request[BUFF_LEN];
   buffer_t ack, data = {.data = (u_char *) "", .len = 0}, response;

   socket_fd = socket(AF_INET, SOCK_DGRAM, IPPROTO_UDP);
   bzero((char *) &server_addr, sizeof server_addr);
   server_addr.sin_family = AF_INET;
   server_addr.sin_addr.s_addr = htonl(INADDR_ANY);
   server_addr.sin_port = htons(PORT);
   bind(socket_fd, (struct sockaddr *) &server_addr, sizeof server_addr);
   while (1) {
      recvfrom(socket_fd, request, BUFF_LEN, 0, (struct sockaddr *) &client_addr,
       (socklen_t *) &client_len);
      if (parse_type((u_char *) request) != T_CON) break;
      if (parse_code((u_char *) request) == MC_POST) {
         if (!strcmp(parse_path((u_char *) request), "/data")) {
            response = build_packet(RC_VALID, "/data", data);
            printf("timestamp: %ld, measurement: %lf\n",
             parse_timestamp(parse_payload((u_char *) request)),
             parse_measurement(parse_payload((u_char *) request)));
         } else break;
      } else break;
      ack = build_ack(parse_message_id((u_char *) request));
      sendto(socket_fd, ack.data, ack.len, 0,
       (struct sockaddr*) &client_addr, client_len);
      free(ack.data);
      sendto(socket_fd, response.data, response.len, 0,
       (struct sockaddr*) &client_addr, client_len);
      free(response.data);
      bzero(request, BUFF_LEN);
      recvfrom(socket_fd, request, BUFF_LEN, 0, (struct sockaddr *) &client_addr,
       (socklen_t *) &client_len);
      if (parse_type((u_char *) request) != T_ACK) break;
   }
   close(socket_fd);

   return 0;
}
