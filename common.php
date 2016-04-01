<?php

date_default_timezone_set('Asia/Calcutta');

error_reporting (0);
include("template.php");
//===============================
// Database Connection Definition
//-------------------------------
//torqhoist.com Connection begin

include("db_mysql.php");

$breakme=0;
$showheader=0;
// Database Initialize
$db = new DB_Sql();
$db->Database = DATABASE_NAME;
$db->User     = DATABASE_USER;
$db->Password = DATABASE_PASSWORD;
$db->Host     = DATABASE_HOST;

$tmb1 = new DB_Sql();
$tmb1->Database = DATABASE_NAME;
$tmb1->User     = DATABASE_USER;
$tmb1->Password = DATABASE_PASSWORD;
$tmb1->Host     = DATABASE_HOST;

$mydb = new DB_Sql();
$mydb->Database = DATABASE_NAME;
$mydb->User     = DATABASE_USER;
$mydb->Password = DATABASE_PASSWORD;
$mydb->Host     = DATABASE_HOST;

$mb = new DB_Sql();
$mb->Database = DATABASE_NAME;
$mb->User     = DATABASE_USER;
$mb->Password = DATABASE_PASSWORD;
$mb->Host     = DATABASE_HOST;


$mb1 = new DB_Sql();
$mb1->Database = DATABASE_NAME;
$mb1->User     = DATABASE_USER;
$mb1->Password = DATABASE_PASSWORD;
$mb1->Host     = DATABASE_HOST;

$mb2 = new DB_Sql();
$mb2->Database = DATABASE_NAME;
$mb2->User     = DATABASE_USER;
$mb2->Password = DATABASE_PASSWORD;
$mb2->Host     = DATABASE_HOST;

$lb = new DB_Sql();
$lb->Database = DATABASE_NAME;
$lb->User     = DATABASE_USER;
$lb->Password = DATABASE_PASSWORD;
$lb->Host     = DATABASE_HOST;


$cnt=0;

// torqhoist.com Connection end

//===============================
// Site Initialization
//-------------------------------
// Obtain the path where this site is located on the server
//-------------------------------
$app_path = ".";
//===============================

function showeditor($editor_name,$editor_val="")
{

	ob_start();
	$oFCKeditor = new FCKeditor($editor_name) ;
	$oFCKeditor->BasePath = './fckeditor/' ;
	$oFCKeditor->Width = '100%' ;
	$oFCKeditor->Height = '350' ;
	$oFCKeditor->Value = $editor_val;
	$oFCKeditor->Create();

	$obcontent=ob_get_contents();
	ob_end_clean();

	return $obcontent;
}

function includer($fn)
{
   ob_start();
   include($fn);
   $cont=ob_get_contents();
   ob_end_clean();
   $value=$cont;
   $value = str_replace("\\","\\\\",$value);
   $value = str_replace("\"","\\\"",$value);
   return $value;
}


//===============================
// Common functions
//-------------------------------
// Convert non-standard characters to HTML
//-------------------------------
function tohtml($strValue)
{
  return htmlspecialchars($strValue);
}

//-------------------------------
// Convert value to URL
//-------------------------------
function tourl($strValue)
{
  return urlencode($strValue);
}

//-------------------------------
// Obtain specific URL Parameter from URL string
//-------------------------------
function get_param($param_name)
{
  global $_POST;
  global $_GET;

  $param_value = "";
  if(isset($_POST[$param_name]))
    $param_value = $_POST[$param_name];
  else if(isset($_GET[$param_name]))
    $param_value = $_GET[$param_name];

  return $param_value;
}



function get_session($param_name)
{
  //global $_POST;
  //global $_GET;
  //global ${$param_name};

  $param_value = "";
  //if(!isset($_POST[$param_name]) && !isset($_GET[$param_name]) && session_is_registered($param_name))
    //$param_value = ${$param_name};
  $param_value = $_SESSION[$param_name];

  return $param_value;
}

function set_session($param_name, $param_value)
{
  //global ${$param_name};
  //if(session_is_registered($param_name))
  //  session_unregister($param_name);
  //${$param_name} = $param_value;
  //session_register($param_name);
  $_SESSION[$param_name]=$param_value;
  //echo "$param_name=$param_value $param_name registered is ".get_session($param_name);
}

function is_number($string_value)
{
  if(is_numeric($string_value) || !strlen($string_value))
    return true;
  else
    return false;
}

//-------------------------------
// Convert value for use with SQL statament
//-------------------------------
function tosql($value, $type)
{
  if(!strlen($value))
    return "NULL";
  else
    if($type == "Number")
      return str_replace (",", ".", doubleval($value));
    else    if($type == "like")
    {
        $value = str_replace("'","''","%".$value."%");
      	return "'" . $value . "'";
    }
	else
    {
        $value = str_replace("'","''",$value);
//        $value = str_replace("\\","\\\\",$value);
      return "'" . $value . "'";
    }
}

function strip($value)
{
  if(get_magic_quotes_gpc() == 0)
    return $value;
  else
    return stripslashes($value);
}

function db_fill_array($sql_query)
{
  global $db;
  $db_fill = new DB_Sql();
  $db_fill->Database = $db->Database;
  $db_fill->User     = $db->User;
  $db_fill->Password = $db->Password;
  $db_fill->Host     = $db->Host;

  $db_fill->query($sql_query);
  if ($db_fill->next_record())
  {
    do
    {
      $ar_lookup[$db_fill->f(0)] = $db_fill->f(1);
    } while ($db_fill->next_record());
    return $ar_lookup;
  }
  else
    return false;

}

