<?php
header('Content-type: text/plain');
ini_set('display_errors', 1);
error_reporting(E_ALL);

/* Read the config files to get the database name
TODO: the /var/www/ path should come out of config rather than being hardcoded*/
$output = `find -L /var/www/* -maxdepth 2 -name config\.php -exec echo {} \; -exec grep DBNAME {} \; -exec grep BASEDIR {} \; -exec grep ALTPLUGINDIR {} \;`;
$output = explode("\n", $output);
$jojodirs = $plugindirs = $altplugindirs = array();
$iMax = count($output);
$altmatch = array();
for ($i = 0; $i < $iMax - 1;) {
    if (!strpos($output[$i+1], 'DBNAME') || !preg_match("#define\(\W*'_BASEDIR',\W*'([^']*)'\W*\);#", $output[$i+2], $match) ) {
        $i+= 1;
        continue;
    }
    $dbname = trim(str_replace(array("define('_DBNAME', '", "define( '_DBNAME', '", "');"), '', $output[$i+1]));
    $plugindirs[$dbname] = dirname(realpath($output[$i])) . '_/plugins/';
    $jojodirs[$dbname] = rtrim($match[1], '/') . '/plugins/';
    preg_match("#define\(\W*'_ALTPLUGINDIR',\W*'([^']*)'\W*\);#", $output[$i+3], $altmatch);
    $altplugindirs[$dbname] =  $altmatch ? rtrim($altmatch[1], '/') . '/' : '';
    if ($altplugindirs[$dbname]) {
        $i+=4;
    } else {
        $i+=3;
    }
}

$plugins = array();
include('dbconnect.php');
$res = $db->query("SHOW DATABASES");
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

    if (!in_array('plugin', $tables) || $database == 'mysql') {
        /* Probably not a jojo site */
        continue;
    }
    $res2 = $db->query("SELECT trim(`name`) FROM `plugin` WHERE `active` = 'yes'");
    while ($row2 = $res2->fetch_array()) {
        if (!isset($plugindirs[$database])) {
            @$plugins[$row2[0]][] = $database . ' - inactive site?';
            continue;
        }

        if (file_exists($jojodirs[$database] . $row2[0])) {
            @$plugins[$row2[0]][] = $database . ' - core plugin - ' . $jojodirs[$database];
            continue;
        }

        if (file_exists($altplugindirs[$database] . $row2[0])) {
            @$plugins[$row2[0]][] = $database . ' - shared plugin - ' . $altplugindirs[$database];
            continue;
        }

        if (!file_exists($plugindirs[$database] . $row2[0])) {
            @$plugins[$row2[0]][] = $database . ' - *missing plugin*';
            continue;
        }

        if (!is_link($plugindirs[$database] . $row2[0])) {
            @$plugins[$row2[0]][] = $database . ' - local plugin - ' . $plugindirs[$database];
            continue;
        }

        $target = readlink($plugindirs[$database] . $row2[0]);
        @$plugins[$row2[0]][] = $database . ' - symlinked plugin - ' . $target;
    }
}

ksort($plugins);
foreach ($plugins as $plugin => $sites) {
    $title = count($sites) > 1 ? sprintf("%s (%d)", $plugin, count($sites)) : $plugin;
    echo $title . "\n" . str_repeat("-", strlen($title)) . "\n";
    asort($sites);
    foreach ($sites as $v) {
        echo "    $v\n";
    }
    echo "\n\n";
}

echo "Site Folders\n============\n";
ksort($plugindirs);
foreach ($plugindirs as $database => $dir) {
    echo str_pad($database . ':', 27, ' ', STR_PAD_RIGHT) . dirname($dir) . "\n";
}
