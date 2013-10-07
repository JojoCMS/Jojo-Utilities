<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
set_time_limit(360);
?><!doctype html>
<html>
    <head>
    <!--
        Jojo Event log agregator
        Copyright (C) 2010 Mike Cochrane <mikec@mikenz.geek.nz>

        This program is free software; you can redistribute it and/or modify
        it under the terms of the GNU AFFERO General Public License as published by
        the Free Software Foundation; either version 3 of the License, or
        any later version.

        This program is distributed in the hope that it will be useful,
        but WITHOUT ANY WARRANTY; without even the implied warranty of
        MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
        GNU General Public License for more details.

        You should have received a copy of the GNU Affero General Public License
        along with this program; if not, write to the Free Software
        Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
        or see http://www.gnu.org/licenses/agpl.txt.
    -->
        <style type="text/css">
        table {
            border-collapse: collapse;
        }

        tr, td, th {
            border: 1px solid black;
        }

        td, th {
            padding: 4px;
        }

        footer {
            margin-top: 10px;
            font-weight: bold;
            text-align: center;
        }
        </style>
    </head>
    <body>
    <pre>Event Log Stats
===============

<strong>Number of Event log entries from the last 7 days
------------------------------------------------</strong>
<?php

/* Array of error message that are fixed and should be deleted on the way through */
$fixed = array();
$fixed[] = '';

$plugins = array();
include('dbconnect.php');
$res = $db->query("SHOW DATABASES");
$errors = array();
while($row = $res->fetch_assoc()) {
    /* Switch database */
    $database = $row['Database'];
    $db->select_db($database);

    /* Get the list of tables */
    $tables = array();
    $res2 = $db->query("SHOW TABLES");
    while ($row2 = $res2->fetch_array()) {
        $tables[] = $row2[0];
    }

    /* Get the webmaster email address */
    if (!in_array('eventlog', $tables)) {
        continue;
    }

    $res2 = $db->query('SELECT count(1) as num FROM `eventlog`');
    if (!$res2) {
        $res2 = $db->query('OPTIMIZE TABLE  `eventlog`');
        continue;
    }

    $row2 = $res2->fetch_array();
    if ($row2[0] == 0) {
        $res2 = $db->query('OPTIMIZE TABLE  `eventlog`');
        continue;
    }

    $res2 = $db->query('DELETE FROM `eventlog` WHERE el_datetime < FROM_UNIXTIME(UNIX_TIMESTAMP() - 7 * 86400)');
    $res2 = $db->query('OPTIMIZE TABLE  `eventlog`');

    foreach($fixed as $error) {
        if (!$error) {
            continue;
        }
        $stmt = $db->prepare("DELETE FROM `eventlog` WHERE el_desc=?");
        $stmt->bind_param('s', $error);
        $stmt->execute();
        $stmt->close();
    }

    $res2 = $db->query('SELECT count(1) as num FROM `eventlog`');
    if ($res2) {
        $row2 = $res2->fetch_array();
        echo $database . '.eventlog: ' . $row2[0] . "\n";
        flush();
    }
    if ($row2[0]) {
        echo "    ";
        $res2 = $db->query('SELECT el_code, count(1) as num FROM `eventlog` GROUP BY el_code ORDER BY el_code');
        while ($row2 = $res2->fetch_array()) {
            echo $row2[0] . ': ' . $row2[1] . '   ';
        }
        echo "\n\n";
    }

    $site = $db->query("SELECT op_value FROM `option` WHERE op_name = 'siteurl'");
    while ($result = $site->fetch_array()) {
        $siteurl = $result[0] . '/';
    }

    $res2 = $db->query("SELECT el_desc, count(1) as num, el_code, el_shortdesc, el_uri FROM `eventlog` WHERE (el_code = 'sql' OR el_code = 'PHP Error') AND el_datetime > FROM_UNIXTIME(UNIX_TIMESTAMP() - 7 * 86400) group by el_desc");
    if (!$res2) {
        continue;
    }
    while ($row2 = $res2->fetch_array()) {
        $error = $row2[0];
        if ($row2[2] == 'sql') {
            $error .= "\n" . $row2[3] . "\n";
        }
        $error .= "Example URI: " . $siteurl . $row2[4];
        $errors[$error] = isset($errors[$error]) ? $errors[$error] + $row2[1] : $row2[1];
    }
}
?>

<strong>Most Common PHP Errors in the last 7 days
-----------------------------------------</strong>
</pre>

        <table>
            <tr><th>Number</th><th>Error</th></tr>
<?php

arsort($errors);
foreach ($errors as $e => $n) {
    echo sprintf("            <tr>\n                <td>%s</td>\n                <td><pre>%s</pre></td>\n            </tr>\n", $n, $e);
}
?>
        </table>

    </body>
</html>
