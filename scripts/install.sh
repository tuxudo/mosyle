#!/bin/bash

# Mosyle controller
CTL="${BASEURL}index.php?/module/mosyle/"

# Get the scripts in the proper directories
"${CURL[@]}" "${CTL}get_script/mosyle.py" -o "${MUNKIPATH}preflight.d/mosyle.py"

# Check exit status of curl
if [ $? = 0 ]; then
	# Make executable
	chmod a+x "${MUNKIPATH}preflight.d/mosyle.py"

	# Set preference to include this file in the preflight check
	setreportpref "mosyle" "${CACHEPATH}mosyle.plist"

else
	echo "Failed to download all required components!"
	rm -f "${MUNKIPATH}preflight.d/mosyle.py"

	# Signal that we had an error
	ERR=1
fi
