#!/bin/sh

#************************************************#
#                     gen.sh                     #
#                   24.03.2012                   #
#                                                #
# Generates links for galleria project.          #
#************************************************#

#------------------------------------#
# Define                             #
#------------------------------------#
m_DIR=$(/usr/bin/dirname $(/bin/readlink -f $0))

#------------------------------------#
# Binary file control                #
#------------------------------------#
BINS="/bin/sed /usr/bin/head /bin/grep /usr/bin/wc /usr/bin/basename /usr/bin/touch /usr/bin/tput /bin/rm /usr/bin/dirname /bin/readlink"
for m_sBin in $BINS
do
    if [ ! -e "$m_sBin" ]; then echo "$m_sBin is missing."; exit 1; fi
done

#------------------------------------#
# Color                              #
#------------------------------------#
COLORRED="$(/usr/bin/tput setaf 1)"
COLORGREEN="$(/usr/bin/tput setaf 2)"
COLORRESET="$(/usr/bin/tput sgr0)"

#------------------------------------#
# Files                              #
#------------------------------------#
m_DIRINOUT="/var/www/galleria/temp"
m_IN="${m_DIRINOUT}/in.txt"
m_OUT="${m_DIRINOUT}/out.txt"

#------------------------------------#
# Functions                          #
#------------------------------------#
DisplayE () {
    if [ -n "$1" ]; then
        echo "${COLORRED}$@${COLORRESET}"
    fi
    return 0
}
Display () {
    if [ -n "$1" ]; then
        echo "$@"
    fi
    return 0
}
Delete () {
    if [ -n "$1" ]; then
        if [ -f "$1" ]; then
            /bin/rm -f "$1"
        fi
        /usr/bin/touch "$1"
    fi
}

#------------------------------------#
# Process number                     #
#------------------------------------#
Display "The PID for `/usr/bin/basename $0` process is:${COLORGREEN}$$${COLORRESET}"

#------------------------------------#
# Delete old output file             #
#------------------------------------#
Delete "$m_OUT"

#------------------------------------#
# Input file should exist            #
#------------------------------------#
if [ ! -f "$m_IN" -o ! -r "$m_IN" ]; then
    DisplayE "Error: $m_IN is not a file or can not be read."
    exit 1
fi

#------------------------------------#
# Read input file                    #
#------------------------------------#
m_iIndex=1
while IFS=  read -r m_Ligne; do

    Display "Processing ${COLORGREEN}$m_Ligne${COLORRESET} ..."

    echo '<li><a class="thumb" href="data/gallerie/galleria-01/slides/'${m_Ligne}'" title="Title #'${m_iIndex}'"><img src="data/gallerie/galleria-01/thumbs/'${m_Ligne}'" alt="Title #'${m_iIndex}'" width="90" height="90" /></a><div class="caption"><div class="download"><a href="data/gallerie/galleria-01/hi-res/'${m_Ligne}'">Download Original</a></div><div class="image-title">Title #'${m_iIndex}'</div><div class="image-desc">Description</div></div></li>' >> "$m_OUT"

    m_iIndex=$(($m_iIndex+1))

done < "$m_IN"
