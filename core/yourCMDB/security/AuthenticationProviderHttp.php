<?php
/********************************************************************
* This file is part of yourCMDB.
*
* Copyright 2013-2016 Michael Batz
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
namespace yourCMDB\security;

use yourCMDB\controller\LocalUserController;
use yourCMDB\entities\CmdbLocalUser;
use \Exception;

/**
* Authentication provider against an HTTP server
* @author Michael Batz <michael@yourcmdb.org>
*/
class AuthenticationProviderHttp implements AuthenticationProvider
{

    //config: HTTP URL
    private $configUrl;

    //config: default accessgroup
    private $configDefaultAccessgroup;

    //config: mapping user -> accessgroup
    private $configAccessMap;

    //config: allowed users for authentication
    private $allowedUsers;

	function __construct($parameters)
	{
        // base config
        $this->configUrl = $this->getParameterValue($parameters, "url", "http://localhost");
        $this->configDefaultAccessgroup = $this->getParameterValue($parameters, "defaultAccessgroup", "user");

        // mapping users to accessgroups
        $this->accessMap = Array();
        foreach(array_keys($parameters) as $parameterKey)
        {
            if(preg_match("/accessuser_(.*)$/", $parameterKey, $matches) === 1)
            {
                $username = $matches[1];
                $accessgroup = $parameters[$parameterKey];
                $this->configAccessMap[$username] = $accessgroup;
            }
        }

        //if configured, get allowed users
        $this->allowedUsers = array();
        if(in_array("allowedUsers", array_keys($parameters)))
        {
            $this->allowedUsers = preg_split("#\s*,\s*#", $this->getParameterValue($parameters, "allowedUsers", ""));
        }
	}

	public function authenticate($username, $password)
    {
        //check if allowedUsers is set
        if(count($this->allowedUsers) > 0)
        {
            //check if user is in allowed users
            if(!in_array($username, $this->allowedUsers))
            {
                return false;
            }
        }

        // ask HTTP server
        $curl = curl_init();
        $curlOptions = array
        (
            CURLOPT_URL     => $this->configUrl,
            CURLOPT_USERPWD => "$username:$password",
            CURLOPT_RETURNTRANSFER  => true
        );
        curl_setopt_array($curl, $curlOptions);
        $result = curl_exec($curl);
        $httpStatus = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        // check result
        if($result !== false && $httpStatus == 200)
        {
            return true;
        }
		return false;
	}

	public function getAccessGroup($username)
    {
        //set default accessgroup from config
        $accessgroup = $this->configDefaultAccessgroup;

        if(array_key_exists($username, $this->configAccessMap))
        {
            $accessgroup = $this->configAccessMap[$username];
        }

        //return accessgroup
        return $accessgroup;
    }

	private function getParameterValue($parameterArray, $parameterKey, $defaultValue)
	{
		$output = $defaultValue;
		if(isset($parameterArray[$parameterKey]))
		{
			$output = $parameterArray[$parameterKey];
		}
		return $output;
	}

}
?>
