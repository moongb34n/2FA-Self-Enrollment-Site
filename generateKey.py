#!/usr/bin/python26
#
# Simple script for generating a Google Authenticator compatible keys
#
# Written by: jreyes@prysm.com
# v1.0 2017-04
#
# 4/6/2017 - latest version
# reference: https://github.com/pyotp/pyotp
#

import pyotp

key = pyotp.random_base32()

print key
