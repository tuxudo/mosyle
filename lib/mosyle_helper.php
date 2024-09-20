<?php

namespace munkireport\module\mosyle;

use Mosyle_model;

class Mosyle_helper
{
    /**
     * Retrieve url and process resulting JSON
     *
     * @return JSON object if successful, FALSE if failed
     * @author n8felton for DeployStudio, tweaked for Jamf by Tuxudo, tweaked again for Mosyle by Tuxudo
     *
     **/
    public function pull_mosyle_data(&$Mosyle_model)
    {
        $mosyle_api_key = conf('mosyle_api_key');

        // Trim off any slashes on the right
        $mosyle_address = rtrim(conf('mosyle_address'), '/');

        $serial_number = $Mosyle_model->serial_number;

        $postData = [
            'operation' => 'list',
            "options" => [
                'os' => 'mac',
                "serial_numbers" => [
                    $serial_number
                ]
            ]
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $mosyle_address."/v1/devices");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10); // Timeout of 10 seconds
        curl_setopt($ch, CURLOPT_HTTPHEADER, array ('Content-Type:application/json', 'Authorization: Bearer '.$this->get_mosyle_bearer_token(), 'accessToken: '.$mosyle_api_key));
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postData)); 

        $mosyle_computer_result = curl_exec($ch);

        curl_close($ch);

        // Check for timeout
        if (curl_errno($ch) && curl_errno($ch) == 28) {
            error_log("MunkiReport:- Mosyle server timed out for - ".$serial_number, 0);
            return false;
        } else if (curl_errno($ch)) {
            error_log("MunkiReport:- There was an error getting data from the Mosyle server: ".curl_errno($ch)." - ".$serial_number, 0);
            return false;
        }

        $json = json_decode($mosyle_computer_result, true);

        // Process Mosyle data
        if (array_key_exists("response", $json) && array_key_exists(0, $json['response']) && array_key_exists("devices", $json['response'][0]) && array_key_exists(0, $json['response'][0]['devices'])){
            $json_select = $json['response'][0]['devices'][0];
        } else if (array_key_exists("response", $json) && array_key_exists(0, $json['response']) && array_key_exists("status", $json['response'][0]) && $json['response'][0]['status'] == "DEVICES_NOTFOUND"){
            error_log("MunkiReport:- Machine not found in Mosyle - ".$serial_number, 0);
            return false;
        } else {
            error_log("MunkiReport:- Unable to expand API response from Mosyle for - ".$serial_number, 0);
            return false;
        }

        // Transpose Mosyle API output into Mosyle model
        $Mosyle_model->device_name = $json_select["device_name"];
        $Mosyle_model->device_udid = $json_select["deviceudid"];
        $Mosyle_model->enrollment_type = ucwords(strtolower($json_select["enrollment_type"]));
        $Mosyle_model->tags = $json_select["tags"]; // json?
        $Mosyle_model->current_console_managed_user = $json_select["CurrentConsoleManagedUser"];
        $Mosyle_model->is_deleted = intval($json_select["is_deleted"]);
        $Mosyle_model->is_muted = intval($json_select["is_muted"]);
        $Mosyle_model->is_supervised = intval($json_select["is_supervised"]);
        $Mosyle_model->last_app_info = $json_select["date_app_info"];
        $Mosyle_model->last_check_in = $json_select["date_checkin"];
        $Mosyle_model->last_check_out = $json_select["date_checkin"];
        $Mosyle_model->last_enroll = $json_select["date_enroll"];
        $Mosyle_model->last_info = $json_select["date_info"];
        $Mosyle_model->last_kinfo = $json_select["date_kinfo"];
        $Mosyle_model->last_beat = $json_select["date_last_beat"];
        $Mosyle_model->last_push = $json_select["date_last_push"];
        $Mosyle_model->last_login = $json_select["date_lastlogin"];
        $Mosyle_model->last_media_info = $json_select["date_media_info"];
        $Mosyle_model->last_muted = $json_select["date_muted"];
        $Mosyle_model->last_printers = $json_select["date_printers"];
        $Mosyle_model->last_profiles_info = $json_select["date_profiles_info"];
        $Mosyle_model->open_direct_device_link = $json_select["open_direct_device_link"];
        $Mosyle_model->lostmode_status = ucwords(strtolower($json_select["lostmode_status"]));
        $Mosyle_model->status = ucwords(strtolower($json_select["status"]));
        $Mosyle_model->status_login = ucwords(strtolower($json_select["status_login"]));
        $Mosyle_model->active_managed_users = $json_select["ActiveManagedUsers"];

        if (array_key_exists("DeviceAttestationStatus", $json_select)){
            $Mosyle_model->device_attestation_status = $json_select["DeviceAttestationStatus"];
        }

        if ($json_select["asset_tag"]){
            $Mosyle_model->asset_tag = str_replace(",", "<br>", $json_select["asset_tag"]);
        }

        // Decode ManagementStatus JSON and fill in
        $management_status = json_decode($json_select["ManagementStatus"], true);
        if (array_key_exists("EnrolledViaDEP", $management_status)){
            $Mosyle_model->enrolled_via_dep = intval($management_status["EnrolledViaDEP"]);
        }
        if (array_key_exists("IsActivationLockManageable", $management_status)){
            $Mosyle_model->is_activation_lock_manageable = intval($management_status["IsActivationLockManageable"]);
        }
        if (array_key_exists("IsUserEnrollment", $management_status)){
            $Mosyle_model->is_user_enrollment = intval($management_status["IsUserEnrollment"]);
        }
        if (array_key_exists("UserApprovedEnrollment", $management_status)){
            $Mosyle_model->user_approved_enrollment = intval($management_status["UserApprovedEnrollment"]);
        }

        // Unassign user if user fields are not in Mosyle data
        if (array_key_exists("username", $json_select)){
            $Mosyle_model->assigned_user_email = $json_select["useremail"];
            $Mosyle_model->assigned_user_id = $json_select["userid"];
            $Mosyle_model->assigned_username = $json_select["username"];
            $Mosyle_model->assigned_usertype = $json_select["usertype"];
        } else {
            $Mosyle_model->assigned_user_email = null;
            $Mosyle_model->assigned_user_id = null;
            $Mosyle_model->assigned_username = null;
            $Mosyle_model->assigned_usertype = null;
        }

        // Set the timestamp of last data
        $Mosyle_model->mosyle_timestamp = time();

        // Save the data, Moooosyle loves cows
        $Mosyle_model->save();
        return 'Mosyle data processed';
    }

    /**
     * Retrieve Mosyle bearer token
     *
     * @return string of Mosyle bearer token, FALSE if failed
     * @author Tuxudo for MunkiReport
     *
     **/
    public function get_mosyle_bearer_token()
    {
        $mosyle_username = conf('mosyle_username');
        $mosyle_password = conf('mosyle_password');
        $mosyle_api_key = conf('mosyle_api_key');

        // Trim off any slashes on the right
        $mosyle_address = rtrim(conf('mosyle_address'), '/');

        $postData = [
            'email' => $mosyle_username,
            'password' => $mosyle_password
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $mosyle_address."/v1/login");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10); // Timeout of 10 seconds
        curl_setopt($ch, CURLOPT_HTTPHEADER, array ('Accept: application/json', 'accessToken: '.$mosyle_api_key));
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postData)); 
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_POST, 1);
        $response = curl_exec($ch);
        curl_close($ch);

        $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        $header = substr($response, 0, $header_size);
        $body = substr($response, $header_size);

        // Only check for bearer token if we've successfully logged into Mosyle
        if (array_key_exists("UserID", json_decode($body, true))){
            $separator = "\r\n";
            $line = strtok($header, $separator);

            while ($line !== false) {
                $line = strtok( $separator );
                if (str_contains($line, "Authorization: Bearer ")) {
                    $token = str_replace("Authorization: Bearer ", "", $line);

                    // Exit the while
                    $line == false;
                }
            }
        }

        // Check for timeout
        if (curl_errno($ch) && curl_errno($ch) == 28) {
            error_log("MunkiReport:- Mosyle server timed out when getting the bearer token!", 0);
            return false;
        } else if (curl_errno($ch)) {
            error_log("MunkiReport:- There was an error getting the bearer token from the Mosyle server: ".curl_errno($ch), 0);
            return false;
        }

        return $token;
    }
}