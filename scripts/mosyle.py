#!/usr/local/munkireport/munkireport-python3

import os
import sys
import plistlib
import time

sys.path.insert(0, '/usr/local/munki')
sys.path.insert(0, '/usr/local/munkireport')

from munkilib import FoundationPlist

def get_app_data(app_path):

    # Read in Info.plist for processing 
    try:
        if os.path.exists(app_path+"/Contents/Info.plist"):
            info_plist = FoundationPlist.readPlist(app_path+"/Contents/Info.plist")
        else:
             return ""

        return info_plist['CFBundleShortVersionString']
    except Exception:
        return ""

def get_local_mosyle_prefs():

    if os.path.isfile('/var/root/Library/Preferences/com.mosyle.macos.manager.mdm.plist'):

        preffile = {}
        pl = FoundationPlist.readPlist('/var/root/Library/Preferences/com.mosyle.macos.manager.mdm.plist')

        for item in pl:
            if item == 'DeviceInfoAttemptDate':
                preffile['device_info_attempt_date'] = str(int(pl["DeviceInfoAttemptDate"]))
            elif item == 'DeviceInfoSuccessDate':
                preffile['device_info_success_date'] = str(int(pl["DeviceInfoSuccessDate"]))
            elif item == 'LastDeviceTokenDate':
                preffile['last_device_token_date'] = str(int(pl["LastDeviceTokenDate"]))
            elif item == 'LastRemoteNotificationDate':
                preffile['last_remote_notification_date'] = str(int(pl["LastRemoteNotificationDate"]))
            elif item == 'MacCommandsAttemptDate':
                preffile['mac_commands_attempt_date'] = str(int(pl["MacCommandsAttemptDate"]))
            elif item == 'MacCommandsReplyAckAttemptDate':
                preffile['mac_commands_reply_ack_attempt_date'] = str(int(pl["MacCommandsReplyAckAttemptDate"]))
            elif item == 'MacCommandsReplyAckSuccessDate':
                preffile['mac_commands_reply_ack_success_date'] = str(int(pl["MacCommandsReplyAckSuccessDate"]))
            elif item == 'MacCommandsReplyResultsAttemptDate':
                preffile['mac_commands_reply_results_attempt_date'] = str(int(pl["MacCommandsReplyResultsAttemptDate"]))
            elif item == 'MacCommandsReplyResultsSuccessDate':
                preffile['mac_commands_reply_results_success_date'] = str(int(pl["MacCommandsReplyResultsSuccessDate"]))
            elif item == 'MacCommandsSuccessDate':
                preffile['mac_commands_success_date'] = str(int(pl["MacCommandsSuccessDate"]))

            elif item == 'com.mosyle.macos.manager.mdm.device.isInvalid':
                preffile['device_is_invalid'] = pl["com.mosyle.macos.manager.mdm.device.isInvalid"]
            elif item == 'com.mosyle.macos.manager.mdm.framework.commands.triggercomputeron.sessionuuid':
                preffile['trigger_session_uuid'] = pl["com.mosyle.macos.manager.mdm.framework.commands.triggercomputeron.sessionuuid"]
            elif item == 'com.mosyle.macos.manager.mdm.framework.commands.triggersignin.username':
                preffile['trigger_username'] = pl["com.mosyle.macos.manager.mdm.framework.commands.triggersignin.username"]

            elif item == 'com.mosyle.macos.manager.mdm.school.schoolName':
                preffile['org_name'] = pl["com.mosyle.macos.manager.mdm.school.schoolName"]
            elif item == 'com.mosyle.macos.manager.mdm.school.schoolWebviewURL':
                preffile['org_url'] = pl["com.mosyle.macos.manager.mdm.school.schoolWebviewURL"]

        return preffile
    else:
        return {}

def main():
    """Main"""

    # Check if Mosyle is setup on the Mac
    if not os.path.isfile('/Library/Application Support/Mosyle/Mosyle MDM Agent.app/Contents/Info.plist'):
        print("ERROR: Mosyle is not installed")
        result = {}
    else:
        # Get results
        result = get_local_mosyle_prefs()
        result['mosyle_mdm_agent_app'] = get_app_data("/Library/Application Support/Mosyle/Mosyle MDM Agent.app")
        result['mosyle_alert_app'] = get_app_data("/Library/Application Support/Mosyle/Mosyle Alert.app")
        result['mosyle_app'] = get_app_data("/Library/Application Support/Mosyle/Mosyle.app")
        result['mosyle_mdm_app'] = get_app_data("/Library/Application Support/Mosyle/MosyleMDM.app")
        result['mosyle_monitor_app'] = get_app_data("/Library/Application Support/Mosyle/MosyleMonitor.app")
        result['mosyle_notification_center_app'] = get_app_data("/Library/Application Support/Mosyle/Notification Center.app")
        result['mosyle_security_app'] = get_app_data("/Applications/MosyleSecurity.app")
        result['mosyle_av_app'] = get_app_data("/Library/Application Support/Mosyle/MosyleAV.app")
        result['mosyle_selfservice_app'] = get_app_data("/Applications/Self-Service.app")
        result['mosyle_timestamp'] = str(int(time.time()))

    # Write results to cache
    cachedir = '%s/cache' % os.path.dirname(os.path.realpath(__file__))
    output_plist = os.path.join(cachedir, 'mosyle.plist')
    try:
        plistlib.writePlist(result, output_plist)
    except:
        with open(output_plist, 'wb') as fp:
            plistlib.dump(result, fp, fmt=plistlib.FMT_XML)

if __name__ == "__main__":
    main()