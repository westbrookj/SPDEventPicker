function verifyLogin(location) {
    $.ajax({
        type: 'GET',
        url: '/eventpicker/phplib/verifyLogin.php',
        data: {'location': location},
        dataType: 'json',
        success: function(data) {
            if(data.status == 'OK') {
                if(data.redirect) {
                    window.location = data.redirect;
                }else {
                    insertHeader(data.user);
                    $('body').show();
                }
            }else if(data.status == 'error') {
                window.location = data.redirect;
            }
        }
    });
}

function insertHeader(user) {
    var header = '<nav class="navbar navbar-fixed-top">'
                    + '<ul class="nav navbar-nav">'
                        + '<li class="active"><a href="/eventpicker">Home</a></li>'
                        + '<li class="dropdown">'
                            + '<a href="#" class="dropdown-toggle" data-toggle="dropdown">Tool <span class="caret"></span></a>'
                            + '<ul class="dropdown-menu">'
                                + '<li><a href="/eventpicker/picker">Event Picker</a></li>'
                                + '<li><a href="/eventpicker/manage">Manage Members</a></li>'
                            + '</ul>'
                        + '</li>'
                        + '<li><a href="/eventpicker/about">About</a></li>'
                    + '</ul>'
                + '</nav>'
                + '<div id="loginButtonWrapper">'
                    + '<a href="/eventpicker/login" class="spdbutton" id="loginButton">Login</a>'
                + '</div>';

    $('#header').html(header);
    $('#loginButton').button();

    if(user != null) {
        $('#loginButtonWrapper').prepend('<p id="user">User : ' + user + '</p>');
        $('#loginButton').text('Logout');
        $('#loginButton').attr('href', '/eventpicker/phplib/logout.php');
        $('ul.dropdown-menu').append('<li><a href="/eventpicker/adduser">Add User</a></li>');
    }
}

function insertFooter() {
    $('.flexbox.root').append('<div id="footer" class="flexbox-footer"><p>&copy; Joshua Westbrook, 2018</p></div>');
}