//-------------------------------
// Deprecated function - use get_db_value($sql)
//-------------------------------
function dlookup($table_name, $field_name, $where_condition)
{
  $sql = "SELECT " . $field_name . " FROM " . $table_name . " WHERE " . $where_condition;
  return get_db_value($sql);
}


//-------------------------------
// Lookup field in the database based on SQL query
//-------------------------------
function get_db_value($sql)
{
  global $db;
  $db_look = new DB_Sql();
  $db_look->Database = $db->Database;
  $db_look->User     = $db->User;
  $db_look->Password = $db->Password;
  $db_look->Host     = $db->Host;

  $db_look->query($sql);
  if($db_look->next_record())
    return $db_look->f(0);
  else
    return "";
}

//-------------------------------
// Obtain Checkbox value depending on field type
//-------------------------------
function get_checkbox_value($value, $checked_value, $unchecked_value, $type)
{
  if(!strlen($value))
    return tosql($unchecked_value, $type);
  else
    return tosql($checked_value, $type);
}

//-------------------------------
// Obtain lookup value from array containing List Of Values
//-------------------------------
function get_lov_value($value, $array)
{
  $return_result = "";

  if(sizeof($array) % 2 != 0)
    $array_length = sizeof($array) - 1;
  else
    $array_length = sizeof($array);
  reset($array);

  for($i = 0; $i < $array_length; $i = $i + 2)
  {
    if($value == $array[$i]) $return_result = $array[$i+1];
  }

  return $return_result;
}

//-------------------------------
// Verify users security level and redirect to login page if needed
//-------------------------------
function checkuserpermission()
{
 $ses=get_session("register_uid");
  if($ses==0)
  {
    header ("Location:index.php");
    exit;
  }
}

function check_security($security_level)
{

  global $UserRights;
  $ses=$_SESSION["SesUserId"];

  if($ses==0 ||$ses=="")
  {
    header ("Location:login.php");
    exit;
  }
}
function rcheck_security($security_level)
{
  global $UserRights;
  $ses=get_session("RUserID");
  if($ses==0)
  {
    header ("Location:index.php");
    exit;
  }
}
function scheck_security($security_level)
{
  global $UserRights;
  $ses=get_session("SUserID");
  if($ses==0)
  {
    header ("Location:index.php");
    exit;
  }
}

function pcheck_security($security_level)
{
	//return;
  global $UserRights;
  $ses=get_session("PUserID");
  //echo $ses;
  if($ses==0)
  {
    header ("Location:plogin.php");
    exit;
  }
}



function tosql1($value)
{
  if(!strlen($value))
    return "NULL";
  else
    {
        $value = str_replace("\\'","'",$value);
        $value = str_replace("\\\"","\"",$value);
      return  $value ;
    }
}


function insertArrayValues($tablename,$array)
{
	global $tmb1;
	$tmb1->query("select * from $tablename where 1=2");
	$sSQL=$tmb1->insertRecord($tablename,$array);
	$tmb1->query($sSQL);
	$lastid=$tmb1->dbInsertID();
	return $lastid;
}
function updateArrayValues($tablename,$array)
{
	global $tmb1;
	$tmb1->query("select * from $tablename where 1=2");
	$sSQL=$tmb1->updateRecord($tablename,$array);
	$tmb1->query($sSQL);
	return;
}
function updateArrayValuescount($tablename,$array,$icount)
{
	global $tmb1;
	$tmb1->query("select * from $tablename where 1=2");
	$sSQL=$tmb1->updateRecordcount($tablename,$array,$icount);
	$tmb1->query($sSQL);

	return;
}
function deleteArrayValues($tablename,$array)
{
	global $tmb1;
	$sSQL=$tmb1->deleteRecord($tablename,$array);
	$tmb1->query($sSQL);
	return;
}
function createArrayValues($tablename)
{
	global $_POST;
	global $_GET;
	$query1="create table $tablename (id int not null primary key auto_increment";
	$form_fields = array_keys($_GET);
	for ($i = 0; $i < sizeof($form_fields); $i++) {
		$thisField = $form_fields[$i];
		$thisValue = $_GET[$thisField];
		$res=strtolower(substr($thisField,0,3));
		if ($res=="mem")
		{
			$query1=$query1.",$thisField text";
		}
		else if ($res=="int")
		{
			$query1=$query1.",$thisField int";
		}
		else if ($res=="num")
		{
			$query1=$query1.",$thisField float(10,2)";
		}
		else
			$query1=$query1.",$thisField varchar(255)";
	}

	$form_fields = array_keys($_POST);
	for ($i = 0; $i < sizeof($form_fields); $i++) {
		$thisField = $form_fields[$i];
		$thisValue = $_POST[$thisField];
		if ($res=="mem")
		{
			$query1=$query1.",$thisField text";
		}
		else if ($res=="int")
		{
			$query1=$query1.",$thisField int";
		}
		else if ($res=="num")
		{
			$query1=$query1.",$thisField float(10,2)";
		}
		else
			$query1=$query1.",$thisField varchar(255)";
	}
	$query1=$query1.");";
   	echo $query1;
   	return;
}

