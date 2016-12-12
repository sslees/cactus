[//]: # (File: README.md)
[//]: # (Author: Samuel Lees (sslees))
[//]: # (Date: 11/05/16)
[//]: # (Class: CPE 458-01)
[//]: # (Assignment: Final Project)

# cactus
Internet of Things Final Project for CPE 458-01

## Dependencies
* Python 3.0
  * `apt-get intall python3`
* SPIdev
  * `pip3 install spidev`
* Apache
  * `apt-get install apache2`

## Installation
* Configure Apache with `cactus.sslees.com.conf`; modify as necessary.
* Compile with `make`.
* Run server with `./cactus_server`.
* Run client with `./cactus_client`.
