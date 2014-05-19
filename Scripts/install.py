#! /usr/bin/env python3

import os, sys, locale, platform
from dialog import Dialog

#code

def checkDistro():
    distro = platform.linux_distribution()
    d = Dialog(dialog="dialog")
    d.set_background_title("Test")
    package_dict = {"Ubuntu":"apt-get",
                    "Debian":"apt-get",
                    "CentOS":"yum",}
    os_name = distro[0]
    os_version = distro[1]
    os_codename = distro[2]
    d.msgbox("""\
Your exact OS:
Name: %s
Version: %s 
CodeName: %s""" % (os_name, os_version, os_codename), width=0, height=0, title="test")


# This is almost always a good thing to do at the beginning of your programs.
locale.setlocale(locale.LC_ALL, '')

# Initialize a dialog.Dialog instance
def first_window():
    d = Dialog(dialog="dialog")
    d.set_background_title("Feathur")

    d.msgbox("""\
This installer will guide you through the process of installing Feathur. If you do not wish to install Feathur at this time please press CTRL+C. Feathur is released under the AGPL 3.0 license.

This installer will start off by checking which version of Linux you are running then will ask you which type of Feathur you wish to install (master, slave openvz, slave kvm or a mixture of master and slave...).
            
To begin hit enter/next below.""",
            width=0, height=0, title="Feathur Installer", ok_label="Next")
def second_window(distro, version):
    d = Dialog(dialog="dialog")
    d.set_background_title("Feathur")
    d.yesno("""\
Your exact version is %s %s %s, using %s for your package manager.
        """, width=0, height=0, title="Feathur Installer", ok_label="Next")

if __name__ == "__main__":
    checkDistro()
    first_window()
    second_window()
    os.system('reset')
