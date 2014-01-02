#!/bin/bash
#Thresholds are in bytes per second and threshold2 must > threshold1
LOGDIR="/var/feathur/data/ddos/"
THRESHOLD1=15000000
THRESHOLD2=30000000

if [ -f /var/feathur/data/abuse.lock ];
then
	exit 1;
fi

touch /var/feathur/data/abuse.lock
trap "rm -f -- '/var/feathur/data/abuse.lock'" EXIT

mkdir $LOGDIR;

while [ true ]
do

	killall -9 wcgrid_fahv_vin
	killall -9 wcgrid_faah_7.15_x86_64-pc-linux-gnu
	killall -9 wcgrid_mcm1_7.28_i686-pc-linux-gnu
	killall -9 wcgrid_mcm1_7.28_x86_64-pc-linux-gnu
	killall -9 wcgrid_fahv_vina_prod_linux_64.x86.7.06
	killall -9 litecoind
	killall -9 minerd
	killall -9 jhprotominer
	killall -9 quarkcoind
	killall -9 primeminer
	killall -9 boinc
	killall -9 havx256
	killall -9 jhprimeminer
	killall -9 cgminer
	killall -STOP wcgrid_fahv_vin
	killall -STOP wcgrid_faah_7.15_x86_64-pc-linux-gnu
	killall -STOP wcgrid_mcm1_7.28_i686-pc-linux-gnu
	killall -STOP wcgrid_mcm1_7.28_x86_64-pc-linux-gnu
	killall -STOP wcgrid_fahv_vina_prod_linux_64.x86.7.06
	killall -STOP litecoind
	killall -STOP minerd
	killall -STOP jhprotominer
	killall -STOP cgminer
	killall -STOP quarkcoind
	killall -STOP primeminer
	killall -STOP boinc
	killall -STOP havx256
	killall -STOP jhprimeminer
	
	for veid in $(/usr/sbin/vzlist -o veid -H)
	do
		# Create the log file if it doesn't already exist
		if ! test -e $LOGDIR/$veid.log; then
			touch $LOGDIR/$veid.log
		fi

		# Parse out the inbound/outbound traffic and assign them to the corresponding variables
		eval $(/usr/sbin/vzctl exec $veid "grep venet0 /proc/net/dev"  |  \
		awk -F: '{print $2}' | awk '{printf"CTOUT=%s\n", $9}')

		# Print the output and a timestamp to a log file
		echo $(date +%s) $CTOUT >> $LOGDIR/$veid.log

		# Read last 10 entries into arrays
		i=0
		while read time byte
		do
			times[i]=$time
			bytes[i]=$byte
			let ++i
		done < <(tail $LOGDIR/$veid.log)

		# Time checks & calculations for higher threshold
		counter=0
		for (( i=0; i<9; i++ ))
		do
			# If we have roughly the right timestamp
			if (( times[9-i] < times[8-i] + 20 ))
			then
				# If the user has gone over the threshold
				if (( bytes[9-i] > bytes[8-i] + THRESHOLD2 * 10 ))
					then let ++counter
				fi
			fi
		done

		# Now check counter
		if (( counter == 9 ))
			then
			vzctl set $veid --disabled yes --save;
			vzctl stop $veid;
			echo "$veid" >> /var/feathur/data/suspended.txt
		fi

		# Same for lower threshold
		counter=0
		for (( i=0; i<3; i++ ))
		do
				# If we have roughly the right timestamp
				if (( times[3-i] < times[2-i] + 20 ))
						then
						# If the user has gone over the threshold
						if (( bytes[3-i] > bytes[2-i] + THRESHOLD1 * 10 ))
								then let ++counter
						fi
				fi
		done

		# Now check counter
		if (( counter == 2 ))
			then
			vzctl set $veid --disabled yes --save;
			vzctl stop $veid;
			echo "$veid" >> /var/feathur/data/suspended.txt
		fi
	done
    sleep 10
done