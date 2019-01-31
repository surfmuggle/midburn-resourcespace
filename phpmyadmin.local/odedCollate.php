<?php

$dbh = new PDO('mysql:host=midburn-jira-db.cwol69gtjljp.eu-west-1.rds.amazonaws.com;dbname=confluence', 'midburn_master', '2kwtb&vy');
$query = $dbh->query('SHOW TABLES');
print_r();

while ($row = $query->fetch(PDO::FETCH_COLUMN)) {
 // $sql[] = "ALTER TABLE `{$row}` CONVERT TO CHARACTER SET utf8 COLLATE utf8_unicode_ci;";
$sql[] = "ALTER TABLE `{$row}`  COLLATE utf8_bin;";

}

print implode("\n", $sql) . "\n";
