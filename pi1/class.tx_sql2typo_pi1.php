<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2009 Dirk Wildt <wildt.at.die-netzmacher.de>
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

//////////////////////////////////////////////////////////////////////
//
// TYPO3 Downwards Compatibility

if (!defined('PATH_typo3'))
{
  //var_dump(get_defined_constants());
  //echo 'Not defined: PATH_typo3.<br />tx_browser_pi1 defines it now.<br />';
  if (!defined('PATH_site'))
  {
    echo '<div style="border:1em solid red;padding:1em;color:red;font-weight:bold;font-size:2em;background:white;line-height:2.4em;text-align:center;">Error<br />
      The constant PATH_typo3 isn\'t defined.<br />
      tx_browser_pi1 tries to get now PATH_site, but it isn\'t defined neither!<br />
      <br />
      Please check your TYPO3 installation.</div>';
  }
  if (!defined('TYPO3_mainDir'))
  {
    echo '<div style="border:1em solid red;padding:1em;color:red;font-weight:bold;font-size:2em;background:white;line-height:2.4em;text-align:center;">Error<br />
      The constant PATH_typo3 isn\'t defined.<br />
      tx_browser_pi1 tries to get now TYPO3_mainDir, but it isn\'t defined neither!<br />
      <br />
      Please check your TYPO3 installation.</div>';
  }
  define('Path_typo3', PATH_site.TYPO3_mainDir);
}
// TYPO3 Downwards Compatibility


require_once(PATH_tslib.'class.tslib_pibase.php');

/**
 * Plugin 'Browser' for the 'browser' extension - the fastest way for your data into the TYPO3 frontend.
 *
 * @author    Dirk Wildt <dirk.wildt.at.die-netzmacher.de>
 * @package    TYPO3
 * @subpackage    tx_browser
 */

/**
 * [CLASS/FUNCTION INDEX of SCRIPT]
 *
 *
 *
 *   82: class tx_sql2typo_pi1 extends tslib_pibase
 *
 *              SECTION: Main Process
 *  142:     function main($content, $conf)
 *
 *              SECTION: DRS - Development Reporting System
 *  279:     function init_drs()
 *  364:     function require_classes()
 *
 * TOTAL FUNCTIONS: 3
 * (This index is automatically created/updated by the extension "extdeveval")
 *
 */
class tx_sql2typo_pi1 extends tslib_pibase {

  // Extension
  var $prefixId = 'tx_sql2typo_pi1';
  // Same as class name
  var $scriptRelPath = 'pi1/class.tx_sql2typo_pi1.php';
  // Path to this script relative to the extension dir.
  var $extKey = 'sql2typo';
  // The extension key.
  var $pi_checkCHash = true;
  // Extension



  // Booleans for DRS - Development Reporting System
  var $lang;
  // [Object] System language Object. $lang->lang cotain the current language.
  var $str_developer_name     = 'Dirk Wildt';
  var $str_developer_mail     = 'wildt[at]die-netzmacher.de';
  var $str_developer_phone    = '+49 361 21655226';
  var $str_developer_company  = 'Die Netzmacher';
  var $str_developer_web      = 'http://die-netzmacher.de';
  var $str_developer_typo3ext = 'http://typo3.org/extensions/repository/view/browser/current/';
  var $str_developer_lang     = 'german, english';
  var $developer_contact      = FALSE; // See init_drs()
  
  var $b_drs_all        = FALSE;
  var $b_drs_error      = FALSE;
  var $b_drs_warn       = FALSE;
  var $b_drs_perform    = FALSE;
  var $b_drs_sql        = FALSE;
  // Booleans for DRS - Development Reporting System

  // Development
  var $boolCache = TRUE;
  // Use cache: FALSE || TRUE; If you develope this extension, it can be helpfull to set this var on FALSE (no cache)
  var $bool_typo3_43 = FALSE;
  // [Boolean] If true, the current version is TYPO3 4.3 at least
  // Development

  // Objects
  var $objController = FALSE;
  // [Object] Controller
  var $objModel = FALSE;
  // [Object] Model
  // Objects







