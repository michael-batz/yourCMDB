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
* WebUI element: search results
* @author Michael Batz <michael@yourcmdb.org>
*/


	//var $objects must be set

	//calculate list view
        $objectCount = count($objects);
        $listPage = $paramPage;
        $listPages = floor((($objectCount - 1) / $paramMax) + 1);
        if($listPages < 1)
        {
                $listPages = 1;
        }
        //check, if $listPage makes sense
        if($listPage > $listPages)
        {
                $listPage = $listPages;
        }
        if($listPage < 1)
        {
                $listPage = 1;
        }
        //calculate start and end
        $listStart = ($listPage - 1) * $paramMax;
        $listEnd = $listStart + $paramMax -1;
        if($listEnd >= $objectCount)
        {
                $listEnd = $objectCount - 1;
        }


	//urls
	$urlSearchForm = "search.php";
	$urlShowActiveBase = "search.php?searchstring=$paramSearchString&amp;typegroup=$paramTypeGroup&amp;type=$paramType&amp;max=$paramMax&amp;activeonly=";
	$listnavUrlBase = "search.php?searchstring=$paramSearchString&amp;typegroup=$paramTypeGroup&amp;type=$paramType&amp;max=$paramMax&amp;activeonly=$paramActiveOnly&amp;page=";

	//generate link for show active/inactive objects
	if($paramActiveOnly)
	{
		$textShowActive = "Show also inactive objects";
		$urlShowActive = $urlShowActiveBase."0";                
	}
	else    
	{
		$textShowActive = "Show only active objects";
		$urlShowActive = $urlShowActiveBase."1";
        }
?>


	<!-- submenu -->
	<div class="submenu">
		<a href="<?php echo $urlShowActive; ?>"><?php echo $textShowActive; ?></a>&nbsp;&nbsp;|&nbsp;&nbsp;
		<a href="<?php echo $urlSearchForm; ?>">Search Form</a>
	</div>


	<!-- title -->
	<h1>Search Results (<?php echo $objectCount; ?>)</h1>
	

	<table class="list">
<?php
	//print found objects
	if($objects != null)
	{
		//print object summary
		for($i = $listStart; $i <= $listEnd; $i++)
		{
			//get all data
			$objectType = $objects[$i]->getType();
			$objectId = $objects[$i]->getId();
			$objectStatus = $objects[$i]->getStatus();
			$objectFields = $objects[$i]->getFieldNames();
			$objectSummaryFields = $config->getObjectTypeConfig()->getSummaryFields($objectType);
			//get fields that matched to search string
			$objectMatchFields = Array();
			foreach($objectFields as $fieldname)
			{
				if(stristr($objects[$i]->getFieldValue($fieldname), $paramSearchString) !== FALSE)
				{
					$objectMatchFields[] = $fieldname;
				}
			}
		
			//get status image
			$statusIcon = "<img src=\"img/icon_active.png\" alt=\"active\" title=\"active object\" />";
			if($objectStatus != 'A')
			{
				$statusIcon = "<img src=\"img/icon_inactive.png\" alt=\"inactive\" title=\"inactive object\" />";
			}

			//print headline
			echo "<tr><td>";
			echo "<p><a href=\"object.php?action=show&amp;id=$objectId\">";
			echo "$statusIcon $objectType: $objectId</a><br />";

			//print matches
			echo "Matches: ";
			for($j = 0; $j < count($objectMatchFields); $j++)
			{
				$fieldname = $objectMatchFields[$j];
				$fieldlabel = $config->getObjectTypeConfig()->getFieldLabel($objectType, $fieldname);
				$fieldvalue = $objects[$i]->getFieldValue($fieldname);
				//mark search string in fieldvalues (use case insensitive match)
				if(preg_match("/.*?((?i:$paramSearchString)).*?/", $fieldvalue, $matchSearchString) == 1)
				{
					$fieldvalue = str_replace($matchSearchString[1], "<em>$matchSearchString[1]</em>", $fieldvalue);
				}
				echo "$fieldlabel: $fieldvalue";
				if($j < count($objectMatchFields) - 1)
				{
					echo " | ";
				}
			}
			echo "<br />";

			//print object summary
			echo "Summary: ";
			$fieldnames = array_keys($objectSummaryFields);
			for($j = 0; $j < count($fieldnames); $j++)
			{
				$fieldname = $fieldnames[$j];
				$fieldlabel = $config->getObjectTypeConfig()->getFieldLabel($objectType, $fieldname);
				$fieldvalue = $objects[$i]->getFieldValue($fieldname);
				echo "$fieldlabel: $fieldvalue";
				if($j < count($fieldnames) - 1)
				{
					echo " | ";
				}
			}
			echo "</p></td></tr>";
		}?>
		</table>

		<!-- list navigation  -->
		<p class="listnav">
                <?php
                        //print prev button
                        if($listPage != 1)
                        {
                                $listnavUrl = $listnavUrlBase .($listPage - 1);
                                echo "<a href=\"$listnavUrl\">&lt; previous</a>";
                        }
                        else
                        {
                                echo "<a href=\"#\" class=\"disabled\">&lt; previous</a>";
                        }
                        //print page numbers
                        for($i = 1; $i <= $listPages; $i++)
                        {
                                $listnavUrl = $listnavUrlBase .$i;
                                if($i == $listPage)
                                {
                                        echo "<a href=\"$listnavUrl\" class=\"active\">$i</a>";
                                }
                                else
                                {
                                        echo "<a href=\"$listnavUrl\">$i</a>";
                                }

                                //jump to current page
                                if($i == 3 && $listPage > 5)
                                {
                                        $i = $listPage - 2;
                                        echo "...";
                                }
                                //jump to last page
                                if($i > 3 && $i > $listPage && $i < ($listPages - 2))
                                {
                                        $i = $listPages - 2;
                                        echo "...";
                                }
                        }
			//print next button
                        if($listPage != $listPages)
                        {
                                $listnavUrl = $listnavUrlBase .($listPage + 1);
                                echo "<a href=\"$listnavUrl\">next &gt;</a>";
                        }
                        else
                        {
                                echo "<a href=\"#\" class=\"disabled\">next &gt;</a>";
                        }

		?>
		</p>

	<?php
	}
	else
	{
		echo "<p>No objects found</p>";
	}



?>
