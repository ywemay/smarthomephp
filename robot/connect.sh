#!/bin/bash
BOARD_IP="192.168.10.177"
BOARD_PORT="23"
DATA_DIR="/var/www/html/data"
ping -c 1 $BOARD_IP
BOARD_ALIVE=$?

LOG_DATE=$(date +%Y-%m-%d)
LOG_TIME=$(date +%H:%M:%S)

LOG_DIR="$DATA_DIR/$(date +%Y/%m)"
LOG_BASE_NAME=$(date +%Y%m%d)

if [ 0 -eq "$BOARD_ALIVE" ]
then
  # Open a socket to the given IP:Port
  exec 3<>/dev/tcp/$BOARD_IP/$BOARD_PORT
fi

echo -e "Board alive: $BOARD_ALIVE; Date: $LOG_DATE; Time: $LOG_TIME; "
echo "Log dir: $LOG_DIR; Base name: $LOG_BASE_NAME;"