  /***********************************************
   *
   * Main Process
   *
   **********************************************/




  /**
 * Main method of your PlugIn
 *
 * @param	string		$content: The content of the PlugIn
 * @param	array		$conf: The PlugIn Configuration
 * @return	string		The content that should be displayed on the website
 */
  function main($content, $conf) {

    $this->conf = $conf;

    $this->pi_setPiVarDefaults();
    $this->pi_loadLL();


    //////////////////////////////////////////////////////////////////////
    //
    // Make cObj instance
    
    $this->local_cObj = t3lib_div::makeInstance('tslib_cObj');
    // Make cObj instance


    ////////////////////////////////////////////////////////////////////
    //
    // TYPO3 Version

    $str_version = TYPO3_version;
    if(!$str_version)
    {
      $str_version = '4.2.9';
    }
    $int_version = t3lib_div::int_from_ver($str_version);
    if($int_version >= 4003000)
    {
      $this->bool_typo3_43 = TRUE;
    }
    if($int_version < 4003000)
    {
      $this->bool_typo3_43 = FALSE;
    }
    // TYPO3 Version


    ////////////////////////////////////////////////////////////////////
    //
    // Timetracking

    require_once(PATH_t3lib.'class.t3lib_timetrack.php');
    $this->TT = new t3lib_timeTrack;
    $this->TT->start();
    if($this->bool_typo3_43)
    {
      $this->startTime = $this->TT->getDifferenceToStarttime();
    }
    if(!$this->bool_typo3_43)
    {
      $this->startTime = $this->TT->mtime();
    }
    // Timetracking


    //////////////////////////////////////////////////////////////////////
    //
    // Init Language
    
//    if(!$this->lang)
//    {
//      $this->initLang();
//    }
    // Init Language


    //////////////////////////////////////////////////////////////////////
    //
    // Init DRS - Development Reporting System

    $this->init_drs();
    if ($this->b_drs_perform)
    {
      t3lib_div::devlog('[INFO/PERFORMANCE] START', $this->extKey, 0);
    }
    // Init DRS - Development Reporting System


    //////////////////////////////////////////////////////////////////////
    //
    // Require and init helper classes

    $this->require_classes();
    // Require and init helper classes


    //////////////////////////////////////////////////////////////////////
    //
    // Controller
    $arr_return = $this->objController->main();
    // Controller


    //////////////////////////////////////////////////////////////////////
    //
    // DRS - Performance

    if ($this->b_drs_perform) {
      if($this->bool_typo3_43)
      {
        $endTime = $this->TT->getDifferenceToStarttime();
      }
      if(!$this->bool_typo3_43)
      {
        $endTime = $this->TT->mtime();
      }
      t3lib_div::devLog('[INFO/PERFORMANCE] END: '. ($endTime - $this->startTime).' ms', $this->extKey, 0);
    }
    // DRS - Performance


    //////////////////////////////////////////////////////////////////////
    //
    // Return the result (HTML string)

    //return $this->pi_wrapInBaseClass($str_template_completed);
    $str_warn   = FALSE;
    $str_header = FALSE;
    $str_prompt = FALSE;
    if($arr_return['error']['status'])
    {
      if(isset($arr_return['error']['warn']))
      {
        $str_warn = $arr_return['error']['warn'];
        $str_warn = '<p style="border: 1em solid red; background:white; color:red; font-weight:bold; text-align:center; padding:2em;">'.$str_warn.'</p>';
        
      }
      $str_header = '<h1 style="color:red">'.$arr_return['error']['header'].'</h1>';
      $str_prompt = '<p style="border: 2px dotted red; font-weight:bold;text-align:center; padding:1em;">'.$arr_return['error']['prompt'].'</p>';
    }
    if(!$arr_return['error']['status'])
    {
      $str_prompt = $arr_return['data']['prompt'];
    }
    
    return $this->pi_wrapInBaseClass($str_warn.$str_header.$str_prompt);
  }
























  /***********************************************
   *
   * DRS - Development Reporting System
   *
   **********************************************/




