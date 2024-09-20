<?php
use CFPropertyList\CFPropertyList;

class Mosyle_model extends \Model {

    protected $error = '';
    protected $module_dir;

    public function __construct($serial = '')
    {
        parent::__construct('id', 'mosyle'); // Primary key, tablename
        $this->rs['id'] = '';
        $this->rs['serial_number'] = $serial;
        $this->rs['device_name'] = '';
        $this->rs['device_udid'] = '';
        $this->rs['enrollment_type'] = '';
        $this->rs['active_managed_users'] = '';
        $this->rs['tags'] = '';
        $this->rs['current_console_managed_user'] = '';
        $this->rs['enrolled_via_dep'] = null; // Boolean
        $this->rs['is_activation_lock_manageable'] = null; // Boolean
        $this->rs['is_user_enrollment'] = null; // Boolean
        $this->rs['user_approved_enrollment'] = null; // Boolean
        $this->rs['is_deleted'] = null; // Boolean
        $this->rs['is_muted'] = null; // Boolean
        $this->rs['is_supervised'] = null; // Boolean
        $this->rs['asset_tag'] = '';
        $this->rs['last_app_info'] = 0;
        $this->rs['last_check_in'] = 0;
        $this->rs['last_check_out'] = 0;
        $this->rs['last_enroll'] = 0;
        $this->rs['last_info'] = 0;
        $this->rs['last_kinfo'] = 0;
        $this->rs['last_beat'] = 0;
        $this->rs['last_push'] = 0;
        $this->rs['last_login'] = 0;
        $this->rs['last_media_info'] = 0;
        $this->rs['last_muted'] = 0;
        $this->rs['last_printers'] = 0;
        $this->rs['last_profiles_info'] = 0;
        $this->rs['open_direct_device_link'] = '';
        $this->rs['lostmode_status'] = '';
        $this->rs['status'] = '';
        $this->rs['status_login'] = '';
        $this->rs['assigned_user_email'] = '';
        $this->rs['assigned_user_id'] = '';
        $this->rs['assigned_username'] = '';
        $this->rs['assigned_usertype'] = '';
        $this->rs['device_attestation_status'] = '';
        $this->rs['device_info_attempt_date'] = 0;
        $this->rs['device_info_success_date'] = 0;
        $this->rs['last_device_token_date'] = 0;
        $this->rs['last_remote_notification_date'] = 0;
        $this->rs['mac_commands_attempt_date'] = 0;
        $this->rs['mac_commands_reply_ack_attempt_date'] = 0;
        $this->rs['mac_commands_reply_ack_success_date'] = 0;
        $this->rs['mac_commands_reply_results_attempt_date'] = 0;
        $this->rs['mac_commands_reply_results_success_date'] = 0;
        $this->rs['mac_commands_success_date'] = 0;
        $this->rs['device_is_invalid'] = null; // Boolean
        $this->rs['trigger_session_uuid'] = '';
        $this->rs['trigger_username'] = '';
        $this->rs['org_name'] = '';
        $this->rs['org_url'] = '';
        $this->rs['mosyle_mdm_agent_app'] = '';
        $this->rs['mosyle_alert_app'] = '';
        $this->rs['mosyle_app'] = '';
        $this->rs['mosyle_mdm_app'] = '';
        $this->rs['mosyle_monitor_app'] = '';
        $this->rs['mosyle_notification_center_app'] = '';
        $this->rs['mosyle_security_app'] = '';
        $this->rs['mosyle_av_app'] = '';
        $this->rs['mosyle_selfservice_app'] = '';
        $this->rs['mosyle_timestamp'] = 0;

        if ($serial) {
            $this->retrieve_record($serial);
        }

        $this->serial_number = $serial;

        $this->module_dir = dirname(__FILE__);

        // Add local config
        configAppendFile(__DIR__ . '/config.php');
    }

    /**
    * Get Mosyle data
    *
    * @return void
    * @author tuxudo
    **/
    public function run_mosyle_stats()
    {
        // Check if we should enable Mosyle lookup
        if (conf('mosyle_enable')) {
            // Load Mosyle helper
            require_once($this->module_dir.'/lib/mosyle_helper.php');
            $mosyle_helper = new munkireport\module\mosyle\mosyle_helper;
            return $mosyle_helper->pull_mosyle_data($this);
            // ^^ Comment and uncomment to turn off and on API bits
        }

        return $this;
    }

    /**
    * Process data sent by postflight
    *
    * @param string data
    * @author tuxudo
    **/
    public function process($data)
    {
        // If data is empty, echo out error
        if (! $data) {
            echo ("Error Processing Mosyle module: No data found");
        } else { 

            // // Process incoming mosyle.plist
            $parser = new CFPropertyList();
            $parser->parse($data, CFPropertyList::FORMAT_XML);
            $plist = $parser->toArray();

            foreach (array('device_info_attempt_date', 'device_info_success_date', 'last_device_token_date', 'last_remote_notification_date', 'mac_commands_attempt_date', 'mac_commands_reply_ack_attempt_date', 'mac_commands_reply_ack_success_date', 'mac_commands_reply_results_attempt_date', 'mac_commands_reply_results_success_date', 'mac_commands_success_date', 'device_is_invalid', 'trigger_session_uuid', 'trigger_username', 'org_name', 'org_url', 'mosyle_mdm_agent_app', 'mosyle_alert_app', 'mosyle_app', 'mosyle_mdm_app', 'mosyle_monitor_app', 'mosyle_notification_center_app', 'mosyle_security_app', 'mosyle_av_app', 'mosyle_selfservice_app', 'mosyle_timestamp') as $item) {

                // If key exists and value is zero, set the db value to zero
                if ( array_key_exists($item, $plist) && ($plist[$item] === 0 || $plist[$item] === False)) {
                    $this->$item = 0;
                                
                // Else if key does not exist in $plist, null it
                } else if (! array_key_exists($item, $plist) || $plist[$item] == '') {
                    $this->$item = null;
                // Set the db fields to be the same as those in the preference file
                } else {
                    $this->$item = $plist[$item];
                }
            }

            // Save the data, Mosyle please make a proper client binary kthx
            $this->save();

            // Trigger the API lookup if enabled
            if (conf('mosyle_enable')) {
                $this->run_mosyle_stats();
            }
        }
    }
}