function getInfoArray()
{
	global $_POST;
	global $_GET;
	$allowtags="<br><b><p><h1><h2><u>";
	if (is_array($_GET))
	{
		$form_fields = array_keys($_GET);
		for ($i = 0; $i < sizeof($form_fields); $i++) {
			$thisField = $form_fields[$i];
			$thisValue = $_GET[$thisField];
			if (is_array($thisValue)){
				for ($j = 0; $j < sizeof($thisValue); $j++) {
					if ($return[$thisField]!="")
						$return[$thisField].=", ".RemoveXSS(stripslashes($thisValue[$j]));
					else
						$return[$thisField].=RemoveXSS(stripslashes($thisValue[$j]));
				}
			} else {
				$return[$thisField]=stripslashes($thisValue);
			}
		}
	}
	if ( is_array($_POST))
	{
		$form_fields = array_keys($_POST);
		for ($i = 0; $i < sizeof($form_fields); $i++) {
			$thisField = $form_fields[$i];
			$thisValue = $_POST[$thisField];
			if (is_array($thisValue)){
				for ($j = 0; $j < sizeof($thisValue); $j++) {
					if ($return[$thisField]!="")
						$return[$thisField].=", ".RemoveXSS(stripslashes($thisValue[$j]));
					else
						$return[$thisField].=RemoveXSS(stripslashes($thisValue[$j]));
				}
			} else {
				$return[$thisField]=stripslashes($thisValue);
			}
		}
	}
	return $return;
}

function setInfoArray($tpl)
{
	global $_POST;
	global $_GET;
	
	if ( is_array($_GET))
	{
		$form_fields = array_keys($_GET);
		for ($i = 0; $i < sizeof($form_fields); $i++) {
			$thisField = $form_fields[$i];
			$thisValue = $_GET[$thisField];
			$mynewValue="";
			if (is_array($thisValue)){
				for ($j = 0; $j < sizeof($thisValue); $j++) {
					$mynewValue.=$thisValue[$j]." ";
				}
			} else {
				$mynewValue=$thisValue;
			}
			setfullvalues($tpl,$thisField,$mynewValue);
		}
	}
	if ( is_array($_POST))
	{
		$form_fields = array_keys($_POST);
		for ($i = 0; $i < sizeof($form_fields); $i++) {
			$thisField = $form_fields[$i];
			$thisValue = $_POST[$thisField];
			$mynewValue="";
			if (is_array($thisValue)){
				for ($j = 0; $j < sizeof($thisValue); $j++) {
					$mynewValue.=$thisValue[$j]." ";
				}
			} else {
				$mynewValue=$thisValue;
			}
			setfullvalues($tpl,$thisField,$mynewValue);
		}
	}
	return $tpl;
}




