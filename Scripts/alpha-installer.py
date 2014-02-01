#!/bin/env python
# -*- coding: utf-8 -*-

##########################
# FEATHUR INSTALL INITIALIZER.
##########################

__author__ = "Daniel Isaksen"
__copyright__ = "Copyright (c) Feathur LLC, 2014"
__license__ = "GNU aGPL"

#####
# Python imports.
#####
import os, time

#####
# Fix for urlopen - 2 modules were merged in Python 3,
#####
try: from urllib2 import urlopen
except ImportError: from urllib.request import urlopen

#####
# Fix for input() - raw_input() was renamed to input() in Python 3.
#####
try: input = raw_input
except NameError: pass

#####
# Links to the GitHub repo for downloading OS/configuration specific installers.
#####
debian_7_configs = {
    0: {'name': 'Debian 7 Master', 'url': 'http://url.tld/dir/file.py'},
    1: {'name': 'Debian 7 Master with KVM and OpenVZ', 'url': 'http://url.tld/dir/file.py'},
    2: {'name': 'Debian 7 Master with KVM', 'url': 'http://url.tld/dir/file.py'},
    3: {'name': 'Debian 7 Master with OpenVZ', 'url': 'http://url.tld/dir/file.py'},
    4: {'name': 'Debian 7 KVM and OpenVZ (Slave Only)', 'url': 'http://url.tld/dir/file.py'},
    5: {'name': 'Debian 7 KVM (Slave Only)', 'url': 'http://url.tld/dir/file.py'},
    6: {'name': 'Debian 7 OpenVZ (Slave Only)', 'url': 'http://url.tld/dir/file.py'},
}

debian_6_configs = {
    1: {'name': 'Debian 6 Master', 'url': 'http://url.tld/dir/file.py'},
    2: {'name': 'Debian 6 Master with KVM and OpenVZ', 'url': 'http://url.tld/dir/file.py'},
    3: {'name': 'Debian 6 Master with KVM', 'url': 'http://url.tld/dir/file.py'},
    4: {'name': 'Debian 6 Master with OpenVZ', 'url': 'http://url.tld/dir/file.py'},
    5: {'name': 'Debian 6 KVM and OpenVZ (Slave Only)', 'url': 'http://url.tld/dir/file.py'},
    6: {'name': 'Debian 6 KVM (Slave Only)', 'url': 'http://url.tld/dir/file.py'},
    7: {'name': 'Debian 6 OpenVZ (Slave Only)', 'url': 'http://url.tld/dir/file.py'},
}

ubuntu_13_configs = {
    1: {'name': 'Ubuntu 13 Master', 'url': 'http://url.tld/dir/file.py'},
    2: {'name': 'Ubuntu 13 Master with KVM and OpenVZ', 'url': 'http://url.tld/dir/file.py'},
    3: {'name': 'Ubuntu 13 Master with KVM', 'url': 'http://url.tld/dir/file.py'},
    4: {'name': 'Ubuntu 13 Master with OpenVZ', 'url': 'http://url.tld/dir/file.py'},
    5: {'name': 'Ubuntu 13 KVM and OpenVZ (Slave Only)', 'url': 'http://url.tld/dir/file.py'},
    6: {'name': 'Ubuntu 13 KVM (Slave Only)', 'url': 'http://url.tld/dir/file.py'},
    7: {'name': 'Ubuntu 13 OpenVZ (Slave Only)', 'url': 'http://url.tld/dir/file.py'},
}

ubuntu_12_configs = {
    1: {'name': 'Ubuntu 12 Master', 'url': 'http://url.tld/dir/file.py'},
    2: {'name': 'Ubuntu 12 Master with KVM and OpenVZ', 'url': 'http://url.tld/dir/file.py'},
    3: {'name': 'Ubuntu 12 Master with KVM', 'url': 'http://url.tld/dir/file.py'},
    4: {'name': 'Ubuntu 12 Master with OpenVZ', 'url': 'http://url.tld/dir/file.py'},
    5: {'name': 'Ubuntu 12 KVM and OpenVZ (Slave Only)', 'url': 'http://url.tld/dir/file.py'},
    6: {'name': 'Ubuntu 12 KVM (Slave Only)', 'url': 'http://url.tld/dir/file.py'},
    7: {'name': 'Ubuntu 12 OpenVZ (Slave Only)', 'url': 'http://url.tld/dir/file.py'},
}