  /**
 * Set the booleans for Warnings, Errors and DRS - Development Reporting System
 *
 * @return	void
 */
  function init_drs()
  {

    //////////////////////////////////////////////////////////////////////
    //
    // Prepaire the developer contact prompt

    $this->developer_contact =
        'company: '.  $this->str_developer_company.'<br />'.
        'name: '.     $this->str_developer_name   .'<br />'.
        'mail: <a href="mailto:'.$this->str_developer_mail.'" title="Send a mail">'.$this->str_developer_mail.'</a><br />'.
        'web: <a href="'.$this->str_developer_web.'" title="Website" target="_blank">'.$this->str_developer_web.'</a><br />'.
        'phone: '.    $this->str_developer_phone  .'<br />'.
        'languages: '.$this->str_developer_lang.'<br /><br />'.
        'TYPO3 Repository:<br /><a href="'.$this->str_developer_typo3ext.'" title="'.$this->extKey.' online" target="_blank">'.
    $this->str_developer_typo3ext.'</a>';
    $i_len = intval($this->conf['drs.']['sql.']['result.']['max_len']);
    if ($i_len > 0)
    {
      $this->i_drs_max_sql_result_len = $i_len;
    }
    // Prepaire the developer contact prompt


    //////////////////////////////////////////////////////////////////////
    //
    // Get the values from the localconf.php file

    $arrConf = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf'][$this->extKey]);
    // Get the values from the localconf.php file


    //////////////////////////////////////////////////////////////////////
    //
    // Set the DRS mode

    if ($arrConf['drs_mode'] == 'All')
    {
      $this->b_drs_all        = TRUE;
      $this->b_drs_error      = TRUE;
      $this->b_drs_warn       = TRUE;
      $this->b_drs_perform    = TRUE;
      $this->b_drs_sql        = TRUE;
      t3lib_div::devlog('[INFO/DRS] DRS - Development Reporting System:<br />'.$arrConf['drs_mode'], $this->extKey, 0);
    }
    if ($arrConf['drs_mode'] == 'Performance')
    {
      $this->b_drs_error      = TRUE;
      $this->b_drs_warn       = TRUE;
      $this->b_drs_perform    = TRUE;
      t3lib_div::devlog('[INFO/DRS] DRS - Development Reporting System:<br />'.$arrConf['drs_mode'], $this->extKey, 0);
    }
    if ($arrConf['drs_mode'] == 'SQL Development')
    {
      $this->b_drs_error      = TRUE;
      $this->b_drs_warn       = TRUE;
      $this->b_drs_perform    = TRUE;
      $this->b_drs_sql        = TRUE;
      t3lib_div::devlog('[INFO/DRS] DRS - Development Reporting System:<br />'.$arrConf['drs_mode'], $this->extKey, 0);
    }
    // Set the DRS mode

  }

















//  /**
// * Inits the class 'language'
// *
// * @param string    Fieldname in the _LOCAL_LANG array or the locallang.xml
// * @return  void
// */
//  function initLang() {
//    require_once(PATH_typo3.'sysext/lang/lang.php');
//    $this->pObj->lang = t3lib_div::makeInstance('language');
//    $this->pObj->lang->init($GLOBALS['TSFE']->lang);
//    if($this->pObj->b_drs_locallang)
//    {
//      t3lib_div::devlog('[INFO/LOCALLANG] Init a language object.', $this->pObj->extKey, 0);
//    }
//  }

















  /**
 * Init the helper classes
 *
 * @return	void
 */
  function require_classes()
  {
    //////////////////////////////////////////////////////////////////////
    //
    // Require and init helper classes

    require_once('class.tx_sql2typo_pi1_controller.php');
    // Class with methods for get flexform values
    $this->objController = new tx_sql2typo_pi1_controller($this);

    require_once('class.tx_sql2typo_pi1_model.php');
    // Class with methods for get flexform values
    $this->objModel = new tx_sql2typo_pi1_model($this);

    // Require and init helper classes

  }












}

















if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/browser/pi1/class.tx_browser_pi1.php']) {
  include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/browser/pi1/class.tx_browser_pi1.php']);
}

?>