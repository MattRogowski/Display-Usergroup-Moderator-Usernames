<?php
/**
 * Display Usergroup Moderator Usernames 0.0.1

 * Copyright 2016 Matthew Rogowski

 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at

 ** http://www.apache.org/licenses/LICENSE-2.0

 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
**/

if(!defined("IN_MYBB"))
{
	die("Direct initialization of this file is not allowed.<br /><br />Please make sure IN_MYBB is defined.");
}

$plugins->add_hook('build_forumbits_forum', 'usergroupmodusernames_build');

function usergroupmodusernames_info()
{
	return array(
		"name" => "Display Usergroup Moderator Usernames",
		"description" => "Display the usernames of usergroup moderators instead of the usergroup name",
		"website" => "https://github.com/MattRogowski/Display-Usergroup-Moderator-Usernames",
		"author" => "Matt Rogowski",
		"authorsite" => "https://matt.rogow.ski",
		"version" => "0.0.1",
		"compatibility" => "18*",
		"codename" => "usergroupmodusernames"
	);
}

function usergroupmodusernames_activate()
{

}

function usergroupmodusernames_deactivate()
{

}

function usergroupmodusernames_build()
{
	global $db, $moderatorcache, $rebuilt_moderatorcache;

	if($rebuilt_moderatorcache)
	{
		return;
	}

	foreach($moderatorcache as $fid => $types)
	{
		if(!$types)
		{
			continue;
		}
		foreach($types['usergroups'] as $gid => $mod_info)
		{
			unset($mod_info['title']);

			$query = $db->simple_select('users', 'uid, username, usergroup, displaygroup', 'usergroup = '.intval($gid));
			while($user = $db->fetch_array($query))
			{
				if(!array_key_exists($user['uid'], $moderatorcache[$fid]['users']))
				{
					$new_mod_info = $mod_info;
					$new_mod_info['id'] = $user['uid'];
					$new_mod_info['isgroup'] = 0;
					$new_mod_info['username'] = $user['username'];
					$new_mod_info['usergroup'] = $user['usergroup'];
					$new_mod_info['displaygroup'] = $user['displaygroup'];

					$moderatorcache[$fid]['users'][$user['uid']] = $new_mod_info;
				}
			}

			$moderatorcache[$fid]['usergroups'] = array();
		}
	}

	$rebuilt_moderatorcache = true;
}