Mosyle module
==============

Mosule integration for MunkiReport. 

The Mosyle Admin tab within the Admin dropdown menu allows an administrator to check if MunkiReport is able to access their Mosyle instance, as well as some details as to how it is configured. There is the option to manually pull data for all Macs within MunkiReport. 

The php-curl module is required for use with this module. You can install it on Ubuntu/Debian with `sudo apt-get install php-curl`

This module does not need access to the Mosyle API, but it benefits from it. There is a client portion of this module that can work and return data if the Mosyle API is not enabled. 

## Configuration

To enable the module add the following information to the `.env` file.

```sh
MOSYLE_ENABLE="TRUE"
MOSYLE_USERNAME="user@domain.com"
MOSYLE_PASSWORD=""
MOSYLE_API_KEY=""
MOSYLE_ADDRESS="https://businessapi.mosyle.com/"
```

The Mosyle API key requires only one permission: GET on Device.

This module was developed and tested with Mosyle Business. It will likely work with Mosyle School, but you will need to change the `MOSYLE_ADDRESS` config option for the Mosyle School URL. It is unknown if this module will work with multi-tenant Mosyle instances. 

Table Schema
---
* id - increments - Incremental value used by MunkiReport
* serial_number - string - Serial number of Mac
* device_name - integer - Name of Mac in Mosyle
* device_udid - string - UUID of Mac in Mosyle
* enrollment_type - string - Mosyle enrollment type
* active_managed_users - text - string of UUIDs of managed users on the Mac
* tags - text - Asset tags from Mosyle
* current_console_managed_user - string - Current user as per Mosyle
* enrolled_via_dep - boolean - If Mac was enrolled into Mosyle via DEP
* is_activation_lock_manageable - boolean - If Mosyle can manage Activation Lock on the Mac
* is_user_enrollment - boolean - If user enrollment into Mosyle
* user_approved_enrollment - boolean - If MDM is user approved
* is_deleted - boolean - If Mac is deleted from Mosyle
* is_muted - boolean - If Mac's alerts are muted in Mosyle
* is_supervised - boolean - If Mac is supervised in Mosyle
* asset_tag - text - Unused, no data returned from Mosyle for this
* last_app_info - bigInteger - Timestamp of last app info from Mosyle agent
* last_check_in - bigInteger - Timestamp of last last agent check in to Mosyle
* last_check_out - bigInteger - Timestamp of last last agent check out
* last_enroll - bigInteger - Timestamp of last enrolled into Mosyle
* last_info - bigInteger - Timestamp of last information update
* last_kinfo - bigInteger - Timestamp of last something, unknown
* last_beat - bigInteger - Timestamp of last agent heartbeat to Mosyle
* last_push - bigInteger - Timestamp of last push, unused
* last_login - bigInteger - Timestamp of last login
* last_media_info - bigInteger - Timestamp of last media info update
* last_muted - bigInteger - Timestamp of last device mute
* last_printers - bigInteger - Timestamp of last printers update
* last_profiles_info - bigInteger - Timestamp of last profiles update
* open_direct_device_link - text - URL to directly open the device in Mosyle
* lostmode_status - string - Status of device's lost mode
* status - string - Status of Mosyle agent install on device
* status_login - string - Status of user logged in
* assigned_user_email - string - Assigned user email address
* assigned_user_id - string - Assigned user ID
* assigned_username - string - Assigned username
* assigned_usertype - string - Assigned user type
* device_attestation_status - string - Status of device attestation
* device_info_attempt_date - bigInteger - Last Mosyle Agent device info update attempt
* device_info_success_date - bigInteger - Last Mosyle Agent device info update success
* last_device_token_date - bigInteger - Last Mosyle Agent device token update
* last_remote_notification_date - bigInteger - Last Mosyle Agent remote notification
* mac_commands_attempt_date - bigInteger - Last Mosyle Agent command attempt
* mac_commands_reply_ack_attempt_date - bigInteger - Last Mosyle Agent command reply attempt
* mac_commands_reply_ack_success_date - bigInteger - Last Mosyle Agent command reply success
* mac_commands_reply_results_attempt_date - bigInteger - Last Mosyle Agent command result attempt
* mac_commands_reply_results_success_date - bigInteger - Last Mosyle Agent command result success
* mac_commands_success_date - bigInteger - Last Mosyle Agent command success
* device_is_invalid - boolean - If Mosyle Agent is paired to Mosyle
* trigger_session_uuid - string - UUID of the Mac's login
* trigger_username - string - Username that triggers local events
* org_name - string - Organization name
* org_url - string - Organization's Mosyle Console URL
* mosyle_mdm_agent_app - string - Version of the Mosyle MDM Agent app
* mosyle_alert_app - string - Version of the Mosyle Alert app
* mosyle_app - string - Version of the Mosyle app
* mosyle_mdm_app - string - Version of the Mosyle MDM app
* mosyle_monitor_app - string - Version of the Mosyle Monitor app
* mosyle_notification_center_app - string - Version of the Notification Center app
* mosyle_security_app - string - Version of the Mosyle Security app
* mosyle_av_app - string - Version of the Mosyle AV app
* mosyle_selfservice_app - string - Version of the Self-Service app
* mosyle_timestamp - bigInteger - Timestamp of either last MunkiReport run on client or last API data pulled
