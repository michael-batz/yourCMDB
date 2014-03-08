<?php
/********************************************************************
* This file is part of yourCMDB.
*
* Copyright 2013-2014 Michael Batz
*
*
* yourCMDB is free software: you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation, either version 3 of the License, or
* (at your option) any later version.
*
* yourCMDB is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
* GNU General Public License for more details.
*
* You should have received a copy of the GNU General Public License
* along with yourCMDB.  If not, see <http://www.gnu.org/licenses/>.
*
*********************************************************************/

/**
* WebUI element: show menu with object types
* @author Michael Batz <michael@yourcmdb.org>
*/


	//get object types from configuration
	$objectGroups = $config->getObjectTypeConfig()->getObjectTypeGroups();
?>
	<div class="menu">
		<?php include "quicksearch.inc.php"; ?>

		<div class="box">
			<h1>Objects</h1>
			<ul id="jsMenu">

				<?php
				//walk through all object type groups
		                foreach(array_keys($objectGroups) as $groupname)
       		 	        {
				?>
					<li><a href="#"><?php echo $groupname;?></a><ul>
				<?php
					foreach($objectGroups[$groupname] as $objectType)
					{
				?>
						<li>
							<a  href="object.php?action=list&amp;type=<?php echo $objectType ?>">
								<?php echo $objectType; ?> (<?php echo $datastore->getObjectCounts($objectType);?>)
							</a>
						</li>
       		         	<?php 
					}?>
					</ul></li>
				<?php
				}?>
			</ul>
		</div>
	</div>
