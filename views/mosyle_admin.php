<?php $this->view('partials/head'); 
  // Add local config
  configAppendFile(__DIR__ . '/../config.php');
?>

<div class="container fluid">
    <div class="row pt-4"><span id="mosyle_pull_all"></span></div>
    <div class="col-lg-6">
        <div id="GetAllMosyle-Progress" class="progress hide">
            <div class="progress-bar progress-bar-success progress-bar-striped active" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="min-width: 2em; width: 0%;">
                <span id="Progress-Bar-Percent"></span>
            </div>
        </div>
        <br id="Progress-Space" class="hide">
        <div id="Mosyle-System-Status"></div>
    </div>
</div>  <!-- /container -->

<script>
var mosyle_pull_all_running = 0;

$(document).on('appReady', function(e, lang) {

    // Generate pull all button and header    
    $('#mosyle_pull_all').html('<h3 class="col-lg-6" >&nbsp;&nbsp;'+i18n.t('mosyle.title_admin')+'&nbsp;&nbsp;<button id="GetAllMosyle" class="btn btn-default btn-xs hide">'+i18n.t("mosyle.pull_in_all")+'</button></h3>');

    // Get Mosyle server URL
    var mosyle_address = "<?php echo rtrim(conf('mosyle_address'), '/'); ?>";

    // Check if Mosyle lookups are enabled
    if ("<?php echo conf('mosyle_enable'); ?>" == true) {
        var mosyle_enabled = i18n.t('yes');
        var mosyle_enabled_int = 1;
        $('#GetAllMosyle').removeClass('hide');
    } else { 
        var mosyle_enabled = i18n.t('no');
        var mosyle_enabled_int = 0;
    }

    mosyle_pull_all_running = 0;

    // Check if Mosyle API password is set
    if (parseInt("<?php echo strlen(conf('mosyle_api_key')); ?>") > 0) {
        var mosyle_api_key = i18n.t('yes');    
    } else { 
        var mosyle_api_key = i18n.t('no');
    }

    // Check if Mosyle username password is set
    if (parseInt("<?php echo strlen(conf('mosyle_password')); ?>") > 0) {
        var mosyle_password = i18n.t('yes');    
    } else { 
        var mosyle_password = i18n.t('no');
    }

    // Build table
    var mosylerows = '<table class="table table-striped table-condensed"><tbody>'
    mosylerows = mosylerows + '<tr><th>'+i18n.t('mosyle.lookups_enabled')+'</th><td>'+mosyle_enabled+'</td></tr>';
    mosylerows = mosylerows + '<tr><th>'+i18n.t('mosyle.mosyle_address')+'</th><td>'+mosyle_address+'</td></tr>';
    mosylerows = mosylerows + '<tr><th>'+i18n.t('mosyle.mosyle_username')+'</th><td>'+"<?php echo conf('mosyle_username'); ?>"+'</td></tr>';
    mosylerows = mosylerows + '<tr><th>'+i18n.t('mosyle.mosyle_password')+'</th><td>'+mosyle_password+'</td></tr>';
    mosylerows = mosylerows + '<tr><th>'+i18n.t('mosyle.mosyle_api_key')+'</th><td>'+mosyle_api_key+'</td></tr>';

    $('#Mosyle-System-Status').html(mosylerows+'</tbody></table>') // Close table framework and assign to HTML ID

    $('#GetAllMosyle').click(function (e) {
        // Disable button and unhide progress bar
        $('#GetAllMosyle').html(i18n.t('mosyle.processing')+'...');
        $('#Progress-Bar-Percent').text('0%');
        $('#GetAllMosyle-Progress').removeClass('hide');
        $('#Progress-Space').removeClass('hide');
        $('#GetAllMosyle').addClass('disabled');
        mosyle_pull_all_running = 1;

        // Get JSON of all serial numbers
        $.getJSON(appUrl + '/module/mosyle/pull_all_mosyle_data', function (processdata) {

            // Set count of serial numbers to be processed
            var progressmax = (processdata.length);
            var progessvalue = 0;;
            $('.progress-bar').attr('aria-valuemax', progressmax);

            var serial_index = 0;
            var serial = processdata[0]

            // Process the serial numbers
            process_serial(serial,progessvalue,progressmax,processdata,serial_index)
        });
    });
});

// Process each Mosyle request one at a time
function process_serial(serial,progessvalue,progressmax,processdata,serial_index){

        // Get JSON for each serial number
        request = $.ajax({
        url: appUrl + '/module/mosyle/pull_all_mosyle_data/'+processdata[serial_index],
        type: "get",
        success: function (obj, resultdata) {

            // Calculate progress bar's percent
            var processpercent = Math.round((((progessvalue+1)/progressmax)*100));
            progessvalue++
            $('.progress-bar').css('width', (processpercent+'%')).attr('aria-valuenow', processpercent);
            $('#Progress-Bar-Percent').text(progessvalue+"/"+progressmax);

            // Cleanup and reset when done processing serials
            if ((progessvalue) == progressmax) {
                // Make button clickable again and hide process bar elements
                $('#GetAllMosyle').html(i18n.t('mosyle.pull_in_all'));
                $('#GetAllMosyle').removeClass('disabled');
                mosyle_pull_all_running = 0;
                $("#Progress-Space").fadeOut(1200, function() {
                    $('#Progress-Space').addClass('hide')
                    var progresselement = document.getElementById('Progress-Space');
                    progresselement.style.display = null;
                    progresselement.style.opacity = null;
                });
                $("#GetAllMosyle-Progress").fadeOut( 1200, function() {
                    $('#GetAllMosyle-Progress').addClass('hide')
                    var progresselement = document.getElementById('GetAllMosyle-Progress');
                    progresselement.style.display = null;
                    progresselement.style.opacity = null;
                    $('.progress-bar').css('width', 0+'%').attr('aria-valuenow', 0);
                });

                return true;
            }

            // Go to the next serial
            serial_index++

            // Get next serial
            serial = processdata[serial_index];

            // Run function again with new serial
            process_serial(serial,progessvalue,progressmax,processdata,serial_index)
        },
        statusCode: {
            500: function() {
                mosyle_pull_all_running = 0;
                alert("An internal server occurred. Please refresh the page and try again.");
            }
        }
    });
}

// Warning about leaving page if Mosyle pull all is running
window.onbeforeunload = function() {
    if (mosyle_pull_all_running == 1) {
        return i18n.t('mosyle.leave_page_warning');
    } else {
        return;
    }
};

</script>

<?php $this->view('partials/foot'); ?>
