# File: Makefile
# Author: Samuel Lees (sslees)
# Date: 11/02/16
# Class: CPE 458-01
# Assignment: Final Project

ALL = cactus_client cactus_server
GCC = gcc -Wall -Werror

all: $(ALL)

cactus_client: cactus_client.c coap.c cactus.c
	$(GCC) -o $@ $^

cactus_server: cactus_server.c coap.c cactus.c cactus_sql.c sqlite3.c
	$(GCC) -Wno-deprecated-declarations -DSQLITE_THREADSAFE=0 \
	 -DSQLITE_OMIT_LOAD_EXTENSION -o $@ $^

coap.c: coap.h cactus.h
	touch $@

cactus.c: cactus.h
	touch $@

cactus_sql.c: cactus_sql.h
	touch $@

coap.h: util.h
	touch $@

cactus.h: util.h
	touch $@

clean:
	rm -rf $(ALL) cactus_sql_test *.dSYM
