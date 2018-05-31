#!/bin/bash
exec 3<>/dev/tcp/192.168.10.177/23
echo -n "Temperature:"
echo -e "#hlo" >&3
timeout 1 cat <&3

for i in `seq 201 214`;
do
  echo -e "t${i}x" >&3
  timeout 1 cat <&3
done
# echo -e "t204x" >&3
#timeout 1 cat <&3
#echo -e "t205x" >&3
#timeout 1 cat <&3
echo -n "|"
echo -n "Security:"
echo -e "s205x" >&3
timeout 1 cat <&3
echo "";
