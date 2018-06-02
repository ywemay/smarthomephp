#!/bin/bash
#exec 3<>/dev/tcp/192.168.10.177/23
echo -n "OK!"
echo -e "#hlo" >&3
timeout 1 cat <&3
echo -e "axxxx" >&3
timeout 1 cat <&3
echo "";
