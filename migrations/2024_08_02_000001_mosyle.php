<?php
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Capsule\Manager as Capsule;

class Mosyle extends Migration
{
    private $tableName = 'mosyle';

    public function up()
    {
        $capsule = new Capsule();
        $capsule::schema()->create($this->tableName, function (Blueprint $table) {
            $table->increments('id');
            $table->string('serial_number')->unique();
            $table->string('device_name')->nullable();
            $table->string('device_udid')->nullable();
            $table->string('enrollment_type')->nullable();
            $table->text('active_managed_users')->nullable();
            $table->text('tags')->nullable();
            $table->string('current_console_managed_user')->nullable();
            $table->boolean('enrolled_via_dep')->nullable();
            $table->boolean('is_activation_lock_manageable')->nullable();
            $table->boolean('is_user_enrollment')->nullable();
            $table->boolean('user_approved_enrollment')->nullable();
            $table->boolean('is_deleted')->nullable();
            $table->boolean('is_muted')->nullable();
            $table->boolean('is_supervised')->nullable();
            $table->text('asset_tag')->nullable();
            $table->bigInteger('last_app_info')->nullable();
            $table->bigInteger('last_check_in')->nullable();
            $table->bigInteger('last_check_out')->nullable();
            $table->bigInteger('last_enroll')->nullable();
            $table->bigInteger('last_info')->nullable();
            $table->bigInteger('last_kinfo')->nullable();
            $table->bigInteger('last_beat')->nullable();
            $table->bigInteger('last_push')->nullable();
            $table->bigInteger('last_login')->nullable();
            $table->bigInteger('last_media_info')->nullable();
            $table->bigInteger('last_muted')->nullable();
            $table->bigInteger('last_printers')->nullable();
            $table->bigInteger('last_profiles_info')->nullable();
            $table->text('open_direct_device_link')->nullable();
            $table->string('lostmode_status')->nullable();
            $table->string('status')->nullable();
            $table->string('status_login')->nullable();
            $table->string('assigned_user_email')->nullable();
            $table->string('assigned_user_id')->nullable();
            $table->string('assigned_username')->nullable();
            $table->string('assigned_usertype')->nullable();
            $table->string('device_attestation_status')->nullable();
            $table->bigInteger('device_info_attempt_date')->nullable();
            $table->bigInteger('device_info_success_date')->nullable();
            $table->bigInteger('last_device_token_date')->nullable();
            $table->bigInteger('last_remote_notification_date')->nullable();
            $table->bigInteger('mac_commands_attempt_date')->nullable();
            $table->bigInteger('mac_commands_reply_ack_attempt_date')->nullable();
            $table->bigInteger('mac_commands_reply_ack_success_date')->nullable();
            $table->bigInteger('mac_commands_reply_results_attempt_date')->nullable();
            $table->bigInteger('mac_commands_reply_results_success_date')->nullable();
            $table->bigInteger('mac_commands_success_date')->nullable();
            $table->boolean('device_is_invalid')->nullable();
            $table->string('trigger_session_uuid')->nullable();
            $table->string('trigger_username')->nullable();
            $table->string('org_name')->nullable();
            $table->string('org_url')->nullable();
            $table->string('mosyle_mdm_agent_app')->nullable();
            $table->string('mosyle_alert_app')->nullable();
            $table->string('mosyle_app')->nullable();
            $table->string('mosyle_mdm_app')->nullable();
            $table->string('mosyle_monitor_app')->nullable();
            $table->string('mosyle_notification_center_app')->nullable();
            $table->string('mosyle_security_app')->nullable();
            $table->string('mosyle_av_app')->nullable();
            $table->string('mosyle_selfservice_app')->nullable();
            $table->bigInteger('mosyle_timestamp')->nullable();
        });


        // Make the indexes
        $capsule::schema()->table($this->tableName, function (Blueprint $table) {
            $table->index('id');
            $table->index('serial_number');
            $table->index('device_name');
            $table->index('device_udid');
            $table->index('enrollment_type');
            $table->index('current_console_managed_user');
            $table->index('enrolled_via_dep');
            $table->index('is_activation_lock_manageable');
            $table->index('is_user_enrollment');
            $table->index('is_deleted');
            $table->index('is_muted');
            $table->index('is_supervised');
            $table->index('last_app_info');
            $table->index('last_check_in');
            $table->index('last_check_out');
            $table->index('last_enroll');
            $table->index('last_info');
            $table->index('last_kinfo');
            $table->index('last_beat');
            $table->index('last_push');
            $table->index('last_login');
            $table->index('last_media_info');
            $table->index('last_muted');
            $table->index('last_printers');
            $table->index('last_profiles_info');
            $table->index('lostmode_status');
            $table->index('status');
            $table->index('status_login');
        });
    }

    public function down()
    {
        $capsule = new Capsule();
        $capsule::schema()->dropIfExists($this->tableName);
    }
}