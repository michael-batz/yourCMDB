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

/**
* Authentication provider for LDAP
* @author Michael Batz <michael@yourcmdb.org>
*/
class AuthenticationProviderLdap implements AuthenticationProvider
{

	//config: LDAP url
	private $configUrl;

	//config: Bind DN
	private $configBindDn;

	//config: Bind password
	private $configBindPw;

	//config: Base DN
	private $configBaseDn;

	//config: Seach filter
	private $configSearchFilter;

	//config: Seach filter for groups
	private $configSearchFilterGroup;

	//config: use LDAP version 3
	private $configUseLdapV3;

	//config: default access group
	private $configDefaultAccessgroup;

	//config: mapping group->accessgroup
	private $configGroupMap;

	function __construct($parameters)
	{
		//ldap base config
		$this->configUrl = $this->getParameterValue($parameters, "url", "ldap://localhost:389");
		$this->configUseLdapV3 = $this->getParameterValue($parameters, "useLdapV3", "true");
		$this->configBindDn = $this->getParameterValue($parameters, "bindDn", "cn=admin,dc=yourcmdb,dc=org");
		$this->configBindPw = $this->getParameterValue($parameters, "bindPw", "cmdb");
		$this->configBaseDn = $this->getParameterValue($parameters, "baseDn", "ou=users,dc=yourcmdb,dc=org");

		//searchfilter for users and groups: %username% will be replaced with the given username
		$this->configSearchFilter = $this->getParameterValue($parameters, "searchFilter", "(uid=%username%)");
		$this->configSearchFilterGroup = $this->getParameterValue($parameters, "searchFilterGroup", "(memberUid=%username%)");

		//mapping between groups and accessgroups
		$this->configDefaultAccessgroup = $this->getParameterValue($parameters, "defaultAccessgroup", "user");
		$this->configGroupMap = Array();
		foreach(array_keys($parameters) as $parameterKey)
		{
			if(preg_match("/groupmap_(.*)$/", $parameterKey, $matches) === 1)
			{
				$groupmapName = $matches[1];
				$groupmapAccessgroup = $parameters[$parameterKey];
				$this->configGroupMap[$groupmapName] = $groupmapAccessgroup;
			}
		}
	}

	public function authenticate($username, $password)
	{

		//open connection to LDAP server
		$ldapConnection = ldap_connect($this->configUrl);
		if($ldapConnection === FALSE)
		{
			error_log("AuthenticationProviderLdap: error connecting to LDAP server $this->url");
			return false;
		}

		//set ldap protocol version 3
		if($this->configUseLdapV3 == "true")
		{
			ldap_set_option($ldapConnection, LDAP_OPT_PROTOCOL_VERSION, 3);
		}

		//bind with readonly user
		$result = @ldap_bind($ldapConnection, $this->configBindDn, $this->configBindPw);
		if(!$result)
		{
			error_log("AuthenticationProviderLdap: bind error ($this->configUrl, $this->configBindDn)");
			@ldap_close($ldapConnection);
			return false;
		}

		//get dn for given username and try to bind with given password
		$searchFilter = preg_replace("/%username%/", $username, $this->configSearchFilter);
		$result = @ldap_search($ldapConnection, $this->configBaseDn, $searchFilter, array("dn"), 0, 1);
		if($result === FALSE)
		{
			error_log("AuthenticationProviderLdap: search error ($this->configUrl, $this->configBaseDn, $searchFilter)");
			@ldap_close($ldapConnection);
			return false;
		}
		$resultEntries = ldap_get_entries($ldapConnection, $result);
		for($i = 0; $i < $resultEntries['count']; $i++)
		{
			//try to bind with found DN and given password
			$entryDn = $resultEntries[$i]["dn"];
			$result = @ldap_bind($ldapConnection, $entryDn, $password);
			if($result)
			{
				@ldap_close($ldapConnection);
				return true;
			}
		}

		//close connection to LDAP server
		ldap_close($ldapConnection);
		return false;
	}

	public function getAccessGroup($username)
	{
		//setup default accessgroup
		$accessgroup = $this->configDefaultAccessgroup;

		//open connection to LDAP server
		$ldapConnection = ldap_connect($this->configUrl);
		if($ldapConnection === FALSE)
		{
			error_log("AuthenticationProviderLdap: error connecting to LDAP server $this->url");
			return $accessgroup;
		}

		//set ldap protocol version 3
		if($this->configUseLdapV3 == "true")
		{
			ldap_set_option($ldapConnection, LDAP_OPT_PROTOCOL_VERSION, 3);
		}

		//bind with readonly user
		$result = @ldap_bind($ldapConnection, $this->configBindDn, $this->configBindPw);
		if(!$result)
		{
			error_log("AuthenticationProviderLdap: bind error ($this->configUrl, $this->configBindDn)");
			@ldap_close($ldapConnection);
			return $accessgroup;
		}

		//get all groups for given username
		$groups = Array();
		$searchFilter = preg_replace("/%username%/", $username, $this->configSearchFilterGroup);
		$result = @ldap_search($ldapConnection, $this->configBaseDn, $searchFilter, array("dn"), 0, 0);
		if($result === FALSE)
		{
			error_log("AuthenticationProviderLdap: search error ($this->configUrl, $this->configBaseDn, $searchFilter)");
			@ldap_close($ldapConnection);
			return $accessgroup;
		}
		$resultEntries = ldap_get_entries($ldapConnection, $result);
		for($i = 0; $i < $resultEntries['count']; $i++)
		{
			if(preg_match("/.*?=(.*?),.*/", $resultEntries[$i]["dn"], $matches) === 1)
			{
				$groupname = $matches[1];
				$groups[] = $groupname;
			}
		}

		//get the best accessgroup for the groups
		foreach(array_keys($this->configGroupMap) as $groupmapping)
		{
			if(in_array($groupmapping, $groups))
			{
				$accessgroup = $this->configGroupMap[$groupmapping];
				break;
			}
		}

		//close connection to LDAP server
		ldap_close($ldapConnection);

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