function showmylist($sSQL,$count,$twoset,$starter="",$includefile=NULL,$includefunction=NULL,$db_count1=0)
{
  global $tpl;
  global $db;
  global $mb;
  global $tmb1;
  $HasParam = false;
  $iRecordsPerPage = $count;
  $iCounter = 0;
  $iPage = 0;
  $bEof = false;

  $db->query($sSQL);

  $iPage = get_session("Form_Page".basename($_SERVER["PHP_SELF"]));
  if ($db_count1>0)
  	$db_count=$db_count1;
  else
  	$db_count = $db->num_rows();
  $tpl->set_var("db_count",$db_count);
  $tpl->set_var("db_count${starter}",$db_count);
  $dResult = intval($db_count) / $iRecordsPerPage;
  $iPageCount = intval($dResult);
  if($iPageCount < $dResult) $iPageCount = $iPageCount + 1;

  $next_record = $db->next_record();
  $tpl->set_var("FullRecord${starter}", "");
  if(!$next_record)
  {
    $tpl->set_var("FullRecord${starter}", "");
//    $tpl->parse("NoRecords${starter}", false);
    $tpl->set_var("Navigator${starter}", "");
  	return;
  }
$tpl->set_var("error", "");
  $iCounter = 0;


  $iPage = get_param("Form_Page");
  if ($iPage=="") $iPage=get_session("Form_Page".basename($_SERVER["PHP_SELF"]));
  if($iPage == "last") $iPage = $iPageCount;
  if ($iPage>$iPageCount) $iPage=$iPageCount;

  set_session("Form_Page".basename($_SERVER["PHP_SELF"]),$iPage);
  //echo basename($_SERVER["PHP_SELF"]);
//  echo $iPage;
//  echo "i am working";



  if(!strlen($iPage)) $iPage = 1; else $iPage = intval($iPage);
  $reviewid=0;
  $kCounter=1;
  if(($iPage - 1) * $iRecordsPerPage != 0)
  {
    do
    {
      $iCounter++;
      $kCounter++;
    } while ($iCounter < ($iPage - 1) * $iRecordsPerPage && $db->next_record());
    $next_record = $db->next_record();
  }

  $iCounter = 0;
  $prevname="";
  $previd=0;
  $tpl->set_var("startsrno${starter}",$kCounter);
  $tpl->set_var("startsrno",$kCounter);
  while($next_record  && $iCounter < $iRecordsPerPage)
  {
		if ($twoset>=2 or $twoset==true)
		{
			$tpl->set_var("firsthalf${starter}", "");
			$tpl->set_var("secondhalf${starter}", "");
		}
		if ($twoset>=3)
		{
			$tpl->set_var("thirdhalf${starter}", "");
		}
		if ($twoset>=4)
		{
			$tpl->set_var("forthhalf${starter}", "");
		}
		if ($twoset>=5)
		{
			for($im=1;$im<=$twoset;$im++)
			{
				$tpl->set_var("${im}half${starter}", "");
			}
		}

		if ($twoset>=5)
		{
			$tpl->set_var("srno${starter}",$iCounter);
			$tpl->set_var("ksrno${starter}",$kCounter);
			$tpl->set_var("srno",$iCounter);
			$tpl->set_var("ksrno",$kCounter);
			$tpl=$db->setvalues($tpl);
			if ($includefunction!=NULL)
			{
				$includefunction();
			}
			$tpl->parse("1half${starter}", false);
			for($im=2;$im<=$twoset;$im++)
			{

				if ($db->next_record())
				{
					$iCounter++;
					$kCounter++;
					$tpl->set_var("srno${starter}",$iCounter);
					$tpl->set_var("ksrno${starter}",$kCounter);
					$tpl->set_var("srno",$iCounter);
					$tpl->set_var("ksrno",$kCounter);
					$tpl=$db->setvalues($tpl);
					if ($includefunction!=NULL)
					{
						$includefunction();
					}
					$tpl->parse("${im}half${starter}", false);
				}
			}
		}
		else
		{

				$tpl->set_var("srno${starter}",$iCounter);
				$tpl->set_var("ksrno${starter}",$kCounter);
				$tpl->set_var("srno",$iCounter);
				$tpl->set_var("ksrno",$kCounter);
				$tpl=$db->setvalues($tpl);
				if ($includefunction!=NULL)
				{
					$includefunction();
				}


				if ($twoset>=2 or $twoset==true)
				{
					$tpl->parse("firsthalf${starter}", false);
					if ($db->next_record())
					{
						$iCounter++;
						$kCounter++;
						$tpl->set_var("srno${starter}",$iCounter);
						$tpl->set_var("ksrno${starter}",$kCounter);
						$tpl->set_var("srno",$iCounter);
						$tpl->set_var("ksrno",$kCounter);
						$tpl=$db->setvalues($tpl);
						if ($includefunction!=NULL)
						{
							$includefunction();
						}
						$tpl->parse("secondhalf${starter}", false);
					}
				}
				if ($twoset>=3)
				{
					if ($db->next_record())
					{
						$iCounter++;
						$kCounter++;
						$tpl->set_var("srno${starter}",$iCounter);
						$tpl->set_var("ksrno${starter}",$kCounter);
						$tpl->set_var("srno",$iCounter);
						$tpl->set_var("ksrno",$kCounter);
						$tpl=$db->setvalues($tpl);
						if ($includefunction!=NULL)
						{
							$includefunction();
						}
						$tpl->parse("thirdhalf${starter}", false);
					}
				}
				if ($twoset>=4)
				{
					if ($db->next_record())
					{
						$iCounter++;
						$kCounter++;
						$tpl->set_var("srno${starter}",$iCounter);
						$tpl->set_var("ksrno${starter}",$kCounter);
						$tpl->set_var("srno",$iCounter);
						$tpl->set_var("ksrno",$kCounter);
						$tpl=$db->setvalues($tpl);
						if ($includefunction!=NULL)
						{
							$includefunction();
						}
						$tpl->parse("forthhalf${starter}", false);
					}
				}
		}
if ($includefile!=NULL)
{
	include($includefile);
}

	    $tpl->parse("FullRecord${starter}", true);
	    $next_record = $db->next_record();
	    $iCounter++;
	    $kCounter++;
  }
  $tpl->set_var("endsrno${starter}",$kCounter-1);
  $tpl->set_var("endsrno",$kCounter-1);

	$tpl->set_var("totalsrno${starter}",$iCounter);

  $tpl->set_var("PageCount", $iPageCount);
  if(!strlen($iPage))
    $iPage = 1;
  else
  {
    if($iPage == "last") $iPage = $iPageCount;
  }

  $bEof = $next_record;
  // Parse Navigator
  if(!$bEof && $iPage == 1)
    $tpl->set_var("Navigator${starter}", "");
  else
  {
    $iCounter = 1;
    $iHasPages = $iPage;
    $iDisplayPages = 0;
    $iNumberOfPages = 10;
    $iHasPages = $iPageCount;
    if (($iHasPages - $iPage) < intval($iNumberOfPages / 2))
      $iStartPage = $iHasPages - $iNumberOfPages;
    else
      $iStartPage = $iPage - $iNumberOfPages + intval($iNumberOfPages / 2);

    if($iStartPage < 0) $iStartPage = 0;
    for($iPageCount = $iStartPage + 1;  $iPageCount <= $iPage - 1; $iPageCount++)
    {
      $tpl->set_var( "NavigatorPageNumber", $iPageCount);
      $tpl->set_var( "NavigatorPageNumberView", $iPageCount);
      $tpl->parse( "NavigatorPages${starter}", true);
      $iDisplayPages++;
    }
    $tpl->set_var( "NavigatorPageSwitch", "_");
    $tpl->set_var( "NavigatorPageNumber", $iPage);
    $tpl->set_var( "NavigatorPageNumberView", "<b><font size='3px'>".$iPage."</font></b>");
    $tpl->parse( "NavigatorPages${starter}", true);
    $iDisplayPages++;
    $tpl->set_var( "NavigatorPageSwitch", "");
    $iPageCount = $iPage + 1;
    while ($iDisplayPages < $iNumberOfPages && $iStartPage + $iDisplayPages < $iHasPages)
    {
      $tpl->set_var( "NavigatorPageNumber", $iPageCount);
      $tpl->set_var( "NavigatorPageNumberView", $iPageCount);
      $tpl->parse( "NavigatorPages${starter}", true);
      $iDisplayPages++;
      $iPageCount++;
    }
    if(!$bEof)
      $tpl->set_var("NavigatorLastPage", "_");
    else
      $tpl->set_var("NextPage", ($iPage + 1));
    if($iPage == 1)
      $tpl->set_var("NavigatorFirstPage", "_");
    else
      $tpl->set_var("PrevPage", ($iPage - 1));
    $tpl->set_var("CurrentPage", $iPage);
    $tpl->parse( "Navigator${starter}", false);
  }


  $tpl->set_var( "NoRecords${starter}", "");
}



