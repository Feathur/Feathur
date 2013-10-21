#!/bin/bash
# Logic to clear "cache"
if [ ! -f /var/feathur/data/bandwidth.txt ]
then
        pkill pmacctd
        pmacctd -c src_host,dst_host -f /usr/local/etc/pmacctd.conf
fi

# Reset file, since pmacctd does the counting
> /var/feathur/data/bandwidth.txt

# Start reading from pmacctd
for ip in $(vzlist -H -o ip | sed 's/\s/\n/g')
do
        bytesin=0; bytesout=0;
        while read src dest packets bytes
        do
                if [[ ${src} == ${ip} ]]
                then
                        bytesout=$((${bytesout} + ${bytes}))
                else
                        bytesin=$((${bytesin} + ${bytes}))
                fi
        done < <(pmacct -s | grep ${ip})
        echo "${ip} ${bytesin} ${bytesout}" >> /var/feathur/data/bandwidth.txt
done