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
* Authentication provider against an OpenNMS server
* @author Michael Batz <michael@yourcmdb.org>
*/
class AuthenticationProviderOpennms implements AuthenticationProvider
{

    //config: OpenNMS URL
    private $configUrl;

    //config: default accessgroup
    private $configDefaultAccessgroup;

    //config: mapping user -> accessgroup
    private $configAccessMap;

	function __construct($parameters)
	{
        // OpenNMS base config
        $this->configUrl = $this->getParameterValue($parameters, "url", "http://localhost:8980/opennms");
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
	}

	public function authenticate($username, $password)
    {
        // ask OpenNMS REST API
        $curl = curl_init();
        $url = $this->configUrl . "/rest/info";
        $curlOptions = array
        (
            CURLOPT_URL     => $url,
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
