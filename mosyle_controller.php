<?php

/**
 * Mosyle module class
 *
 * @package munkireport
 * @author tuxudo
 **/
class mosyle_controller extends Module_controller
{
    public function __construct()
    {
        // No authentication, the client needs to get here
        // Store module path
        $this->module_path = dirname(__FILE__);

        // Add local config
        configAppendFile(__DIR__ . '/config.php');
    }

    public function index()
    {
        echo "You've loaded the Mosyle module!";
    }

    // Add the admin page
    public function admin()
    {
        $obj = new View();
        $obj->view('mosyle_admin', [], $this->module_path.'/views/');
    }

    /**
     * Get data for last date widget
     *
     * @return void
     * @author tuxudo
     **/
    public function get_last_date_widget($column)
    {
        // Remove non-column name characters
        $column = preg_replace("/[^A-Za-z0-9_\-]]/", '', $column);

        $currentdate = date_timestamp_get(date_create());
        $week = $currentdate - 604800;
        $month = $currentdate - 2592000;

        $sql = "SELECT COUNT(CASE WHEN ".$column." <= $month <> '' AND ".$column." IS NOT NULL THEN 1 END) AS red, 
                COUNT(CASE WHEN ".$column." <= $week AND ".$column." > $month <> '' AND ".$column." IS NOT NULL THEN 1 END) AS yellow,
                COUNT(CASE WHEN ".$column." > $week AND ".$column." > 0 <> '' AND ".$column." IS NOT NULL THEN 1 END) AS green
                FROM mosyle
                LEFT JOIN reportdata USING (serial_number)
                ".get_machine_group_filter();

        $out = [];
        $queryobj = new Mosyle_model();
        foreach($queryobj->query($sql)[0] as $label => $value){
                $out[] = ['label' => $label, 'count' => $value];
        }
        jsonView($out);
    }


    /**
     * Get data for scroll widget
     *
     * @return void
     * @author tuxudo
     **/
    public function get_scroll_widget($column)
    {
        // Remove non-column name characters
        $column = preg_replace("/[^A-Za-z0-9_\-]]/", '', $column);

        $sql = "SELECT COUNT(CASE WHEN ".$column." <> '' AND ".$column." IS NOT NULL THEN 1 END) AS count, ".$column."
                FROM mosyle
                LEFT JOIN reportdata USING (serial_number)
                ".get_machine_group_filter()."
                AND ".$column." <> '' AND ".$column." IS NOT NULL
                GROUP BY ".$column."
                ORDER BY count DESC";

        $queryobj = new Mosyle_model;
        jsonView($queryobj->query($sql));
    }

    /**
     * Pull in Mosyle data for all serial numbers :D
     *
     * @return void
     * @author tuxudo
     **/
    public function pull_all_mosyle_data($incoming_serial = '')
    {
        // Check if we are returning a list of all serials or processing a serial
        // Returns either a list of all serial numbers in MunkiReport OR
        // a JSON of what serial number was just ran with the status of the run
        if ( $incoming_serial == ''){
            // Get all the serial numbers in an object
            $machine = new Mosyle_model();
            $filter = get_machine_group_filter();

            $sql = "SELECT machine.serial_number
                FROM machine
                LEFT JOIN reportdata USING (serial_number)
                $filter";

            // Loop through each serial number for processing
            $out = array();
            foreach ($machine->query($sql) as $serialobj) {
                $out[] = $serialobj->serial_number;
            }
            jsonView($out);
        } else {

            $mosyle = new Mosyle_model($incoming_serial);
            $mosyle_status = $mosyle->run_mosyle_stats();

            // Check if machine exists in Mosyle
            if ($mosyle_status == false){
                $out = array("serial"=>$incoming_serial,"status"=>"Machine not found in Mosyle!");
            } else {
                $out = array("serial"=>$incoming_serial,"status"=>"Machine processed");
            }

            jsonView($out);
        }
    }

    /**
     * Force data pull from Mosyle
     *
     * @return void
     * @author tuxudo
     **/
    public function recheck_mosyle($serial = '')
    {
        if (authorized_for_serial($serial)) {
            $mosyle = new Mosyle_model($serial);
            $mosyle->run_mosyle_stats();
        }

        redirect("clients/detail/$serial#tab_mosyle-tab");
    }

    /**
     * Get Mosyle information for serial_number
     *
     * @param string $serial serial number
     **/
    public function get_data($serial_number = '')
    {
        // Remove non-serial number characters
        $serial_number = preg_replace("/[^A-Za-z0-9_\-]]/", '', $serial_number);

        $obj = new View();

        if (! $this->authorized()) {
            $obj->view('json', array('msg' => 'Not authorized'));
            return;
        }

        $sql = "SELECT open_direct_device_link, mosyle_timestamp, status, org_name, org_url, device_name, device_udid, is_deleted, is_muted, asset_tag, tags, lostmode_status, device_attestation_status, assigned_user_email, assigned_user_id, assigned_username, assigned_usertype, current_console_managed_user, active_managed_users, status_login, last_login, enrollment_type, last_enroll, enrolled_via_dep, is_user_enrollment, user_approved_enrollment, is_supervised, is_activation_lock_manageable, last_beat, last_push, last_check_in, last_check_out, last_info, last_media_info, last_printers, last_profiles_info, last_app_info, last_muted, mosyle_mdm_agent_app, mosyle_alert_app, mosyle_app, mosyle_mdm_app, mosyle_monitor_app, mosyle_notification_center_app, mosyle_selfservice_app, mosyle_security_app, mosyle_av_app, device_is_invalid, device_info_attempt_date, device_info_success_date, mac_commands_attempt_date, mac_commands_success_date, mac_commands_reply_ack_attempt_date, mac_commands_reply_ack_success_date, mac_commands_reply_results_attempt_date, mac_commands_reply_results_success_date, last_device_token_date, last_remote_notification_date
                FROM mosyle 
                LEFT JOIN reportdata USING (serial_number)
                ".get_machine_group_filter()."
                AND serial_number = '$serial_number'";

        $queryobj = new Mosyle_model();
        $obj->view('json', array('msg' => current(array('msg' => $queryobj->query($sql)[0])))); 
    }
} // End class Mosyle_module
