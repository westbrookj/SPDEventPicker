function resetCards(membersList) {
    if(membersList) {
        initMembersList();
    }
    
    $('#memberDetails').html('<p>There is no member selected!</p>');
    
    $('#addMemberCard').html('<div class="spdbutton" id="addMemberButton" onclick="initAddMember()">Add Member</div>');
    
    $('#resetMembersCard').html('<div class="spdbutton" id="resetMembersButton" onclick="resetMembers()">Reset Members</div>');
    
    $('#resetMembersCard, #addMemberCard, #memberDetails').css("text-align", "center");
    
    $('.spdbutton').button();
}

function resetMembers() {
    $.ajax({
        url: '/eventpicker/phplib/resetMembers.php',
        method: 'get',
        dataType: 'json',
        data: {},
        success: function(data) {
            if(data.status == 'OK') {
                resetCards(true);
                $('#resetMembersCard').append('<p>Successfully reset all members!</p>');
                $('#resetMembersCard p').css("margin-top", "10px");
            }else if(data.status == 'error') {
                $('#error').html(data.error);
            }
        }
    });
}

function deleteMember() {
    var id = $('#membersList option:selected').val();
    var firstName = $('#memberDetails input[name=firstName]').val();
    var lastName = $('#memberDetails input[name=lastName]').val();
    
    $.ajax({
        url: '/eventpicker/phplib/deleteMember.php',
        method: 'post',
        dataType: 'json',
        data: {'id': id},
        success: function(data) {
            if(data.status == 'OK') {
                resetCards(true);
                $('#memberDetails').html('<p>Successfully deleted ' + firstName + ' ' + lastName + '!</p>');
            }else if(data.status == 'error') {
                $('#error').html(data.error);
            }
        }
    });
}

function updateMember() {    
    var id = $('#membersList option:selected').val();
    var firstName = $('#memberDetails input[name=firstName]').val();
    var lastName = $('#memberDetails input[name=lastName]').val();
    var setup = $('#memberDetails select[name=setup]').val();
    var cleanup = $('#memberDetails select[name=cleanup]').val();
    var monitor = $('#memberDetails select[name=monitor]').val();
    var drive =$('#memberDetails select[name=drive]').val();
    
    $.ajax({
        url: '/eventpicker/phplib/updateMember.php',
        method: 'post',
        dataType: 'json',
        data: {'id': id,
                'firstName': firstName,
                'lastName': lastName,
                'setup': setup,
                'cleanup': cleanup,
                'monitor': monitor,
                'drive': drive
        },
        success: function(data) {
            if(data.status == 'OK') {
                resetCards(true);
                $('#memberDetails').html('<p>Successfully updated ' + firstName + ' ' + lastName + '!</p>');
            }else if(data.status == 'error') {
                $('#error').html(data.error);
            }
        }
    });
}

function addMember() {    
    var firstName = $('#addMemberCard input[name=firstName]').val();
    var lastName = $('#addMemberCard input[name=lastName]').val();
    var setup = $('#addMemberCard select[name=setup]').val();
    var cleanup = $('#addMemberCard select[name=cleanup]').val();
    var monitor = $('#addMemberCard select[name=monitor]').val();
    var drive =$('#addMemberCard select[name=drive]').val();
    
    $.ajax({
        url: '/eventpicker/phplib/addMember.php',
        method: 'post',
        dataType: 'json',
        data: {'firstName': firstName,
                'lastName': lastName,
                'setup': setup,
                'cleanup': cleanup,
                'monitor': monitor,
                'drive': drive
        },
        success: function(data) {
            if(data.status == 'OK') {
                resetCards(true);
                $('#addMemberCard').append('<p>Successfully added ' + firstName + ' ' + lastName + '!</p>');
                $('#addMemberCard p').css("margin-top", "10px");
            }else if(data.status == 'error') {
                $('#error').html(data.error);
            }
        }
    });
}

