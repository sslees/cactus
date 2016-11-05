/*
 * File: cactus_client.c
 * Author: Samuel Lees (sslees)
 * Date: 11/02/16
 * Class: CPE 458-01
 * Assignment: Final Project
 * Resources:
 *    http://www.binarytides.com/programming-udp-sockets-c-linux
 *       by Silver Moon (m00n.silv3r@gmail.com)
 */

#include <arpa/inet.h>
#include <netdb.h> // new
#include <stdlib.h>
#include <string.h>
#include <time.h>
#include <unistd.h>

#include <stdio.h>

#include "cactus.h"
#include "coap.h"

double measure() {
   return 3.14159L;
}

int main() {
   struct sockaddr_in server_addr;
   int socket_fd, server_len = sizeof server_addr;
   char response[BUFF_LEN];
   buffer_t data, request, ack;

   // new
   struct hostent *server = gethostbyname(HOST);

   socket_fd = socket(PF_INET, SOCK_DGRAM, IPPROTO_UDP);
   bzero((char *) &server_addr, sizeof server_addr);
   server_addr.sin_family = AF_INET;
   bcopy((char *) server->h_addr, (char *) &server_addr.sin_addr.s_addr,
    server->h_length);
   // inet_aton(HOST , &server_addr.sin_addr);
   server_addr.sin_port = htons(PORT);
   while (1) {
      u_char payload[PAYLOAD_LEN];
      time_t timestamp = time(NULL);
      double measurement = measure();

      build_payload(payload, timestamp, measurement);
      data = (buffer_t) {.data = payload, .len = PAYLOAD_LEN};
      request = build_packet(MC_POST, "/data", data);
      sendto(socket_fd, request.data, request.len, 0,
       (struct sockaddr *) &server_addr, server_len);
      printf("sending...\n");
      free(request.data);
      bzero(response, BUFF_LEN);
      recvfrom(socket_fd, response, BUFF_LEN, 0, (struct sockaddr *)
       &server_addr, (socklen_t *) &server_len);
      if (parse_type((u_char *) response) != T_ACK) break;
      recvfrom(socket_fd, response, BUFF_LEN, 0, (struct sockaddr *)
       &server_addr, (socklen_t *) &server_len);
      ack = build_ack(parse_message_id((u_char *) response));
      sendto(socket_fd, ack.data, ack.len, 0,
       (struct sockaddr *) &server_addr, server_len);
      free(ack.data);
      if (parse_code((u_char *) response) != RC_VALID) break;
      sleep(5);
   }
   close(socket_fd);

   return 0;
}
