<?php
    require('globalFunctions.php');

    $counts = array(
        'setup' => empty($_GET['setup']) ? 0 : $_GET['setup'],
        'cleanup' => empty($_GET['cleanup']) ? 0 : $_GET['cleanup'],
        'monitor' => empty($_GET['monitor']) ? 0 : $_GET['monitor'],
        'drive' => empty($_GET['drive']) ? 0 : $_GET['drive']
    );
    $counts['total'] = $counts['setup'] + $counts['cleanup'] + $counts['monitor'] + $counts['drive'];
    
    $status = 'OK';
    $error = '';
    $totalAvailable = 0;

    require('db_credentials.php');
    $mysqli = new mysqli($servername, $username, $password, $dbname);
    if ($mysqli->connect_error) {
        $status = 'error';
        $error = ' Failed to connect to database: ' . $mysqli->connect_error;
        errorAndDie();
    }

    $allMembers = getMembers();

    $initialAvailable = array(
        'setup' => getAvailableMembers('setup'),
        'cleanup' => getAvailableMembers('cleanup'),
        'monitor' => getAvailableMembers('monitor'),
        'drive' => getAvailableMembers('drive')
    );

    if(count($initialAvailable['setup']) < $counts['setup']) {
        $status = 'error';
        $error = ' Not enough people available to setup.';
        errorAndDie();
    }else if(count($initialAvailable['cleanup']) < $counts['cleanup']) {
        $status = 'error';
        $error = ' Not enough people available to cleanup.';
        errorAndDie();
    }else if(count($initialAvailable['monitor']) < $counts['monitor']) {
        $status = 'error';
        $error = ' Not enough people available to sober monitor.';
        errorAndDie();
    }else if(count($initialAvailable['drive']) < $counts['drive']) {
        $status = 'error';
        $error = 'Not enough people available to sober drive.';
        errorAndDie();
    }

    foreach($allMembers as $key => $val) {
        if(in_array($val['id'], $initialAvailable['setup']) or in_array($val['id'], $initialAvailable['cleanup']) or in_array($val['id'], $initialAvailable['monitor']) or in_array($val['id'], $initialAvailable['drive'])) {
            $totalAvailable += 1;
        }
    }

    if($totalAvailable < $counts['total']) {
        $status = 'error';
        $error = 'Not enough people available to fill all positions.';
        errorAndDie();
    }
    
    $actionOrder = orderActions($initialAvailable);

    $attempts = 0;
    $selectError = false;
    $selected = array();
    $chosen = array();
    $available;

    while(true) {
        $available = $initialAvailable;
        if($attempts > 3) {
            $status = 'error';
            $error = 'Could not select members after 3 attempts.';
            errorAndDie();
        }
        
        $selected = array();
        
        foreach($actionOrder as $action) {
            $selected[$action] = selectMembers($counts[$action], $action);
            if($selected[$action] == 'error') {$selectError = true; break;}
        }
        if($selectError) {$attempts += 1;}else{break;}
    }


    $json = array('status' => $status);

    commitSelection();
    $mysqli->close();

    $json = json_encode($json);

    echo $json;

    /* Returns array of IDs of all members in the database */
    function getMembers() {
        global $mysqli;
        
        $members = array();
        
        if($result = $mysqli->query("SELECT id, firstName, lastName FROM members ORDER BY id ASC")) {
            while($person = $result->fetch_assoc()) {
                array_push($members, array('id' => $person['id'],
                                           'firstName' => $person['firstName'],
                                           'lastName' => $person['lastName'])
                          );
            }
            $result->close();
        }
        
        return $members;
    }

    function orderActions($avail) {
        $list = array('setup' => count($avail['setup']),
                      'cleanup' => count($avail['cleanup']),
                      'monitor' => count($avail['monitor']),
                      'drive' => count($avail['drive'])
                     );
        
        asort($list);
        
        $actionOrder = array();
        
        foreach($list as $action => $count) {
            array_push($actionOrder, $action);
        }
        
        return $actionOrder;
    }

    function getAvailableMembers($action) {
        global $mysqli;
        
        $choices = array();
        
        $sql = "SELECT id FROM members WHERE $action='No'";
        $result = $mysqli->query($sql);

        while($person = $result->fetch_assoc()) {
            array_push($choices, $person['id']);
        }
        $result->close();
        
        return $choices;
    }

    function selectMembers($count, $action) {
        global $chosen;
        global $available;
        
        $list = array('setup', 'cleanup', 'monitor', 'drive');
        
        if(count($available[$action]) < $count) {
            $status = 'error';
            $error = ' Not enough people available to ' . $action . '.';
            errorAndDie();
        }
        
        $selected = array();
        
        for($count; $count > 0; $count--) {
            $random = $available[$action][array_rand($available[$action], 1)];
            while(in_array($random, $chosen)) {
                $random = $available[$action][array_rand($available[$action], 1)];
            }
            
            foreach($list as $temp) {
                if(in_array($random, $available[$temp])) {
                    unset($available[$temp][array_search($random, $available[$temp])]);
                }
            }
            
            array_push($chosen, $random);
            array_push($selected, $random);
        }
        
        return $selected;
    }

    function commitSelection() {
        global $mysqli;
        global $allMembers;
        global $selected;
        global $json;
        
        $list = array('setup', 'cleanup', 'monitor', 'drive');
        
        foreach($list as $action) {
            $json[$action] = array();
            $members = $selected[$action];
            foreach($members as $id) {
                $sql = "UPDATE members SET $action='Yes' WHERE id=$id";
                $mysqli->query($sql);
                $person = $allMembers[array_search($id, array_column($allMembers, 'id'))];
                array_push($json[$action], $person['firstName'] . ' ' . $person['lastName']);
            }
        }
    }
?>