 <?php

//use this code at your own risk, not responsible for any problems with you DB or Dropbox if you set things up wrong or there is an unforeseen code conflict in this script!

# Include the Dropbox SDK libraries
require_once './common/lib/dropbox-sdk/lib/Dropbox/autoload.php';
use \Dropbox as dbx;

//your access token from the Dropbox App Panel
$accessToken = 'ONKIZcLnUBAAAAAAAAAAB8z48X7hn2KiBDLh8HM7xfszzyuIdOovFX0Y5yKONq6K';
echo 'Backup started '.date('Y-m-d h:i:s')."<br><br>";
//run the MySQL dump and zip;

// location of your temp directory
$tmpDir = __DIR__."/test/";
// username for MySQL
$user = "slistem";
// password for MySQL
$password = "smwXN2RTDm6Zz3hR";
// database name to backup
$dbName = "slistem";
// hostname or IP where database resides
$dbHost = "127.0.0.1";
// the zip file will have this prefix
$prefix = "sql_db";

// Create the database backup file
$sqlFile = $tmpDir.$prefix.date('Y_m_d_h:i:s').".sql";
$backupFilename = $prefix.".tgz";
$backupFile = $tmpDir.$backupFilename;

$createBackup = "mysqldump -h ".$dbHost." -u ".$user." --password='".$password."' ".$dbName." --> ".$sqlFile;
//echo $createBackup;
//echo '<br><br>';
//$createZip = "zip --password slateuppermainland $backupFile $sqlFile";
$createZip = "tar cvzf $backupFile $sqlFile";
//echo $createZip;
//echo '<br><br>';
//echo '<br><br>';
exec($createBackup);
exec($createZip);

//now run the DBox app info and set the client; we are naming the app folder SQL_Backup but CHANGE THAT TO YOUR ACTUAL APP FOLDER NAME;

$appInfo = dbx\AppInfo::loadFromJsonFile(__DIR__."/config.json");
$dbxClient = new dbx\Client($accessToken, "SQL_Backup");


//now the main handling of the zipped file upload;

//this message will send in a system e-mail from your cron job (assuming you set up cron to email you);
echo("Uploading $backupFilename to Dropbox").date('Y-m-d h:i:s')."<br><br>";

//this is the actual Dropbox upload method;
$f = fopen($backupFile, "rb");
$result = $dbxClient->uploadFile('/SQL_Backup/'.$backupFilename, dbx\WriteMode::force(), $f);
fclose($f);

// Delete the temporary files
unlink($sqlFile);
unlink($backupFile);

$to      = 'munir@slate-ghc.com';
$subject = 'Backup completed';
$message = 'Backup completed';
$headers = 'From: slistem@slate.co.jp' . "\r\n" .
    'Reply-To: munir@slate-ghc.com' . "\r\n" .
    'X-Mailer: PHP/' . phpversion();
$headers .= "CC: munir_anameric@hotmail.com\r\n";
//$headers .= "BCC: munir@slate-ghc.com\r\n";

mail($to, $subject, $message, $headers);

echo 'Backup uploaded successfully'.date('Y-m-d h:i:s');

?>