centos_5_configs = {
    1: {'name': 'CentOS 5 Master', 'url': 'http://url.tld/dir/file.py'},
    2: {'name': 'CentOS 5 Master with KVM and OpenVZ', 'url': 'http://url.tld/dir/file.py'},
    3: {'name': 'CentOS 5 Master with KVM', 'url': 'http://url.tld/dir/file.py'},
    4: {'name': 'CentOS 5 Master with OpenVZ', 'url': 'http://url.tld/dir/file.py'},
    5: {'name': 'CentOS 5 KVM and OpenVZ (Slave Only)', 'url': 'http://url.tld/dir/file.py'},
    6: {'name': 'CentOS 5 KVM (Slave Only)', 'url': 'http://url.tld/dir/file.py'},
    7: {'name': 'CentOS 5 OpenVZ (Slave Only)', 'url': 'http://url.tld/dir/file.py'},
}

centos_6_configs = {
    1: {'name': 'CentOS 6 Master', 'url': 'http://url.tld/dir/file.py'},
    2: {'name': 'CentOS 6 Master with KVM and OpenVZ', 'url': 'http://url.tld/dir/file.py'},
    3: {'name': 'CentOS 6 Master with KVM', 'url': 'http://url.tld/dir/file.py'},
    4: {'name': 'CentOS 6 Master with OpenVZ', 'url': 'http://url.tld/dir/file.py'},
    5: {'name': 'CentOS 6 KVM and OpenVZ (Slave Only)', 'url': 'http://url.tld/dir/file.py'},
    6: {'name': 'CentOS 6 KVM (Slave Only)', 'url': 'http://url.tld/dir/file.py'},
    7: {'name': 'CentOS 6 OpenVZ (Slave Only)', 'url': 'http://url.tld/dir/file.py'},
}

#####
# An exception alias to make the error look a little better + knowing where it happened.
#####
class FeathurError(Exception): pass

