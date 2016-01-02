#! /bin/bash
######################################################################
# This file is part of yourCMDB.
#
# Copyright 2013-2016 Michael Batz
#
#
# yourCMDB is free software: you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation, either version 3 of the License, or
# (at your option) any later version.
#
# yourCMDB is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
#
# You should have received a copy of the GNU General Public License
# along with yourCMDB.  If not, see <http://www.gnu.org/licenses/>.
#
######################################################################
#
# yourCMDB exporter - wrapper script for ansible dynamic inventory
# @author Michael Batz <michael@yourcmdb.org>
#
######################################################################

EXPORTER_TASK=ansible-example
YOURCMDB_REST_URL=http://localhost/yourCMDB/web/rest.php/exporter/export
YOURCMDB_REST_USER=admin
YOURCMDB_REST_PASSWORD=yourcmdb

if [ "$1" == "--list" ] 
then
	curl -X PUT -u ${YOURCMDB_REST_USER}:${YOURCMDB_REST_PASSWORD} ${YOURCMDB_REST_URL}/${EXPORTER_TASK}
else
	echo "[]"
fi
