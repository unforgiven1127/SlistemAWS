<?php

require_once './common/lib/dropbox-sdk/lib/Dropbox/autoload.php';;
use \Dropbox as dbx;

$accessToken = 'ONKIZcLnUBAAAAAAAAAAB8z48X7hn2KiBDLh8HM7xfszzyuIdOovFX0Y5yKONq6K';

/* @var dbx\Client $client */
/* @var string $dropboxPath */
/* @var string $localPath */

$dropboxPath = "/SQL_Backup.zip";
$localPath = "/web/backup/SQL_Backup_".date("Y-m-d-His");

$pathError = dbx\Path::findErrorNonRoot($dropboxPath);
if ($pathError !== null) {
    echo("Invalid <dropbox-path>: $pathError\n");
    die;
}

$appInfo = dbx\AppInfo::loadFromJsonFile(__DIR__."/config.json");
$client = new dbx\Client($accessToken, "SQL_Backup");

$metadata = $client->getFile($dropboxPath, fopen($localPath, "wb"));
if ($metadata === null) {
    echo("File not found on Dropbox.\n");
    die;
}

echo "Backup downloaded successfully <br><br> Metadata: ";
print_r($metadata);
echo "<br><br>";
echo "File contents written to \"$localPath\"\n";