function createoldthumb($sourcedir,$destinationdir,$filename,$height,$width)
{

	$pic=$sourcedir.$filename;
	if ($height>0)
	{
		$thumb_w=$width;
		$thumb_h=$height;
		list($old_w, $old_h, $type, $attr) = getimagesize("$pic");
		if ($old_w<=0) $old_w=$width;
		if ($old_h<=0) $old_h=$height;
		$thumb_h=sprintf("%d",$width*$old_h/$old_w);
		if ($thumb_h>$height)
		{
			$thumb_w=sprintf("%d",$height*$old_w/$old_h);
			$thumb_h=$height;
		}
		if ($thumb_w<=0) $thumb_w=$width;
		if ($thumb_h<=0) $thumb_h=$height;
	}



	$thumbfile=$destinationdir.'/t_'.$filename;
	$thumbfile=str_replace(".tif",".jpg",$thumbfile);

	if ($height>0)
	{
		if (ereg(".jpg", strtolower($pic)) || ereg(".jpeg", strtolower($pic)))
			$im= ImageCreatefromjpeg($pic);
		else
			$im= ImageCreatefromgif($pic);
		if (!$im)
		{
			echo "Error Creating thumbnail. Sorry file could not open $pic";
		}
		$thumb_w=$width;
		$thumb_h=$height;
		$old_w= imagesx($im);
		$old_h = imagesy($im);
		if ($old_w<=0) $old_w=$width;
		if ($old_h<=0) $old_h=$height;
		$thumb_h=sprintf("%d",$width*$old_h/$old_w);
		if ($thumb_h>$height)
		{
			$thumb_w=sprintf("%d",$height*$old_w/$old_h);
			$thumb_h=$height;
		}
		if ($thumb_w<=0) $thumb_w=$width;
		if ($thumb_h<=0) $thumb_h=$height;
		$thumb=@imagecreatetruecolor($thumb_w,$thumb_h);
		imagecopyresampled($thumb,$im,0,0,0,0,$thumb_w,$thumb_h,$old_w,$old_h);
		imagejpeg($thumb,$thumbfile,100);
		imagedestroy($thumb);
	}
	else
	{
		copy($pic,$thumbfile);
	}
	if (!file_exists($thumbfile))
	{
		echo "Error Creating thumbnail. File does not exists $thumbfile<br>";
		$thumbfile="";
	}
	return $thumbfile;
}
function getdaynum($a)
{
	$a=$a*1;
	if ($a<10)
		return "0".$a;
	else
		return $a;
}
function getmonthnum($a)
{
	if ($a=="Jan" || $a=="January")
		return "01";
	if ($a=="Feb" || $a=="February")
		return "02";
	if ($a=="Mar" || $a=="March")
		return "03";
	if ($a=="Apr" || $a=="April")
		return "04";
	if ($a=="May" || $a=="May")
		return "05";
	if ($a=="Jun" || $a=="June")
		return "06";
	if ($a=="Jul" || $a=="July")
		return "07";
	if ($a=="Aug" || $a=="August")
		return "08";
	if ($a=="Sep" || $a=="September")
		return "09";
	if ($a=="Oct" || $a=="October")
		return "10";
	if ($a=="Nov" || $a=="November")
		return "11";
	if ($a=="Dec" || $a=="December")
		return "12";
}

function es($input)
{
	$str="";
	$arr="ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890@*-_+./";
	for($i=0;$i<strlen($input);$i++)
	{
	   $check=substr($input,$i,1);
	   if(strpos($arr,$check))
			$str=$str.$check;
	   else
	   		$str=$str."%".sprintf("%02X",ord($check));

	}
	return $str;
}


function setfullvalues(&$tplf,$fieldname,$valuename)
{
      	$tplf->set_var($fieldname,$valuename);
      	$tplf->set_var($fieldname."_html",tohtml($valuename));
      	$tplf->set_var($fieldname."_nl",nl2br($valuename));
      	$tplf->set_var($fieldname."_lower",strtolower($valuename));
      	$tplf->set_var($fieldname."_upper",strtoupper($valuename));
      	$tplf->set_var($fieldname."_escape",es($valuename));
      	$vowels = array("%0A", "%0D");
      	$tplf->set_var($fieldname."_url",str_replace($vowels,"",urlencode($valuename)));
      	$tplf->set_var($fieldname."_url1",urlencode(str_replace("&","_",$valuename)));
      	if ($valuename!="")
      	{
      		$valuename=str_replace(array(" ","-","+"),"_",$valuename);
      		$tplf->set_var("$fieldname$valuename","selected=selected checked=checked");
      	}
}


