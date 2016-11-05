/*
 * File: coap.h
 * Author: Samuel Lees (sslees)
 * Date: 10/24/16
 * Class: CPE 458-01
 * Assignment: Final Project
 */

#ifndef COAP_H
#define COAP_H

#include "util.h"

/* Version */
#define VER 1 // Version 1

/* Type */
#define T_CON 0 // Confirmable
#define T_NON 1 // Non-confirmable
#define T_ACK 2 // Acknowledgement
#define T_RST 3 // Reset

/* Token Length */
#define TKL 0

/* Method Codes */
#define MC_GET (0 << 5 | 01) // 0.01
#define MC_POST (0 << 5 | 02) // 0.02
#define MC_PUT (0 << 5 | 03) // 0.03
#define MC_DELETE (0 << 5 | 04) // 0.04

/* Response Codes */
#define RC_CREATED (2 << 5 | 01) // 2.01
#define RC_DELETED (2 << 5 | 02) // 2.02
#define RC_VALID (2 << 5 | 03) // 2.03
#define RC_CHANGED (2 << 5 | 04) // 2.04
#define RC_CONTENT (2 << 5 | 05) // 2.05

#define RC_BAD_REQUEST (4 << 5 | 00) // 4.00
#define RC_UNAUTHORIZED (4 << 5 | 01) // 4.01
#define RC_BAD_OPTION (4 << 5 | 02) // 4.02
#define RC_FORBIDDEN (4 << 5 | 03) // 4.03
#define RC_NOT_FOUND (4 << 5 | 04) // 4.04
#define RC_METHOD_NOT_ALLOWED (4 << 5 | 05) // 4.05
#define RC_NOT_ACCEPTABLE (4 << 5 | 06) // 4.06
#define RC_PRECONDITION_FAILED (4 << 5 | 12) // 4.12
#define RC_REQUEST_ENTITY_TOO_LARGE (4 << 5 | 13) // 4.13
#define RC_UNSUPPORTED_CONTENT_FORMAT (4 << 5 | 15) // 4.15

#define RC_INTERNAL_SERVER_ERROR (5 << 5 | 00) // 5.00
#define RC_NOT_IMPLEMENTED (5 << 5 | 01) // 5.01
#define RC_BAD_GATEWAY (5 << 5 | 02) // 5.02
#define RC_SERVICE_UNAVAILABLE (5 << 5 | 03) // 5.03
#define RC_GATEWAY_TIMEOUT (5 << 5 | 04) // 5.04
#define RC_PROXYING_NOT_SUPPORTED (5 << 5 | 05) // 5.05

/* Option Numbers */
#define ON_IF_MATCH 1 // If-Match
#define ON_URI_HOST 3 // Uri-Host
#define ON_ETAG 4 // ETag
#define ON_IF_NONE_MATCH 5 // If-None-Match
#define ON_URI_PORT 7 // Uri-Port
#define ON_LOCATION_PATH 8 // Location-Path
#define ON_URI_PATH 11 // Uri-Path
#define ON_CONTENT_FORMAT 12 // Content-Format
#define ON_MAX_AGE 14 // Max-Age
#define ON_URI_QUERY 15 // Uri-Query
#define ON_ACCEPT 17 // Accept
#define ON_LOCATION_QUERY 20 // Location-Query
#define ON_PROXY_URI 35 // Proxy-Uri
#define ON_PROXY_SCHEME 39 // Proxy-Scheme
#define ON_SIZE1 60 // Size1

/* Content Formats */
#define CF_TEXT_PLAIN_UTF_8 0 // text/plain; charset=utf-8
#define CF_APPLICATION_LINK_FORMAT 40 // application/link-format
#define CF_APPLICATION_XML 41 // application/xml
#define CF_APPLICATION_OCTET_STREAM 42 // application/octet-stream
#define CF_APPLICATION_EXI 47 // application/exi
#define CF_APPLICATION_JSON 50 // application/json

char *response_decrip(u_char code);
buffer_t build_packet(u_char code, char *path, buffer_t data);
buffer_t build_ack(u_short messageID);
u_char parse_type(u_char *packet);
u_char parse_code(u_char *packet);
u_short parse_message_id(u_char *packet);
char *parse_path(u_char *packet);
u_char *parse_payload(u_char *packet);

#endif