function initMembersList() {
    $('#membersList').empty();
    
    $.ajax({
        url: '/eventpicker/phplib/getMembers.php',
        method: 'get',
        data: {},
        dataType: 'json',
        success: function(data) {
            if(data.status == 'OK') {
                if(data.memberCount < 12) {
                    $('#membersList').attr('size', data.memberCount);
                }

                $.each(data.members, function(index, member) {
                    $('#membersList').append($('<option>', {
                        value: member.id,
                        text: member.firstName + ' ' + member.lastName
                    }));
                });
            }else if(data.status == 'error') {
                $('#error').html(data.error);
            }
        }
    });
        
    $('#membersList').change(function() {
        var selectedMember = $('#membersList option:selected').val();
        
        $.ajax({
            url: '/eventpicker/phplib/getMemberDetails.php',
            method: 'get',
            data: {'id': selectedMember},
            dataType: 'json',
            success: function(data) {
                if(data.status == 'OK') {
                    // Generate data array
                    var titles = [
                        {'name': 'firstName', 'text': 'First Name: ', 'value': data.memberDetails.firstName},
                        {'name': 'lastName', 'text': 'Last Name: ', 'value': data.memberDetails.lastName},
                        {'name': 'setup', 'text': 'Set-Up: ', 'value': data.memberDetails.setup},
                        {'name': 'cleanup', 'text': 'Cleaned Up: ', 'value': data.memberDetails.cleanup},
                        {'name': 'monitor', 'text': 'Sober Monitored: ', 'value': data.memberDetails.monitor},
                        {'name': 'drive', 'text': 'Sober Drove: ', 'value': data.memberDetails.drive}
                    ];

                    resetCards(false);
                    $('#memberDetails').empty();

                    var i = 0;

                    // Insert text inputs
                    for(i; i < 2; i++) {
                        $('#memberDetails').append($('<label>', {
                            for: titles[i].name,
                            text: titles[i].text
                        }));
                        $('#memberDetails').append($('<input>', {
                            type: 'text',
                            size: '15',
                            name: titles[i].name,
                            value: titles[i].value
                        }));
                    }

                    // Insert select inputs
                    for(i; i < 6; i++) {
                        $('#memberDetails').append($('<label>', {
                            for: titles[i].name,
                            text: titles[i].text
                        }));
                        var $select = $('<select>', {
                            name: titles[i].name,
                        }).appendTo($('#memberDetails'));

                        $select.append([
                            $('<option>', {
                                value: 'Yes',
                                text: 'Yes'
                            }),
                            $('<option>', {
                                value: 'No',
                                text: 'No'
                            })
                        ]);

                        $select.val(titles[i].value);
                    }

                    // Insert breaks before labels to cleanup the form
                    $('<br>').insertBefore('#memberDetails label:not(:first-child)');

                    // Insert buttons to form
                    $('#memberDetails').append('<br><br><div class="spdbutton" id="updateButton" onclick="updateMember()">Update</div>');

                    $('#memberDetails').append('<br><div class="spdbutton" id="deleteButton" onclick="deleteMember()">Delete</div>');

                    // Create the jQuery buttons
                    $('.spdbutton').button();

                    // Make the filled out form aligned to the left
                    $('#memberDetails').css('text-align', 'left');
                }else if(data.status == 'error') {
                    $('#error').html(data.error);
                }
            }
        });
    });
}
    
function initAddMember() {
    $('#addMemberCard').empty();

    // Generate data array
    var titles = [
        {'name': 'firstName', 'text': 'First Name: '},
        {'name': 'lastName', 'text': 'Last Name: '},
        {'name': 'setup', 'text': 'Set-Up: '},
        {'name': 'cleanup', 'text': 'Cleaned Up: '},
        {'name': 'monitor', 'text': 'Sober Monitored: '},
        {'name': 'drive', 'text': 'Sober Drove: '}
    ];

    var i = 0;

    // Insert text inputs
    for(i; i < 2; i++) {
        $('#addMemberCard').append($('<label>', {
            for: titles[i].name,
            text: titles[i].text
        }));
        $('#addMemberCard').append($('<input>', {
            type: 'text',
            size: '15',
            name: titles[i].name,
            value: ''
        }));
    }

    // Insert select inputs
    for(i; i < 6; i++) {
        $('#addMemberCard').append($('<label>', {
            for: titles[i].name,
            text: titles[i].text
        }));
        var $select = $('<select>', {
            name: titles[i].name,
        }).appendTo($('#addMemberCard'));

        $select.append([
            $('<option>', {
                value: 'Yes',
                text: 'Yes'
            }),
            $('<option>', {
                value: 'No',
                text: 'No'
            })
        ]);

        $select.val('No');
    }

    // Insert breaks before labels to cleanup the form
    $('<br>').insertBefore('#addMemberCard label:not(:first-child)');

    // Insert buttons to form
    $('#addMemberCard').append('<br><br><div class="spdbutton" id="addButton" onclick="addMember()">Add Member</div>');

    // Create the jQuery buttons
    $('.spdbutton').button();

    // Make the filled out form aligned to the left
    $('#addMemberCard').css('text-align', 'left');
}