#!/bin/bash
watchdir=/etc/httpd/vhost.d
logfile=/var/log/inotify.log
while : ; do
    inotifywait $watchdir|while read path action file; do
	ts=$(date +"%C%y%m%d%H%M%S")
        echo "$ts :: file: $file :: $action :: $path">>$logfile
		sleep 10
		/usr/sbin/apachectl restart
		#echo '' > flag
		echo `date +"%F %T"` ' reloading' >> reload.log  
    done
done
exit 0