function showmylistnew($sSQL,$count,$twoset,$starter="",$includefunction=NULL,$db_count1=0,$includefile=NULL,$extradiv=0)
{

  global $tpl;
  global $db;
  global $mb;
  global $tmb1;
  global $breakme;
  global $showheader;
  $HasParam = false;
  $iRecordsPerPage = $count;
  $iCounter = 0;
  $iPage = 0;
  $bEof = false;

  $db->query($sSQL);

  $iPage = get_session("Form_Page".basename($_SERVER["PHP_SELF"]));
  if ($db_count1>0)
  	$db_count=$db_count1;
  else
  	$db_count = $db->num_rows();
  $tpl->set_var("db_count",$db_count);
  $dResult = intval($db_count) / $iRecordsPerPage;
  $iPageCount = intval($dResult);
  if($iPageCount < $dResult) $iPageCount = $iPageCount + 1;

  $next_record = $db->next_record();
  $tpl->set_var("FullRecord${starter}", "");
  if(!$next_record)
  {
    $tpl->set_var("FullRecord${starter}", "");
//    $tpl->parse("NoRecords${starter}", false);
    $tpl->set_var("Navigator${starter}", "");
  	return;
  }
$tpl->set_var("error", "");
  $iCounter = 0;


  $iPage = get_param("Form_Page");
  if ($iPage=="") $iPage=get_session("Form_Page".basename($_SERVER["PHP_SELF"]));
  if ($iPage>$iPageCount) $iPage=$iPageCount;

  set_session("Form_Page".basename($_SERVER["PHP_SELF"]),$iPage);



  if(!strlen($iPage)) $iPage = 1; else $iPage = intval($iPage);
  $reviewid=0;
  $kCounter=1;
  if(($iPage - 1) * $iRecordsPerPage != 0)
  {
    do
    {
      $iCounter++;
      $kCounter++;
    } while ($iCounter < ($iPage - 1) * $iRecordsPerPage && $db->next_record());
    $next_record = $db->next_record();
  }

  $iCounter = 0;
  $prevname="";
  $previd=0;
  $tpl->set_var("startsrno${starter}",$kCounter);
  $tpl->set_var("startsrno",$kCounter);
  $showheader=1;
  while($next_record  && $iCounter < $iRecordsPerPage)
  {
  		$tpl->set_var("Header${starter}","");
  		$breakme=0;

		if ($twoset>=0)
		{
			for($im=1;$im<=$twoset;$im++)
			{
				$tpl->set_var("${im}half${starter}", "");
				if ($extradiv==1) $tpl->set_var("1_${im}half${starter}", "");
			}
		}

		if ($twoset>=0)
		{
			$tpl->set_var("srno${starter}",$iCounter);
			$tpl->set_var("ksrno${starter}",$kCounter);
			$tpl->set_var("srno",$iCounter);
			$tpl->set_var("ksrno",$kCounter);
			$tpl=$db->setvalues($tpl);
			if ($includefunction!=NULL)
			{
				$includefunction();
			}
			if ($showheader==1)
			{
				$tpl->parse("Header${starter}",false);
				$showheader=0;
			}
			if ($breakme==1) $breakme=0;
			$tpl->parse("1half${starter}", false);
			if ($extradiv==1) $tpl->parse("1_1half${starter}", false);
			$breakouter=false;
			$continueouter=false;
			for($im=2;$im<=$twoset && $breakme==0 && $iCounter < $iRecordsPerPage;$im++)
			{

				if ($db->next_record())
				{
					$iCounter++;
					$kCounter++;
					$tpl->set_var("srno${starter}",$iCounter);
					$tpl->set_var("ksrno${starter}",$kCounter);
					$tpl->set_var("srno",$iCounter);
					$tpl->set_var("ksrno",$kCounter);
					$tpl=$db->setvalues($tpl);
					if ($includefunction!=NULL)
					{
						$includefunction();
					}
					if ($breakme==1)
					{
						$tpl->parse("FullRecord${starter}", true);
						$next_record = true;
						$continueouter=true;
						break;
					}
					if ($iCounter < $iRecordsPerPage)
					{
						$tpl->parse("${im}half${starter}", false);
						if ($extradiv==1) $tpl->parse("1_${im}half${starter}", false);
					}
					else
					{
						//$iCounter--;
						//$kCounter--;
						$tpl->parse("FullRecord${starter}", true);
						$next_record=true;
						$breakouter=true;
						break;
					}
				}
			}
			if ($continueouter==true) continue;
			if ($breakouter==true) break;

		}


if ($includefile!=NULL)
{
	include($includefile);
}

	    $tpl->parse("FullRecord${starter}", true);
	    $next_record = $db->next_record();
	    $iCounter++;
	    $kCounter++;
  }
  $tpl->set_var("endsrno${starter}",$kCounter-1);
  $tpl->set_var("endsrno",$kCounter-1);

  $tpl->set_var("totalsrno${starter}",$iCounter);
  $tpl->set_var("totalsrno",$iCounter);

  $tpl->set_var("PageCount", $iPageCount);
  $tpl->set_var("PageCount${starter}", $iPageCount);
  if(!strlen($iPage))
    $iPage = 1;
  else
  {
    if($iPage == "last") $iPage = $iPageCount;
  }

  $bEof = $next_record;
  // Parse Navigator
  if(!$bEof && $iPage == 1)
    $tpl->set_var("Navigator${starter}", "");
  else
  {
    $iCounter = 1;
    $iHasPages = $iPage;
    $iDisplayPages = 0;
    $iNumberOfPages = 10;
    $iHasPages = $iPageCount;
    if (($iHasPages - $iPage) < intval($iNumberOfPages / 2))
      $iStartPage = $iHasPages - $iNumberOfPages;
    else
      $iStartPage = $iPage - $iNumberOfPages + intval($iNumberOfPages / 2);

    if($iStartPage < 0) $iStartPage = 0;
    for($iPageCount = $iStartPage + 1;  $iPageCount <= $iPage - 1; $iPageCount++)
    {
      $tpl->set_var( "NavigatorPageNumber", $iPageCount);
      $tpl->set_var( "NavigatorPageNumberView", $iPageCount);
      $tpl->parse( "NavigatorPages${starter}", true);
      $iDisplayPages++;
    }
    $tpl->set_var( "NavigatorPageSwitch", "_");
    $tpl->set_var( "NavigatorPageNumber", $iPage);
    $tpl->set_var( "NavigatorPageNumberView", $iPage);
    $tpl->parse( "NavigatorPages${starter}", true);
    $iDisplayPages++;
    $tpl->set_var( "NavigatorPageSwitch", "");
    $iPageCount = $iPage + 1;
    while ($iDisplayPages < $iNumberOfPages && $iStartPage + $iDisplayPages < $iHasPages)
    {
      $tpl->set_var( "NavigatorPageNumber", $iPageCount);
      $tpl->set_var( "NavigatorPageNumberView", $iPageCount);
      $tpl->parse( "NavigatorPages${starter}", true);
      $iDisplayPages++;
      $iPageCount++;
    }
    if(!$bEof)
      $tpl->set_var("NavigatorLastPage", "_");
    else
      $tpl->set_var("NextPage", ($iPage + 1));
    if($iPage == 1)
      $tpl->set_var("NavigatorFirstPage", "_");
    else
      $tpl->set_var("PrevPage", ($iPage - 1));
    $tpl->set_var("CurrentPage", $iPage);
    $tpl->parse( "Navigator${starter}", false);
  }


  $tpl->set_var( "NoRecords${starter}", "");
}



