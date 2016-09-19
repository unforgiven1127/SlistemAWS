<?php
require_once 'htmlpurifier/library/HTMLPurifier.auto.php';

define('URL_FORMAT','_^(?:(?:https?|ftp)://)(?:\S+(?::\S*)?@)?(?:(?!10(?:\.\d{1,3}){3})(?!127(?:\.\d{1,3}){3})(?!169\.254(?:\.\d{1,3}){2})(?!192\.168(?:\.\d{1,3}){2})(?!172\.(?:1[6-9]|2\d|3[0-1])(?:\.\d{1,3}){2})(?:[1-9]\d?|1\d\d|2[01]\d|22[0-3])(?:\.(?:1?\d{1,2}|2[0-4]\d|25[0-5])){2}(?:\.(?:[1-9]\d?|1\d\d|2[0-4]\d|25[0-4]))|(?:(?:[a-z\x{00a1}-\x{ffff}0-9]+-?)*[a-z\x{00a1}-\x{ffff}0-9]+)(?:\.(?:[a-z\x{00a1}-\x{ffff}0-9]+-?)*[a-z\x{00a1}-\x{ffff}0-9]+)*(?:\.(?:[a-z\x{00a1}-\x{ffff}]{2,})))(?::\d{2,5})?(?:/[^\s]*)?$_iuS');

function array_search_multi($needle, $haystack)
{
  for($i = 0, $l = count($haystack); $i < $l; ++$i)
  {
    $bResult = strpos($haystack[$i],$needle);
    if($bResult !== false)
    return $i;
  }
  return false;
}

function array_first($pavArray)
{
  if(!assert('is_array($pavArray)'))
    return null;

  //PHP >= 5.4
  //return array_values($pavArray)[0];

  foreach($pavArray as $vData)
    return $vData;
}
function array_first_key($pavArray)
{
  if(!assert('is_array($pavArray)'))
    return null;

  foreach($pavArray as $vkey => $vData)
    return $vkey;
}


function is_date($psDate, $psFormat = 'Y-m-d', $pbStrict = true)
{
  if(empty($psDate))
  {
    if($pbStrict)
      return false;
    else
      return true;
  }

  //bad format and date = 0000-00-00 00:00:00
  $nTime = strtotime($psDate);
  if(!$nTime)
    return false;

  if(empty($psFormat))
    $psFormat = 'Y-m-d';

  if($psDate == date($psFormat, $nTime))
    return true;

  return false;
}

//alias of previous for easier asserts
function is_datetime($psDate, $pbStrict = true)
{
  return is_date($psDate, 'Y-m-d H:i:s', $pbStrict);
}


function getDateDifference($psStartDate, $psEndDate = '', $pbCloserScale = true, $psFormat = 'd')
{
  if(!assert('!empty($psStartDate) && is_bool($pbCloserScale)'))
    return '-';

  if(empty($psEndDate))
    $psEndDate = date('Y-m-d H:i:s');

  $asFormat = array('s' => 'sec', 'i' => 'min', 'h' => 'h', 'd' => 'd', 'm' => 'mth', 'y' => 'y');

  if(!isset($asFormat[$psFormat]))
    return '-';

  $oStartDate = date_create($psStartDate);
  $oEndDate = date_create($psEndDate);

  if(!$oStartDate || !$oStartDate)
    return '-';

  $oInterval = date_diff($oStartDate, $oEndDate);
  if(!$oInterval)
    return '-';

  if($pbCloserScale)
  {
    $sValue = $oInterval->format('%y,%m,%d,%h,%i,%s');
    $asValue = explode(',', $sValue);

    if($asValue[0] > 0)
    {
      if($asValue[1] > 2)
        return ((int)$asValue[0]).' ~ '.((int)$asValue[0]+1).' years';
      else
        return (int)$asValue[0].' year'.((((int)$asValue[0]) > 1)? 's':'');
    }

    if($asValue[1] > 0)
    {
      if($asValue[2] > 5)
        return ((int)$asValue[1]).'~'.((int)$asValue[1] + 1).' months';
      else
        return (int)$asValue[1].' month'.((((int)$asValue[1]) > 1)? 's':'');
    }

    if($asValue[2] > 0)
    {
      return (int)$asValue[2].' day'.((((int)$asValue[2]) > 1)? 's':'');
    }

    if($asValue[3] > 0)
    {
      return (int)$asValue[3].' hr';
    }

    if($asValue[4] > 0)
    {
      return (int)$asValue[4].' min';
    }

    return $asValue[5].' sec';
  }

  return $oInterval->format('%'.$psFormat).$asFormat[$psFormat];
}


function is_key($pnPk)
{
  if((empty($pnPk)) || (!is_integer($pnPk)) || ($pnPk < 1))
    return false;

  return true;
}

function is_integerString($psString, $pnMinLength = 1, $pnMaxLength = PHP_INT_SIZE, $pnMax = PHP_INT_MAX)
{
  if(preg_match('/^[0-9]{'.$pnMinLength.','.$pnMaxLength.'}$/', $psString) === false)
    return false;

  if((int)$psString > $pnMax)
    return false;

  return true;
}

function is_listOfInt($psList, $psDelimiter = ',')
{
  if(!assert('!empty($psDelimiter)') || empty($psList))
    return false;

  if(is_array($psList))
    return false;

  $asList = explode($psDelimiter, $psList);
  if(empty($asList) || !is_array($asList))
    return false;

  foreach($asList as $sInteger)
  {
    if((int)$sInteger != trim($sInteger))
      return false;
  }

  return true;
}

function is_arrayOfInt($pasArray)
{
  if(!is_array($pasArray) || empty($pasArray))
    return false;

  foreach($pasArray as $sInteger)
  {
    if((string)((int)$sInteger) != trim($sInteger))
      return false;
  }

  return true;
}
function cast_arrayOfInt($pasArray)
{
  if(!assert('is_array($pasArray)') || empty($pasArray))
    return $pasArray;

  foreach($pasArray as $vKey => $vValue)
  {
    $pasArray[$vKey] = (int)$vValue;
  }

  return $pasArray;
}

function is_cpUid($psUid)
{
  if(!assert('!empty($psUid) && is_string($psUid)'))
    return false;

  return true;
}

function is_cpAction($psAction)
{
  if(!assert('is_string($psAction)'))
    return false;

  return true;
}

function is_cpType($psType)
{
 if(!assert('is_string($psType)'))
   return false;

 return true;
}

function is_cpPk($pnPk)
{
  if (!assert('(is_integer($pnPk)) && ($pnPk>=0)'))
    return false;

  return true;
}

function is_cpValues($avValues)
{
  if(!is_array($avValues) || count($avValues) < 4)
    return false;

  if (!is_cpUid($avValues[CONST_CP_UID]) || !is_cpAction($avValues[CONST_CP_ACTION]) || !is_cpType($avValues[CONST_CP_TYPE]) || !is_cpPk($avValues[CONST_CP_PK]))
    return false;

  return true;
}

function is_cpValuesInPost()
{
  $sCpUid=getValue(CONST_CP_UID);
  $sCpAction=getValue(CONST_CP_ACTION);
  $sCpType=getValue(CONST_CP_TYPE);
  $sCpPk=(int)getValue(CONST_CP_PK);

  if  (
         (is_string($sCpType) && !empty($sCpType))
      && (is_string($sCpAction) && !empty($sCpAction))
      && (is_string($sCpUid) && !empty($sCpUid))
      && (is_key($sCpPk))
      )
    return true;

  return false;
}

function getCpValuesFromPost()
{
  $sCpUid=getValue(CONST_CP_UID);
  $sCpAction=getValue(CONST_CP_ACTION);
  $sCpType=getValue(CONST_CP_TYPE);
  $nCpPk=(int)getValue(CONST_CP_PK);

    if  (
         (is_string($sCpType) && !empty($sCpType))
      && (is_string($sCpAction) && !empty($sCpAction))
      && (is_string($sCpUid) && !empty($sCpUid))
      && (is_key($nCpPk))
      )
    return array(
        CONST_CP_UID => $sCpUid,
        CONST_CP_ACTION => $sCpAction,
        CONST_CP_TYPE => $sCpType,
        CONST_CP_PK => $nCpPk
        );
    else
      return array();
}

function displayAssert($psFile, $pnLine, $psMessage)
{
  //limit at  100 asserts. More than that is a loop (maybe infinite)
  if($_SESSION['assert'] > 100)
     return false;

  $_SESSION['assert']++;

  echo '<div class="assert" style="width:100%;">
        <div>
          <strong>Assert spotted in '.$psFile.' line '.$pnLine.": <br /> \n".$psMessage." <br /> \n</strong>
        </div>";

  $asDebug = debug_backtrace();

  foreach($asDebug as $asLine)
  {
    echo ' -> ';

    if(isset($asLine['file']))
      echo 'called in '.$asLine['file'].' / ';

    echo $asLine['function'].' ';

    if(isset($asLine['file']))
      echo 'line '.$asLine['line'].' ';

    echo '<br />';
  }

  echo '</div>';
}

function logAssert($psFile, $pnLine, $psMessage)
{
  //limit at  100 asserts. More than that is a loop (maybe infinite)
  if($_SESSION['assert'] > 100)
     return false;

  $_SESSION['assert']++;

  $sText = date('Y-m-d H:i:s')."\n".'Assert spotted in '.$psFile.' line '.$pnLine.": \n".$psMessage." \n ";

  $asDebug = debug_backtrace();

  foreach($asDebug as $asLine)
  {
    $sText.= ' -> ';

    if(isset($asLine['file']))
      $sText.= 'called in '.$asLine['file'].' / ';

    $sText.= $asLine['function'].' ';

    if(isset($asLine['file']))
      $sText.= 'line '.$asLine['line'].' ';

    //echo 'with['.implode(' | ', $asLine['args']).'] <br />';
    $sText.= "\n";
  }

  $sText.= "\n\n\n".'Url: '.$_SERVER['REQUEST_METHOD'].' - '.$_SERVER['SERVER_PROTOCOL'].'://'.$_SERVER['SERVER_NAME'].'/'.$_SERVER['REQUEST_URI'];
  $sText.= "\n\n".'POST: '.  var_export($_POST, true);
  $sText.= "\n\n".'SESSION: '. getAssertSessionDump("\n");

  $sText.= "\n";

  $oFs = fopen(CONST_DEBUG_ASSERT_LOG_PATH, 'a+');
  if($oFs)
  {
    fwrite($oFs, $sText);
    fclose($oFs);

    //rotate file if size > 2Mo
    if(filesize(CONST_DEBUG_ASSERT_LOG_PATH) > (2*1024*1024))
    {
      copy(CONST_DEBUG_ASSERT_LOG_PATH, CONST_DEBUG_ASSERT_LOG_PATH.'-'.time());
    }
  }
}

function mailAssert($psFile, $pnLine, $psMessage)
{
  //limit 5 assert email per page, to avoid infinite loops
  if($_SESSION['mail_assert'] > 5)
     displayAssert($psFile, $pnLine, $psMessage);

  $_SESSION['mail_assert']++;

  //limit at  100 asserts. More than that is a loop (maybe infinite)
  if($_SESSION['assert'] > 100)
     return false;

  $_SESSION['assert']++;

  $sAssert = '<div style="width:100%;">
        <div>
          <strong>Assert spotted in '.$psFile.' line '.$pnLine.": <br /> \n".$psMessage." <br /> \n</strong>
        </div>";

  $asDebug = debug_backtrace();

  foreach($asDebug as $asLine)
  {
    $sAssert.= ' -> ';

    if(isset($asLine['file']))
      $sAssert.= 'called in '.$asLine['file'].' / ';

    $sAssert.= $asLine['function'].' ';

    if(isset($asLine['file']))
      $sAssert.= 'line '.$asLine['line'].' ';

    $sAssert.= '<br />';
  }

  $sAssert.= '</div>';


  $sAssert.= '<br /><br /><br />Url: '.$_SERVER['REQUEST_METHOD'].' - '.$_SERVER['SERVER_PROTOCOL'].'://'.$_SERVER['SERVER_NAME'].'/'.$_SERVER['REQUEST_URI'];
  $sAssert.= '<br /><br />POST: '.  var_export($_POST, true);
  $sAssert.= '<br /><br />SESSION: '.  getAssertSessionDump();


  try
  {
    $bSent = false;
    imap_timeout(IMAP_OPENTIMEOUT, 10);
    $oMail =  imap_open('{mail.bulbouscell.com:143/debug/readonly/imap/tls/novalidate-cert}inbox', 'bcm@bulbouscell.com', 'AB1gOne!', OP_HALFOPEN, 0);

    if($oMail)
    {
      $sHeaders = 'MIME-Version: 1.0' . "\r\n" . 'Content-type: text/html; charset=utf-8' . "\r\n";
      $sHeaders.= 'From: '.CONST_CRM_MAIL_SENDER . "\r\n" . 'Reply-To: '.CONST_CRM_MAIL_SENDER . "\r\n" . 'X-Mailer: PHP/' . phpversion();

      ini_set ("SMTP", "mail.bulbouscell.com");
      $bSent = imap_mail(CONST_DEV_EMAIL, 'BCM assertion', $sAssert, $sHeaders);

      if($bSent && CONST_DEV_EMAIL_2 != '' && CONST_DEV_EMAIL != CONST_DEV_EMAIL_2)
        $bSent = imap_mail(CONST_DEV_EMAIL_2, 'BCM assertion', $sAssert, $sHeaders);

      imap_close($oMail);
    }
  }
  catch(Exception $oEx)
  {
    displayAssert($psFile, $pnLine, $psMessage);
  }

  if(!$bSent)
    displayAssert($psFile, $pnLine, $psMessage);
}

function getAssertSessionDump($psSeparator = '<br />', $pasArray = null, $pnIndent = 0)
{
  $asToIgnore = array('menunav1','menunav2','menunav3','user_rights','user_data_rights','searchHistory','debug');
  $sText = '';

  if($pasArray === null)
  {
    $pasArray = $_SESSION;
    $pasArray = array_reverse($pasArray, true);
  }

  $sIndent = '';
  for($nCount=0; $nCount < $pnIndent; $nCount++)
    $sIndent.= '&nbsp;&nbsp;&nbsp;';

  foreach((array)$pasArray as $sName => $vValue)
  {
    if(!in_array($sName, $asToIgnore))
    {
      if(is_array($vValue) && !empty($vValue))
        $sText.=  $psSeparator.$sIndent.'['.$sName.'] = '.getAssertSessionDump($psSeparator, $vValue, ++$pnIndent);
      else
        $sText.= $psSeparator.$sIndent.'['.$sName.'] = '.var_export($vValue, true).' ';
    }
  }

  return $sText;
}


/*function dump($pvVar, $psLabel=null, $psDisplay=true)
{
  // format the label
  $psLabel = ($psLabel===null) ? '' : rtrim($psLabel) . ' ';

  // var_dump the variable into a buffer and keep the output
  ob_start();
  var_dump($pvVar);
  $output = ob_get_clean();

  // neaten the newlines and indents
  $output = preg_replace("/\]\=\>\n(\s+)/m", "] => ", $output);

  $output = '<pre>'
  . $psLabel
  . htmlspecialchars($output, ENT_QUOTES)
  . '</pre>';

  if($psDisplay)
    echo '<div class="dump" style="padding-left: 100px;">'.$output.'</div>';

  return '<div class="dump" style="padding-left: 100px;">'.$output.'</div>';
}*/

function dev_dump($var, $label=null, $echo=true)
{
  if($_SESSION['userData']['loginpk'] == 1 || $_SESSION['userData']['loginpk'] == 5)
    return dump($var, $label, $echo);
}

function debugLine($pvVariable, $pnLine = 0, $psFile = '', $pbHtml = true)
{
  if($pbHtml)
  {
    echo '<hr />line '.$pnLine.' '.$psFile.':<br /><pre>';
    dump($pvVariable);
    echo '</pre><br />';
  }
  else
  {
    echo "-------------------------------\nline '.$pnLine.' '.$psFile.':\n";
    dump($pvVariable);
    echo "\n";
  }

}

