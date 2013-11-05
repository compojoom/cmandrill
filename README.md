CMandrill - Joomla extension that integrates with the Mandrill API
=======================================================

This Joomla extension integrates with the Mandrill API and routes all the e-mails* on your joomla
site thought the Mandrill service.

* Emails that are sent using the JFactory::getMailer(); function

## USAGE##
Zip package with the latest stable version of the extension is available on compojoom.com (https://compojoom.com/downloads/mandrill)

If you are developer and want to contribute to this extension you can fork this repo.

## Building the zip package from this repository
In order to build the installation packages of this library you need to have
the following tools:

- A command line environment. Bash under Linux / Mac OS X . On Windows
  you will need to run most tools using an elevated privileges (administrator)
  command prompt.
- The PHP CLI binary in your path

- Command line Subversion and Git binaries(*)

- PEAR and Phing installed, with the Net_FTP and VersionControl_SVN PEAR
  packages installed

You will also need the following path structure on your system

- com_cmandrill - This repository
- buildtools - Compojoom build tools (https://github.com/compojoom/buildtools)
- lib_cmandrill - PHP Mandrill library for Joomla (https://github.com/compojoom/lib_cmandrill)
- lib_compojoom - Compojoom library with common functions that are used between extension (https://github.com/compojoom/lib_compojoom)

## COPYRIGHT AND DISCLAIMER
CMandrill -  Copyright (c) 2008-2013 Compojoom.com

This program is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the
Free Software Foundation, either version 3 of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or
FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with this program. If not, see http://www.gnu.org/licenses/.