function checkLogin(){
   /* Check if user has been remembered */
   if (!isset($_SESSION['members_id']))
   {
	   if(isset($_COOKIE['cookname']) && isset($_COOKIE['cookpass'])){
		  $_SESSION['members_id'] = $_COOKIE['cookname'];
		  $_SESSION['members_type'] = $_COOKIE['cookpass'];
	   }
   }

   /* Username and password have been set */
   if(isset($_SESSION['members_id']) && isset($_SESSION['members_type'])){
      /* Confirm that username and password are valid */
      if($_SESSION['members_id']==0 || $_SESSION['members_type']=="" || confirmID()==false)
      {
         /* Variables are incorrect, user not logged in */
         unset($_SESSION['members_id']);
         unset($_SESSION['members_type']);
         return false;
      }

      return true;
   }
   /* User not logged in */
   else{
      return false;
   }
}

function confirmID()
{
	global $db;
	$db->query("select * from member where id=$_SESSION[members_id] and membership_type=".tosql($_SESSION["members_type"],"text"));
	if ($db->next_record())
	{
		return true;
	}
	return false;
}

function checkinmultiplestring($multiplestring,$stringtosearch)
{
	if (strpos(", ".$multiplestring.",",", ".$stringtosearch.",")===false)
		return false;
	else
		return true;
}

/*
function getadminemail()
{
	global $db;
	global $adminemailid;
	$sSQL="select * from admin where id=1";
	$db->query($sSQL);
	if($db->next_record())
	{
		$adminemailid="No More Chaos Team <".$db->f("email").">";
	}
	return $adminemailid;
}
getadminemail();
*/


/* function to remove Xss
   * decription : The goal of this function is to be a generic function that can be used to parse almost any input and render it XSS safe.
**/
function RemoveXSS($val) {
   // remove all non-printable characters. CR(0a) and LF(0b) and TAB(9) are allowed
   // this prevents some character re-spacing such as <java\0script>
   // note that you have to handle splits with \n, \r, and \t later since they *are* allowed in some inputs
   $val = preg_replace('/([\x00-\x08][\x0b-\x0c][\x0e-\x20])/', '', $val);

   // straight replacements, the user should never need these since they're normal characters
   // this prevents like <IMG SRC=&#X40&#X61&#X76&#X61&#X73&#X63&#X72&#X69&#X70&#X74&#X3A&#X61&#X6C&#X65&#X72&#X74&#X28&#X27&#X58&#X53&#X53&#X27&#X29>
   $search = 'abcdefghijklmnopqrstuvwxyz';
   $search .= 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
   $search .= '1234567890!@#$%^&*()';
   $search .= '~`";:?+/={}[]-_|\'\\';
   for ($i = 0; $i < strlen($search); $i++) {
      // ;? matches the ;, which is optional
      // 0{0,7} matches any padded zeros, which are optional and go up to 8 chars

      // &#x0040 @ search for the hex values
      $val = preg_replace('/(&#[x|X]0{0,8}'.dechex(ord($search[$i])).';?)/i', $search[$i], $val); // with a ;
      // &#00064 @ 0{0,7} matches '0' zero to seven times
      $val = preg_replace('/(&#0{0,8}'.ord($search[$i]).';?)/', $search[$i], $val); // with a ;
   }

   // now the only remaining whitespace attacks are \t, \n, and \r
   $ra1 = Array('javascript', 'vbscript', 'expression', 'applet', 'meta', 'xml', 'blink', 'link', 'style', 'script', 'embed', 'object', 'iframe', 'frame', 'frameset', 'ilayer', 'layer', 'bgsound', 'title', 'base');
   $ra2 = Array('onabort', 'onactivate', 'onafterprint', 'onafterupdate', 'onbeforeactivate', 'onbeforecopy', 'onbeforecut', 'onbeforedeactivate', 'onbeforeeditfocus', 'onbeforepaste', 'onbeforeprint', 'onbeforeunload', 'onbeforeupdate', 'onblur', 'onbounce', 'oncellchange', 'onchange', 'onclick', 'oncontextmenu', 'oncontrolselect', 'oncopy', 'oncut', 'ondataavailable', 'ondatasetchanged', 'ondatasetcomplete', 'ondblclick', 'ondeactivate', 'ondrag', 'ondragend', 'ondragenter', 'ondragleave', 'ondragover', 'ondragstart', 'ondrop', 'onerror', 'onerrorupdate', 'onfilterchange', 'onfinish', 'onfocus', 'onfocusin', 'onfocusout', 'onhelp', 'onkeydown', 'onkeypress', 'onkeyup', 'onlayoutcomplete', 'onload', 'onlosecapture', 'onmousedown', 'onmouseenter', 'onmouseleave', 'onmousemove', 'onmouseout', 'onmouseover', 'onmouseup', 'onmousewheel', 'onmove', 'onmoveend', 'onmovestart', 'onpaste', 'onpropertychange', 'onreadystatechange', 'onreset', 'onresize', 'onresizeend', 'onresizestart', 'onrowenter', 'onrowexit', 'onrowsdelete', 'onrowsinserted', 'onscroll', 'onselect', 'onselectionchange', 'onselectstart', 'onstart', 'onstop', 'onsubmit', 'onunload');
   $ra = array_merge($ra1, $ra2);

   $found = true; // keep replacing as long as the previous round replaced something
   while ($found == true) {
      $val_before = $val;
      for ($i = 0; $i < sizeof($ra); $i++) {
         $pattern = '/';
         for ($j = 0; $j < strlen($ra[$i]); $j++) {
            if ($j > 0) {
               $pattern .= '(';
               $pattern .= '(&#[x|X]0{0,8}([9][a][b]);?)?';
               $pattern .= '|(&#0{0,8}([9][10][13]);?)?';
               $pattern .= ')?';
            }
            $pattern .= $ra[$i][$j];
         }
         $pattern .= '/i';
         $replacement = substr($ra[$i], 0, 2).'<x>'.substr($ra[$i], 2); // add in <> to nerf the tag
         $val = preg_replace($pattern, $replacement, $val); // filter out the hex tags
         if ($val_before == $val) {
            // no replacements were made, so exit the loop
            $found = false;
         }
      }
   }
   return $val;
}
function nincluder($fn)
{
   ob_start();
   include($fn);
   $cont=ob_get_contents();
   ob_end_clean();
   $value=$cont;
   return $value;
}

