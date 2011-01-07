<?php
	function syncuser($phorum_path, &$user_data = array()) {
		global $modx, $PHORUM;
		// get common functions
		include_once($phorum_path.'/common.php');
		// get the api code for various user-related functions
		include_once($phorum_path.'/include/api/user.php');
		include_once($phorum_path.'/include/api/custom_profile_fields.php');
		// use the external username to get a Phorum user_id
		// First try find by custom field
		$userid_customfield = phorum_api_custom_profile_field_byname('phorum_mod_external_authentication_modx_modxuserid');
		$user_id = phorum_api_user_search_custom_profile_field($userid_customfield['id'],$user_data['id']);
		if (!$user_id) {
			//Custom field not found
			//Try search by username
			$user_id = phorum_api_user_search("username",$user_data['username']);
			if ($user_id) {
				//User found, add custom profile field
				$phorum_user_data = array(
					"user_id" => $user_id,
					"phorum_mod_external_authentication_modx_modxuserid" => $user_data['id']
					);
				phorum_api_user_save($phorum_user_data);	
			}
		}
		// then get the Phorum user data from that user_id
		$phorum_user_data = phorum_api_user_get($user_id);

		if (!empty($PHORUM["phorum_mod_external_authentication"]["transfer_admin_status"]) &&
			$PHORUM["phorum_mod_external_authentication"]["transfer_admin_status"] &&
			strlen($PHORUM["phorum_mod_external_authentication"]["MODx_Admin_Group"])>=0 &&
			IsAdmin($user_data['id'],$PHORUM["phorum_mod_external_authentication"]["MODx_Admin_Group"])) {
				  $user_data["admin"] = 1;   
		}
		// if the Phorum user does not exist then we need to create them
		if (empty($phorum_user_data)) {
			$phorum_user_data = array(
				// The user_id must be NULL to create a new user
				"user_id" => NULL,
				"username" => $user_data["username"],
				"real_name" => $user_data["fullname"],
				// by transferring the password, we are ensuring that the user will be
				// able to login if the admin enables Phorum login
				"password" => $user_data["password"],
				// Phorum requires an email.  If the external application does not, 
				// a fake email should be used.
				"email" => $user_data["email"],
				// By default, create a non-admin user.  Admin status is handled later.
				"admin" => 0,
				"active" => PHORUM_USER_ACTIVE,
				"phorum_mod_external_authentication_modx_modxuserid" => $user_data['id']
				);
		   
			// if the admin wants to automatically transfer admin status
			if (!empty($PHORUM["phorum_mod_external_authentication"]["transfer_admin_status"])) {
				$phorum_user_data["admin"] = $user_data["admin"];
			}
			// create the new user and get the user_id with which to create a session.
			// Please note, most applications will give you the md5 of the user's 
			// password.  The constant PHORUM_FLAG_RAW_PASSWORD tells Phorum that the 
			// password is already in md5.  If you need to create a user with a plain
			// text password, simply omit the second variable in this call
			$user_id = phorum_api_user_save($phorum_user_data, PHORUM_FLAG_RAW_PASSWORD);
			
		// however, if the user exists but is not active, then we should not log them in    
		} elseif (empty($phorum_user_data["active"])) {
			return NULL;
		// or, if the user exists, then run some check on the user's data
		} else {
			// if the extenal application user's password or real name has changed, update the phorum 
			// user's password
			if ($phorum_user_data["password"] != $user_data["password"] || 
				$phorum_user_data["real_name"] != $user_data["fullname"]) {
				$phorum_user_data["password"] = $user_data["password"];
				$phorum_user_data["real_name"] = $user_data["fullname"];
				// save the updated user data, again with a preset md5 password
				$user_id = phorum_api_user_save($phorum_user_data,PHORUM_FLAG_RAW_PASSWORD);
			}
			
			// if the admin wants to automatically transfer admin status and the 
			// external user has been upgraded to admin, upgrade the phorum user, again 
			// assuming the external application establishes admin status this way
			if ($user_data["admin"] && empty($phorum_user_data["admin"]) && !empty($PHORUM["phorum_mod_external_authentication"]["transfer_admin_status"])) {
				$phorum_user_data["admin"] = 1;
				// save the updates user data
				$user_id = phorum_api_user_save($phorum_user_data);
			// if the admin wants to automatically transfer admin status and the 
			// external user has been downgraded from admin, downgrade the phorum user
			} elseif (!$user_data["admin"] && !empty($phorum_user_data["admin"]) && !empty($PHORUM["phorum_mod_external_authentication"]["transfer_admin_status"])) {
				$phorum_user_data["admin"] = 0;
				// save the updates user data
				$user_id = phorum_api_user_save($phorum_user_data);
			}
		}
		return $user_id;

	}
	function IsAdmin($userid, $GroupNamesString) {
		
		global $modx;
		$groupNames = explode(',',$GroupNamesString);
		if ($modx->isBackend()) {
			$tbl= $modx->getFullTableName("webgroup_names");
			$tbl2= $modx->getFullTableName("web_groups");
			$sql= "SELECT wgn.name
					FROM $tbl wgn
					INNER JOIN $tbl2 wg ON wg.webgroup=wgn.id AND wg.webuser='" . $userid . "'";
			$grpNames= $modx->db->getColumn("name", $sql);
			foreach ($groupNames as $k => $v)
				if (in_array(trim($v), $grpNames))
					return true;
			
		} else {
			return $modx->isMemberOfWebGroup($groupNames);
		}
		return false;
	}
?>
