#!/bin/sh
result=`curl http://wfkj1.papapoi.com/check.php -s`
if [ "$result" == "ok" ];then
echo "ok"
else
#sudo reboot
sudo /etc/init.d/nginx restart
sudo killall natapp
screen -dm sudo /home/pi/natapp -authtoken=xxxxxxxxxxx &
fi