function export($sSQL,$tplname)
{
	global $db;
	$a=$db->query($sSQL);
	header("Content-Type: application/force-download\n");
	header('Content-transfer-encoding: binary');
	$date=date("d_m_Y_H_i_s");
	header("Content-Disposition: attachment;filename=".$tplname."_$date".".xls");
	include("xls.php");
	xlsBOF();
	$rows=0;
	while($db->next_record())
	{

		$mars=$db->getarray();
		if ($rows==0)
		{
			$cols=0;
			foreach($mars as $hea=>$val)
			{
				//rows,cols,$hea
				//rows,cols,$val
					 //echo "$hea&nbsp;";
					 xlsWriteLabel($rows,$cols,$hea);
				$cols++;

			}
			$rows++;
		}

		$cols=0;
		foreach($mars as $hea=>$val)
		{
			//rows,cols,$hea
			//rows,cols,$val
				 //echo "$hea&nbsp;";
				 xlsWriteLabel($rows,$cols,$val);
			$cols++;

		}

		$rows++;
	}
	xlsEOF();
}

function clearsess()
{
	$_SESSION["couponid"]=0;
	return $_SESSION["couponid"];
}
function addtolog($page,$code,$api)
{
	$info["username"]=$code;
	$info["pagename"]=$page;
	$info["api"]=$api;
	$info['date']=date('Y-m-d');
    insertarrayvalues("kol_smslogs",$info);
}

function str_rand($length = 8, $seeds = 'alphanum')
{
    // Possible seeds
    $seedings['alpha'] = 'abcdefghijklmnopqrstuvwqyz';
    $seedings['numeric'] = '0123456789';
    $seedings['alphanum'] = 'abcdefghijklmnopqrstuvwqyz0123456789';
    $seedings['hexidec'] = '0123456789abcdef';

    // Choose seed
    if (isset($seedings[$seeds]))
    {
        $seeds = $seedings[$seeds];
    }

    // Seed generator
    list($usec, $sec) = explode(' ', microtime());
    $seed = (float) $sec + ((float) $usec * 100000);
    mt_srand($seed);

    // Generate
    $str = '';
    $seeds_count = strlen($seeds);

    for ($i = 0; $length > $i; $i++)
    {
        $str .= $seeds{mt_rand(0, $seeds_count - 1)};
    }

    return $str;
}
function getnow()
{
	$dt=date("date('r')+interval 12 hour+interval 30 minute");
     $dt1=explode(",",$dt);
	$dt3=explode(" ",$dt1[1]);
	$m=date("m");
	$date1=$dt3[1]."/".$m."/".$dt3[3];
	$h=date("$date1 H:i:s");
	return $h;
}


function systemup()
{
	global $mb2;	
	if(date('l')=='Saturday') {
			$sSQL="select * from ht_admin where time(now()) between sfrtime and stotime limit 0,1";
			
	} 
	else 
			$sSQL="select * from ht_admin where time(now()) between frtime and totime limit 0,1";
	
	$mb2->query($sSQL);
	if($mb2->next_record())
	{
		return 1;
	}
	else
		return 0;
}


session_start();
$page=substr($_SERVER[("SCRIPT_NAME")],strrpos($_SERVER[("SCRIPT_NAME")],"/")+1);
if($page!="login.php")
{
	$userid=get_session("SesUserId");
	$date=date('Y-m-d H:i:s');
	if($userid!="")
	{
		$sql="update ht_users set lastaccess='$date' where id='$userid'";
		$db->query($sql);
	}
}

function sendsms($mob,$mess)
{
	$mobile=$mob;
	$msg=$mess;

	$msg=urlencode($msg);

	/*$ch = curl_init();
	//curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_GET, true);
	curl_setopt($ch, CURLOPT_HEADER, false);*/

	 $url = "http://sms.bulksmssurat.in/sendurlcomma.asp?user=20033635&pwd=eybpkp&senderid=maruti&mobileno=$mobile&msgtext=$msg&priority=High";
	
	$output=file_get_contents($url);
	echo $output;
	
	/*curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$output = curl_exec($ch);
	curl_close($ch);*/

	//Sms log Details
	//addtolog($page,$user,$url);
}

//~ if(systemup()==0 && $file == "")
	//~ header("Location:systemdown.php");
?>
