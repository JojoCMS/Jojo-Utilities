<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
set_time_limit(360);

/* Safe mode doesn't make any changes, just outputs the SQL it would have run */
$safemode = false;

/* Only work if the page is being requested from one of these IP addresses */
$allowIPs = array('121.98.141.240');

/* Define Indexes that should be present */
$indexes['page'] = array(
                    'pg_url',
                    'pg_link',
                    'pg_parent',
                    'pg_mainnav',
                    'pg_mainnavalways',
                    'pg_secondarynav',
                    'pg_footernav',
                    'pg_order',
                    'pg_sitemapnav',
                    'pg_index',
                    'pg_status',
                    'pg_title',
                    array('pg_mainnav', 'pg_mainnavalways'),
                    array('pg_livedate', 'pg_expirydate')
                );

$indexes['fielddata'] = array(
                            'fd_table',
                            'fd_field'
                            );

$indexes['tabledata'] = array(
                            'td_name'
                            );

$indexes['article'] = array(
                            'ar_url',
                            'ar_category',
                            array('ar_livedate', 'ar_expirydate')
                            );

$indexes['articlecategory'] = array(
                            'pageid',
                            'ac_url',
                            );

$indexes['productcategory'] = array(
                            'pc_url',
                            'pc_parent',
                            );

$indexes['gallerycategory'] = array(
                            'pageid',
                            );

$indexes['groupcategory'] = array(
                            'gc_url',
                            );

$indexes['group'] = array(
                            'gp_category',
                            array('gp_livedate', 'gp_expirydate')
                            );

$indexes['articlecomment'] = array(
                            'ac_ip',
                            'ac_timestamp',
                            );

$indexes['option'] = array (
                            'op_category'
                            );

$indexes['eventcategory'] = array (
                            'pageid'
                            );

$indexes['language'] = array (
                            'active',
                            'languageid'
                            );

$indexes['lang_country'] = array (
                            'lc_code',
                            );

$indexes['tag'] = array (
                            'tg_tag'
                            );

$indexes['cart'] = array (
                            'actioncode'
                            );

$indexes['tag_item'] = array (
                            array('itemid', 'plugin')
                            );

$indexes['sessiondata'] = array (
                            'session_lastmodified'
                            );

$indexes['sessionData'] = array (
                            'session_lastmodified'
                            );

$indexes['collection'] = array (
                            'url',
                            array('livedate', 'expirydate'),
                            );

$indexes['ad'] = array (
                            array('ad_livedate', 'ad_expirydate'),
                            );

$indexes['ad_page'] = array (
                            array('adid', 'pageid'),
                            );

$indexes['label'] = array (
                            'url',
                            );

$indexes['garment'] = array (
                            'code',
                            );

$indexes['quote'] = array (
                            'qt_language',
                            array('qt_livedate', 'qt_expirydate'),
                            );

$indexes['product'] = array (
                            'pr_code',
                            'pd_category',
                            'pr_language',
                            array('pr_livedate', 'pr_expirydate'),
                            );

$indexes['plugin'] = array (
                            'active',
                            'name',
                            );

$indexes['theme'] = array (
                            'active',
                            );

$indexes['gallery3_image'] = array (
                            'gallery3id',
                            );

$indexes['gallery3'] = array (
                            'name',
                            'language',
                            'category',
                            );

$indexes['course'] = array (
                            'cr_category',
                            'cr_language',
                            array('cr_livedate', 'cr_expirydate'),
                            );

$indexes['coursecategory'] = array (
                            'ac_url',
                            );

$indexes['curriculum'] = array (
                            'cu_language',
                            array('cu_livedate', 'cu_expirydate'),
                            );

$indexes['baptist'] = array (
                            'bt_language',
                            'bt_surname',
                            array('bt_livedate', 'bt_expirydate'),
                            );

$indexes['profile'] = array (
                            'pr_language',
                            'pr_displayorder',
                            array('pr_livedate', 'pr_expirydate'),
                            );

$indexes['product_specs'] = array (
                            'sp_code',
                            'productid',
                            );

$indexes['invoices'] = array (
                            'project_id'
                            );

$indexes['visitor'] = array (
                            'vi_sessionid'
                            );

$indexes['teacher'] = array(
                            'tc_language',
                            array('tc_livedate', 'tc_expirydate')
                            );

$indexes['enrol_price'] = array(
                            'for',
                            );

$indexes['quote'] = array(
                            array('qt_livedate', 'qt_expirydate')
                            );

$indexes['phplist_message'] = array(
                            'status',
                            'embargo',
                            );

$indexes['order_line_items'] = array(
                            'order_id',
                            );

$indexes['transactions'] = array(
                            'unique_id',
                            );

$indexes['location'] = array(
                            'schoolID',
                            'region'
                            );

$indexes['school'] = array(
                            'is_visible',
                            );

$indexes['people'] = array(
                            'login',
                            );

$indexes['map'] = array(
                            array('mp_name', 'mapid'),
                            );

?><!doctype html>
<html>
    <head>
    <!--
        Jojo Database table index checker
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
    <pre><?php
//if (!isset($_SERVER['REMOTE_ADDR']) || !in_array($_SERVER['REMOTE_ADDR'], $allowIPs)) {
//    echo "Sorry, access denied";
//    exit;
//}

//$db = new mysqli('127.0.0.1', 'username', 'really strong password', 'anydatabase');
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

    /* Go through the tables */
    foreach ($tables as $t) {
        if (!isset($indexes[$t])) {
            /* De have no expected indexes for this table */
            continue;
        }
        echo "\n$database.$t\n";
        echo str_repeat('-', strlen("$database.$t")) . "\n";


        /* Get the existing indexes */
        $res2 = $db->query("SHOW INDEXES FROM `$t`");
        $existingIndexes = array();
        while ($row2 = $res2->fetch_array(MYSQL_ASSOC)) {
            if (!isset($existingIndexes[$row2['Key_name']])) {
                $existingIndexes[$row2['Key_name']] = $row2['Column_name'];
            } else {
                $existingIndexes[$row2['Key_name']] = (array)$existingIndexes[$row2['Key_name']];
                $existingIndexes[$row2['Key_name']][] = $row2['Column_name'];
            }
        }

        /* Check for any missing indexes */
        foreach ($indexes[$t] as $i) {
            echo "    Index on `" . implode((array)$i, '` and `') . '`';
            if (in_array($i, $existingIndexes)) {
                echo " <span style='color:green'>found</span>.\n";
                continue;
            }
            echo " <span style='color:orange'>missing</span>";
            $sql = "ALTER TABLE  `$t` ADD INDEX (`" . implode((array)$i, '`, `') . '`);';
            if ($safemode) {
                echo ".\n        <span style='color:blue'>$sql</span>\n";
            } else {
                $res2 = $db->query($sql);
                if ($res2) {
                    echo ", <span style='color:green'>added</span>.\n";
                } else {
                    echo ", <span style='color:red'>unable to add</span>.\n";
                }
            }
        }
    }
}
?>
    </pre>
    </body>
</html>