#####
# Installer class.
#####
class Installer(object):
    def __init__(self, override = None, options = {
                     'Debian 7':   debian_7_configs,
                     'Debian 6':   debian_6_configs,
                     'Ubuntu 13': ubuntu_13_configs,
                     'Ubuntu 12': ubuntu_12_configs,
                     'CentOS 6':   centos_6_configs,
                     'CentOS 5':   centos_5_configs}):
        self.override = override # Override what option to install - should be a URL.
        self.options = options
        self.run()
    
    def get_os(self):
        '''
        Check for files that might contain OS/version
        info and lazy-parse them.
        
        TODO:
            Clean up.
            Perhaps a regex or two?
        '''
        if os.path.isfile('/etc/os-release') and not os.path.isfile('/etc/debian_version'): # There's a version file in /etc. I won't dare doing a regex on this until a later update.
            try: # In case of an index/slice failure.
                ubuntu = open('/etc/os-release', 'r').read().split('\n')
                for line in ubuntu:
                    if "VERSION_ID" in line: ubuntu = line # Find the line we're looking for.
                if ubuntu.split('VERSION_ID="')[1].split('"')[0].split('.')[0] == "12": return ['Ubuntu', 12] # A match on 12.x.
                elif ubuntu.split('VERSION_ID="')[1].split('"')[0].split('.')[0] == "13": return ['Ubuntu', 13] # A match on 13.x. \o/
                else: return ['Ubuntu', 0] # Unsupported.
            except: return ['Ubuntu', None] # Slicing pls, y u so failure.
        
        elif os.path.isfile("/etc/debian_version"):
            debian = open("/etc/debian_version", "r").read().replace("\n", "").lower()
            try: # In case slicing fails.
                if debian.split("/")[0] == "wheezy" or debian.split(".")[0] == "6": return ['Debian', 6]
                elif debian.split("/")[0] == "squeeze" or debian.split(".")[0] == "7": return ['Debian', 7]
                else: return ['Debian', 0]
            except: return ['Debian', None]
        
        elif os.path.isfile("/etc/centos-release"):
            centos = open("/etc/centos-release", "r").read().replace("\n", "").lower()
            if "centos release" in centos: # To confirm it's CentOS
                try:
                    if centos.split("centos release ")[1].split(" ")[0].split(".")[0] == "5": return ['CentOS', 5]
                    elif centos.split("centos release ")[1].split(" ")[0].split(".")[0] == "6": return ['CentOS', 6]
                    else: return ['CentOS', 0]
                except: return ['CentOS', None]
            else: return ['CentOS', None]
        else: return [None, None]
    
    def run(self):
        '''
        Confirms with user / asks which config to use
        and downloads it from a predefined URL to a 
        variable. Basically starts the installer itself.
        
        TODO:
            Prettier printing.
            Better way to tell the installer what config to use.
        '''
        print("==============================")
        print("Welcome to Feathur Installation")
        print("==============================")
        print("It is recommended that you run this")
        print("installer in a screen.")
        print("")
        print("This script will begin installing")
        print("Feathur in 10 seconds. If you wish to")
        print("cancel the install press CTRL + C now.")
        
        try: time.sleep(1)
        except KeyboardInterrupt: # When CTRL + C is pressed.
            print("") # When we exit, the shell doesn't go on a newline for some reason - fix.
            exit(0) # Exit with exit code 0.
        
        if os.getuid() != 0: # Get the current process' UID.
            print("You must run this installer as root!") # Byebye.
            print("")
            exit(0)
        
        detected_os = self.get_os()
        _ask_os = False
        _d, _i = {}, 1
        for _key in sorted(self.options.keys()):
            _d[_i] = _key # Map to a dictionary to make printing easier.
            _i += 1
        
        print("")
        
        if detected_os[1] in [0, None] or detected_os == [None, None]: # If the version and/or the distro didn't get detected.
        
            if detected_os[1] in [0, None]: print("Feathur was unable to determine your OS version.")
            else: print("Feathur was unable to determine your OS distribution.")
            _choice = input("Would you like to manually select an OS? [y/n] ") # Ask for input.
            if _choice.lower() not in ['n', 'y']: # If choice is not 'y' or 'n'.
                while _choice.lower() not in ['n', 'y']: # Retry until we get a proper choice.
                    _choice = input("Please try again. [y/n] ")
            
            if _choice == 'n':
                print("Error: Unable to determine OS.")
                print("")
                exit(0)
            else: _ask_os = True
        
        else:
        
            print("Feathur has detected that your OS is \"%s %d.x\""%(detected_os[0].capitalize(), detected_os[1]))
            _choice = input("Is this correct? [y/n] ")
            if _choice.lower() not in ['n', 'y']:
                while _choice.lower() not in ['n', 'y']:
                    _choice = input("Please try again. [y/n] ")
            if _choice == 'n': _ask_os = True
            else:
                for k, v in _d.items(): # Iterate.
                    if v == "%s %d"%(detected_os[0], detected_os[1]): # Get key from value.
                        os = k
        
        print("")
        
        if _ask_os:
            
            print("Supported OSes:")
            for _i, os in _d.items():
                print("[%d]: %s.x"%(_i, os)) # Ex. "[2] CentOS 6.x
                time.sleep(0.2)
            
            print("")
            
            _selected_os = input("Which OS would you like to specify? [1-%d] "%(len(_d.keys())))
            
            if not _selected_os.isdigit(): # If input isn't a number.
                while not _selected_os.isdigit():
                    _selected_os = input("Please try again. [1-%d] "%(len(_d.keys())))
            if not int(_selected_os) in _d.keys():
                while not _selected_os in _d.keys():
                    _selected_os = input("Please try again. [1-%d] "%(len(_d.keys())))
            
            os = int(_selected_os)
        
        print("==============================")
            
        print("Available configurations:")
        
        for k, v in self.options[_d[os]].items(): # Prints all the options with a slight delay between each line.
            time.sleep(0.2)
            print("[%d]: %s"%(k, v['name']))
        
        _choice = input("Which configuration would you like to specify? [1-%d] "%(sorted(self.options[_d[os]].keys())[-1]))
        
        if not _choice.isdigit():
            while not _choice.isdigit():
                _choice = input("Please try again. [1-%d] "%(sorted(self.options[_d[os]].keys())[-1]))
        if int(_choice) not in sorted(self.options[_d[os]].keys()):
            while int(_choice) not in sorted(self.options[_d[os]].keys()):
                _choice = input("Please try again. [1-%d] "%(sorted(self.options[_d[os]].keys())[-1]))
        
        print("==============================")
        print("Downloading prerequisite files...")
        
        installer_script = urlopen(self.options[_d[os]][int(_choice)]['url']).read().decode("UTF-8") # Call the dictionary with possible configrations, downloading the selected one into a variable.
        
        print("Beginning installation...")
        print("==============================")
        
        exec(installer_script) # Magic happens here - not making exceptions to let the script itself raise and handle them as(if) needed.
        
        exit(0)

if __name__ == "__main__":
    installer = Installer()
    installer.run() 
