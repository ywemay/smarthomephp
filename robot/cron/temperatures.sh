#!/bin/bash
cd /var/www/html/robot
. connect.sh

case $BOARD_ALIVE in
  0)
    TEMPERATURES=$(bash read_temperatures.sh)
    LOGSTR="$LOG_TIME: $TEMPERATURES"
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
LAST_READING="${DATA_DIR}/temperature_last.dat"
echo "$LOG_DATE $LOGSTR" > $LAST_READING
php -f parse_temperatures.php
