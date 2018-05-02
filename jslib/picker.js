var flexSkeleton = '<div class="flexbox">'
                    + '<div class="card-row">'
                        + '<div class="card">'
                            + '<p>Setting Up:</p>'
                            + '<ul id="setupList"></ul>'
                        + '</div>'
                        + '<div class="card">'
                            + '<p>Cleaning Up:</p>'
                            + '<ul id="cleanupList"></ul>'
                        + '</div>'
                    + '</div>'
                    + '<div class="card-row">'
                        + '<div class="card">'
                            + '<p>Sober Monitoring:</p>'
                            + '<ul id="monitoringList"></ul>'
                        + '</div>'
                        + '<div class="card">'
                            + '<p>Sober Driving:</p>'
                            + '<ul id="drivingList"></ul>'
                        + '</div>'
                    + '</div>'
                + '</div>';

function picker() {
    var setup = $('input[name=setup]').val();
    var cleanup = $('input[name=cleanup]').val();
    var monitor = $('input[name=monitor]').val();
    var drive = $('input[name=drive]').val();
    
    $.ajax({
        url: '/eventpicker/phplib/select.php',
        method: 'GET',
        dataType: 'json',
        data: {
            'setup': setup,
            'cleanup': cleanup,
            'monitor': monitor,
            'drive': drive
        },
        success: function(data) {
            if(data['status'] === 'OK') {
                $('#result').html(flexSkeleton);
                
                $.each(data.setup, function(index, str) {
                    if(index == 0) {
                        $('#setupList').html('<li>' + str + '</li>');
                    }else {
                        $('<li>' + str + '</li>').insertAfter('#setupList li:last');
                    }
                });
                
                $.each(data.cleanup, function(index, str) {
                    if(index == 0) {
                        $('#cleanupList').html('<li>' + str + '</li>');
                    }else {
                        $('<li>' + str + '</li>').insertAfter('#cleanupList li:last');
                    }
                });
                
                $.each(data.monitor, function(index, str) {
                    if(index == 0) {
                        $('#monitoringList').html('<li>' + str + '</li>');
                    }else {
                        $('<li>' + str + '</li>').insertAfter('#monitoringList li:last');
                    }
                });
                
                $.each(data.drive, function(index, str) {
                    if(index == 0) {
                        $('#drivingList').html('<li>' + str + '</li>');
                    }else {
                        $('<li>' + str + '</li>').insertAfter('#drivingList li:last');
                    }
                });
            }else {
                var error = '<div class="card-row">'
                                + '<div class="card">'
                                    + '<p class="error"> ERROR: ' + data['error'] + '<br><br>You can reset members on the \'Manage Members\' page!</p>'
                                + '</div>'
                            + '</div>';
                $('#result').html(error);
            }
            
        }
    });
}