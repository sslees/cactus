/*
 * File: coap.c
 * Author: Samuel Lees (sslees)
 * Date: 10/24/16
 * Class: CPE 458-01
 * Assignment: Final Project
 */

#include <stdlib.h>
#include <string.h>

#include "cactus.h"
#include "coap.h"

#define PACKET_BASE 13

char *response_decrip(u_char code) {
   switch(code) {
   case RC_CREATED: return "Created";
   case RC_DELETED: return "Deleted";
   case RC_VALID: return "Valid";
   case RC_CHANGED: return "Changed";
   case RC_CONTENT: return "Content";
   case RC_BAD_REQUEST: return "Bad Request";
   case RC_UNAUTHORIZED: return "Unauthorized";
   case RC_BAD_OPTION: return "Bad Option";
   case RC_FORBIDDEN: return "Forbidden";
   case RC_NOT_FOUND: return "Not Found";
   case RC_METHOD_NOT_ALLOWED: return "Method Not Allowed";
   case RC_NOT_ACCEPTABLE: return "Not Acceptable";
   case RC_PRECONDITION_FAILED: return "Precondition Failed";
   case RC_REQUEST_ENTITY_TOO_LARGE: return "Request Entity Too Large";
   case RC_UNSUPPORTED_CONTENT_FORMAT: return "Unsupported Content-Format";
   case RC_INTERNAL_SERVER_ERROR: return "Internal Server Error";
   case RC_NOT_IMPLEMENTED: return "Not Implemented";
   case RC_BAD_GATEWAY: return "Bad Gateway";
   case RC_SERVICE_UNAVAILABLE: return "Service Unavailable";
   case RC_GATEWAY_TIMEOUT: return "Gateway Timeout";
   case RC_PROXYING_NOT_SUPPORTED: return "Proxying Not Supported";
   default: return "";
   }
}

buffer_t build_packet(u_char code, char *path, buffer_t data) {
   static u_short messageID = 0;

   u_char *packet = calloc(1, PACKET_BASE + strlen(HOST) + strlen(path) +
    data.len + 2);
   int pos = 0;

   packet[pos++] = VER << 6 | T_CON << 4 | TKL;
   packet[pos++] = code;
   /* Message ID */
   packet[pos++] = messageID >> 8 & 0xFF;
   packet[pos++] = messageID++ & 0xFF;
   /* Token */
   // no Token (TKL is 0)
   /* Uri-Host Option */
   packet[pos++] = ON_URI_HOST << 4 | (strlen(HOST) + 1);
   strcpy((char *) packet + pos, HOST);
   pos += strlen(HOST) + 1;
   /* Uri-Port Option */
   packet[pos++] = (ON_URI_PORT - ON_URI_HOST) << 4 | 2;
   packet[pos++] = PORT >> 8 & 0xFF;
   packet[pos++] = PORT & 0xFF;
   /* Uri-Path Option */
   packet[pos++] = (ON_URI_PATH - ON_URI_PORT) << 4 | (strlen(path) + 1);
   strcpy((char *) packet + pos, path);
   pos += strlen(path) + 1;
   /* Content-Format Option */
   packet[pos++] = (ON_CONTENT_FORMAT - ON_URI_PATH) << 4 | 2;
   packet[pos++] = CF_TEXT_PLAIN_UTF_8 >> 8 & 0xFF;
   packet[pos++] = CF_TEXT_PLAIN_UTF_8 & 0xFF;
   /* Payload Marker */
   packet[pos++] = 0xFF;
   /* Payload */
   memcpy(packet + pos, data.data, data.len);
   pos += data.len;

   return (buffer_t) {.data = packet, .len = pos};
}

buffer_t build_ack(u_short messageID) {
   u_char *packet = calloc(1, 5);

   packet[0] = VER << 6 | T_ACK << 4 | TKL;
   packet[2] = messageID >> 8 & 0xFF;
   packet[3] = messageID & 0xFF;
   packet[4] = 0xFF;

   return (buffer_t) {.data = packet, .len = 5};
}

u_char parse_type(u_char *packet) {
   return packet[0] >> 4 & 0x2;
}

u_char parse_code(u_char *packet) {
   return packet[1];
}

u_short parse_message_id(u_char *packet) {
   return (u_short) packet[2] << 8 | packet[3];
}

char *parse_path(u_char *packet) {
   return (char *) packet + 10 + strlen(HOST);
}

u_char *parse_payload(u_char *packet) {
   return packet + PACKET_BASE + strlen(HOST) + strlen(parse_path(packet)) + 2;
}
