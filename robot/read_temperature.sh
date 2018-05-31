#!/bin/bash
cd /var/www/html/robot
. connect.sh
if [ ! -d "$LOG_DIR" ]
then
  echo "Making $LOG_DIR..."
  mkdir -p "$LOG_DIR"
fi
LOG_FILE="${LOG_DIR}/${LOG_BASE_NAME}_temp.log"

case $BOARD_ALIVE in
  0)
    TEMPERATURES=$(bash readers/temperature.sh)
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
echo $LOGSTR >> $LOG_FILE;
LAST_READING="${DATA_DIR}/temperature_last.dat"
echo "$LOG_DATE $LOGSTR" > $LAST_READING
php -f inc/temperature_math.php -- "$DATA_DIR"
