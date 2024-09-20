<h2>Mosyle  <a data-i18n="mosyle.recheck" class="btn btn-default btn-xs" href="<?php echo url('module/mosyle/recheck_mosyle/' . $serial_number);?>"></a><span id="mosyle_view_in"></span></h2>

<div id="mosyle-msg" data-i18n="listing.loading" class="col-lg-12 text-center"></div>


<script>    
$(document).on('appReady', function(){
    $.getJSON( appUrl + '/module/mosyle/get_data/' + serialNumber, function(data) {
        formatted_data = []
        formatted_data.push(data)
        data = formatted_data

        // Check if we have data
        if( ! data[0]){
        // if( ! data[0]['mosyle_id']){
            $('#mosyle-msg').text(i18n.t('no_data'));
        }else{

            // Hide
            $('#mosyle-msg').text('');
            $('#mosyle-msg').removeClass('hide');

            var skipThese = ['id','serial_number'];
            $.each(data, function(i,d){

                // Generate rows from data
                var rows = ''
                var rows_client = ''

                for (var prop in d){
                    // Skip skipThese
                    if(skipThese.indexOf(prop) == -1){
                        // Do nothing for empty values to blank them
                        if ((d[prop] == '' || d[prop] == null) && d[prop] !== 0){
                            rows = rows

                        // Format date 
                        } else if ((prop == "last_app_info" || prop == "last_check_in" || prop == "last_check_out" || prop == "last_enroll" || prop == "last_info" || prop == "last_kinfo" || prop == "last_beat" || prop == "last_push" || prop == "last_login" || prop == "last_media_info" || prop == "last_muted" || prop == "last_printers" || prop == "last_profiles_info") && d[prop] > 10){
                            var date = new Date(d[prop] * 1000);
                            rows = rows + '<tr><th>'+i18n.t('mosyle.'+prop)+'</th><td><span title="'+moment(date).format('llll')+'">'+moment(date).fromNow()+'</span></td></tr>';

                        // Format date for client rows
                        } else if ((prop == "device_info_attempt_date" || prop == "device_info_success_date" || prop == "last_device_token_date" || prop == "last_remote_notification_date" || prop == "mac_commands_attempt_date" || prop == "mac_commands_reply_ack_attempt_date" || prop == "mac_commands_reply_ack_success_date" || prop == "mac_commands_reply_results_attempt_date" || prop == "mac_commands_reply_results_success_date" || prop == "mac_commands_success_date" || prop == "mosyle_timestamp") && d[prop] > 10){
                            var date = new Date(d[prop] * 1000);
                            rows_client = rows_client + '<tr><th>'+i18n.t('mosyle.'+prop)+'</th><td><span title="'+moment(date).format('llll')+'">'+moment(date).fromNow()+'</span></td></tr>';
                        
                        // Hide date rows that are zero
                        } else if ((prop == "last_app_info" || prop == "last_check_in" || prop == "last_check_out" || prop == "last_enroll" || prop == "last_info" || prop == "last_kinfo" || prop == "last_beat" || prop == "last_push" || prop == "last_login" || prop == "last_media_info" || prop == "last_muted" || prop == "last_printers" || prop == "last_profiles_info" || prop == "device_info_attempt_date" || prop == "device_info_success_date" || prop == "last_device_token_date" || prop == "last_remote_notification_date" || prop == "mac_commands_attempt_date" || prop == "mac_commands_reply_ack_attempt_date" || prop == "mac_commands_reply_ack_success_date" || prop == "mac_commands_reply_results_attempt_date" || prop == "mac_commands_reply_results_success_date" || prop == "mac_commands_success_date" || prop == "mosyle_timestamp") && d[prop] == 0){
                            rows = rows 

                        } else if ((prop == 'enrolled_via_dep' || prop == 'is_activation_lock_manageable' || prop == 'is_user_enrollment' || prop == 'user_approved_enrollment' || prop == 'is_deleted' || prop == 'is_muted' || prop == 'is_supervised' || prop == 'device_is_invalid') && d[prop] == 1){
                            rows = rows + '<tr><th>'+i18n.t('mosyle.'+prop)+'</th><td>'+i18n.t('yes')+'</td></tr>';

                        } else if ((prop == 'enrolled_via_dep' || prop == 'is_activation_lock_manageable' || prop == 'is_user_enrollment' || prop == 'user_approved_enrollment' || prop == 'is_deleted' || prop == 'is_muted' || prop == 'is_supervised' || prop == 'device_is_invalid') && d[prop] == 0){
                            rows = rows + '<tr><th>'+i18n.t('mosyle.'+prop)+'</th><td>'+i18n.t('no')+'</td></tr>';

                        // Client rows
                        } else if ((prop == 'device_is_invalid') && d[prop] == 1){
                            rows_client = rows_client + '<tr><th>'+i18n.t('mosyle.'+prop)+'</th><td>'+i18n.t('yes')+'</td></tr>';
                        // Client rows
                        } else if ((prop == 'device_is_invalid') && d[prop] == 0){
                            rows_client = rows_client + '<tr><th>'+i18n.t('mosyle.'+prop)+'</th><td>'+i18n.t('no')+'</td></tr>';

                        // Generate buttons and tabs
                        } else if ((prop == "open_direct_device_link") && d[prop]){
                            $('#mosyle_view_in').html('<a data-i18n-"mosyle.view_in_mosyle" class="btn btn-default btn-xs" href="'+d[prop]+'" target="_blank" title="'+i18n.t('mosyle.view_in_mosyle')+'">'+i18n.t('mosyle.view_in_mosyle')+'</a>');

                        } else if (prop == "tags" && d[prop]){
                            rows = rows + '<tr><th>'+i18n.t('mosyle.'+prop)+'</th><td>'+d[prop].replace(",", "<br>")+'</td></tr>';

                        } else if (prop == "status_login" && d[prop]){
                            rows = rows + '<tr><th>'+i18n.t('mosyle.'+prop)+'</th><td>'+d[prop].replace("Logged", "Logged In")+'</td></tr>';

                        } else if (prop == "active_managed_users" && d[prop]){
                            rows = rows + '<tr><th>'+i18n.t('mosyle.'+prop)+'</th><td>'+d[prop].replace('["', "").replace('"]', "").replace('","', "<br>")+'</td></tr>';


                        // Else, build out rows from entries
                        } else {
                            rows = rows + '<tr><th>'+i18n.t('mosyle.'+prop)+'</th><td>'+d[prop]+'</td></tr>';
                        }
                    }
                }

                if (rows != ''){
                    $('#mosyle-tab')
                        .append($('<div style="max-width:700px;">')
                            .append($('<table>')
                                .addClass('table table-striped table-condensed')
                                .append($('<tbody>')
                                    .append(rows))))
                }

                if (rows_client != ''){
                    $('#mosyle-tab')
                        // Write out client info table
                        .append($('<h4>')
                            .append($('<i>')
                                .addClass('fa fa-crosshairs'))
                            .append(' '+i18n.t('mosyle.client_table')))
                        .append($('<div style="max-width:700px;">')
                            .append($('<table>')
                                .addClass('table table-striped table-condensed')
                                .append($('<tbody>')
                                    .append(rows_client))))
                }
            })
        }
    });
});

// Make button groups active
$(".btn-group > .btn").click(function(){
    $(this).addClass("active").siblings().removeClass("active");
});

</script>
