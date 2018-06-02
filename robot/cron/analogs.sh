#!/bin/bash

# Reads analog detectors values, Light, Moisture, Noisy ...
cd /var/www/html/robot
. connect.sh

case $BOARD_ALIVE in
  0)
    ANALOGS=$(bash read_analogs.sh)
    LOGSTR="$LOG_TIME: $ANALOGS"
    ;;
  1)
    LOGSTR="$LOG_TIME: Failed to connnect to the board..."
    ;;
  2)
    LOGSTR="$LOG_TIME: No network..."
    ;;
  *)
    LOGSTR="$LOG_TIME: Unknown network error..."
    ;;
esac
# echo $LOGSTR >> $LOG_FILE;
LAST_READING="${DATA_DIR}/analog_last.log"
echo "$LOG_DATE $LOGSTR" > $LAST_READING
if [[ "$1" == "test" ]] 
then
  cat "$LAST_READING"
else
  php -f parse_analogs.php
fi