function _live_dump($pvTrace, $psTitle = null)
{
  $sIp = $_SERVER['REMOTE_ADDR'];
  if($sIp == '127.0.0.1' || $sIp == '118.243.81.245')
  {
    if($psTitle)
      echo '| -> '.$psTitle.'<br />';

    dump($pvTrace);
  }

  return true;
}

  /**
   * get the value of a superglobal field
   * Control if isset, as default value, and manage post, get, session, cookie
   */
  function getValue($psVarName, $pvDefaultValue = '', $psSpecificVar = '', $pbStoreInSession = false)
  {

    $avDataSrc = array();

    if(!empty($psSpecificVar))
    {
      switch(strtolower($psSpecificVar))
      {
        case 'post':
          $avDataSrc = $_POST;
          break;

        case 'get':
          $avDataSrc = $_GET;
          break;

        case 'session':
          $avDataSrc = $_SESSION;
          break;

        case 'cookie':
          $avDataSrc = $_COOKIE;
          break;

        case 'redis':
          if(check_redis_key($psVarName))
            return $GLOBALS['redis']->get($psVarName);
          break;

        default:
          assert('false; // wrong type');
          return '';

      }

      if(empty($avDataSrc) || !isset($avDataSrc[$psVarName]))
      {
        if($pbStoreInSession)
          $_SESSION[$psVarName] = $pvDefaultValue;

        return $pvDefaultValue;
      }
      else
      {
        if($pbStoreInSession)
          $_SESSION[$psVarName] = $avDataSrc[$psVarName];

        return $avDataSrc[$psVarName];
      }

    }

    //try to fetch the value from POSt, then GEt, then Session, then cookie

    if(isset($_POST[$psVarName]))
    {
      if($pbStoreInSession)
          $_SESSION[$psVarName] = $_POST[$psVarName];

      return $_POST[$psVarName];
    }

    if(isset($_GET[$psVarName]))
    {
      if($pbStoreInSession)
          $_SESSION[$psVarName] = $_GET[$psVarName];

      return $_GET[$psVarName];
    }

    if(isset($_SESSION[$psVarName]))
    {
      return $_SESSION[$psVarName];
    }

    if(isset($_COOKIE[$psVarName]))
    {
      if($pbStoreInSession)
        $_SESSION[$psVarName] = $_COOKIE[$psVarName];

      return $_COOKIE[$psVarName];
    }

    if(check_redis_key($psVarName))
    {
      $temp_var = $GLOBALS['redis']->get($psVarName);

      return $temp_var;
    }

    return $pvDefaultValue;
  }

  function save_to_redis($key_name, $value, $expiration = 0)
  {
    if ($expiration > 0)
    {
      $GLOBALS['redis']->set($key_name, $value);
      $GLOBALS['redis']->setTimeout($key_name, $expiration);
    }
    else
      return $GLOBALS['redis']->set($key_name, $value);
  }

  function delete_from_redis($key_name)
  {
    return $GLOBALS['redis']->delete($key_name);
  }

  function check_redis_key($key_name)
  {
    return $GLOBALS['redis']->exists($key_name);
  }

  function getFormatedDate($psFormat, $psDate = '')
  {
    if(!assert('!empty($psFormat)'))
      return '';

    if(empty($psDate))
      return date($psFormat);

    $psDate = trim($psDate);

    switch($psFormat)
    {
      case 'Y-m-d':
        $asDate = explode(' ', $psDate);
        $asDate[0] = str_replace('/', '-', $asDate[0]);
        return $asDate[0];
        break;

      case 'Y-m-d H:i:s':
        $asDate = explode(' ', $psDate);
        $asDate[0] = str_replace('/', '-', $asDate[0]);

        if(!isset($asDate[1]) || empty($asDate[1]))
        $asDate[1] = '00:00:00';

        return implode(' ', $asDate);
        break;

      default:
        assert('formatedDate doesn\'t know this format. ');
        break;
    }
  }

  function getRelativeUploadPath($sPath)
  {
    return str_ireplace($_SERVER['DOCUMENT_ROOT'], '', $sPath);
  }


  function makePath($psPath)
  {
    if(!assert('is_string($psPath) && !empty($psPath)'))
      return false;

    //remove filename if present
    $asPath = pathinfo($psPath);
    if(isset($asPath['extension']))
      $psPath = $asPath['dirname'];

    // be sure we won't try to create forlders outside the website directory
    // if given a absolute path
    $psPath = str_replace($_SERVER['DOCUMENT_ROOT'], '', $psPath);
    if(!$psPath)
    {
      assert('false; // path is not valid.');
      return false;
    }

    if(!mkdir($_SERVER['DOCUMENT_ROOT'].'/'.$psPath, 0755, true))
    {
      assert('false; // couldn\'t create directories.');
      return false;
    }

    return true;
  }

  function isValidEmail($psString, $pbStrict = true)
  {
    if($pbStrict && empty($psString))
      return false;

    if(!$pbStrict && empty($psString))
      return true;

    return filter_var($psString, FILTER_VALIDATE_EMAIL) !== false;
  }


  function isEmptyArray($paArray)
  {
    if(!assert('is_array($paArray)'))
      return false;

    if(empty($paArray))
      return true;

    foreach($paArray as $vValue)
    {
      if(!empty($vValue))
        return false;

      if(is_array($vValue))
      {
        if(!isEmptyArray($vValue))
          return false;
      }
    }

    return true;
  }

  function formatUrl($psString)
  {
    $psString = trim($psString);

    if(empty($psString) || strlen($psString) < 4)
      return '';

    $asUrl = parse_url($psString);

    //based on scheme presence in the url, the hostname goes in path or host
    if(!$asUrl || ( (!isset($asUrl['host']) || empty($asUrl['host'])) && (!isset($asUrl['path']) || empty($asUrl['path'])) ))
      return '';

    if(!isset($asUrl['port']) || empty($asUrl['port']))
      $asUrl['port'] = '';

    $bHasScheme = true;
    if(!isset($asUrl['scheme']) || empty($asUrl['scheme']))
    {
      $bHasScheme = false;

      switch($asUrl['port'])
      {
        case 443:
          $asUrl['scheme'] = 'https'; break;

        case 22:
          $asUrl['scheme'] = 'ssh'; break;

        case 21:
          $asUrl['scheme'] = 'ftp'; break;

        default:
          $asUrl['scheme'] = 'http'; break;
      }
    }

    if( ($bHasScheme && substr_count($asUrl['host'], '.') == 1) || (!$bHasScheme && substr_count($asUrl['path'], '.') == 1))
    {
      if($bHasScheme)
        $asUrl['host'] = 'www.'.$asUrl['host'];
      else
        $asUrl['path'] = 'www.'.$asUrl['path'];
    }

    //rebuild url from update array
    $sUser = isset($asUrl['user']) ? $asUrl['user'] : '';
    $sPass = isset($asUrl['pass']) ? ':' . $asUrl['pass']  : '';
    $sPass = ($sUser || $sPass) ? $sPass.'@' : '';

    $sUrl = isset($asUrl['scheme']) ? $asUrl['scheme'] . '://' : '';
    $sUrl.= $sUser.$sPass;

    //no port if we can't detect the hostname
    if(isset($asUrl['host']) && !empty($asUrl['host']))
    {
      $sUrl.= isset($asUrl['host']) ? $asUrl['host'] : '';
      $sUrl.= isset($asUrl['port']) && !empty($asUrl['port']) ? ':' . $asUrl['port'] : '';
    }

    $sUrl.= isset($asUrl['path']) ? $asUrl['path'] : '';
    $sUrl.= isset($asUrl['query']) ? '?' . $asUrl['query'] : '';
    $sUrl.= isset($asUrl['fragment']) ? '#' . $asUrl['fragment'] : '';
    return $sUrl;
  }

  /**
   * Return an integer to indicate if the url is correct.
   * 1: ok, 0: bad format url, -1: when we can't reach the destination
   * @param string $psString
   * @param boolean $pbFormatbefore
   * @param boolean $pbLiveTestUrl
   * @return integer
   */
  function isValidUrl($psString, $pbFormatbefore = false, $pbLiveTestUrl = false)
  {
    $psString = trim($psString);
    if(empty($psString) || strlen($psString) < 4)
      return false;

    if($pbFormatbefore)
    {
      $psString = formatUrl($psString);
    }

    //remove the starting www. from the url
    $psString = preg_replace('|^([a-z]{3,6}://)?(www\.)?(.*)|i', '$1$3', $psString);

    //Add http:// if no protocole has been defined
    $sScheme = strtolower(substr($psString, 0, 6));
    if(!in_array($sScheme, array('http:/','https:', 'ftp://', 'ftps:/', 'nfs://', 'smtp:/', 'imap:/', 'xmpp:/', 'lpd://', 'smb://', 'samba:')))
      $psString = 'http://'.$psString;

    //apply the perfect regexp (better than filter_var)  http://mathiasbynens.be/demo/url-regex
    $nResult = (int)preg_match(URL_FORMAT, $psString);

    if($nResult > 0)
      return 1;

    if(checkUrlAvailability($psString))
      return 1;

    return 0;
  }

  function checkUrlAvailability($psUrl)
  {
    if(empty($psUrl) || strlen($psUrl) < 4)
      return false;

    $oCurl = curl_init($psUrl);
    curl_setopt($oCurl,  CURLOPT_RETURNTRANSFER, true);
    curl_setopt($oCurl,  CURLOPT_FAILONERROR, true);
    curl_setopt($oCurl,  CURLOPT_FRESH_CONNECT, true);
    curl_setopt($oCurl,  CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($oCurl,  CURLOPT_TIMEOUT, 3);
    curl_setopt($oCurl,  CURLOPT_CONNECTTIMEOUT, 3);
    curl_setopt($oCurl,  CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($oCurl,  CURLOPT_AUTOREFERER, true);

    /* Get the HTML or whatever is linked to the $url. */
    curl_exec($oCurl);

    /* Check for 404 (file not found). */
    $nHttpCode = (int)curl_getinfo($oCurl, CURLINFO_HTTP_CODE);
    curl_close($oCurl);

    if($nHttpCode >= 200 && $nHttpCode < 300)
      return true;

    return false;
  }


  function isDevelopment()
  {
    if(isset($_GET['set_debug']))
    {
      $_SESSION['debug'] = (int)$_GET['set_debug'];
    }

    if(CONST_DEV_SERVER || getValue('debug, 0') || !empty($_SESSION['debug']))
      return true;

    return false;
  }


  function getEventTypeList($pbOnlyValues = false, $psType = '', $is_admin = false)
  {

    if(CONST_WEBSITE == 'slistem')
    {

      if($pbOnlyValues)
      {
        if(empty($psType) || $psType == CONST_CANDIDATE_TYPE_CANDI)
        {
          $asEvent['character'] = 'character';
          $asEvent['note'] = 'note';
          $asEvent['email'] = 'email';
          $asEvent['meeting'] = 'meeting';
          $asEvent['phone'] = 'phone';
          $asEvent['update'] = 'update';
          $asEvent['update'] = 'update';
          $asEvent['cp_history'] = 'cp_history';
        }
        if(empty($psType) || $psType == CONST_CANDIDATE_TYPE_COMP)
        {
          $asEvent['note'] = 'note';
          $asEvent['email'] = 'email';
          $asEvent['phone'] = 'phone';
          $asEvent['update'] = 'update';
          $asEvent['description'] = 'description';
        }
      }
      else
      {
        if(empty($psType) || $psType == CONST_CANDIDATE_TYPE_CANDI)
        {
          $asEvent['character'] = array('label' => 'Character note', 'value' => 'character', 'group' => '');
          $asEvent['note'] = array('label' => 'Note', 'value' => 'note', 'group' => '');
          $asEvent['email'] = array('label' => 'Email', 'value' => 'email', 'group' => '');
          $asEvent['meeting'] = array('label' => 'Meeting', 'value' => 'meeting', 'group' => '');
          $asEvent['phone'] = array('label' => 'Phone call', 'value' => 'phone', 'group' => '');
          $asEvent['update'] = array('label' => 'Update', 'value' => 'update', 'group' => '');
          if ($is_admin)
            $asEvent['cp_history'] = array('label' => 'Company history', 'value' => 'cp_history', 'group' => '');
        }

        if(empty($psType) || $psType == CONST_CANDIDATE_TYPE_COMP)
        {
          $asEvent['note'] = array('label' => 'Note', 'value' => 'note', 'group' => '');
          $asEvent['email'] = array('label' => 'Email', 'value' => 'email', 'group' => '');
          $asEvent['phone'] = array('label' => 'Phone call', 'value' => 'phone', 'group' => '');
          $asEvent['update'] = array('label' => 'Update', 'value' => 'update', 'group' => '');
          $asEvent['description'] = array('label' => 'Description', 'value' => 'Company description', 'group' => '');
        }
      }
    }
    else
    {
      if($pbOnlyValues)
      {
        $asEvent[] = 'article';
        $asEvent[] = 'email';
        $asEvent[] = 'meeting';
        $asEvent[] = 'phone';
        $asEvent[] = 'update';
        $asEvent[] = 'deal';
        $asEvent[] = 'proposal';
        $asEvent[] = 'invoice';
        $asEvent[] = 'payment';
        $asEvent[] = 'info';
        $asEvent[] = 'it-project';
      }
      else
      {
        $asEvent['article'] = array('label' => 'Article', 'value' => 'article', 'group' => 'Info');
        $asEvent['email'] = array('label' => 'Email', 'value' => 'email', 'group' => 'Info');
        $asEvent['meeting'] = array('label' => 'Meeting', 'value' => 'meeting', 'group' => 'Info');
        $asEvent['phone'] = array('label' => 'Phone call', 'value' => 'phone', 'group' => 'Info');
        $asEvent['update'] = array('label' => 'Update', 'value' => 'update', 'group' => 'Info');
        $asEvent['info'] = array('label' => 'General Info', 'value' => 'info', 'group' => 'Info');
        $asEvent['deal'] = array('label' => 'Deal', 'value' => 'deal', 'group' => 'Business');
        $asEvent['proposal'] = array('label' => 'Proposal', 'value' => 'proposal', 'group' => 'Business');
        $asEvent['invoice'] = array('label' => 'Invoice', 'value' => 'invoice', 'group' => 'Finance');
        $asEvent['payment'] = array('label' => 'Payment', 'value' => 'payment', 'group' => 'Finance');
        $asEvent['it-project'] = array('label' => 'IT project', 'value' => 'it-project', 'group' => 'IT');
      }
    }

    return $asEvent;
  }

  function getCompanyRelation($pnPk=0)
  {
    $asCompany = array(1 =>array('Label'=>'Client','icon'=>'client.png','icon_small'=>'client_small.png', 'style' => ''),
        2 =>array('Label'=>'Supplier','icon'=>'supplier.png','icon_small'=>'supplier_small.png', 'style' => ''),
        3 =>array('Label'=>'Candidate','icon'=>'candidate.png','icon_small'=>'candidate_small.png', 'style' => ''),
        4 =>array('Label'=>'Collaborator','icon'=>'collaborator.png','icon_small'=>'collaborator_small.png', 'style' => ''),
        5 =>array('Label'=>'Prospect','icon'=>'prospect.png','icon_small'=>'prospect_small.png', 'style' => ''),
        6 =>array('Label'=>'Prospect (temp)','icon'=>'prospect.png','icon_small'=>'prospect_small.png', 'style' => 'color: #D4662A;'));

    if($pnPk)
     return $asCompany[$pnPk];
    else
     return $asCompany;
  }


  function getContactGrade($pnPk=0)
  {
    $asGrade = array('1'=>'Top decision maker', '2'=>'Mid management, senior staff', '4'=>'Staff, Junior ', '5'=>'Unknown');
    if($pnPk)
      return $asGrade[$pnPk];
    else
      return $asGrade;
  }


  function isUidAvailable($psUid)
  {
      $asDependency = array('database'=>'124-546','page'=>'845-187','display'=>'569-741','form'=>'668-313','login'=>'579-704'
          , 'addressbook'=>'777-249','project'=>'456-789','sharedspace'=>'999-111','search'=>'898-775','pager'=>'140-510',
          'event'=>'007-770','webmail'=>'009-724','mail'=>'008-724','querybuilder'=>'210-482');
      if(in_array($psUid,$asDependency))
          return true;
      else
         return false;
  }

  //================================================
  //SearchHistory functions
  function manageSearchHistory($psGuid='', $psType='', $pbForceNew = false)
  {
    //starts with maintenance: keep session array size under 50 for every component/type
    if(isset($_SESSION['searchHistory'][$psGuid][$psType]) && count($_SESSION['searchHistory'][$psGuid][$psType]) > 50)
    {
      $_SESSION['searchHistory'][$psGuid][$psType] = array_reverse($_SESSION['searchHistory'][$psGuid][$psType], true);
      //array_pop($_SESSION['searchHistory']);
      array_pop($_SESSION['searchHistory'][$psGuid][$psType]);
      $_SESSION['searchHistory'][$psGuid][$psType] = array_reverse($_SESSION['searchHistory'][$psGuid][$psType], true);
    }

    //to prevent dev errors, let's check lower case name too
    $sSearchId = getValue('searchId', 0);

    if(empty($sSearchId))
      $sSearchId = getValue('searchid', 0);

    //Create a new id and save current post/get datas (undefined too prevent javascript errors)
    if($pbForceNew || empty($sSearchId) || $sSearchId == 'undefined' || !isset($_SESSION['searchHistory'][$psGuid][$psType][$sSearchId]) || empty($_SESSION['searchHistory'][$psGuid][$psType][$sSearchId]))
    {
      $sSearchId = uniqid('search_');
      $_SESSION['searchHistory'][$psGuid][$psType][$sSearchId]['post'] = $_POST;
      $_SESSION['searchHistory'][$psGuid][$psType][$sSearchId]['get'] = $_GET;
      $_SESSION['searchHistory'][$psGuid][$psType][$sSearchId]['sortfield'] = '';
      $_SESSION['searchHistory'][$psGuid][$psType][$sSearchId]['sortorder'] = '';
      return $sSearchId;
    }

    global $gbNewSearch;
    $gbNewSearch = false;

    //store sorting and pager options
    $sSortField = getValue('sortfield');
    $sSortOrder = getValue('sortorder');
    $nPageOffset = (int)getValue('pageoffset', 0);
    $nNbResult = (int)getValue('nbresult', 0);

    if(!empty($sSortField))
    {
      if(!empty($sSortOrder))
      {
        $_SESSION['searchHistory'][$psGuid][$psType][$sSearchId]['sortfield'] = $sSortField;
        $_SESSION['searchHistory'][$psGuid][$psType][$sSearchId]['sortorder'] = $sSortOrder;
      }
      else
      {
        //check for sort order in history
        if($sSortField == $_SESSION['searchHistory'][$psGuid][$psType][$sSearchId]['sortfield'])
        {
          if($_SESSION['searchHistory'][$psGuid][$psType][$sSearchId]['sortorder'] == 'ASC')
            $_SESSION['searchHistory'][$psGuid][$psType][$sSearchId]['sortorder'] = 'DESC';
          else
            $_SESSION['searchHistory'][$psGuid][$psType][$sSearchId]['sortorder'] = 'ASC';
        }
        else
        {
          $_SESSION['searchHistory'][$psGuid][$psType][$sSearchId]['sortfield'] = $sSortField;
          $_SESSION['searchHistory'][$psGuid][$psType][$sSearchId]['sortorder'] = 'ASC';
        }
      }
    }

    //update the page number and page result
    if($nPageOffset)
      $_SESSION['searchHistory'][$psGuid][$psType][$sSearchId]['post']['pageoffset'] = $nPageOffset;

    if($nNbResult)
      $_SESSION['searchHistory'][$psGuid][$psType][$sSearchId]['post']['nbresult'] = $nNbResult;


    //we ve got an id: overwrite current POST/GET data with saved ones
    //exclude sortfield if passed in parameters
    foreach($_SESSION['searchHistory'][$psGuid][$psType][$sSearchId]['post'] as $sParamName => $sParamValue)
    {
      if(substr($sParamName, 0, 2) != '__' && empty($sSortField) || $sParamName != 'sortfield')
        $_POST[$sParamName] = $sParamValue;
    }

    foreach($_SESSION['searchHistory'][$psGuid][$psType][$sSearchId]['get'] as $sParamName => $sParamValue)
    {
      if(substr($sParamName, 0, 2) != '__' && empty($sSortField) || $sParamName != 'sortfield')
        $_GET[$sParamName] = $sParamValue;
    }

    //restore the new offset if erased just above
    /*$_POST['pageoffset'] = $nPageOffset;
    $_POST['nbresult'] = $nNbResult;*/

    return $sSearchId;
  }

  function saveSearchHistorySql($psSearchId, $psSql, $psGuid='', $psType='')
  {
    if(empty($psSearchId) || empty($psSql))
      return false;

    if(!isset($_SESSION['searchHistory'][$psGuid][$psType][$psSearchId]))
      return false;

    $_SESSION['searchHistory'][$psSearchId]['sql'] = $psSql;
    return true;
  }

  function getSearchHistory($psSearchId, $psGuid='', $psType='')
  {
    if(empty($psSearchId))
      return array();

    if(!isset($_SESSION['searchHistory'][$psGuid][$psType][$psSearchId]))
      return array();

    return $_SESSION['searchHistory'][$psGuid][$psType][$psSearchId];
  }

  function setSearchHistory($asHistoryData, $psSearchId, $psGuid='', $psType='')
  {
    if(empty($psSearchId) || !is_array($asHistoryData))
      return false;

    if(!isset($_SESSION['searchHistory'][$psGuid][$psType][$psSearchId]))
      return false;

    $_SESSION['searchHistory'][$psGuid][$psType][$psSearchId] = $asHistoryData;
    return true;
  }

  function reloadLastSearch($psGuid = '', $psType = '')
  {
    $sId = getLastSearchId($psGuid, $psType);
    $_POST['searchId'] = $sId;

    manageSearchHistory($psGuid, $psType);
      return $sId;
   }

  function getLastSearchId($psGuid='', $psType='')
  {
    if(!isset($_SESSION['searchHistory'][$psGuid][$psType]))
      return '';

    $asSearchId = array_keys($_SESSION['searchHistory'][$psGuid][$psType]);
      return end($asSearchId);
  }

  function getLanguageLevel($pnLanguagelevel)
  {
    $asLangArray =   array(0 => 'None',1 =>'Basic',2 => 'Conversational',3 =>'Business',4 => 'Fluent',5 =>'Native');
    return  $asLangArray[$pnLanguagelevel];
  }

  function showHideSearchForm($psSetTime, $psType)
  {
    $oPage = CDependency::getCpPage();

    if(!isset($_SESSION['storetime'][$psType]))
      $_SESSION['storetime'][$psType] = 0;

    if(!empty($psSetTime) && ($_SESSION['storetime'][$psType] != $psSetTime))
    {
      $_SESSION['storetime'][$psType] = $psSetTime;
      $sJavascript = " $(document).ready(function(){ $('.searchContainer').show(); }); ";
      $oPage->addCustomJs($sJavascript);
    }
    else
    {
      $sJavascript = " $(document).ready(function(){ $('.searchContainer').hide(); }); ";
      $oPage->addCustomJs($sJavascript);
     }
   }

   function curPageURL()
   {
      $sPageURL = 'http';
      if(isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on") {$sPageURL.= "s";}
      $sPageURL .= "://";
      if($_SERVER["SERVER_PORT"] != "80")
      {
        $sPageURL.= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
      }
      else
      {
        $sPageURL.= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
      }
     return $sPageURL;
  }



  //========================================================================================================
  //========================================================================================================
  // String related functions


  //Check for kanji
  function isKanji($sStr)
  {
     return preg_match('/[\x{4E00}-\x{9FBF}]/u', $sStr) > 0;
  }

  //Check for Hiragana
  function isHiragana($sStr)
  {
     return preg_match('/[\x{3040}-\x{309F}]/u', $sStr) > 0;
  }

  //Check for all japanese
  function isJapanese($sStr)
  {
    return isKanji($sStr) || isHiragana($sStr) || isKatakana($sStr);
  }

  //Check for Katakana
  function isKatakana($sStr)
  {
    return preg_match('/[\x{30A0}-\x{30FF}]/u', $sStr) > 0;
  }

  function isCJK($sStr)
  {
    if(preg_match('/[\x{1100}-\x{11FF}]/u', $sStr) == 1)
      return true;

    if(preg_match('/[\x{2E80}-\x{D7FF}]/u', $sStr) == 1)
      return true;

    if(preg_match('/[\x{20000}-\x{2B73F}]/u', $sStr) == 1)
      return true;

    if(preg_match('/[\x{2F800}-\x{2FA1F}]/u', $sStr) == 1)
      return true;

    return false;
  }

  function cutString($psStringToCut, $pnLetters = 25, $psCutedEol = ' ...')
  {
    if(isJapanese($psStringToCut))
      return $psStringToCut;

    $nLength = strlen($psStringToCut);
    if($nLength <= $pnLetters)
      return $psStringToCut;

    return substr($psStringToCut, 0, ($pnLetters-strlen($psCutedEol))).$psCutedEol;
  }

  function cutStringByWords($psStringToCut, $pnWords = 5)
  {
    if(isJapanese($psStringToCut))
      return $psStringToCut;

    $asString = explode(' ', $psStringToCut);
    if(count($asString) <= $pnWords)
      return $psStringToCut;

    $asString = array_slice($asString, 0, $pnWords);
    return implode(' ', $asString);
  }


  function array_trim($pasArray, $pbRemoveEmpty = false, $pbUnique = false)
  {
    if(!assert('is_array($pasArray)'))
      return array();

    foreach($pasArray as $vKey => $vValue)
    {
      if(!is_object($vValue) || !is_array($vValue))
      {
        $vValue = trim($vValue);
        if($pbRemoveEmpty && empty($pbRemoveEmpty))
          unset($pasArray[$vKey]);
        else
          $pasArray[$vKey] = $vValue;
      }
    }

    if($pbUnique)
      return array_unique($pasArray);

    return $pasArray;
  }



  /**
   * Check a string an return the "language" it contains en/cjk/mix
   * If strict it check if the string is 100% en or CJK, if not a specific ratio is applied
   *
   * @param string $psFileContent
   * @param boolean $pbStrict
   * @return string
   */
  function getTextLangType($psFileContent, $pbStrict = false)
  {
    $nLength = (int)mb_strlen($psFileContent);

    if(empty($psFileContent) || empty($nLength))
      return 'mix';

    //remove number working for both
    $sEnText = preg_replace('/[0-9\x{1100}-\x{11FF}\x{2E80}-\x{D7FF}\x{20000}-\x{2B73F}\x{2F800}-\x{2FA1F}]/u', '', $psFileContent);

    $nEnText = (int)mb_strlen($sEnText);
    $nCjkText = (($nLength-$nEnText) /3);   //1 CJK char length = 3, so i need to balance it
    $nTotal = $nEnText + $nCjkText;
    $fRatio = ($nEnText / $nTotal);
    //dump($nTotal);  dump($nEnText);  dump($fRatio);

    if($pbStrict)
    {
      if($fRatio == 1)
        return 'en';
      elseif($fRatio == 0)
        return 'cjk';

      return 'mix';
    }


    if($fRatio > 0.95)
      return 'en';

    //japanese is meant to contain some english words, at least abreviation or signs
    if($fRatio < 0.15)
      return 'cjk';

    return 'mix';
  }


  //========================================================================================================
  //========================================================================================================

  function countDays($year, $month, $ignore) {
    $count = 0;
    $counter = mktime(0, 0, 0, $month, 1, $year);
    while (date("n", $counter) == $month) {
        if (in_array(date("w", $counter), $ignore) == false) {
            $count++;
        }
        $counter = strtotime("+1 day", $counter);
    }
    return $count;
  }

  function getWorkingDays($startDate,$endDate,$holidays)
  {
    // do strtotime calculations just once
    $endDate = strtotime($endDate);
    $startDate = strtotime($startDate);


    //The total number of days between the two dates. We compute the no. of seconds and divide it to 60*60*24
    //We add one to inlude both dates in the interval.
    $days = ($endDate - $startDate) / 86400 + 1;

    $no_full_weeks = floor($days / 7);
    $no_remaining_days = fmod($days, 7);

    //It will return 1 if it's Monday,.. ,7 for Sunday
    $the_first_day_of_week = date("N", $startDate);
    $the_last_day_of_week = date("N", $endDate);

    //---->The two can be equal in leap years when february has 29 days, the equal sign is added here
    //In the first case the whole interval is within a week, in the second case the interval falls in two weeks.
    if ($the_first_day_of_week <= $the_last_day_of_week) {
        if ($the_first_day_of_week <= 6 && 6 <= $the_last_day_of_week) $no_remaining_days--;
        if ($the_first_day_of_week <= 7 && 7 <= $the_last_day_of_week) $no_remaining_days--;
    }
    else {
        // (edit by Tokes to fix an edge case where the start day was a Sunday
        // and the end day was NOT a Saturday)

        // the day of the week for start is later than the day of the week for end
        if ($the_first_day_of_week == 7) {
            // if the start date is a Sunday, then we definitely subtract 1 day
            $no_remaining_days--;

            if ($the_last_day_of_week == 6) {
                // if the end date is a Saturday, then we subtract another day
                $no_remaining_days--;
            }
        }
        else {
            // the start date was a Saturday (or earlier), and the end date was (Mon..Fri)
            // so we skip an entire weekend and subtract 2 days
            $no_remaining_days -= 2;
        }
    }

    //The no. of business days is: (number of weeks between the two dates) * (5 working days) + the remainder
//---->february in none leap years gave a remainder of 0 but still calculated weekends between first and last day, this is one way to fix it
   $workingDays = $no_full_weeks * 5;
    if ($no_remaining_days > 0 )
    {
      $workingDays += $no_remaining_days;
    }

    //We subtract the holidays
    foreach($holidays as $holiday){
        $time_stamp=strtotime($holiday);
        //If the holiday doesn't fall in weekend
        if ($startDate <= $time_stamp && $time_stamp <= $endDate && date("N",$time_stamp) != 6 && date("N",$time_stamp) != 7)
            $workingDays--;
    }

    return $workingDays;
  }

  function getCharacterNotes($candidate_id)
  {
    $oDB = CDependency::getComponentByName('database');
    $sDate = date('Y-m-d H:i:s');
    $sDate = strtotime($sDate.' -1 years');
    $sDate = date('Y-m-d',$sDate);
    $sDate .= " 00:00:00";

    $sQuery = "SELECT * FROM event e INNER JOIN event_link el on el.eventfk = e.eventpk WHERE el.cp_pk = '".$candidate_id."' AND e.type in('character') AND e.date_create > '".$sDate."'  AND LENGTH(e.content) >= 100";

    $db_result = $oDB->executeQuery($sQuery);

    $result = $db_result->getAll();

    return $result;

  }

  function getCandidateActiveMeetings($candidate_id)
  {
    $oDB = CDependency::getComponentByName('database');

    $sQuery = "SELECT * FROM sl_meeting l WHERE l.candidatefk = '".$candidate_id."' AND l.meeting_done = '0'";

    $db_result = $oDB->executeQuery($sQuery);

    $result = $db_result->getAll();

    return $result;
  }

  function getCompletedMeetings($candidate_id)
  {
    $oDB = CDependency::getComponentByName('database');

    $sQuery = "SELECT * FROM sl_meeting l WHERE l.candidatefk = '".$candidate_id."' AND l.meeting_done = '1'";

    $db_result = $oDB->executeQuery($sQuery);

    $result = $db_result->getAll();

    return $result;
  }

  function getMeetingInformation($meeting_id)
  {
    $oDB = CDependency::getComponentByName('database');

    $sQuery = "SELECT * FROM sl_meeting l WHERE l.sl_meetingpk = ".$meeting_id;

    $db_result = $oDB->executeQuery($sQuery);

    $read = $db_result->readFirst();

    while ($read)
    {
      $row = $db_result->getData();
      $read = $db_result->readNext();
    }

    return $row;
  }

  function get_objectives_in_play($user_id, $start_date, $end_date)
  {
    $oDB = CDependency::getComponentByName('database');
    $new_in_play_info = array();

    $user_info = getUserInformaiton($user_id);
    $group = strtolower($user_info['position']);

    $add = " ";
    if($group == 'researcher')
    {
      $add = " AND m.meeting_done = 1 ";
    }

    // gets new_candidates_in_play START
    $query = 'SELECT min(pl2.sl_position_linkpk) as min_date_position, pl.sl_position_linkpk, pl.created_by as pl_created_by ,m.*, min(m2.sl_meetingpk) as min_date, pl.status as pl_status, pl.active as pl_active, slc._sys_status as candidate_status
        ,pl.date_completed , pl.date_created as ccm_create_date
        FROM sl_meeting m
        INNER JOIN sl_meeting m2 ON m2.candidatefk = m.candidatefk
        INNER JOIN sl_position_link pl ON pl.candidatefk = m.candidatefk
        INNER JOIN sl_candidate slc on slc.sl_candidatepk = m.candidatefk AND slc._sys_status = 0
        INNER JOIN sl_position_link pl2 ON pl2.candidatefk = pl.candidatefk
        WHERE pl.date_completed >= "'.$start_date.'"
        AND pl.date_completed <= "'.$end_date.'"
        AND pl.status = 51
        AND pl.active = 0
        AND pl2.status = 51
        AND pl2.active = 0
        AND slc._sys_status = 0
        '.$add.'
        group by pl.candidatefk, pl.positionfk
        order by m.candidatefk';


    /*$query = 'SELECT m.*, min(m2.sl_meetingpk) as min_date, pl.status as pl_status, pl.active as pl_active
        FROM sl_meeting m
        INNER JOIN sl_meeting m2 ON m2.candidatefk = m.candidatefk
        INNER JOIN sl_position_link pl ON pl.candidatefk = m.candidatefk
        WHERE m.created_by = "'.$user_id.'"
        AND m.date_created >= "2016-05-01 00:00:00"
        AND m.date_created < "2016-05-31 23:59:59"
        AND m.meeting_done = 1
        AND pl.status > 51
        AND pl.active != 1
        group by m.sl_meetingpk
        order by m.candidatefk';*/

    $oDbResult = array();

/*echo 'new candi in play: ';
var_dump($query);
echo '<br><br>';*/

    $oDbResult = $oDB->executeQuery($query);
    $read = $oDbResult->readFirst();

    while($read)
    {
      $temp = $oDbResult->getData();

      $create_date = strtotime($temp['ccm_create_date']);
      $date_completed = strtotime($temp['date_completed']);

      $diff = $date_completed - $create_date;
      $diff = floor($diff/(60*60*24)); // gun cinsinden veriyor..

      if($group == 'researcher')
      {
        $user = $temp['created_by'];
      }
      else
      {
        $user = $temp['pl_created_by'];
      }

      if($temp['min_date'] == $temp['sl_meetingpk'] && $temp['min_date_position'] == $temp['sl_position_linkpk'] && $temp['meeting_done'] == 1 && $temp['pl_status'] >= 51 && $temp['pl_active'] == 0 && $diff < 180)
      {
        if(isset($new_in_play_info[$user]['new_candidates']))
        {
          array_push($new_in_play_info[$user]['new_candidates'], $temp);
        }
        else
        {
          $new_in_play_info[$user]['new_candidates'] = array();
          array_push($new_in_play_info[$user]['new_candidates'], $temp);
        }
        //$asData[$temp['created_by']] = $temp;
      }
      $read = $oDbResult->readNext();
    }
    //var_dump($new_in_play_info[314]['new_candidates']);
    // gets new_candidates_in_play END

    // gets new_positions_in_play START

    $query = 'SELECT m.*, min(m2.sl_meetingpk) as min_date, pl.status as pl_status, pl.active as pl_active, pl.created_by as pl_created_by, pl.sl_position_linkpk,
        min(pl2.sl_position_linkpk) as min_date_position, pl.positionfk as positionfk, slc._sys_status as candidate_status
        ,pl.date_completed , pl.date_created as ccm_create_date
        FROM sl_meeting m
        INNER JOIN sl_candidate slc on slc.sl_candidatepk = m.candidatefk AND slc._sys_status = 0
        INNER JOIN sl_meeting m2 ON m2.candidatefk = m.candidatefk
        INNER JOIN sl_position_link pl ON pl.candidatefk = m.candidatefk
        INNER JOIN sl_position_link pl2 ON pl2.positionfk = pl.positionfk
        WHERE pl.date_completed >= "'.$start_date.'"
        AND pl.date_completed <= "'.$end_date.'"
        AND pl.status = 51
        AND pl.active = 0
        AND pl2.status = 51
        AND pl2.active = 0
        '.$add.'
        AND slc._sys_status = 0
        group by pl.candidatefk, pl.positionfk
        order by m.candidatefk';

    /*$query = 'SELECT m.*, min(m2.sl_meetingpk) as min_date, pl.status as pl_status, pl.active as pl_active, pl.sl_position_linkpk,
        min(pl2.sl_position_linkpk) as min_date_position, pl.positionfk as positionfk
        FROM sl_meeting m
        INNER JOIN sl_meeting m2 ON m2.candidatefk = m.candidatefk
        INNER JOIN sl_position_link pl ON pl.candidatefk = m.candidatefk
        INNER JOIN sl_position_link pl2 ON pl2.positionfk = pl.positionfk
        WHERE m.created_by = "'.$user_id.'"
        AND m.date_created >= "2016-05-01 00:00:00"
        AND m.date_created < "2016-05-31 23:59:59"
        AND pl.status = 51
        AND pl.active != 1
        AND pl2.status = 51
        AND pl2.active != 1
        group by m.sl_meetingpk
        order by m.candidatefk';*/

    $oDbResult = array();
/*echo '<br><br>';echo '<br><br>';
echo 'new position in play: ';
var_dump($query);*/

//exit;
    $oDbResult = $oDB->executeQuery($query);
    $read = $oDbResult->readFirst();

    while($read)
    {
      $temp = $oDbResult->getData();

      $create_date = strtotime($temp['ccm_create_date']);
      $date_completed = strtotime($temp['date_completed']);

      $diff = $date_completed - $create_date;
      $diff = floor($diff/(60*60*24)); // gun cinsinden veriyor..

      if($group == 'researcher')
      {
        $user = $temp['created_by'];
      }
      else
      {
        $user = $temp['pl_created_by'];
      }

      if($temp['min_date'] == $temp['sl_meetingpk'] && $temp['min_date_position'] == $temp['sl_position_linkpk'] && $temp['pl_status'] == 51 && $temp['pl_active'] == 0 && $diff < 180)
      {
        if(isset($new_in_play_info[$user]['new_positions']))
        {
          array_push($new_in_play_info[$user]['new_positions'], $temp);
        }
        else
        {
          $new_in_play_info[$user]['new_positions'] = array();
          array_push($new_in_play_info[$user]['new_positions'], $temp);
        }
        //$asData[$temp['created_by']] = $temp;
      }
      $read = $oDbResult->readNext();
    }
    // gets new_positions_in_play END

    return $new_in_play_info;
  }

  function get_objectives_new_candidate_met($user_id, $start_date, $end_date)
  {
    $oDB = CDependency::getComponentByName('database');
    $user_ids = array($user_id);

    $user_info = getUserInformaiton($user_id);
    $group = strtolower($user_info['position']);

    //$asData = array();

    $query = 'SELECT m.*, min(m2.sl_meetingpk) as min_date, slc._sys_status as candidate_status
        FROM sl_meeting m
        INNER JOIN sl_meeting m2 on m2.candidatefk = m.candidatefk and m2.meeting_done = 1
        INNER JOIN sl_candidate slc on slc.sl_candidatepk = m.candidatefk AND slc._sys_status = 0
        WHERE m.created_by IN ('.implode(',', $user_ids).')
        AND m.date_met >= "'.$start_date.'"
        AND m.date_met < "'.$end_date.'"
        group by m.sl_meetingpk
        order by m.candidatefk';


    $oDbResult = array();

    $oDbResult = $oDB->executeQuery($query);
    $read = $oDbResult->readFirst();

    while($read)
    {
      $temp = $oDbResult->getData();

      if(!isset($asData[$temp['created_by']]))
      {
        $asData[$temp['created_by']] = array();
      }

      if($temp['min_date'] == $temp['sl_meetingpk'] && $temp['meeting_done'] == 1)
      {
        array_push($asData[$temp['created_by']], $temp);

        //$asData[$temp['created_by']] = $temp;
      }
      $read = $oDbResult->readNext();
    }

    $count = 0;
    foreach ($asData[$user_id] as $key => $value) {
      $count++;
    }


    //$count = count($asData[$user_id]);

    return $count;
  }

  function get_not_happened_meetings($user_id, $start_date, $end_date)
  {
    $oDB = CDependency::getComponentByName('database');
    $user_ids = array($user_id);

    $user_info = getUserInformaiton($user_id);
    $group = strtolower($user_info['position']);

    //$asData = array();

    $query = 'SELECT m.*, min(m2.sl_meetingpk) as min_date, slc._sys_status as candidate_status
        FROM sl_meeting m
        LEFT JOIN sl_meeting m2 on m2.candidatefk = m.candidatefk and m2.meeting_done = 1
        INNER JOIN sl_candidate slc on slc.sl_candidatepk = m.candidatefk AND slc._sys_status = 0
        WHERE (m.created_by IN ('.implode(',', $user_ids).') OR m.attendeefk IN ('.implode(',', $user_ids).') )
        AND m.date_created >= "'.$start_date.'"
        AND m.date_created <= "'.$end_date.'"
        AND m.meeting_done = "0"
        group by m.sl_meetingpk
        order by m.candidatefk';
//echo '<br><br>';
//var_dump($query);

    $oDbResult = array();

    $oDbResult = $oDB->executeQuery($query);
    $read = $oDbResult->readFirst();
//echo '<br><br>';
    while($read)
    {
      $temp = $oDbResult->getData();
//var_dump($temp);
//echo '<br><br>';
      if(!isset($asData[$user_id]))
      {
        $asData[$user_id] = array();
      }
      if($temp['min_date'] == null || $temp['min_date'] == $temp['sl_meetingpk'])
      {
        array_push($asData[$user_id], $temp);

        //$asData[$temp['created_by']] = $temp;
      }
      $read = $oDbResult->readNext();
    }

    $count = 0;
    foreach ($asData[$user_id] as $key => $value) {
      $count++;
    }

    return $count;
  }

  function get_target_to_date()
  {
    $year = date('Y'); // 2016
    $month = date('n'); // 5 not with zero
    $day = date('j'); // 8 not with zero
    $work_days = countDays($year, $month, array(0, 6));

    $_month = date('m');
    $_day = date('d');
    $_start_date = $year."-".$_month."-01";
    $_end_date = $year."-".$_month."-".$_day;

    $totalDay = getWorkingDays($_start_date,$_end_date,"");

    $met_temp = (27 / $work_days)*$totalDay;
    $play_temp = (7 / $work_days)*$totalDay;
    $position_temp = (5 / $work_days)*$totalDay;

    $array['met_target'] = round($met_temp);
    $array['in_play_target'] = round($play_temp);
    $array['position_target'] = round($position_temp);

    return $array;
  }

  function get_percent($in_play_candidate, $in_play_position, $new_met)
  {
    $array['met_percent'] = round($new_met * 100 / 27);
    $array['play_percent'] = round($in_play_candidate * 100 / 7);
    $array['position_percent'] = round($in_play_position * 100 / 5);

    return $array;
  }

  function create_objectives_table($in_play_candidate, $in_play_position, $new_met)
  {
    $targets = get_target_to_date();
    $percents = get_percent($in_play_candidate, $in_play_position, $new_met);

    $table = "<div style='height: 240px; width: 450px;  margin: 0 auto;'>
        <div class='obj-container'>
          <div class='obj-row obj-header'>
            <div class='obj-desc'></div>
            <div class='obj-value'>Met *</div>
            <div class='obj-value'>In play</div>
            <div class='obj-value'>Positions **</div>
          </div>

          <div class='obj-row'>
            <div class='obj-desc'>Month target</div>
            <div class='obj-value'>27</div>
            <div class='obj-value'>7</div>
            <div class='obj-value'>5</div>
          </div>

          <div class='obj-row'>
            <div class='obj-desc'>Target to date</div>
            <div class='obj-value'>".$targets['met_target']."</div>
            <div class='obj-value'>".$targets['in_play_target']."</div>
            <div class='obj-value'>".$targets['position_target']."</div>
          </div>

          <div class='obj-row'>
            <div class='obj-desc'>Current</div>
            <div class='obj-value obj-bad'>".$new_met."</div>
            <div class='obj-value obj-bad'>".$in_play_candidate."</div>
            <div class='obj-value obj-bad'>".$in_play_position."</div>
          </div>

          <div class='obj-row'>
            <div class='obj-desc'>%</div>
            <div class='obj-value obj-bad'>".$percents['met_percent']."%</div>
            <div class='obj-value obj-bad'>".$percents['play_percent']."%</div>
            <div class='obj-value obj-bad'>".$percents['position_percent']."%</div>
          </div>
        </div>

        <div class='portal-legend'>
          <span style='color: #888; font-style: italic; font-size: 11px;'>* Meeting created with the new meeting feature. </span><br>
          <span style='color: #888; font-style: italic; font-size: 11px;'>** Newly active positions, having their first CCM. </span>
        </div>
        </div>";

    return $table;
  }

  function create_new_candidate_table($user_short_name,$months,$candidate_in_plays)
  {
    $table = "

      <div style='height: 240px; width: 450px;  margin: 0 auto;' id='mainPageCandi' data-highchartsc-chart='0'>
        <div class='highchartsc-container' id='highchartsc-0' style='position: relative; overflow: hidden; width: 450px; height: 240px; text-align: left; line-height: normal; z-index: 0; left: 0px; top: 0px;'>
            <svg version='1.1' style='font-family:&quot;Lucida Grande&quot;, &quot;Lucida Sans Unicode&quot;, Arial, Helvetica, sans-serif;font-size:12px;'
                xmlns='http://www.w3.org/2000/svg' width='450' height='240'>
                <desc>Created with highchartsc 4.0.3 /Highstock 2.0.3</desc>
                <defs>
                    <clipPath id='highchartsc-1'>
                        <rect x='0' y='0' width='262' height='194'/>
                    </clipPath>
                </defs>
                <rect x='0' y='0' width='450' height='240' strokeWidth='0' fill='#FFFFFF' class=' highchartsc-background'/>
                <g class='highchartsc-grid' zIndex='1'/>
                <g class='highchartsc-grid' zIndex='1'>
                    <path fill='none' d='M 61 9.5 L 323 9.5' stroke='#C0C0C0' stroke-width='1' zIndex='1' opacity='1'/>
                    <path fill='none' d='M 61 75.5 L 323 75.5' stroke='#C0C0C0' stroke-width='1' zIndex='1' opacity='1'/>
                    <path fill='none' d='M 61 139.5 L 323 139.5' stroke='#C0C0C0' stroke-width='1' zIndex='1' opacity='1'/>
                    <path fill='none' d='M 61 204.5 L 323 204.5' stroke='#C0C0C0' stroke-width='1' zIndex='1' opacity='1'/>
                </g>
                <g class='highchartsc-axis' zIndex='2'>
                    <path fill='none' d='M 235.5 204 L 235.5 214' stroke='#C0D0E0' stroke-width='1' opacity='1'/>
                    <path fill='none' d='M 323.5 204 L 323.5 214' stroke='#C0D0E0' stroke-width='1' opacity='1'/>
                    <path fill='none' d='M 147.5 204 L 147.5 214' stroke='#C0D0E0' stroke-width='1' opacity='1'/>
                    <path fill='none' d='M 60.5 204 L 60.5 214' stroke='#C0D0E0' stroke-width='1' opacity='1'/>
                    <path fill='none' d='M 61 204.5 L 323 204.5' stroke='#C0D0E0' stroke-width='1' zIndex='7' visibility='visible'/>
                </g>
                <g class='highchartsc-axis' zIndex='2'>
                    <text x='24' zIndex='7' text-anchor='middle' transform='translate(0,0) rotate(270 24 107)' class=' highchartsc-yaxis-title' style='color:#707070;fill:#707070;' visibility='visible' y='107'>Meetings</text>
                </g>
                <g class='highchartsc-series-group' zIndex='3'>
                    <g class='highchartsc-series' visibility='visible' zIndex='0.1' transform='translate(61,10) scale(1 1)' clip-path='url(#highchartsc-1)'>
                        <path fill='none' d='M 43.666666666666664 19.400000000000006 L 131 19.400000000000006 L 218.33333333333331 19.400000000000006' stroke='black' stroke-width='5' zIndex='1' stroke-dasharray='6,2' isShadow='true' stroke-opacity='0.049999999999999996' transform='translate(1, 1)'/>
                        <path fill='none' d='M 43.666666666666664 19.400000000000006 L 131 19.400000000000006 L 218.33333333333331 19.400000000000006' stroke='black' stroke-width='3' zIndex='1' stroke-dasharray='6,2' isShadow='true' stroke-opacity='0.09999999999999999' transform='translate(1, 1)'/>
                        <path fill='none' d='M 43.666666666666664 19.400000000000006 L 131 19.400000000000006 L 218.33333333333331 19.400000000000006' stroke='black' stroke-width='1' zIndex='1' stroke-dasharray='6,2' isShadow='true' stroke-opacity='0.15' transform='translate(1, 1)'/>
                        <path fill='none' d='M 0 19.400000000000006 L 43.666666666666664 19.400000000000006 L 131 19.400000000000006 L 218.33333333333331 19.400000000000006 L 262 19.400000000000006' stroke='#000' stroke-width='2' zIndex='1' stroke-dasharray='6,2'/>
                        <path fill='none' d='M 33.666666666666664 19.400000000000006 L 43.666666666666664 19.400000000000006 L 131 19.400000000000006 L 218.33333333333331 19.400000000000006 L 228.33333333333331 19.400000000000006' stroke-linejoin='round' visibility='visible' stroke='rgba(192,192,192,0.0001)' stroke-width='22' zIndex='2' class=' highchartsc-tracker' style=''/>
                    </g>
                    <g class='highchartsc-markers highchartsc-tracker' visibility='visible' zIndex='0.1' transform='translate(61,10) scale(1 1)' clip-path='none' style=''>
                        <path fill='#000' d='M 218 15.400000000000006 C 223.328 15.400000000000006 223.328 23.400000000000006 218 23.400000000000006 C 212.672 23.400000000000006 212.672 15.400000000000006 218 15.400000000000006 Z'/>
                        <path fill='#000' d='M 131 15.400000000000006 C 136.328 15.400000000000006 136.328 23.400000000000006 131 23.400000000000006 C 125.672 23.400000000000006 125.672 15.400000000000006 131 15.400000000000006 Z'/>
                        <path fill='#000' d='M 43 15.400000000000006 C 48.328 15.400000000000006 48.328 23.400000000000006 43 23.400000000000006 C 37.672 23.400000000000006 37.672 15.400000000000006 43 15.400000000000006 Z'/>
                    </g>
                    <g class='highchartsc-series highchartsc-tracker' visibility='visible' zIndex='0.1' transform='translate(61,10) scale(1 1)' style='' clip-path='url(#highchartsc-1)'>
                        <rect x='22.5' y='32.5' width='42' height='0' fill='none' rx='0' ry='0' stroke='black' stroke-width='5' isShadow='true' stroke-opacity='0.049999999999999996' transform='translate(1, 1)'/>
                        <rect x='22.5' y='32.5' width='42' height='0' fill='none' rx='0' ry='0' stroke='black' stroke-width='3' isShadow='true' stroke-opacity='0.09999999999999999' transform='translate(1, 1)'/>
                        <rect x='22.5' y='32.5' width='42' height='0' fill='none' rx='0' ry='0' stroke='black' stroke-width='1' isShadow='true' stroke-opacity='0.15' transform='translate(1, 1)'/>
                        <rect x='22.5' y='32.5' width='42' height='0' fill='#FF2224' rx='0' ry='0' stroke='#FFFFFF' stroke-width='1'/>
                        <rect x='109.5' y='84.5' width='42' height='14' fill='none' rx='0' ry='0' stroke='black' stroke-width='5' isShadow='true' stroke-opacity='0.049999999999999996' transform='translate(1, 1)'/>
                        <rect x='109.5' y='84.5' width='42' height='16' fill='none' rx='0' ry='0' stroke='black' stroke-width='3' isShadow='true' stroke-opacity='0.09999999999999999' transform='translate(1, 1)'/>
                        <rect x='109.5' y='84.5' width='42' height='18' fill='none' rx='0' ry='0' stroke='black' stroke-width='1' isShadow='true' stroke-opacity='0.15' transform='translate(1, 1)'/>
                        <rect x='109.5' y='84.5' width='42' height='19' fill='#FF2224' rx='0' ry='0' stroke='#FFFFFF' stroke-width='1'/>
                        <rect x='196.5' y='65.5' width='42' height='40' fill='none' rx='0' ry='0' stroke='black' stroke-width='5' isShadow='true' stroke-opacity='0.049999999999999996' transform='translate(1, 1)'/>
                        <rect x='196.5' y='65.5' width='42' height='42' fill='none' rx='0' ry='0' stroke='black' stroke-width='3' isShadow='true' stroke-opacity='0.09999999999999999' transform='translate(1, 1)'/>
                        <rect x='196.5' y='65.5' width='42' height='44' fill='none' rx='0' ry='0' stroke='black' stroke-width='1' isShadow='true' stroke-opacity='0.15' transform='translate(1, 1)'/>
                        <rect x='196.5' y='65.5' width='42' height='45' fill='#FF2224' rx='0' ry='0' stroke='#FFFFFF' stroke-width='1'/>
                    </g>
                    <g class='highchartsc-markers' visibility='visible' zIndex='0.1' transform='translate(61,10) scale(1 1)' clip-path='none'/>
                    <g class='highchartsc-series highchartsc-tracker' visibility='visible' zIndex='0.1' transform='translate(61,10) scale(1 1)' style='' clip-path='url(#highchartsc-1)'>
                        <rect x='22.5' y='32.5' width='42' height='157' fill='none' rx='0' ry='0' stroke='black' stroke-width='5' isShadow='true' stroke-opacity='0.049999999999999996' transform='translate(1, 1)'/>
                        <rect x='22.5' y='32.5' width='42' height='159' fill='none' rx='0' ry='0' stroke='black' stroke-width='3' isShadow='true' stroke-opacity='0.09999999999999999' transform='translate(1, 1)'/>
                        <rect x='22.5' y='32.5' width='42' height='161' fill='none' rx='0' ry='0' stroke='black' stroke-width='1' isShadow='true' stroke-opacity='0.15' transform='translate(1, 1)'/>
                        <rect x='22.5' y='32.5' width='42' height='162' fill='#2073CC' rx='0' ry='0' stroke='#FFFFFF' stroke-width='1'/>
                        <rect x='109.5' y='103.5' width='42' height='86' fill='none' rx='0' ry='0' stroke='black' stroke-width='5' isShadow='true' stroke-opacity='0.049999999999999996' transform='translate(1, 1)'/>
                        <rect x='109.5' y='103.5' width='42' height='88' fill='none' rx='0' ry='0' stroke='black' stroke-width='3' isShadow='true' stroke-opacity='0.09999999999999999' transform='translate(1, 1)'/>
                        <rect x='109.5' y='103.5' width='42' height='90' fill='none' rx='0' ry='0' stroke='black' stroke-width='1' isShadow='true' stroke-opacity='0.15' transform='translate(1, 1)'/>
                        <rect x='109.5' y='103.5' width='42' height='91' fill='#2073CC' rx='0' ry='0' stroke='#FFFFFF' stroke-width='1'/>
                        <rect x='196.5' y='110.5' width='42' height='79' fill='none' rx='0' ry='0' stroke='black' stroke-width='5' isShadow='true' stroke-opacity='0.049999999999999996' transform='translate(1, 1)'/>
                        <rect x='196.5' y='110.5' width='42' height='81' fill='none' rx='0' ry='0' stroke='black' stroke-width='3' isShadow='true' stroke-opacity='0.09999999999999999' transform='translate(1, 1)'/>
                        <rect x='196.5' y='110.5' width='42' height='83' fill='none' rx='0' ry='0' stroke='black' stroke-width='1' isShadow='true' stroke-opacity='0.15' transform='translate(1, 1)'/>
                        <rect x='196.5' y='110.5' width='42' height='84' fill='#2073CC' rx='0' ry='0' stroke='#FFFFFF' stroke-width='1'/>
                    </g>
                    <g class='highchartsc-markers' visibility='visible' zIndex='0.1' transform='translate(61,10) scale(1 1)' clip-path='none'/></g>
                <g class='highchartsc-data-labels highchartsc-tracker' visibility='visible' zIndex='6' transform='translate(61,10) scale(1 1)' opacity='1' style=''>
                    <g zIndex='1' transform='translate(35,33)' style='cursor:default;'>
                        <text x='3' zIndex='1' style='font-size:11px;color:#FFFFFF;fill:#FFFFFF;' y='15'>25</text>
                    </g>
                    <g zIndex='1' transform='translate(122,104)' style='cursor:default;'>
                        <text x='3' zIndex='1' style='font-size:11px;color:#FFFFFF;fill:#FFFFFF;' y='15'>14</text>
                    </g>
                    <g zIndex='1' transform='translate(209,111)' style='cursor:default;'>
                        <text x='3' zIndex='1' style='font-size:11px;color:#FFFFFF;fill:#FFFFFF;' y='15'>13</text>
                    </g>
                </g>
                <g class='highchartsc-legend' zIndex='7' transform='translate(343,95)'>
                    <g zIndex='1'>
                        <g>
                            <g class='highchartsc-legend-item' zIndex='1' transform='translate(8,3)'>
                                <path fill='none' d='M 0 11 L 16 11' stroke-dasharray='6,2' stroke='#000' stroke-width='2'/>
                                <path fill='#000' d='M 8 7 C 13.328 7 13.328 15 8 15 C 2.6719999999999997 15 2.6719999999999997 7 8 7 Z'/>
                                <text x='21' style='color:#333333;font-size:12px;font-weight:bold;cursor:pointer;fill:#333333;' text-anchor='start' zIndex='2' y='15'>Target</text>
                            </g>

                            <g class='highchartsc-legend-item' zIndex='1' transform='translate(8,31)'>
                                <text x='21' y='15' style='color:#333333;font-size:12px;font-weight:bold;cursor:pointer;fill:#333333;' text-anchor='start' zIndex='2'>
                                    <tspan>Met - ".$user_short_name."</tspan>
                                </text>
                                <rect x='0' y='4' width='16' height='12' zIndex='3' fill='#2073CC'/>
                            </g>
                        </g>
                    </g>
                </g>
                <g class='highchartsc-axis-labels highchartsc-xaxis-labels' zIndex='7'>
                    <text x='104.66666666666666' text-anchor='middle' style='width:67px;color:#606060;cursor:default;font-size:11px;fill:#606060;' y='224' opacity='1'>Mar</text>
                    <text x='192' text-anchor='middle' style='width:67px;color:#606060;cursor:default;font-size:11px;fill:#606060;' y='224' opacity='1'>Apr</text>
                    <text x='279.3333333333333' text-anchor='middle' style='width:67px;color:#606060;cursor:default;font-size:11px;fill:#606060;' y='224' opacity='1'>May</text>
                </g>
                <g class='highchartsc-axis-labels highchartsc-yaxis-labels' zIndex='7'>
                    <text x='46' text-anchor='end' style='width:129px;color:#606060;cursor:default;font-size:11px;fill:#606060;' y='209.5' opacity='1'>0</text>
                    <text x='46' text-anchor='end' style='width:129px;color:#606060;cursor:default;font-size:11px;fill:#606060;' y='144.83333333333331' opacity='1'>3</text>
                    <text x='46' text-anchor='end' style='width:129px;color:#606060;cursor:default;font-size:11px;fill:#606060;' y='80.16666666666666' opacity='1'>6</text>
                    <text x='46' text-anchor='end' style='width:129px;color:#606060;cursor:default;font-size:11px;fill:#606060;' y='15.5' opacity='1'>9</text>
                </g>
                <g class='highchartsc-tooltip' zIndex='8' style='cursor:default;padding:0;white-space:nowrap;' transform='translate(0,-9999)'>
                    <path fill='none' d='M 3 0 L 13 0 C 16 0 16 0 16 3 L 16 13 C 16 16 16 16 13 16 L 3 16 C 0 16 0 16 0 13 L 0 3 C 0 0 0 0 3 0' isShadow='true' stroke='black' stroke-opacity='0.049999999999999996' stroke-width='5' transform='translate(1, 1)'/>
                    <path fill='none' d='M 3 0 L 13 0 C 16 0 16 0 16 3 L 16 13 C 16 16 16 16 13 16 L 3 16 C 0 16 0 16 0 13 L 0 3 C 0 0 0 0 3 0' isShadow='true' stroke='black' stroke-opacity='0.09999999999999999' stroke-width='3' transform='translate(1, 1)'/>
                    <path fill='none' d='M 3 0 L 13 0 C 16 0 16 0 16 3 L 16 13 C 16 16 16 16 13 16 L 3 16 C 0 16 0 16 0 13 L 0 3 C 0 0 0 0 3 0' isShadow='true' stroke='black' stroke-opacity='0.15' stroke-width='1' transform='translate(1, 1)'/>
                    <path fill='rgba(249, 249, 249, .85)' d='M 3 0 L 13 0 C 16 0 16 0 16 3 L 16 13 C 16 16 16 16 13 16 L 3 16 C 0 16 0 16 0 13 L 0 3 C 0 0 0 0 3 0'/>
                    <text x='8' zIndex='1' style='font-size:12px;color:#333333;fill:#333333;' y='21'></text>
                </g>
            </svg>
        </div>
    </div>
    <script>
        $(function ()
        {
          $('#mainPageCandi').highcharts(
          {
            chart:
            {
              events :
              {
                load : edgeExtend,
                redraw : edgeExtend
              }
            },
            title: {
                text: ''
            },
            legend: {
                layout: 'vertical',
                align: 'right',
                verticalAlign: 'middle',
                borderWidth: 0
            },
            plotOptions:
            {
              column:
              {
                stacking: 'normal'
              },
              area:
              {
                marker:
                {
                  enabled: false,
                  symbol: 'circle',
                  radius: 1,
                  states: {
                      hover: {
                          enabled: true
                      }
                  }
                },
                fillColor:
                {
                  linearGradient: { x1: 0, y1: 0, x2: 0, y2: 1},
                  stops:
                  [
                    [0, '#FFF6E5'],
                    [1, '#FFEECE']
                  ]
                },
                fillOpacity: 0.5,
                lineWidth: 0,
                shadow: false,
                threshold: null
              }
            },
            xAxis: {
              categories: ['".$months[0]."','".$months[1]."','".$months[2]."']
            },
            yAxis:
             {
                title:
                {
                  text: 'New Candidates In Play'
                },
             },
            tooltip: {
                shared: true,
                valueSuffix: ' candidate(s)'
            },
            series: [{
                type: 'line',
                color: '#000',
                name: 'Target',
                data: [7,7,7],
                dashStyle: 'ShortDash'
            },

            {
            type: 'column',
            name: '".$user_short_name."',
            stack: '".$user_short_name."',
            data: [".$candidate_in_plays[0].",".$candidate_in_plays[1].",".$candidate_in_plays[2]."],
            color: '#2073CC' ,
            dataLabels:
            {
              enabled: true,
              rotation: 0,
              color: '#FFFFFF',
              align: 'center',
              verticalAlign: 'top'
            }},]
          });
        });
      </script>

    ";

    return $table;
  }

  function create_new_position_table($user_short_name,$months,$positions_in_plays)
  {
    $table = "

      <div style='height: 240px; width: 450px;  margin: 0 auto;' id='mainPagePosition' data-highchartsc-chart='0'>
        <div class='highchartsc-container' id='highchartsc-0' style='position: relative; overflow: hidden; width: 450px; height: 240px; text-align: left; line-height: normal; z-index: 0; left: 0px; top: 0px;'>
            <svg version='1.1' style='font-family:&quot;Lucida Grande&quot;, &quot;Lucida Sans Unicode&quot;, Arial, Helvetica, sans-serif;font-size:12px;'
                xmlns='http://www.w3.org/2000/svg' width='450' height='240'>
                <desc>Created with highchartsc 4.0.3 /Highstock 2.0.3</desc>
                <defs>
                    <clipPath id='highchartsc-1'>
                        <rect x='0' y='0' width='262' height='194'/>
                    </clipPath>
                </defs>
                <rect x='0' y='0' width='450' height='240' strokeWidth='0' fill='#FFFFFF' class=' highchartsc-background'/>
                <g class='highchartsc-grid' zIndex='1'/>
                <g class='highchartsc-grid' zIndex='1'>
                    <path fill='none' d='M 61 9.5 L 323 9.5' stroke='#C0C0C0' stroke-width='1' zIndex='1' opacity='1'/>
                    <path fill='none' d='M 61 75.5 L 323 75.5' stroke='#C0C0C0' stroke-width='1' zIndex='1' opacity='1'/>
                    <path fill='none' d='M 61 139.5 L 323 139.5' stroke='#C0C0C0' stroke-width='1' zIndex='1' opacity='1'/>
                    <path fill='none' d='M 61 204.5 L 323 204.5' stroke='#C0C0C0' stroke-width='1' zIndex='1' opacity='1'/>
                </g>
                <g class='highchartsc-axis' zIndex='2'>
                    <path fill='none' d='M 235.5 204 L 235.5 214' stroke='#C0D0E0' stroke-width='1' opacity='1'/>
                    <path fill='none' d='M 323.5 204 L 323.5 214' stroke='#C0D0E0' stroke-width='1' opacity='1'/>
                    <path fill='none' d='M 147.5 204 L 147.5 214' stroke='#C0D0E0' stroke-width='1' opacity='1'/>
                    <path fill='none' d='M 60.5 204 L 60.5 214' stroke='#C0D0E0' stroke-width='1' opacity='1'/>
                    <path fill='none' d='M 61 204.5 L 323 204.5' stroke='#C0D0E0' stroke-width='1' zIndex='7' visibility='visible'/>
                </g>
                <g class='highchartsc-axis' zIndex='2'>
                    <text x='24' zIndex='7' text-anchor='middle' transform='translate(0,0) rotate(270 24 107)' class=' highchartsc-yaxis-title' style='color:#707070;fill:#707070;' visibility='visible' y='107'>Meetings</text>
                </g>
                <g class='highchartsc-series-group' zIndex='3'>
                    <g class='highchartsc-series' visibility='visible' zIndex='0.1' transform='translate(61,10) scale(1 1)' clip-path='url(#highchartsc-1)'>
                        <path fill='none' d='M 43.666666666666664 19.400000000000006 L 131 19.400000000000006 L 218.33333333333331 19.400000000000006' stroke='black' stroke-width='5' zIndex='1' stroke-dasharray='6,2' isShadow='true' stroke-opacity='0.049999999999999996' transform='translate(1, 1)'/>
                        <path fill='none' d='M 43.666666666666664 19.400000000000006 L 131 19.400000000000006 L 218.33333333333331 19.400000000000006' stroke='black' stroke-width='3' zIndex='1' stroke-dasharray='6,2' isShadow='true' stroke-opacity='0.09999999999999999' transform='translate(1, 1)'/>
                        <path fill='none' d='M 43.666666666666664 19.400000000000006 L 131 19.400000000000006 L 218.33333333333331 19.400000000000006' stroke='black' stroke-width='1' zIndex='1' stroke-dasharray='6,2' isShadow='true' stroke-opacity='0.15' transform='translate(1, 1)'/>
                        <path fill='none' d='M 0 19.400000000000006 L 43.666666666666664 19.400000000000006 L 131 19.400000000000006 L 218.33333333333331 19.400000000000006 L 262 19.400000000000006' stroke='#000' stroke-width='2' zIndex='1' stroke-dasharray='6,2'/>
                        <path fill='none' d='M 33.666666666666664 19.400000000000006 L 43.666666666666664 19.400000000000006 L 131 19.400000000000006 L 218.33333333333331 19.400000000000006 L 228.33333333333331 19.400000000000006' stroke-linejoin='round' visibility='visible' stroke='rgba(192,192,192,0.0001)' stroke-width='22' zIndex='2' class=' highchartsc-tracker' style=''/>
                    </g>
                    <g class='highchartsc-markers highchartsc-tracker' visibility='visible' zIndex='0.1' transform='translate(61,10) scale(1 1)' clip-path='none' style=''>
                        <path fill='#000' d='M 218 15.400000000000006 C 223.328 15.400000000000006 223.328 23.400000000000006 218 23.400000000000006 C 212.672 23.400000000000006 212.672 15.400000000000006 218 15.400000000000006 Z'/>
                        <path fill='#000' d='M 131 15.400000000000006 C 136.328 15.400000000000006 136.328 23.400000000000006 131 23.400000000000006 C 125.672 23.400000000000006 125.672 15.400000000000006 131 15.400000000000006 Z'/>
                        <path fill='#000' d='M 43 15.400000000000006 C 48.328 15.400000000000006 48.328 23.400000000000006 43 23.400000000000006 C 37.672 23.400000000000006 37.672 15.400000000000006 43 15.400000000000006 Z'/>
                    </g>
                    <g class='highchartsc-series highchartsc-tracker' visibility='visible' zIndex='0.1' transform='translate(61,10) scale(1 1)' style='' clip-path='url(#highchartsc-1)'>
                        <rect x='22.5' y='32.5' width='42' height='0' fill='none' rx='0' ry='0' stroke='black' stroke-width='5' isShadow='true' stroke-opacity='0.049999999999999996' transform='translate(1, 1)'/>
                        <rect x='22.5' y='32.5' width='42' height='0' fill='none' rx='0' ry='0' stroke='black' stroke-width='3' isShadow='true' stroke-opacity='0.09999999999999999' transform='translate(1, 1)'/>
                        <rect x='22.5' y='32.5' width='42' height='0' fill='none' rx='0' ry='0' stroke='black' stroke-width='1' isShadow='true' stroke-opacity='0.15' transform='translate(1, 1)'/>
                        <rect x='22.5' y='32.5' width='42' height='0' fill='#FF2224' rx='0' ry='0' stroke='#FFFFFF' stroke-width='1'/>
                        <rect x='109.5' y='84.5' width='42' height='14' fill='none' rx='0' ry='0' stroke='black' stroke-width='5' isShadow='true' stroke-opacity='0.049999999999999996' transform='translate(1, 1)'/>
                        <rect x='109.5' y='84.5' width='42' height='16' fill='none' rx='0' ry='0' stroke='black' stroke-width='3' isShadow='true' stroke-opacity='0.09999999999999999' transform='translate(1, 1)'/>
                        <rect x='109.5' y='84.5' width='42' height='18' fill='none' rx='0' ry='0' stroke='black' stroke-width='1' isShadow='true' stroke-opacity='0.15' transform='translate(1, 1)'/>
                        <rect x='109.5' y='84.5' width='42' height='19' fill='#FF2224' rx='0' ry='0' stroke='#FFFFFF' stroke-width='1'/>
                        <rect x='196.5' y='65.5' width='42' height='40' fill='none' rx='0' ry='0' stroke='black' stroke-width='5' isShadow='true' stroke-opacity='0.049999999999999996' transform='translate(1, 1)'/>
                        <rect x='196.5' y='65.5' width='42' height='42' fill='none' rx='0' ry='0' stroke='black' stroke-width='3' isShadow='true' stroke-opacity='0.09999999999999999' transform='translate(1, 1)'/>
                        <rect x='196.5' y='65.5' width='42' height='44' fill='none' rx='0' ry='0' stroke='black' stroke-width='1' isShadow='true' stroke-opacity='0.15' transform='translate(1, 1)'/>
                        <rect x='196.5' y='65.5' width='42' height='45' fill='#FF2224' rx='0' ry='0' stroke='#FFFFFF' stroke-width='1'/>
                    </g>
                    <g class='highchartsc-markers' visibility='visible' zIndex='0.1' transform='translate(61,10) scale(1 1)' clip-path='none'/>
                    <g class='highchartsc-series highchartsc-tracker' visibility='visible' zIndex='0.1' transform='translate(61,10) scale(1 1)' style='' clip-path='url(#highchartsc-1)'>
                        <rect x='22.5' y='32.5' width='42' height='157' fill='none' rx='0' ry='0' stroke='black' stroke-width='5' isShadow='true' stroke-opacity='0.049999999999999996' transform='translate(1, 1)'/>
                        <rect x='22.5' y='32.5' width='42' height='159' fill='none' rx='0' ry='0' stroke='black' stroke-width='3' isShadow='true' stroke-opacity='0.09999999999999999' transform='translate(1, 1)'/>
                        <rect x='22.5' y='32.5' width='42' height='161' fill='none' rx='0' ry='0' stroke='black' stroke-width='1' isShadow='true' stroke-opacity='0.15' transform='translate(1, 1)'/>
                        <rect x='22.5' y='32.5' width='42' height='162' fill='#2073CC' rx='0' ry='0' stroke='#FFFFFF' stroke-width='1'/>
                        <rect x='109.5' y='103.5' width='42' height='86' fill='none' rx='0' ry='0' stroke='black' stroke-width='5' isShadow='true' stroke-opacity='0.049999999999999996' transform='translate(1, 1)'/>
                        <rect x='109.5' y='103.5' width='42' height='88' fill='none' rx='0' ry='0' stroke='black' stroke-width='3' isShadow='true' stroke-opacity='0.09999999999999999' transform='translate(1, 1)'/>
                        <rect x='109.5' y='103.5' width='42' height='90' fill='none' rx='0' ry='0' stroke='black' stroke-width='1' isShadow='true' stroke-opacity='0.15' transform='translate(1, 1)'/>
                        <rect x='109.5' y='103.5' width='42' height='91' fill='#2073CC' rx='0' ry='0' stroke='#FFFFFF' stroke-width='1'/>
                        <rect x='196.5' y='110.5' width='42' height='79' fill='none' rx='0' ry='0' stroke='black' stroke-width='5' isShadow='true' stroke-opacity='0.049999999999999996' transform='translate(1, 1)'/>
                        <rect x='196.5' y='110.5' width='42' height='81' fill='none' rx='0' ry='0' stroke='black' stroke-width='3' isShadow='true' stroke-opacity='0.09999999999999999' transform='translate(1, 1)'/>
                        <rect x='196.5' y='110.5' width='42' height='83' fill='none' rx='0' ry='0' stroke='black' stroke-width='1' isShadow='true' stroke-opacity='0.15' transform='translate(1, 1)'/>
                        <rect x='196.5' y='110.5' width='42' height='84' fill='#2073CC' rx='0' ry='0' stroke='#FFFFFF' stroke-width='1'/>
                    </g>
                    <g class='highchartsc-markers' visibility='visible' zIndex='0.1' transform='translate(61,10) scale(1 1)' clip-path='none'/></g>
                <g class='highchartsc-data-labels highchartsc-tracker' visibility='visible' zIndex='6' transform='translate(61,10) scale(1 1)' opacity='1' style=''>
                    <g zIndex='1' transform='translate(35,33)' style='cursor:default;'>
                        <text x='3' zIndex='1' style='font-size:11px;color:#FFFFFF;fill:#FFFFFF;' y='15'>25</text>
                    </g>
                    <g zIndex='1' transform='translate(122,104)' style='cursor:default;'>
                        <text x='3' zIndex='1' style='font-size:11px;color:#FFFFFF;fill:#FFFFFF;' y='15'>14</text>
                    </g>
                    <g zIndex='1' transform='translate(209,111)' style='cursor:default;'>
                        <text x='3' zIndex='1' style='font-size:11px;color:#FFFFFF;fill:#FFFFFF;' y='15'>13</text>
                    </g>
                </g>
                <g class='highchartsc-legend' zIndex='7' transform='translate(343,95)'>
                    <g zIndex='1'>
                        <g>
                            <g class='highchartsc-legend-item' zIndex='1' transform='translate(8,3)'>
                                <path fill='none' d='M 0 11 L 16 11' stroke-dasharray='6,2' stroke='#000' stroke-width='2'/>
                                <path fill='#000' d='M 8 7 C 13.328 7 13.328 15 8 15 C 2.6719999999999997 15 2.6719999999999997 7 8 7 Z'/>
                                <text x='21' style='color:#333333;font-size:12px;font-weight:bold;cursor:pointer;fill:#333333;' text-anchor='start' zIndex='2' y='15'>Target</text>
                            </g>

                            <g class='highchartsc-legend-item' zIndex='1' transform='translate(8,31)'>
                                <text x='21' y='15' style='color:#333333;font-size:12px;font-weight:bold;cursor:pointer;fill:#333333;' text-anchor='start' zIndex='2'>
                                    <tspan>Met - ".$user_short_name."</tspan>
                                </text>
                                <rect x='0' y='4' width='16' height='12' zIndex='3' fill='#2073CC'/>
                            </g>
                        </g>
                    </g>
                </g>
                <g class='highchartsc-axis-labels highchartsc-xaxis-labels' zIndex='7'>
                    <text x='104.66666666666666' text-anchor='middle' style='width:67px;color:#606060;cursor:default;font-size:11px;fill:#606060;' y='224' opacity='1'>Mar</text>
                    <text x='192' text-anchor='middle' style='width:67px;color:#606060;cursor:default;font-size:11px;fill:#606060;' y='224' opacity='1'>Apr</text>
                    <text x='279.3333333333333' text-anchor='middle' style='width:67px;color:#606060;cursor:default;font-size:11px;fill:#606060;' y='224' opacity='1'>May</text>
                </g>
                <g class='highchartsc-axis-labels highchartsc-yaxis-labels' zIndex='7'>
                    <text x='46' text-anchor='end' style='width:129px;color:#606060;cursor:default;font-size:11px;fill:#606060;' y='209.5' opacity='1'>0</text>
                    <text x='46' text-anchor='end' style='width:129px;color:#606060;cursor:default;font-size:11px;fill:#606060;' y='144.83333333333331' opacity='1'>2</text>
                    <text x='46' text-anchor='end' style='width:129px;color:#606060;cursor:default;font-size:11px;fill:#606060;' y='80.16666666666666' opacity='1'>4</text>
                    <text x='46' text-anchor='end' style='width:129px;color:#606060;cursor:default;font-size:11px;fill:#606060;' y='15.5' opacity='1'>6</text>
                </g>
                <g class='highchartsc-tooltip' zIndex='8' style='cursor:default;padding:0;white-space:nowrap;' transform='translate(0,-9999)'>
                    <path fill='none' d='M 3 0 L 13 0 C 16 0 16 0 16 3 L 16 13 C 16 16 16 16 13 16 L 3 16 C 0 16 0 16 0 13 L 0 3 C 0 0 0 0 3 0' isShadow='true' stroke='black' stroke-opacity='0.049999999999999996' stroke-width='5' transform='translate(1, 1)'/>
                    <path fill='none' d='M 3 0 L 13 0 C 16 0 16 0 16 3 L 16 13 C 16 16 16 16 13 16 L 3 16 C 0 16 0 16 0 13 L 0 3 C 0 0 0 0 3 0' isShadow='true' stroke='black' stroke-opacity='0.09999999999999999' stroke-width='3' transform='translate(1, 1)'/>
                    <path fill='none' d='M 3 0 L 13 0 C 16 0 16 0 16 3 L 16 13 C 16 16 16 16 13 16 L 3 16 C 0 16 0 16 0 13 L 0 3 C 0 0 0 0 3 0' isShadow='true' stroke='black' stroke-opacity='0.15' stroke-width='1' transform='translate(1, 1)'/>
                    <path fill='rgba(249, 249, 249, .85)' d='M 3 0 L 13 0 C 16 0 16 0 16 3 L 16 13 C 16 16 16 16 13 16 L 3 16 C 0 16 0 16 0 13 L 0 3 C 0 0 0 0 3 0'/>
                    <text x='8' zIndex='1' style='font-size:12px;color:#333333;fill:#333333;' y='21'></text>
                </g>
            </svg>
        </div>
    </div>
    <script>
        $(function ()
        {
          $('#mainPagePosition').highcharts(
          {
            chart:
            {
              events :
              {
                load : edgeExtend,
                redraw : edgeExtend
              }
            },
            title: {
                text: ''
            },
            legend: {
                layout: 'vertical',
                align: 'right',
                verticalAlign: 'middle',
                borderWidth: 0
            },
            plotOptions:
            {
              column:
              {
                stacking: 'normal'
              },
              area:
              {
                marker:
                {
                  enabled: false,
                  symbol: 'circle',
                  radius: 1,
                  states: {
                      hover: {
                          enabled: true
                      }
                  }
                },
                fillColor:
                {
                  linearGradient: { x1: 0, y1: 0, x2: 0, y2: 1},
                  stops:
                  [
                    [0, '#FFF6E5'],
                    [1, '#FFEECE']
                  ]
                },
                fillOpacity: 0.5,
                lineWidth: 0,
                shadow: false,
                threshold: null
              }
            },
            xAxis: {
              categories: ['".$months[0]."','".$months[1]."','".$months[2]."']
            },
            yAxis:
             {
                title:
                {
                  text: 'New Positions In Play'
                },
             },
            tooltip: {
                shared: true,
                valueSuffix: ' position(s)'
            },
            series: [{
                type: 'line',
                color: '#000',
                name: 'Target',
                data: [5,5,5],
                dashStyle: 'ShortDash'
            },

            {
            type: 'column',
            name: '".$user_short_name."',
            stack: '".$user_short_name."',
            data: [".$positions_in_plays[0].",".$positions_in_plays[1].",".$positions_in_plays[2]."],
            color: '#2073CC' ,
            dataLabels:
            {
              enabled: true,
              rotation: 0,
              color: '#FFFFFF',
              align: 'center',
              verticalAlign: 'top'
            }},]
          });
        });
      </script>

    ";

    return $table;
  }

  function create_meetings_table($user_short_name,$monthly_new_candidate_met,$months,$not_mets)
  {
    $table = "

    <div style='height: 240px; width: 450px;  margin: 0 auto;' id='mainPageMeetings' data-highcharts-chart='0'>
        <div class='highcharts-container' id='highcharts-0' style='position: relative; overflow: hidden; width: 450px; height: 240px; text-align: left; line-height: normal; z-index: 0; left: 0px; top: 0px;'>
            <svg version='1.1' style='font-family:&quot;Lucida Grande&quot;, &quot;Lucida Sans Unicode&quot;, Arial, Helvetica, sans-serif;font-size:12px;'
                xmlns='http://www.w3.org/2000/svg' width='450' height='240'>
                <desc>Created with Highcharts 4.0.3 /Highstock 2.0.3</desc>
                <defs>
                    <clipPath id='highcharts-1'>
                        <rect x='0' y='0' width='262' height='194'/>
                    </clipPath>
                </defs>
                <rect x='0' y='0' width='450' height='240' strokeWidth='0' fill='#FFFFFF' class=' highcharts-background'/>
                <g class='highcharts-grid' zIndex='1'/>
                <g class='highcharts-grid' zIndex='1'>
                    <path fill='none' d='M 61 9.5 L 323 9.5' stroke='#C0C0C0' stroke-width='1' zIndex='1' opacity='1'/>
                    <path fill='none' d='M 61 75.5 L 323 75.5' stroke='#C0C0C0' stroke-width='1' zIndex='1' opacity='1'/>
                    <path fill='none' d='M 61 139.5 L 323 139.5' stroke='#C0C0C0' stroke-width='1' zIndex='1' opacity='1'/>
                    <path fill='none' d='M 61 204.5 L 323 204.5' stroke='#C0C0C0' stroke-width='1' zIndex='1' opacity='1'/>
                </g>
                <g class='highcharts-axis' zIndex='2'>
                    <path fill='none' d='M 235.5 204 L 235.5 214' stroke='#C0D0E0' stroke-width='1' opacity='1'/>
                    <path fill='none' d='M 323.5 204 L 323.5 214' stroke='#C0D0E0' stroke-width='1' opacity='1'/>
                    <path fill='none' d='M 147.5 204 L 147.5 214' stroke='#C0D0E0' stroke-width='1' opacity='1'/>
                    <path fill='none' d='M 60.5 204 L 60.5 214' stroke='#C0D0E0' stroke-width='1' opacity='1'/>
                    <path fill='none' d='M 61 204.5 L 323 204.5' stroke='#C0D0E0' stroke-width='1' zIndex='7' visibility='visible'/>
                </g>
                <g class='highcharts-axis' zIndex='2'>
                    <text x='24' zIndex='7' text-anchor='middle' transform='translate(0,0) rotate(270 24 107)' class=' highcharts-yaxis-title' style='color:#707070;fill:#707070;' visibility='visible' y='107'>Meetings</text>
                </g>
                <g class='highcharts-series-group' zIndex='3'>
                    <g class='highcharts-series' visibility='visible' zIndex='0.1' transform='translate(61,10) scale(1 1)' clip-path='url(#highcharts-1)'>
                        <path fill='none' d='M 43.666666666666664 19.400000000000006 L 131 19.400000000000006 L 218.33333333333331 19.400000000000006' stroke='black' stroke-width='5' zIndex='1' stroke-dasharray='6,2' isShadow='true' stroke-opacity='0.049999999999999996' transform='translate(1, 1)'/>
                        <path fill='none' d='M 43.666666666666664 19.400000000000006 L 131 19.400000000000006 L 218.33333333333331 19.400000000000006' stroke='black' stroke-width='3' zIndex='1' stroke-dasharray='6,2' isShadow='true' stroke-opacity='0.09999999999999999' transform='translate(1, 1)'/>
                        <path fill='none' d='M 43.666666666666664 19.400000000000006 L 131 19.400000000000006 L 218.33333333333331 19.400000000000006' stroke='black' stroke-width='1' zIndex='1' stroke-dasharray='6,2' isShadow='true' stroke-opacity='0.15' transform='translate(1, 1)'/>
                        <path fill='none' d='M 0 19.400000000000006 L 43.666666666666664 19.400000000000006 L 131 19.400000000000006 L 218.33333333333331 19.400000000000006 L 262 19.400000000000006' stroke='#000' stroke-width='2' zIndex='1' stroke-dasharray='6,2'/>
                        <path fill='none' d='M 33.666666666666664 19.400000000000006 L 43.666666666666664 19.400000000000006 L 131 19.400000000000006 L 218.33333333333331 19.400000000000006 L 228.33333333333331 19.400000000000006' stroke-linejoin='round' visibility='visible' stroke='rgba(192,192,192,0.0001)' stroke-width='22' zIndex='2' class=' highcharts-tracker' style=''/>
                    </g>
                    <g class='highcharts-markers highcharts-tracker' visibility='visible' zIndex='0.1' transform='translate(61,10) scale(1 1)' clip-path='none' style=''>
                        <path fill='#000' d='M 218 15.400000000000006 C 223.328 15.400000000000006 223.328 23.400000000000006 218 23.400000000000006 C 212.672 23.400000000000006 212.672 15.400000000000006 218 15.400000000000006 Z'/>
                        <path fill='#000' d='M 131 15.400000000000006 C 136.328 15.400000000000006 136.328 23.400000000000006 131 23.400000000000006 C 125.672 23.400000000000006 125.672 15.400000000000006 131 15.400000000000006 Z'/>
                        <path fill='#000' d='M 43 15.400000000000006 C 48.328 15.400000000000006 48.328 23.400000000000006 43 23.400000000000006 C 37.672 23.400000000000006 37.672 15.400000000000006 43 15.400000000000006 Z'/>
                    </g>
                    <g class='highcharts-series highcharts-tracker' visibility='visible' zIndex='0.1' transform='translate(61,10) scale(1 1)' style='' clip-path='url(#highcharts-1)'>
                        <rect x='22.5' y='32.5' width='42' height='0' fill='none' rx='0' ry='0' stroke='black' stroke-width='5' isShadow='true' stroke-opacity='0.049999999999999996' transform='translate(1, 1)'/>
                        <rect x='22.5' y='32.5' width='42' height='0' fill='none' rx='0' ry='0' stroke='black' stroke-width='3' isShadow='true' stroke-opacity='0.09999999999999999' transform='translate(1, 1)'/>
                        <rect x='22.5' y='32.5' width='42' height='0' fill='none' rx='0' ry='0' stroke='black' stroke-width='1' isShadow='true' stroke-opacity='0.15' transform='translate(1, 1)'/>
                        <rect x='22.5' y='32.5' width='42' height='0' fill='#FF2224' rx='0' ry='0' stroke='#FFFFFF' stroke-width='1'/>
                        <rect x='109.5' y='84.5' width='42' height='14' fill='none' rx='0' ry='0' stroke='black' stroke-width='5' isShadow='true' stroke-opacity='0.049999999999999996' transform='translate(1, 1)'/>
                        <rect x='109.5' y='84.5' width='42' height='16' fill='none' rx='0' ry='0' stroke='black' stroke-width='3' isShadow='true' stroke-opacity='0.09999999999999999' transform='translate(1, 1)'/>
                        <rect x='109.5' y='84.5' width='42' height='18' fill='none' rx='0' ry='0' stroke='black' stroke-width='1' isShadow='true' stroke-opacity='0.15' transform='translate(1, 1)'/>
                        <rect x='109.5' y='84.5' width='42' height='19' fill='#FF2224' rx='0' ry='0' stroke='#FFFFFF' stroke-width='1'/>
                        <rect x='196.5' y='65.5' width='42' height='40' fill='none' rx='0' ry='0' stroke='black' stroke-width='5' isShadow='true' stroke-opacity='0.049999999999999996' transform='translate(1, 1)'/>
                        <rect x='196.5' y='65.5' width='42' height='42' fill='none' rx='0' ry='0' stroke='black' stroke-width='3' isShadow='true' stroke-opacity='0.09999999999999999' transform='translate(1, 1)'/>
                        <rect x='196.5' y='65.5' width='42' height='44' fill='none' rx='0' ry='0' stroke='black' stroke-width='1' isShadow='true' stroke-opacity='0.15' transform='translate(1, 1)'/>
                        <rect x='196.5' y='65.5' width='42' height='45' fill='#FF2224' rx='0' ry='0' stroke='#FFFFFF' stroke-width='1'/>
                    </g>
                    <g class='highcharts-markers' visibility='visible' zIndex='0.1' transform='translate(61,10) scale(1 1)' clip-path='none'/>
                    <g class='highcharts-series highcharts-tracker' visibility='visible' zIndex='0.1' transform='translate(61,10) scale(1 1)' style='' clip-path='url(#highcharts-1)'>
                        <rect x='22.5' y='32.5' width='42' height='157' fill='none' rx='0' ry='0' stroke='black' stroke-width='5' isShadow='true' stroke-opacity='0.049999999999999996' transform='translate(1, 1)'/>
                        <rect x='22.5' y='32.5' width='42' height='159' fill='none' rx='0' ry='0' stroke='black' stroke-width='3' isShadow='true' stroke-opacity='0.09999999999999999' transform='translate(1, 1)'/>
                        <rect x='22.5' y='32.5' width='42' height='161' fill='none' rx='0' ry='0' stroke='black' stroke-width='1' isShadow='true' stroke-opacity='0.15' transform='translate(1, 1)'/>
                        <rect x='22.5' y='32.5' width='42' height='162' fill='#2073CC' rx='0' ry='0' stroke='#FFFFFF' stroke-width='1'/>
                        <rect x='109.5' y='103.5' width='42' height='86' fill='none' rx='0' ry='0' stroke='black' stroke-width='5' isShadow='true' stroke-opacity='0.049999999999999996' transform='translate(1, 1)'/>
                        <rect x='109.5' y='103.5' width='42' height='88' fill='none' rx='0' ry='0' stroke='black' stroke-width='3' isShadow='true' stroke-opacity='0.09999999999999999' transform='translate(1, 1)'/>
                        <rect x='109.5' y='103.5' width='42' height='90' fill='none' rx='0' ry='0' stroke='black' stroke-width='1' isShadow='true' stroke-opacity='0.15' transform='translate(1, 1)'/>
                        <rect x='109.5' y='103.5' width='42' height='91' fill='#2073CC' rx='0' ry='0' stroke='#FFFFFF' stroke-width='1'/>
                        <rect x='196.5' y='110.5' width='42' height='79' fill='none' rx='0' ry='0' stroke='black' stroke-width='5' isShadow='true' stroke-opacity='0.049999999999999996' transform='translate(1, 1)'/>
                        <rect x='196.5' y='110.5' width='42' height='81' fill='none' rx='0' ry='0' stroke='black' stroke-width='3' isShadow='true' stroke-opacity='0.09999999999999999' transform='translate(1, 1)'/>
                        <rect x='196.5' y='110.5' width='42' height='83' fill='none' rx='0' ry='0' stroke='black' stroke-width='1' isShadow='true' stroke-opacity='0.15' transform='translate(1, 1)'/>
                        <rect x='196.5' y='110.5' width='42' height='84' fill='#2073CC' rx='0' ry='0' stroke='#FFFFFF' stroke-width='1'/>
                    </g>
                    <g class='highcharts-markers' visibility='visible' zIndex='0.1' transform='translate(61,10) scale(1 1)' clip-path='none'/></g>
                <g class='highcharts-data-labels highcharts-tracker' visibility='visible' zIndex='6' transform='translate(61,10) scale(1 1)' opacity='1' style=''>
                    <g zIndex='1' transform='translate(35,33)' style='cursor:default;'>
                        <text x='3' zIndex='1' style='font-size:11px;color:#FFFFFF;fill:#FFFFFF;' y='15'>25</text>
                    </g>
                    <g zIndex='1' transform='translate(122,104)' style='cursor:default;'>
                        <text x='3' zIndex='1' style='font-size:11px;color:#FFFFFF;fill:#FFFFFF;' y='15'>14</text>
                    </g>
                    <g zIndex='1' transform='translate(209,111)' style='cursor:default;'>
                        <text x='3' zIndex='1' style='font-size:11px;color:#FFFFFF;fill:#FFFFFF;' y='15'>13</text>
                    </g>
                </g>
                <g class='highcharts-legend' zIndex='7' transform='translate(343,95)'>
                    <g zIndex='1'>
                        <g>
                            <g class='highcharts-legend-item' zIndex='1' transform='translate(8,3)'>
                                <path fill='none' d='M 0 11 L 16 11' stroke-dasharray='6,2' stroke='#000' stroke-width='2'/>
                                <path fill='#000' d='M 8 7 C 13.328 7 13.328 15 8 15 C 2.6719999999999997 15 2.6719999999999997 7 8 7 Z'/>
                                <text x='21' style='color:#333333;font-size:12px;font-weight:bold;cursor:pointer;fill:#333333;' text-anchor='start' zIndex='2' y='15'>Target</text>
                            </g>
                            <g class='highcharts-legend-item' zIndex='1' transform='translate(8,17)'>
                                <text x='21' y='15' style='color:#333333;font-size:12px;font-weight:bold;cursor:pointer;fill:#333333;' text-anchor='start' zIndex='2'>
                                    <tspan>Not met</tspan>
                                </text>
                                <rect x='0' y='4' width='16' height='12' zIndex='3' fill='#FF2224'/>
                            </g>
                            <g class='highcharts-legend-item' zIndex='1' transform='translate(8,31)'>
                                <text x='21' y='15' style='color:#333333;font-size:12px;font-weight:bold;cursor:pointer;fill:#333333;' text-anchor='start' zIndex='2'>
                                    <tspan>Met - ".$user_short_name."</tspan>
                                </text>
                                <rect x='0' y='4' width='16' height='12' zIndex='3' fill='#2073CC'/>
                            </g>
                        </g>
                    </g>
                </g>
                <g class='highcharts-axis-labels highcharts-xaxis-labels' zIndex='7'>
                    <text x='104.66666666666666' text-anchor='middle' style='width:67px;color:#606060;cursor:default;font-size:11px;fill:#606060;' y='224' opacity='1'>Mar</text>
                    <text x='192' text-anchor='middle' style='width:67px;color:#606060;cursor:default;font-size:11px;fill:#606060;' y='224' opacity='1'>Apr</text>
                    <text x='279.3333333333333' text-anchor='middle' style='width:67px;color:#606060;cursor:default;font-size:11px;fill:#606060;' y='224' opacity='1'>May</text>
                </g>
                <g class='highcharts-axis-labels highcharts-yaxis-labels' zIndex='7'>
                    <text x='46' text-anchor='end' style='width:129px;color:#606060;cursor:default;font-size:11px;fill:#606060;' y='209.5' opacity='1'>0</text>
                    <text x='46' text-anchor='end' style='width:129px;color:#606060;cursor:default;font-size:11px;fill:#606060;' y='144.83333333333331' opacity='1'>10</text>
                    <text x='46' text-anchor='end' style='width:129px;color:#606060;cursor:default;font-size:11px;fill:#606060;' y='80.16666666666666' opacity='1'>20</text>
                    <text x='46' text-anchor='end' style='width:129px;color:#606060;cursor:default;font-size:11px;fill:#606060;' y='15.5' opacity='1'>30</text>
                </g>
                <g class='highcharts-tooltip' zIndex='8' style='cursor:default;padding:0;white-space:nowrap;' transform='translate(0,-9999)'>
                    <path fill='none' d='M 3 0 L 13 0 C 16 0 16 0 16 3 L 16 13 C 16 16 16 16 13 16 L 3 16 C 0 16 0 16 0 13 L 0 3 C 0 0 0 0 3 0' isShadow='true' stroke='black' stroke-opacity='0.049999999999999996' stroke-width='5' transform='translate(1, 1)'/>
                    <path fill='none' d='M 3 0 L 13 0 C 16 0 16 0 16 3 L 16 13 C 16 16 16 16 13 16 L 3 16 C 0 16 0 16 0 13 L 0 3 C 0 0 0 0 3 0' isShadow='true' stroke='black' stroke-opacity='0.09999999999999999' stroke-width='3' transform='translate(1, 1)'/>
                    <path fill='none' d='M 3 0 L 13 0 C 16 0 16 0 16 3 L 16 13 C 16 16 16 16 13 16 L 3 16 C 0 16 0 16 0 13 L 0 3 C 0 0 0 0 3 0' isShadow='true' stroke='black' stroke-opacity='0.15' stroke-width='1' transform='translate(1, 1)'/>
                    <path fill='rgba(249, 249, 249, .85)' d='M 3 0 L 13 0 C 16 0 16 0 16 3 L 16 13 C 16 16 16 16 13 16 L 3 16 C 0 16 0 16 0 13 L 0 3 C 0 0 0 0 3 0'/>
                    <text x='8' zIndex='1' style='font-size:12px;color:#333333;fill:#333333;' y='21'></text>
                </g>
            </svg>
        </div>
    </div>
    <script>
        $(function ()
        {
          $('#mainPageMeetings').highcharts(
          {
            chart:
            {
              events :
              {
                load : edgeExtend,
                redraw : edgeExtend
              }
            },
            title: {
                text: ''
            },
            legend: {
                layout: 'vertical',
                align: 'right',
                verticalAlign: 'middle',
                borderWidth: 0
            },
            plotOptions:
            {
              column:
              {
                stacking: 'normal'
              },
              area:
              {
                marker:
                {
                  enabled: false,
                  symbol: 'circle',
                  radius: 1,
                  states: {
                      hover: {
                          enabled: true
                      }
                  }
                },
                fillColor:
                {
                  linearGradient: { x1: 0, y1: 0, x2: 0, y2: 1},
                  stops:
                  [
                    [0, '#FFF6E5'],
                    [1, '#FFEECE']
                  ]
                },
                fillOpacity: 0.5,
                lineWidth: 0,
                shadow: false,
                threshold: null
              }
            },
            xAxis: {
              categories: ['".$months[0]."','".$months[1]."','".$months[2]."']
            },
            yAxis:
             {
                title:
                {
                  text: 'Meetings'
                },
             },
            tooltip: {
                shared: true,
                valueSuffix: ' meeting(s)'
            },
            series: [{
                type: 'line',
                color: '#000',
                name: 'Target',
                data: [27,27,27],
                dashStyle: 'ShortDash'
            },
            {
            type: 'column',
            name: 'Not met',
            stack: '".$user_short_name."',
            data: [".$not_mets[0].",".$not_mets[1].",".$not_mets[2]."],
            color: '#FF2224' },
            {
            type: 'column',
            name: 'Met - ".$user_short_name."',
            stack: '".$user_short_name."',
            data: [".$monthly_new_candidate_met[0].",".$monthly_new_candidate_met[1].",".$monthly_new_candidate_met[2]."],
            color: '#2073CC' ,
            dataLabels:
            {
              enabled: true,
              rotation: 0,
              color: '#FFFFFF',
              align: 'center',
              verticalAlign: 'top'
            }},]
          });
        });
      </script>
    ";

    return $table;
  }

  function strtotimefix($val,$timestamp=0)
  {
    if ($timestamp==0){ $timestamp = time(); }
    if (date('m') == date('m',strtotime('-1 month'))){
      $timestamp = strtotime('-3 days',$timestamp);
    }else{
      if($timestamp==0){$timestamp = time();}
    }
    $strtotime = strtotime($val,$timestamp);
    return $strtotime;
  }

  function get_new_candidates_in_play($user_id)
  {
    $user_information = getUserInformaiton($user_id);
    $user_short_name = $user_information['id'];

    $start_date_3 = (new DateTime('first day of this month'))->format("Y-m-d");
    $start_date_3.= ' 00:00:00';

    $end_date_3 = (new DateTime('last day of this month'))->format("Y-m-d");
    $end_date_3 .= ' 23:59:59';

    $start_date3 = strtotime($start_date_3);
    $end_date3 = strtotime($end_date_3);

    $start_date2 = strtotime($start_date_3.' -1 months');
    $end_date2 = strtotime($end_date_3.' -1 months');
    if(date('m',$start_date2) != date('m',$end_date2))
    {
      $end_date2 = strtotime(date('Y-m-d H:i:s',$end_date2).' -1 days');
    }

    $start_date1 = strtotime($start_date_3.' -2 months');
    $end_date1 = strtotime($end_date_3.' -2 months');
    if(date('m',$start_date1) != date('m',$end_date1))
    {
      $end_date1 = strtotime(date('Y-m-d H:i:s',$end_date1).' -1 days');
    }


    $monthName3 = date('M',$start_date3);
    $monthName2 = date('M',$start_date2);
    $monthName1 = date('M',$start_date1);

    $months = array($monthName1,$monthName2,$monthName3);

    $start_date3 = date('Y-m-d H:i:s',$start_date3);
    $end_date3 = date('Y-m-d H:i:s',$end_date3);

    $start_date2 = date('Y-m-d H:i:s',$start_date2);
    $end_date2 = date('Y-m-d H:i:s',$end_date2);

    $start_date1 = date('Y-m-d H:i:s',$start_date1);
    $end_date1 = date('Y-m-d H:i:s',$end_date1);

    $in_play1 = get_objectives_in_play($user_id, $start_date1, $end_date1);
    $in_play2 = get_objectives_in_play($user_id, $start_date2, $end_date2);
    $in_play3 = get_objectives_in_play($user_id, $start_date3, $end_date3);

    $count_in_play_candidate1 = count($in_play1[$user_id]['new_candidates']);
    $count_in_play_candidate2 = count($in_play2[$user_id]['new_candidates']);
    $count_in_play_candidate3 = count($in_play3[$user_id]['new_candidates']);

    $candidate_in_plays = array($count_in_play_candidate1,$count_in_play_candidate2,$count_in_play_candidate3);

    $table = create_new_candidate_table($user_short_name,$months,$candidate_in_plays);

    return $table;
  }

  function get_new_positions_in_play($user_id)
  {
    $user_information = getUserInformaiton($user_id);
    $user_short_name = $user_information['id'];

    $start_date_3 = (new DateTime('first day of this month'))->format("Y-m-d");
    $start_date_3.= ' 00:00:00';

    $end_date_3 = (new DateTime('last day of this month'))->format("Y-m-d");
    $end_date_3 .= ' 23:59:59';

    $start_date3 = strtotime($start_date_3);
    $end_date3 = strtotime($end_date_3);

    $start_date2 = strtotime($start_date_3.' -1 months');
    $end_date2 = strtotime($end_date_3.' -1 months');
    if(date('m',$start_date2) != date('m',$end_date2))
    {
      $end_date2 = strtotime(date('Y-m-d H:i:s',$end_date2).' -1 days');
    }

    $start_date1 = strtotime($start_date_3.' -2 months');
    $end_date1 = strtotime($end_date_3.' -2 months');
    if(date('m',$start_date1) != date('m',$end_date1))
    {
      $end_date1 = strtotime(date('Y-m-d H:i:s',$end_date1).' -1 days');
    }


    $monthName3 = date('M',$start_date3);
    $monthName2 = date('M',$start_date2);
    $monthName1 = date('M',$start_date1);

    $months = array($monthName1,$monthName2,$monthName3);

    $start_date3 = date('Y-m-d H:i:s',$start_date3);
    $end_date3 = date('Y-m-d H:i:s',$end_date3);

    $start_date2 = date('Y-m-d H:i:s',$start_date2);
    $end_date2 = date('Y-m-d H:i:s',$end_date2);

    $start_date1 = date('Y-m-d H:i:s',$start_date1);
    $end_date1 = date('Y-m-d H:i:s',$end_date1);

    $in_play1 = get_objectives_in_play($user_id, $start_date1, $end_date1);
    $in_play2 = get_objectives_in_play($user_id, $start_date2, $end_date2);
    $in_play3 = get_objectives_in_play($user_id, $start_date3, $end_date3);

    $count_in_play_candidate1 = count($in_play1[$user_id]['new_positions']);
    $count_in_play_candidate2 = count($in_play2[$user_id]['new_positions']);
    $count_in_play_candidate3 = count($in_play3[$user_id]['new_positions']);

    $positions_in_plays = array($count_in_play_candidate1,$count_in_play_candidate2,$count_in_play_candidate3);

    $table = create_new_position_table($user_short_name,$months,$positions_in_plays);

    return $table;
  }

  function get_meetings($user_id)
  {
    $user_information = getUserInformaiton($user_id);
    $user_short_name = $user_information['id'];

    $start_date_3 = (new DateTime('first day of this month'))->format("Y-m-d");
    $start_date_3.= ' 00:00:00';

    $end_date_3 = (new DateTime('last day of this month'))->format("Y-m-d");
    $end_date_3 .= ' 23:59:59';

    $start_date3 = strtotime($start_date_3);
    $end_date3 = strtotime($end_date_3);

    $start_date2 = strtotime($start_date_3.' -1 months');
    $end_date2 = strtotime($end_date_3.' -1 months');
    if(date('m',$start_date2) != date('m',$end_date2))
    {
      $end_date2 = strtotime(date('Y-m-d H:i:s',$end_date2).' -1 days');
    }

    $start_date1 = strtotime($start_date_3.' -2 months');
    $end_date1 = strtotime($end_date_3.' -2 months');
    if(date('m',$start_date1) != date('m',$end_date1))
    {
      $end_date1 = strtotime(date('Y-m-d H:i:s',$end_date1).' -1 days');
    }


    $monthName3 = date('M',$start_date3);
    $monthName2 = date('M',$start_date2);
    $monthName1 = date('M',$start_date1);


    $start_date3 = date('Y-m-d H:i:s',$start_date3);
    $end_date3 = date('Y-m-d H:i:s',$end_date3);

    $start_date2 = date('Y-m-d H:i:s',$start_date2);
    $end_date2 = date('Y-m-d H:i:s',$end_date2);

    $start_date1 = date('Y-m-d H:i:s',$start_date1);
    $end_date1 = date('Y-m-d H:i:s',$end_date1);



    $count_new_met_3 = get_objectives_new_candidate_met($user_id, $start_date3, $end_date3);
    $count_new_met_2 = get_objectives_new_candidate_met($user_id, $start_date2, $end_date2);
    $count_new_met_1 = get_objectives_new_candidate_met($user_id, $start_date1, $end_date1);

    $count_not_met_3 = get_not_happened_meetings($user_id, $start_date3, $end_date3);
    $count_not_met_2 = get_not_happened_meetings($user_id, $start_date2, $end_date2);
    $count_not_met_1 = get_not_happened_meetings($user_id, $start_date1, $end_date1);

    $monthly_new_candidate_met = array($count_new_met_1,$count_new_met_2,$count_new_met_3);
    $months = array($monthName1,$monthName2,$monthName3);
    $not_mets = array($count_not_met_1,$count_not_met_2,$count_not_met_3);

    /*var_dump($months);
    echo '<br><br>';
    var_dump($start_date3);
    var_dump($end_date3);
    echo '<br><br>';
    var_dump($start_date2);
    var_dump($end_date2);
    echo '<br><br>';
    var_dump($start_date1);
    var_dump($end_date1);
    exit;*/

    $table = create_meetings_table($user_short_name,$monthly_new_candidate_met,$months,$not_mets);

    return $table;
  }

  function get_objectives($user_id)
  {
    //$user_id = '314'; // test Mitch
    $start_date = (new DateTime('first day of this month'))->format("Y-m-d");
    $start_date.= ' 00:00:00';

    $end_date = (new DateTime('last day of this month'))->format("Y-m-d");
    $end_date .= ' 23:59:59';

    $in_play = get_objectives_in_play($user_id, $start_date, $end_date);

    //$in_play_candidate = $in_play[$user_id]['new_candidates'];
    $count_in_play_candidate = count($in_play[$user_id]['new_candidates']);

    $count_in_play_position = count($in_play[$user_id]['new_positions']);
    //$in_play_position = $in_play[$user_id]['new_positions'];

    $new_met = get_objectives_new_candidate_met($user_id, $start_date, $end_date);

    $count_new_met = $new_met;//count($new_met[$user_id]);

    $table = create_objectives_table($count_in_play_candidate, $count_in_play_position, $count_new_met);

    return $table;
    //$start_date = start month;
    //$end_date = end month;

  }

  function getCandidateNotes($candidate_id)
  {
    $oDB = CDependency::getComponentByName('database');

    /*$sQuery = "SELECT e.* FROM event_link el
               INNER JOIN event e on e.eventpk = el.eventfk
               WHERE el.cp_pk ='".$candidate_id."' AND e.type = 'character' ORDER BY e.eventpk DESC";*/

   $sQuery = "SELECT LEFT(e.content,500) as content FROM event_link el
   INNER JOIN event e on e.eventpk = el.eventfk
   WHERE el.cp_pk ='".$candidate_id."' AND e.type = 'character' ORDER BY e.eventpk DESC";

    $db_result = $oDB->executeQuery($sQuery);
    $read = $db_result->readFirst();
    $result = $db_result->getData();

    $result['content'] = str_replace("'","",$result['content']);
    $result['content'] = str_replace('"',"",$result['content']);
    return $result;
  }

  function suggestCandidate($array)
  {
    $oDB = CDependency::getComponentByName('database');
    $sDate = date('Y-m-d H:i:s');
    $client_id = $array['client_id'];
    $position_link_id = $array['sl_position_linkpk'];
    $consultant_note = $array['consultant_note'];
    $user_id = $array['user_id'];
    $note = $array['note'];

    $sQuery = "INSERT INTO `suggested_candidates` (`client_id`,`position_link_id`, `consultant_note`, `user_id`,`first_activity`,`last_activity`)
               VALUES('".$client_id."','".$position_link_id."','".$note."','".$user_id."','".$sDate."','".$sDate."')";

    $db_result = $oDB->executeQuery($sQuery);

  }

  function getClientUsers()
  {
    $oDB = CDependency::getComponentByName('database');

    $sQuery = "SELECT * FROM client_login cl WHERE cl.flag = 'a' ";

    $db_result = $oDB->executeQuery($sQuery);

    $result = $db_result->getAll();

    return $result;
  }

  function getCandidateInformation($candidate_id)
  {
    $sNow = date('Y-m-d H:i:s');
    $oDB = CDependency::getComponentByName('database');

    $sQuery = "SELECT slc.*,slcp.*,sc.name as company_name,
               IFNULL(TIMESTAMPDIFF(YEAR, slc.date_birth, '".$sNow."'), '-') AS age
               FROM sl_candidate slc
               LEFT JOIN sl_candidate_profile slcp on slcp.candidatefk = slc.sl_candidatepk
               LEFT JOIN sl_company sc on sc.sl_companypk = slcp.companyfk
               WHERE slc.sl_candidatepk = '".$candidate_id."'";

    $db_result = $oDB->executeQuery($sQuery);
    $read = $db_result->readFirst();

    $result = $db_result->getData();

    return $result;
  }

  function getCandidateContactInfo($candidate_id)
  {
    $oDB = CDependency::getComponentByName('database');

    $sQuery = "SELECT slc.* FROM sl_contact slc
    WHERE slc.itemfk = ".$candidate_id;

    $db_result = $oDB->executeQuery($sQuery);

    $read = $db_result->readFirst();
    $contact_info = array();

    while ($read)
    {
      $row = $db_result->getData();
      array_push($contact_info, $row);
      $read = $db_result->readNext();
    }

    return $contact_info;
  }

  function getPositionByCompany($company_id)
  {
    $oDB = CDependency::getComponentByName('database');

    $sQuery = "SELECT slp.* FROM sl_position slp
    WHERE slp.companyfk = ".$company_id;

    $db_result = $oDB->executeQuery($sQuery);

    $result = $db_result->getAll();

    return $result;
  }

  function getCandidateByCompany($company_id)
  {
    $oDB = CDependency::getComponentByName('database');

    $sQuery = "SELECT slp.* FROM sl_candidate_profile slp
    WHERE slp.companyfk = ".$company_id;

    $db_result = $oDB->executeQuery($sQuery);

    $result = $db_result->getAll();

    return $result;
  }

  function getOldCompanyByCompanyId($company_id)
  {
    $oDB = CDependency::getComponentByName('database');

    $sQuery = "SELECT slp.* FROM sl_candidate_old_companies slp
    WHERE slp.company_id = ".$company_id;

    $db_result = $oDB->executeQuery($sQuery);

    $result = $db_result->getAll();

    return $result;
  }

  function getDocumentLinkByCompany($company_id)
  {
    $oDB = CDependency::getComponentByName('database');

    $sQuery = "SELECT slp.* FROM document_link slp
    WHERE slp.cp_type = 'comp' AND slp.cp_pk = ".$company_id;

    $db_result = $oDB->executeQuery($sQuery);

    $result = $db_result->getAll();

    return $result;
  }

  function getEventByCompany($company_id)
  {
    $oDB = CDependency::getComponentByName('database');

    $sQuery = "SELECT slp.* FROM event_link slp
    WHERE slp.cp_type = 'comp' AND slp.cp_pk = ".$company_id;

    $db_result = $oDB->executeQuery($sQuery);

    $result = $db_result->getAll();

    return $result;
  }

  function updateMergedCompanies($id,$id_name,$company_id,$company_id_name,$table_name)
  {
    $oDB = CDependency::getComponentByName('database');
    $sDate = date('Y-m-d H:i:s');

    $sQuery = "UPDATE ".$table_name." SET ".$company_id_name." = '".$company_id."' WHERE ".$id_name." = '".$id."' ";
    //ChromePhp::log($sQuery);
    $db_result = $oDB->executeQuery($sQuery);
  }

  function updateMergedCompany($old_company_id,$new_company_id)
  {
    $oDB = CDependency::getComponentByName('database');
    $sDate = date('Y-m-d H:i:s');

    $sQuery = "UPDATE sl_company SET merged_company_id = '".$new_company_id."' WHERE sl_companypk = '".$old_company_id."' ";
    //ChromePhp::log($sQuery);
    $db_result = $oDB->executeQuery($sQuery);
  }

  function findRelatedCompanies($old_company_id,$new_company_id)
  {
    updateMergedCompany($old_company_id,$new_company_id);
    $relatedEvents = getEventByCompany($old_company_id); // company_id = cp_pk
    $relatedDocuments = getDocumentLinkByCompany($old_company_id); // company_id = cp_pk
    $relatedOldCompanies = getOldCompanyByCompanyId($old_company_id); // company_id = company_id
    $relatedCandidates = getCandidateByCompany($old_company_id); // company_id = companyfk
    $relatedPositions = getPositionByCompany($old_company_id); // company_id = companyfk

    if(count($relatedEvents) > 0)
    {
      foreach ($relatedEvents as $key => $value)
      {
        $id = $value['event_linkpk'];
        $id_name = 'event_linkpk';
        $company_id = $new_company_id;// sent new company id to change
        $company_id_name = 'cp_pk';
        $table_name = 'event_link';

        updateMergedCompanies($id,$id_name,$company_id,$company_id_name,$table_name);
      }
    }

    if(count($relatedDocuments) > 0)
    {
      foreach ($relatedDocuments as $key => $value)
      {
        $id = $value['document_linkpk'];
        $id_name = 'document_linkpk';
        $company_id = $new_company_id;// sent new company id to change
        $company_id_name = 'cp_pk';
        $table_name = 'document_link';

        updateMergedCompanies($id,$id_name,$company_id,$company_id_name,$table_name);
      }
    }

    if(count($relatedOldCompanies) > 0)
    {
      foreach ($relatedOldCompanies as $key => $value)
      {
        $id = $value['id'];
        $id_name = 'id';
        $company_id = $new_company_id;// sent new company id to change
        $company_id_name = 'company_id';
        $table_name = 'sl_candidate_old_companies';

        updateMergedCompanies($id,$id_name,$company_id,$company_id_name,$table_name);
      }
    }

    if(count($relatedCandidates) > 0)
    {
      foreach ($relatedCandidates as $key => $value)
      {
        $id = $value['sl_candidate_profilepk'];
        $id_name = 'sl_candidate_profilepk';
        $company_id = $new_company_id;// sent new company id to change
        $company_id_name = 'companyfk';
        $table_name = 'sl_candidate_profile';

        updateMergedCompanies($id,$id_name,$company_id,$company_id_name,$table_name);
      }
    }

    if(count($relatedPositions) > 0)
    {
      foreach ($relatedPositions as $key => $value)
      {
        $id = $value['sl_positionpk'];
        $id_name = 'sl_positionpk';
        $company_id = $new_company_id;// sent new company id to change
        $company_id_name = 'companyfk';
        $table_name = 'sl_position';

        updateMergedCompanies($id,$id_name,$company_id,$company_id_name,$table_name);
      }
    }

  }

  function checkCompanyLevels()
  {
    $oDB = CDependency::getComponentByName('database');

    $dateNow = date('Y-m-d H:i:s');
    $m6 = date('Y-m-d H:i:s', strtotime('-6 months'));

    $sQuery = "SELECT * FROM sl_company slc WHERE slc.";

    $db_result = $oDB->executeQuery($sQuery);

    $result = $db_result->getAll();
  }

  function updateCompanyLevel($company_id, $level,$user_id)
  {
    $oDB = CDependency::getComponentByName('database');
    $sDate = date('Y-m-d H:i:s');

    $sQuery = "UPDATE  sl_company SET is_client = '1', level = '".$level."', level_update_day = '".$sDate."',
    updated_by = '".$user_id."' WHERE sl_companypk = '".$company_id."' ";


    $db_result = $oDB->executeQuery($sQuery);

  }

  function getCompanyOwner($company_id)
  {
    $oDB = CDependency::getComponentByName('database');

    $sQuery = "SELECT DISTINCT(co.user_id) as owner FROM client_owner_list co WHERE co.company_id = '".$company_id."' AND co.flag = 'a'";

    $db_result = $oDB->executeQuery($sQuery);

    $result = $db_result->getAll();

    return $result;
  }

  function fillCompanyOwnerTable()
  {
    $oDB = CDependency::getComponentByName('database');

    $sQuery = "SELECT l.* FROM sl_position l";

    $db_result = $oDB->executeQuery($sQuery);

    $result = $db_result->getAll();

    foreach ($result as $key => $value)
    {
      $company_id = $value['companyfk'];
      $first_activity = $value['date_created'];
      $last_activity = $value['date_created'];
      $user_id = $value['created_by'];

      $sQueryInsert = "INSERT INTO `client_owner_list` (`user_id`,`company_id`, `first_activity`, `last_activity`)
               VALUES('".$user_id."','".$company_id."','".$first_activity."','".$last_activity."')";

      $db_result = $oDB->executeQuery($sQueryInsert);
    }
  }

  function getActiveUsers()
  {
    $oDB = CDependency::getComponentByName('database');

    $sQuery = "SELECT l.* FROM login l
    WHERE l.status = 1 and l.kpi_flag = 'a'";

    $db_result = $oDB->executeQuery($sQuery);

    $result = $db_result->getAll();

    return $result;

  }

  function reduceOwners()
  {
    $oDB = CDependency::getComponentByName('database');

    $sQuery = "SELECT l.* FROM client_owner_list l ";

    $db_result = $oDB->executeQuery($sQuery);

    $result = $db_result->getAll();

    return $result;

  }

  function getCompanyInformation($company_id)
  {
    $oDB = CDependency::getComponentByName('database');

    $sQuery = "SELECT slc.* FROM sl_company slc
    WHERE slc.sl_companypk = ".$company_id;

    $db_result = $oDB->executeQuery($sQuery);

    $read = $db_result->readFirst();

    while ($read)
    {
      $row = $db_result->getData();
      $read = $db_result->readNext();
    }

    return $row;
  }

  function getLevels($level)
  {
    if($level == 1)
    {
      return 'A';
    }
    else if($level == 2)
    {
      return 'B';
    }
    else if($level == 3)
    {
      return 'C';
    }
    else
    {
      return 'H';
    }
  }

  function getLevelIcon($level)
  {
    if($level == 1)
    {
      return 'clientLevelA';
    }
    else if($level == 2)
    {
      return 'clientLevelB';
    }
    else if($level == 3)
    {
      return 'clientLevelC';
    }
    else if($level == 4)
    {
      return 'clientLevelH';
    }
    else
    {
      return '';
    }
  }


  function getPositionInformation($position_id)
  {
    $oDB = CDependency::getComponentByName('database');

    $sQuery = "SELECT slc.* FROM sl_position slp
    INNER JOIN sl_company slc on slc.sl_companypk = slp.companyfk
    WHERE slp.sl_positionpk = ".$position_id;

    $db_result = $oDB->executeQuery($sQuery);

    $read = $db_result->readFirst();

    while ($read)
    {
      $row = $db_result->getData();
      $read = $db_result->readNext();
    }

    return $row;
  }

  function getPositionData($position_id)
  {
    $oDB = CDependency::getComponentByName('database');

    $sQuery = "SELECT slpd.* FROM sl_position_detail slpd
    WHERE slpd.positionfk = '".$position_id."'";

    $db_result = $oDB->executeQuery($sQuery);

    $read = $db_result->readFirst();

    while ($read)
    {
      $row = $db_result->getData();
      $read = $db_result->readNext();
    }

    return $row;
  }

  function insertEvent($type,$content,$created_by, $cp_pk)
  {

  }

  function getPositionRelatedUsers($position_id, $exclude_user,$user_id)
  {
    $oDB = CDependency::getComponentByName('database');

    $sQuery = "SELECT DISTINCT(slp.candidatefk) as candidate_id FROM sl_position_link slp WHERE slp.active = '1' AND slp.positionfk = '".$position_id."' AND slp.candidatefk <> '".$exclude_user."' ";

    $db_result = $oDB->executeQuery($sQuery);

    $result = $db_result->getAll();

    $return = multiCandidateFallenOff($result,$position_id.$user_id);

    return $return;
  }

  function updateCandidateSkills($candidate_id, $skillArray)
  {
    $oDB = CDependency::getComponentByName('database');

    $skill_ag = $skillArray['skill_ag'];
    $skill_ap = $skillArray['skill_ap'];
    $skill_am = $skillArray['skill_am'];
    $skill_mp = $skillArray['skill_mp'];
    $skill_in = $skillArray['skill_in'];
    $skill_ex = $skillArray['skill_ex'];
    $skill_fx = $skillArray['skill_fx'];
    $skill_ch = $skillArray['skill_ch'];
    $skill_ed = $skillArray['skill_ed'];
    $skill_pl = $skillArray['skill_pl'];
    $skill_e  = $skillArray['skill_e'];

    $sQuery = "UPDATE  sl_candidate SET skill_ag = '".$skill_ag."',skill_ap = '".$skill_ap."'
    ,skill_am = '".$skill_am."',skill_mp = '".$skill_mp."',skill_in = '".$skill_in."'
    ,skill_ex = '".$skill_ex."',skill_fx = '".$skill_fx."',skill_ch = '".$skill_ch."'
    ,skill_ed = '".$skill_ed."',skill_pl = '".$skill_pl."',skill_e = '".$skill_e."'
    WHERE sl_candidatepk = '".$candidate_id."' ";

    $db_result = $oDB->executeQuery($sQuery);
  }

  function multiCandidateFallenOff($candidates,$position_id,$user_id)
  {
    $oDB = CDependency::getComponentByName('database');
    $sDate = date('Y-m-d H:i:s');

    foreach ($candidates as $key => $value)
    {
      $candidate_id = $value['candidate_id'];
      $sQuery = "UPDATE sl_position_link SET flag = 'p' WHERE candidatefk = '".$candidate_id."' AND positionfk = '".$position_id."' ";

      $db_result = $oDB->executeQuery($sQuery);

      $sQuery = "INSERT INTO sl_position_link VALUES ('".$position_id."','".$candidate_id."','".$sDate."','".$user_id."','200','0','Auto fallen off','".$sDate."','1','".$sDate."') ";

      $db_result = $oDB->executeQuery($sQuery);
    }

    return true;
  }

  function returnSerializedSearch()
  {
    $oDB = CDependency::getComponentByName('database');

    $sQuery = "SELECT * FROM customfield cf WHERE cf.customfieldpk = '1' ";

    $db_result = $oDB->executeQuery($sQuery);

    $result = $db_result->getAll();

    return $result;
  }

  function getCompanyHistory($candidate_id)
  {
    $oDB = CDependency::getComponentByName('database');

    $sQuery = "SELECT * FROM login_system_history lhs WHERE lhs.table = 'company_history' AND lhs.cp_pk = '".$candidate_id."' AND lhs.flag = 'a'";

    $db_result = $oDB->executeQuery($sQuery);

    $result = $db_result->getAll();

    return $result;
  }

  /*function getCandidateInformation($candidate_id)
  {
    $oDB = CDependency::getComponentByName('database');

    $sQuery = "SELECT c.*,cp.*
               FROM sl_candidate c
               INNER JOIN sl_candidate_profile cp on cp.candidatefk = c.sl_candidatepk
               WHERE c.sl_candidatepk = '".$candidate_id."'";

    $db_result = $oDB->executeQuery($sQuery);

    $result = $db_result->getAll();

    return $result;
  }*/

  function getLoggedQuery($searchID)
  {
    $oDB = CDependency::getComponentByName('database');

    $sQuery = "SELECT * FROM login_system_history lhs WHERE lhs.login_system_historypk = '".$searchID."'";

    $db_result = $oDB->executeQuery($sQuery);

    $result = $db_result->getAll();

    return $result;
  }

  function getSearchLogs($user_id)
  {
    $oDB = CDependency::getComponentByName('database');

    $sQuery = "SELECT * FROM login_system_history lhs WHERE (lhs.table = 'quick_search' OR lhs.table = 'complex_search') AND lhs.userfk = '".$user_id."' ORDER BY lhs.login_system_historypk DESC";

    $db_result = $oDB->executeQuery($sQuery);

    $result = $db_result->getAll();

    return $result;
  }

  function getGrade($grade_id)
  {
    if($grade_id == 0)
    {
      return 'No grade';
    }
    else if($grade_id == 1)
    {
      return 'Met';
    }
    else if($grade_id == 2)
    {
      return 'Low notable';
    }
    else if($grade_id == 3)
    {
      return 'High notable';
    }
    else if($grade_id == 4)
    {
      return 'Top shelf';
    }
    else
    {
      return '-';
    }
  }

  function clientExistCheck($email)
  {
    $oDB = CDependency::getComponentByName('database');

    $sQuery = "SELECT COUNT(*) as count FROM client_login cl WHERE cl.email = '".$email."'";

    $db_result = $oDB->executeQuery($sQuery);

    $result = $db_result->getAll();

    if($result[0]['count'] == 0)//ekleyebiliriz
    {
      return true;
    }
    else
    {
      return false;
    }
  }

  function addNewClient($array)
  {
    $oDB = CDependency::getComponentByName('database');

    $sDate = date('Y-m-d H:i:s');

    $email = $array['email'];
    $firstname = $array['firstname'];
    $lastname = $array['lastname'];
    $password = $array['password_crypted'];
    $company = $array['position'];
    $user_id = $array['user_id'];
    $first_activity = $sDate;
    $last_activity = $sDate;
    $phone = $array['phone'];

    $addFlag = clientExistCheck($email);

    if($addFlag)
    {
      $sQuery = "INSERT INTO `client_login`(`email`,`firstname`,`lastname`,`phone`,`password`, `company`, `user_id`
              , `first_activity`,`last_activity`)
               VALUES('".$email."','".$firstname."','".$lastname."','".$phone."','".$password."','".$company."','".$user_id."','".$first_activity."','".$last_activity."')";

      $db_result = $oDB->executeQuery($sQuery);

      return true;
    }
    else
    {
      return false;
    }

  }

  function insertLog($loginfk, $cp_pk, $text,$table = "user_history",$desctiption = '',$cp_type = "candi")
  {
    if($loginfk > 0)
    {
      $cp_uid = "555-001";
      $cp_action = "ppasa";
      $sDate = date('Y-m-d H:i:s');
      $component = "555-001_ppav_candi_".$cp_pk;
      $uri = "https://slistem.slate.co.jp/index.php5?uid=555-001&ppa=ppav&ppt=candi&ppk=".$cp_pk."&pg=ajx";

      $oDB = CDependency::getComponentByName('database');

      $sQuery = "INSERT INTO `login_system_history`(`date`,`userfk`,`action`,`component`, `cp_uid`, `cp_action`, `cp_type`, `cp_pk`, `uri`, `table`, `description`)
                 VALUES('".$sDate."','".$loginfk."','".$text."','".$component."','".$cp_uid."','".$cp_action."','".$cp_type."','".$cp_pk."','".$uri."','".$table."','".$desctiption."')";


      $db_result = $oDB->executeQuery($sQuery);
    }

    return true;
  }

  function getCandidateOldCompanies($candidate_id)
  {
    $oDB = CDependency::getComponentByName('database');

    $sQuery = "SELECT * from sl_candidate_old_companies oc WHERE oc.candidate_id = '".$candidate_id."' ORDER BY oc.id DESC";

    $db_result = $oDB->executeQuery($sQuery);

    $result = $db_result->getAll();

    return $result;
  }

  function deletePlacementNote($asPosition,$user_id)
  {
    $oDB = CDependency::getComponentByName('database');

    $candidate_id = $asPosition['candidatefk'];
    $company_id = $asPosition['companyfk'];
    $position_id = $asPosition['positionfk'];


    if($asPosition['status'] == '101') // placement geri alinmissa silecegiz
    {
      $now = date('Y-m-d H:i:s');

      $sQuery = "SELECT e.* FROM event e
                 INNER JOIN event_link el on el.eventfk = e.eventpk
                 WHERE el.cp_pk = '".$candidate_id."' AND (content LIKE '%Placement%' OR content LIKE '%[placed]%')
                 AND (content LIKE '%".$company_id."%')";

      $db_result = $oDB->executeQuery($sQuery);

      $result = $db_result->getAll();


      foreach ($result as $key => $value)
      {
        $event_id = $value['eventpk'];
        $sQuery = "UPDATE event SET flag = 'p' , date_update = '".$now."' , updated_by = '".$user_id."' WHERE eventpk = '".$event_id."'";

        $db_result = $oDB->executeQuery($sQuery);
      }


      $sQuery2 = " SELECT * FROM login_system_history lsh WHERE lsh.cp_pk = '".$candidate_id."'
                AND ( action LIKE '%[placed]%') AND (action LIKE '%#".$position_id."%')";


      $db_result2 = $oDB->executeQuery($sQuery2);

      $result2 = $db_result2->getAll();

      foreach ($result2 as $key => $value)
      {

        $sQuery3 = "UPDATE login_system_history SET flag = 'p' WHERE login_system_historypk = '".$value['login_system_historypk']."'";

        $db_result3 = $oDB->executeQuery($sQuery3);
      }

      $old_companies = getCandidateOldCompanies($candidate_id);
      //ChromePhp::log($old_companies);
      if(isset($old_companies[1]))
      {
        $previousCompany = $old_companies[1];// 0 suanki company oluyor.
        $previous_company_id = $previousCompany['company_id'];
        //ChromePhp::log($previousCompany); // hem old company yenilenecek hem candidate...

        updateOldCompany($candidate_id,$previous_company_id);

        $sQuery = "UPDATE sl_candidate_profile SET companyfk = '".$previous_company_id."'  WHERE candidatefk = '".$candidate_id."'";

        $oDB->executeQuery($sQuery);


      }

    }
  }

  function getGeneratedKpi($date)
  {
    $oDB = CDependency::getComponentByName('database');
    $sQuery = "SELECT * FROM sl_generated_kpi gk WHERE gk.created_date = '".$date."' ORDER BY gk.id ASC";

    $db_result = $oDB->executeQuery($sQuery);

    $result = $db_result->getAll();

    $result = str_replace('|','\'',$result);

    return $result;
  }

  function insertGeneratedKpi($json_data,$user_id,$type)
  {
    $oDB = CDependency::getComponentByName('database');
    $dateTimeNow = date('Y-m-d H:i:s');
    $dateNow = date('Y-m-d');

    $json_data = str_replace('\'','|',$json_data);

    $sQuery = "INSERT INTO sl_generated_kpi (type, json_data, created_date, first_activity, last_activity,user_id)
               VALUES ('".$type."','".$json_data."','".$dateNow."','".$dateTimeNow."','".$dateTimeNow."','".$user_id."')";

    $oDB->executeQuery($sQuery);

    return true;
  }


  function autoUpdateCompanyLevels()
  {
    $oDB = CDependency::getComponentByName('database');
    $dateNow = date('Y-m-d H:i:s');
    $m6 = date('Y-m-d H:i:s', strtotime('-6 months'));
    $m12 = date('Y-m-d H:i:s', strtotime('-12 months'));;
    $m18 = date('Y-m-d H:i:s', strtotime('-18 months'));

    //ChromePhp::log($m6);
    //ChromePhp::log($m12);
    //ChromePhp::log($m18);

    $sQuery = "SELECT slpl.date_created as pl_date, slp.companyfk as company_id
               FROM sl_position_link slpl
               INNER JOIN sl_position slp on slp.sl_positionpk = slpl.positionfk
               WHERE slpl.status < 102";

    $db_result = $oDB->executeQuery($sQuery);

    $allPositionLinks = $db_result->getAll();

    $in = '';

    foreach ($allPositionLinks as $key => $value)
    {
      $pl_date = $value['pl_date'];
      $company_id = $value['company_id'];

      if($pl_date > $m6)
      {
        $level = 1;
      }
      elseif($pl_date <= $m6 && $pl_date > $m12)
      {
        $level = 2;
      }
      elseif($pl_date <= $m12 && $pl_date > $m18)
      {
        $level = 3;
      }
      else
      {
        $level = 8;
      }

      $sQuery = "SELECT * FROM sl_company slc WHERE slc.sl_companypk = '".$company_id."'";

      $db_result = $oDB->executeQuery($sQuery);

      $selectedCompany = $db_result->getAll();
      $selectedCompany = $selectedCompany[0];

      if($selectedCompany['level'] > $level && $level < 8)
      {
        $in .= $company_id.",";
        $sQuery = "UPDATE sl_company SET is_client = 1, level = '".$level."', level_update_day = '".$dateNow."'
        WHERE sl_companypk = '".$company_id."'";

        $oDB->executeQuery($sQuery);
      }
    }

    //ChromePhp::log($in);

  }

  function updateOldCompany($candidate_id,$company_id)
  {

    $oDB = CDependency::getComponentByName('database');

    $dateNow = date('Y-m-d H:i:s');
    $sQuery = "UPDATE sl_candidate_old_companies SET flag = 'p' , last_activity = '".$dateNow."' WHERE candidate_id = '".$candidate_id."'";

    $oDB->executeQuery($sQuery);

    $sQuery = "INSERT INTO sl_candidate_old_companies (candidate_id, company_id, first_activity, last_activity)
               VALUES ('".$candidate_id."','".$company_id."','".$dateNow."','".$dateNow."')";

    $oDB->executeQuery($sQuery);

    return true;

  }

  function securityCheckSearch($user_id)
  {
    // if user do more than 5 search in 5 minutes

    $now = date('Y-m-d H:i:s');
    $fiveMinBefore = date('Y-m-d H:i:s', strtotime('-5 minutes'));

    $oDB = CDependency::getComponentByName('database');

    $sQuery = "SELECT COUNT(*) as count
               FROM login_system_history lsh
               WHERE (lsh.table = 'quick_search' OR lsh.table = 'complex_search' OR lsh.table = 'other_search')
               AND lsh.userfk = '".$user_id."' AND date >= '".$fiveMinBefore."' ";

    $db_result = $oDB->executeQuery($sQuery);

    $result = $db_result->getAll();
    $count = $result[0]['count'];

    if($user_id != '101' AND $count >= 10) // count starts from 0
    {
      //ChromePhp::log('Action: Do more than 5 searches in 5 minutes.');
      $dNow = date('Y-m-d H:i:s'); // Japan time
      $sQuery = "INSERT INTO `security_alert` (`user_id`,`type`,`action_date`)
                 VALUES('".$user_id."','search_in_five','".$dNow."')";

      $db_result = $oDB->executeQuery($sQuery);

      $user_information = getUserInformaiton($user_id);
      $username = $user_information['firstname']." ".$user_information['lastname'];

      //$to      = 'munir@slate-ghc.com';
      $to      = 'ray@slate-ghc.com;mmoir@slate.co.jp;munir@slate-ghc.com;rkiyamu@slate.co.jp';
      $subject = 'Slistem Activity Flag';
      $message = "Slistem activity flag, user: ".$username." (#".$user_id.") date: ".$dNow." (Japan time)";
      $message .= "\r\n"."Action: Do more than 10 searches in 5 minutes.";
      $headers = 'From: slistem@slate.co.jp' . "\r\n" .
          'Reply-To: munir@slate-ghc.com' . "\r\n" .
          'X-Mailer: PHP/' . phpversion();

      $flag = securityMailControl($user_id,'search_in_five');
      if($flag) // ayni gun mail atilmis mi kontrol ediyoruz
      {
        mail($to, $subject, $message, $headers);
      }
    }

  }

  function securityCheckContactView($user_id)
  {
    $oDB = CDependency::getComponentByName('database');

    $sQuery = "SELECT * FROM  login_system_history lsh WHERE lsh.action = 'Contacts viewed' AND lsh.userfk = '".$user_id."'
               ORDER BY lsh.login_system_historypk DESC LIMIT 5"; // desc yapmazsak son kayitlara ulasamiyoruz

    $db_result = $oDB->executeQuery($sQuery);

    $result = $db_result->getAll();

    if($user_id != '101' AND isset($result[4]))
    {
      $first = $result[4]; // 5 kayittan ilk olani sectik
      $controlDate = $first['date'];

      $sQuery = "SELECT COUNT(*) as count FROM  login_system_history lsh
                 WHERE (lsh.action LIKE '%created a new character note%'
                 OR lsh.action LIKE '%created a new email note%'
                 OR lsh.action LIKE '%created a new meeting note%'
                 OR lsh.action LIKE '%created a new phone note%'
                 OR lsh.action LIKE '%created a new update note%'
                 OR lsh.action LIKE '%created a new company history note%'
                 OR lsh.action LIKE '%created a new note%')
                 AND lsh.userfk = '".$user_id."' AND lsh.date > '".$controlDate."'";

      $db_result = $oDB->executeQuery($sQuery);

      $result = $db_result->getAll();

      $count = $result[0]['count'];

      if($count == 0) // 0 ise herhangi bir not girmemis demek oluyor.
      {
        //ChromePhp::log('Action: View 5 contact details but not any note entry.');
        $dNow = date('Y-m-d H:i:s'); // Japan time
        $sQuery = "INSERT INTO `security_alert` (`user_id`,`type`,`action_date`)
                   VALUES('".$user_id."','contact_view','".$dNow."')";

        $db_result = $oDB->executeQuery($sQuery);

        $user_information = getUserInformaiton($user_id);
        $username = $user_information['firstname']." ".$user_information['lastname'];

        //$to      = 'munir@slate-ghc.com';
        $to      = 'ray@slate-ghc.com;mmoir@slate.co.jp;munir@slate-ghc.com;rkiyamu@slate.co.jp';
        $subject = 'Slistem Activity Flag';
        $message = "Slistem activity flag, user: ".$username." (#".$user_id.") date: ".$dNow." (Japan time)";
        $message .= "\r\n"."Action: View 5 contact details but not any note entry.";
        $headers = 'From: slistem@slate.co.jp' . "\r\n" .
            'Reply-To: munir@slate-ghc.com' . "\r\n" .
            'X-Mailer: PHP/' . phpversion();

        $flag = securityMailControl($user_id,'contact_view');
        if($flag) // ayni gun mail atilmis mi kontrol ediyoruz
        {
          mail($to, $subject, $message, $headers);
        }
      }
    }

  }

  function sendHtmlMail($to,$subject, $message)
  {
    //$to      = 'ray@slate-ghc.com;mmoir@slate.co.jp;munir@slate-ghc.com;rkiyamu@slate.co.jp';
    //$subject = 'Slistem Activity Flag';
    //$message = "Slistem activity flag, user: ".$username." (#".$user_id.") date: ".$dNow." (Japan time)";
    //$message .= "\r\n"."Action: View more than 50 candidates on holiday.";
    $headers = 'From: slistem@slate.co.jp' . "\r\n" .
        'Cc: rkiyamu@slate.co.jp' . "\r\n" .
        'Bcc: munir@slate-ghc.com' . "\r\n" .
        'X-Mailer: PHP/' . phpversion();

    mail($to, $subject, $message, $headers);

  }

  function securityCheckView($user_id)
  {
    // if saturday and holiday than look for that days count > 50?
    // db holiday table a bugunun tarihini yolla donen olursa holiday flag 1 yap
    $dayname = date('l'); // dayname
    $dNow = date('Y-m-d'); // sadece yil-ay-gun

    $holidays = getHolidayCount($dNow); // 0 gelince patlamiyor...
    //ChromePhp::log($holidays[0]['count']);

    if($user_id != '101' AND ($dayname == 'Saturday' || $dayname == 'Sunday') && $holidays[0]['count'] > 0) //Japan Saturday & Sunday
    {
      $startDate = $dNow." 00:00:00";
      $endDate = $dNow." 23:59:59";

      $oDB = CDependency::getComponentByName('database');

      $sQuery = "SELECT count(*) as count FROM  login_system_history lsh
      WHERE lsh.table = 'user_history_all_view' AND userfk = '".$user_id."'
      AND lsh.date >= '".$startDate."' AND lsh.date <= '".$endDate."' ";

      $db_result = $oDB->executeQuery($sQuery);

      $result = $db_result->getAll();

      if($result[0]['count'] > 50) // 50 den buyuk ise mail
      {
        //ChromePhp::log('Action: View more than 50 candidates on holiday.');
        $dNow = date('Y-m-d H:i:s'); // Japan time
        $sQuery = "INSERT INTO `security_alert` (`user_id`,`type`,`action_date`)
                   VALUES('".$user_id."','holiday_fifty_view','".$dNow."')";

        $db_result = $oDB->executeQuery($sQuery);

        $user_information = getUserInformaiton($user_id);
        $username = $user_information['firstname']." ".$user_information['lastname'];

        //$to      = 'munir@slate-ghc.com';
        $to      = 'ray@slate-ghc.com;mmoir@slate.co.jp;munir@slate-ghc.com;rkiyamu@slate.co.jp';
        $subject = 'Slistem Activity Flag';
        $message = "Slistem activity flag, user: ".$username." (#".$user_id.") date: ".$dNow." (Japan time)";
        $message .= "\r\n"."Action: View more than 50 candidates on holiday.";
        $headers = 'From: slistem@slate.co.jp' . "\r\n" .
            'Reply-To: munir@slate-ghc.com' . "\r\n" .
            'X-Mailer: PHP/' . phpversion();

        $flag = securityMailControl($user_id,'holiday_fifty_view');
        if($flag) // ayni gun mail atilmis mi kontrol ediyoruz
        {
          mail($to, $subject, $message, $headers);
        }

      }
    }

  }

  function getCompanyInfo($company_id)
  {
    $oDB = CDependency::getComponentByName('database');

    $sQuery = "SELECT scom.*, sind.label as indus_name, sind.sl_industrypk FROM sl_company as scom
        LEFT JOIN sl_attribute as satt ON (satt.`type` = 'cp_indus' AND satt.itemfk = scom.sl_companypk)
        LEFT JOIN sl_industry as sind ON (sind.sl_industrypk = satt.attributefk)
        WHERE scom.sl_companypk = '".$company_id."'";

    $db_result = $oDB->executeQuery($sQuery);

    $result = $db_result->getAll();

    return $result;
  }

  function securityMailControl($user_id,$type)
  {
    $oDB = CDependency::getComponentByName('database');

    $dNow = date('Y-m-d');
    $startDate = $dNow." 00:00:00";
    $endDate = $dNow." 23:59:59";

    $sQuery = "SELECT count(*) as count FROM  security_alert sa
    WHERE sa.user_id = '".$user_id."' AND sa.type = '".$type."'
    AND sa.action_date >= '".$startDate."' AND sa.action_date <= '".$endDate."' ";

    $db_result = $oDB->executeQuery($sQuery);

    $result = $db_result->getAll();
    if($result[0]['count'] > 1) // once kaydettigimiz icin 1 mi diye bakiyoruz
    {
      return false; // ayni gun mail atmisiz birdaha atmayalim
    }
    else
    {
      return true;
    }
  }

  function getHolidayCount($date)
  {
    $oDB = CDependency::getComponentByName('database');

    $sQuery = "SELECT count(*) as count FROM  holidays h WHERE h.flag = 'a' AND holiday_date = '".$date."' ORDER BY h.holiday_date ASC";

    $db_result = $oDB->executeQuery($sQuery);

    $result = $db_result->getAll();

    return $result;
  }

  function getHolidays()
  {
    $oDB = CDependency::getComponentByName('database');

    $sQuery = "SELECT * FROM  holidays h WHERE h.flag = 'a' ORDER BY h.holiday_date ASC";

    $db_result = $oDB->executeQuery($sQuery);

    $result = $db_result->getAll();

    return $result;
  }

  function getAILogsCount($type, $user_id)
  {
    $oDB = CDependency::getComponentByName('database');

    $sQuery = "SELECT data, COUNT(*) as count FROM ai_logs WHERE user_id = '".$user_id."' AND type = '".$type."' GROUP BY data ORDER BY count DESC LIMIT 10 ";

    $db_result = $oDB->executeQuery($sQuery);

    $result = $db_result->getAll();

    return $result;
  }

  function getAILogs($type)
  {
    $oDB = CDependency::getComponentByName('database');

    $sQuery = "SELECT * FROM ai_logs WHERE type = '".$type."' ";

    $db_result = $oDB->executeQuery($sQuery);

    $result = $db_result->getAll();

    return $result;
  }

  function getSelectedSlNote($id)
  {
    $oDB = CDependency::getComponentByName('database');

    $sQuery = "SELECT * FROM sl_notes n WHERE id = '".$id."'";

    $db_result = $oDB->executeQuery($sQuery);

    $result = $db_result->getAll();

    return $result;
  }

  function getSlNotes($candidate_id)
  {
    $oDB = CDependency::getComponentByName('database');
    //$sDate = date('Y-m-d H:i:s');
    //$sDate = strtotime($sDate.' -1 years');
    //$sDate = date('Y-m-d',$sDate);
    //$sDate .= " 00:00:00";

    $sQuery = "SELECT * FROM sl_notes n WHERE n.candidate_id = '".$candidate_id."' ORDER BY n.id ASC";

    $db_result = $oDB->executeQuery($sQuery);

    $result = $db_result->getAll();

    return $result;

  }

  function insertNote($array)
  {
    $sDate = date('Y-m-d H:i:s');
    $oDB = CDependency::getComponentByName('database');

    $candidate_id = $array['candidate_id'];
    $type = $array['type'];
    $content = $array['content'];
    $content = str_replace('\'','`',$content);
    $user_id = $array['user_id'];

    $sQuery = "INSERT INTO `sl_notes` (`candidate_id`,`type`,`content`,`user_id`, `first_activity`, `last_activity`)
               VALUES('".$candidate_id."','".$type."','".$content."','".$user_id."','".$sDate."','".$sDate."')";

    $db_result = $oDB->executeQuery($sQuery);

    return true;
  }

  function editNote($editCandidate,$array)
  {
    $sDate = date('Y-m-d H:i:s');
    $oDB = CDependency::getComponentByName('database');

    $candidate_id = $array['candidate_id'];
    $type = $array['type'];
    $content = $array['content'];
    $content = str_replace('\'','`',$content);
    $user_id = $array['user_id'];

    $sQuery="UPDATE `sl_notes` SET `content` = '".$content."',`updated_by` = '".$user_id."',`last_activity` = '".$sDate."' WHERE `candidate_id` = '".$editCandidate."' AND `type` = '".$type."'";

    $db_result = $oDB->executeQuery($sQuery);

    return $db_result;
  }

  function insertAILog($type,$data,$user_id)
  {

    $sDate = date('Y-m-d H:i:s');
    $oDB = CDependency::getComponentByName('database');

    $sQuery = "INSERT INTO `ai_logs` (`type`,`data`,`first_activity`,`last_activity`, `user_id`)
               VALUES('".$type."','".$data."','".$sDate."','".$sDate."','".$user_id."')";

    $db_result = $oDB->executeQuery($sQuery);

    return true;
  }

  function getPreStatus($candidate_id, $position_id)
  {
    $oDB = CDependency::getComponentByName('database');

    $sQuery = "SELECT * FROM sl_position_link slp WHERE slp.status <> '150' AND slp.status <> '151' AND slp.status <> '200' AND slp.status <> '201' AND slp.status <> '251' AND slp.candidatefk = '".$candidate_id."' AND slp.positionfk = '".$position_id."' ORDER BY slp.sl_position_linkpk DESC";

    $db_result = $oDB->executeQuery($sQuery);

    $result = $db_result->getAll();
//ChromePhp::log($result);
    if(isset($result[0]))
    {
      $preStatus = $result[0]['status'];
      $preDate = $result[0]['date_created'];
    }
    else
    {
      $preStatus = 0;
      $preDate = ' - ';
    }

    $statusTitle = getStatusTitle($preStatus);
//ChromePhp::log($statusTitle);
    $returnArray = array($statusTitle,$preDate);
    return $returnArray;

  }

  function getNoteTitle($type)
  {
    if($type == 'personality_note')
    {
      return "What Does he/she do/Skills?";
    }
    else if($type == 'career_note')
    {
      return "Management and Leadership";
    }
    else if($type == 'education_note')
    {
      return "Major career accomplishments/Education";
    }
    else if($type == 'move_note')
    {
      return "Presence and Communication";
    }
    else if($type == 'compensation_note')
    {
      return "Career Plan and Compensation";
    }
    else if($type == 'past_note')
    {
      return "Current Company Description/Recent Intros";
    }
    else
    {
      return $type;
    }
  }

  function getStatusTitle($status_id)
  {
    if($status_id == '1')
    {
      return "Pitch";
    }
    else if($status_id == '2')
    {
      return "Resume sent";
    }
    else if($status_id == '51')
    {
      return "CCM1";
    }
    else if($status_id == '52')
    {
      return "CCM2";
    }
    else if($status_id == '53')
    {
      return "CCM3";
    }
    else if($status_id == '54')
    {
      return "CCM4";
    }
    else if($status_id > '54' && $status_id <= '70')
    {
      return "MCCM";
    }
    else if($status_id == '100')
    {
      return "Offer";
    }
    else if($status_id == '101')
    {
      return "Placed";
    }
    else if($status_id == '150' || $status_id == '151')
    {
      return "Expired";
    }
    else if($status_id == '200')
    {
      return "Fallen off";
    }
    else if($status_id == '201')
    {
      return "Not Interested";
    }
    else
    {
      return "-";
    }

  }

  function getUserInformaiton($user_id)
  {
    $oDB = CDependency::getComponentByName('database');

    $sQuery = "SELECT * FROM login l WHERE l.loginpk = ".$user_id;

    $db_result = $oDB->executeQuery($sQuery);

    $read = $db_result->readFirst();

    while ($read)
    {
      $row = $db_result->getData();
      $read = $db_result->readNext();
    }

    return $row;

  }

  function addIndustry($pnPk, $psLabel, $pnStatus, $pnParentfk)
  {
    if(!assert('is_integer($pnPk) && is_integer($pnStatus) && is_integer($pnStatus)'))
      return false;

    $oDb = CDependency::getComponentByName('database');

    if(!empty($pnPk))
    {
      $sQuery = 'INSERT INTO `addressbook_industry` (`addressbook_industrypk`, `name`, `status`, `parentfk`) VALUES ';
      $sQuery.= ' ('.$pnPk.', '.$oDb->dbEscapeString($psLabel).', '.$oDb->dbEscapeString($pnStatus).', '.$oDb->dbEscapeString($pnParentfk).') ';
    }
    else
    {
      $sQuery = 'INSERT INTO `addressbook_industry` (`name`, `status`, `parentfk`) VALUES ';
      $sQuery.= ' ('.$oDb->dbEscapeString($psLabel).', '.$oDb->dbEscapeString($pnStatus).', '.$oDb->dbEscapeString($pnParentfk).') ';
    }

    $oDbResult = $oDb->ExecuteQuery($sQuery);
    if(!$oDbResult)
      return false;

    return true;
  }


  function formatNumber($pvValue, $psDisplayMode = '', $psSeparator = ',', $psCurrency = '')
  {
    if(!assert('is_numeric($pvValue)'))
      return ' - ';

    if(is_float($pvValue))
      $vValue = (float)$pvValue;
    else
      $vValue = (int)$pvValue;

    if($vValue == 0)
      return 0;

    switch($psDisplayMode)
    {
      case 'compact':

        if($vValue > 1000000)
        {
          if($psCurrency)
            $psCurrency.= ' M';

           return number_format(round(($vValue/1000000), 3), 0, '.', $psSeparator).$psCurrency;
        }

        if($vValue > 1000)
        {
          if($psCurrency)
            $psCurrency.= ' K';

          return number_format(round(($vValue/1000), 2), 0, '.', $psSeparator).$psCurrency;
        }

        if($psCurrency)
            $psCurrency.= ' ';

        return number_format(round($vValue, 2), 0, '.', $psSeparator).$psCurrency;
        break;

      case 'M':
        if($psCurrency)
            $psCurrency.= ' M';

        if($vValue >= 500000)
          return number_format(round(($vValue/1000000), 3), 1, '.', $psSeparator).$psCurrency;
        else
          return number_format(round(($vValue/1000000), 3), 2, '.', $psSeparator).$psCurrency;
        break;

       case 'K':
         if($psCurrency)
            $psCurrency.= ' K';

         if($vValue >= 500)
           return number_format(round(($vValue/1000), 2), 1, '.', $psSeparator).$psCurrency;
         else
           return number_format(round(($vValue/1000), 2), 2, '.', $psSeparator).$psCurrency;
        break;

      default:
        return number_format($vValue, 0, '.', $psSeparator).' '.$psCurrency;
    }



  }

function trace($psMessage, $pbForce = false)
{
  if($pbForce || isDevelopment())
  {
    $_SESSION['debug']['trace'][] = date('H:i:s').': '.$psMessage;

    $nTraces = count($_SESSION['debug']['trace']);
    if($nTraces > 25)
      array_splice ($_SESSION['debug']['trace'] , 0, $nTraces-25 );
  }

  return true;
}




  //========================================================================================================
  //========================================================================================================
  // Array related functions

  function simplexmlToArray($poXml, $pasResult = array ())
  {
    if(!is_object($poXml))
      return $pasResult;

    foreach ((array)$poXml as $vKey => $oNode)
    {
      $pasResult[(string)$vKey] = ( is_object ($oNode) ) ? xml2array ($oNode) : (string)$oNode;
    }

    return $pasResult;
  }

/**
 *Multi-usage function:
 *
 *    - initialize array indexes if used like:              set_array($asArray['non_existant_key']);
 *
 *    - replace if(!isset(){ ... } :                        set_array($asArray['key'], 'aaa');
 *
 *    - replace if(!isset(){ $asArray['key'] = 'aaa'; }
 *              else{ $asArray['key'] = 'bbb'; } :          set_array($asArray['key'], 'aaa', 'bbb');
 *
 *    - as a consequence, initialize variables !! to not use for that!!
 *                                                          set_array($vDoNotExist);
 *
 * @param php variable $poSrcString
 * @param mixed $pvNotSet
 * @param mixed $pvIfExist
 * @return true
 */
function set_array(&$poSrcString, $pvNotSet = null, $pvIfExist = null)
{
  //simply initialize $poSrcString with null
  if($pvIfExist === null && $pvNotSet === null)
  {
    return $poSrcString = '';
  }

  //based on params, concat $pvNotSet or $pvIfExist to $poSrcString
  if($pvIfExist !== null && $poSrcString !== null)
  {
    $poSrcString.= $pvIfExist;
  }
  elseif($poSrcString === null && $pvNotSet !== null)
  {
    $poSrcString.= $pvNotSet;
  }

  return true;
}


function highlightKeywords($psText, $psKeywords)
{
  if(!assert('is_string($psText) && !empty($psText) && is_string($psKeywords) && !empty($psKeywords)'))
    return '';

  $sOutput = str_replace(strtolower($psKeywords), '<span class=\'highlight\'>'.strtolower($psKeywords).'</span>', strtolower($psText));

  return $sOutput;
}

function getNationality($pvNationalityPk = 0, $pbCompact = true)
{
  $oDB = CDependency::getComponentByName('database');
  if(!$oDB)
    return array();

  if(!assert('is_integer($pvNationalityPk) || is_arrayOfInt($pvNationalityPk)'))
    return array();

  $sQuery = ' SELECT * FROM system_nationality ';

  if(!empty($pvNationalityPk))
  {
    if(is_integer($pvNationalityPk))
    {
      $sQuery.= ' WHERE system_nationalitypk = '.$pvNationalityPk;
    }
    else
      $sQuery.= ' WHERE system_nationalitypk IN ('.implode(',', $pvNationalityPk).') ';
  }

  $sQuery.= ' ORDER BY nationality_name ';

  $oDbResult = $oDB->executeQuery($sQuery);
  $bRead = $oDbResult->readFirst();
  if(!$bRead)
    return array();

  $asNationality = array();
  while($bRead)
  {
    if($pbCompact)
      $asNationality[$oDbResult->getFieldValue('system_nationalitypk')] = $oDbResult->getFieldValue('iso');
    else
      $asNationality[$oDbResult->getFieldValue('system_nationalitypk')] = $oDbResult->getData();

    $bRead = $oDbResult->readNext();
  }

  return $asNationality;
}


function getDateRange($psFieldName, $psDefaultStart = '', $psDefaultEnd = '+1 week')
{
  $sDateStart = getValue($psFieldName);
  $sDateEnd = '';

  if(!empty($sDateStart))
  {
    $asDate = explode(' to ', $sDateStart);
    if(is_date($asDate[0]))
      $sDateStart = $asDate[0];

    if(!isset($asDate[1]) || !is_date($asDate[1]))
      $sDateEnd = date('Y-m', strtotime('+1 months')).'-01';
  }

  if(empty($sDateStart))
    $sDateStart = date('Y-m', strtotime($psDefaultStart)).'-01';

  if(empty($sDateEnd))
    $sDateEnd = date('Y-m', strtotime($psDefaultEnd)).'-01';

  return array('start' => $sDateStart, 'end' => $sDateEnd);
}

$gasEncoding = mb_list_encodings();
function convertToUtf8(&$psString)
{
  global $gasEncoding;
  $sEncoding = mb_detect_encoding($psString);

  if(!$sEncoding || !in_array($sEncoding, $gasEncoding))
    $sEncoding = 'UTF-8';

  return mb_convert_encoding($psString, 'UTF-8', $sEncoding);

}


function cleanXmlString($psString)
{
  if(empty($psString))
    return '';

  $psString = str_replace('&', 'and', $psString);
  $sEncoding = mb_detect_encoding($psString);
  $sUTF8 = @mb_convert_encoding($psString, 'utf8'. $sEncoding);

  if(!empty($sUTF8))
   return $sUTF8;

  return $psString;
}

/**
 * Remove "dangerous" html tags from a string
 * @param string $psHTML
 * @param boolean $pbStrict
 * @param string $pbCustomTags
 * @return type
 */
function sanitizeHtml($psHTML, $pbStrict = false, $psCustomTags = '')
{
  if(empty($psCustomTags))
  {
    if($pbStrict)
      $psCustomTags = '<a><b><em><p><i><span><br>';
    else
      $psCustomTags = '<a><b><em><p><div><i><ul><li><span><br>';
  }

  $psHTML = strip_tags($psHTML, $psCustomTags);
  $psHTML = preg_replace('/(javascript|javascript\:|onload=|onclick=|onchange=|onfocus=|onblur=|onkeypress=|onkeydown=|onkeyup=|onmousedown=|onmouseup=|onmouseover=|onmouseout=)/i', '', $psHTML);

  return $psHTML;
}

function purify_html($dirty_html)
{
  $purifier = new HTMLPurifier();

  return $purifier->purify($dirty_html);
}

/**
 * array_merge_recursive + distinct + not null
 */
function &array_clean_merge(array &$pasArray1, &$pasArray2 = null)
{
  /*dump('merging.... ');
  dump($pasArray1);
  dump(' ++++++ ');
  dump($pasArray2);*/

  $asMerged = $pasArray1;

  if(is_array($pasArray2))
  {
    foreach($pasArray2 as $vKey => $vValue)
    {
      if(!empty($vValue))
      {
        if(is_object($vValue))
          $vValue = (array)$vValue;

        if(is_object($pasArray2[$vKey]))
          $pasArray2[$vKey] = (array)$pasArray2[$vKey];

        if(is_array($pasArray2[$vKey]))
        {
          if(isset($asMerged[$vKey]) && is_array($asMerged[$vKey]))
          {
            $asMerged[$vKey] = array_clean_merge($asMerged[$vKey], $pasArray2[$vKey]);
          }
          else
          {
            if(!empty($pasArray2[$vKey]))
              $asMerged[$vKey] = $pasArray2[$vKey];
          }
        }
        else
        {
          if(isset($asMerged[$vKey]))
          {
            //add the element to existing entry of array 1
            if(is_array($asMerged[$vKey]))
              $asMerged[$vKey][] = $vValue;
            else
            {
              $asMerged[$vKey] = array($asMerged[$vKey]);
              $asMerged[$vKey][] = $vValue;
            }
          }
          else  //insert new element from array2 in array 1
            $asMerged[$vKey] = array($asMerged[$vKey]);

          $asMerged[$vKey] = array_unique((array)$asMerged[$vKey]);
        }
      }
    }
  }

  /*dump(' ===== ');
  dump($asMerged);*/

  return $asMerged;
}


/**
 * maximize compatibility with html-ified urls: copied/paste or comming from emails ...
 * check if we have &amp; symbol in urls and convert variables acordingly
 */
function sanitizeUrl()
{

  if(strpos($_SERVER['QUERY_STRING'], '&amp;'))
  {
    $_SERVER['QUERY_STRING'] = str_replace('&amp;', '&', $_SERVER['QUERY_STRING']);
    $_SERVER['REQUEST_URI'] = str_replace('&amp;', '&', $_SERVER['REQUEST_URI']);

    foreach($_GET as $sVarName => $sValue)
    {
      if(substr($sVarName, 0, 4) == 'amp;')
      {
        $_GET[substr($sVarName, 4)] = $sValue;
      }
    }
  }
}

/**
 * Sorts multidimensional array by string value using natural order, can be reversed
 * @param string $field
 * @param string $order
 * @return -1, 0, 1
 */
function sort_multi_array_by_value($field, $order = 'natural')
{
  return function ($a, $b) use ($field, $order) {
    if ($order == 'reverse')
    {
      return strnatcmp($b[$field], $a[$field]);
    }
    else
    {
      return strnatcmp($a[$field], $b[$field]);
    }
  };
}

function check_session_expiry()
{
  $expiry_time = 60 * 60 * 3; // 3 hour
  //$expiry_time = 60; // 1 min?
  $logout = false;
  if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > $expiry_time))
  {
    $logout = true;
    //$url = 'https://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
    //header($url);
    //exit();
  }
  $_SESSION['LAST_ACTIVITY'] = time(); // update last activity time stamp

  regenerate_seesion_id();

  return $logout;
}

function regenerate_seesion_id()
{
  $expiry_time = 60 * 30; // 30 minutes
  if (!isset($_SESSION['CREATED']))
  {
    $_SESSION['CREATED'] = time();
  }
  else if (time() - $_SESSION['CREATED'] > $expiry_time)
  {
    session_regenerate_id(true);    // change session ID for the current session and invalidate old session ID
    $_SESSION['CREATED'] = time();  // update creation time
  }
}

function currency_html_code($currency)
{
  $currency_code = '';
  switch ($currency)
  {
    case 'cad':
      $currency_code = 'C&#36;';
      break;

    case 'usd':
      $currency_code = '&#36;';
      break;

    case 'aud':
      $currency_code = 'A&#36;';
      break;

    case 'eur':
      $currency_code = '&#128;';
      break;

    case 'hkd':
      $currency_code = 'HK&#36;';
      break;

    case 'php':
      $currency_code = '&#8369;';
      break;

    default:
      $currency_code = '&#165;';
      break;
  }
  return $currency_code;
}