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



/**
 * [CLASS/FUNCTION INDEX of SCRIPT]
 *
 *
 *
 *   51: class tx_sql2typo_pi1_controller
 *   78:     function __construct($parentObj)
 *  112:     function main()
 *  166:     function table($str_table)
 *  270:     function wrapdata($str_table, $arr_data)
 *
 * TOTAL FUNCTIONS: 4
 * (This index is automatically created/updated by the extension "extdeveval")
 *
 */


 /**
  * The class tx_sql2typo_pi1_controller is the controller class for sql2typo.
  *
  * @author    Dirk Wildt <wildt.at.die-netzmacher.de>
  * @package    TYPO3
  * @subpackage    tx_sql2auto_pi1
  */
class tx_sql2typo_pi1_controller
{
  /////////////////////////////////////////////////
  //
  // Vars set by methods in the current class

  //var $mode = FALSE;
  // [Integer] The ID of the current mode/view

  // Vars set by methods in the current class











/**
 * Constructor. The method initiate the parent object
 *
 * @param	object		The parent object
 * @return	void
 */
  function __construct($parentObj)
  {
    // Set the Parent Object
    $this->pObj = $parentObj;

  }























/**
 * Process ...
 *
 * @return	void
 */
  function main()
  {
    $arr_return = FALSE;

    //////////////////////////////////////////////////////////////////////
    //
    // DRS - Development Reporting System

    if ($this->pObj->b_drs_plugin)
    {
      $str_header     = $this->pObj->cObj->data['header'];
      $int_uid        = $this->pObj->cObj->data['uid'];
      $int_pid        = $this->pObj->cObj->data['pid'];
      t3lib_div::devlog('[INFO/PLUGIN] \''.$str_header.'\' (pid: '.$int_pid.', uid: '.$int_uid.')', $this->pObj->extKey, 0);
    }
    // DRS - Development Reporting System


    //////////////////////////////////////////////////////////////////////
    //
    // RETURN, if there isn't any TypoScript configuration
    
    if(!is_array($this->pObj->conf['tables.']))
    {
      //:TODO:
      $arr_return['error']['status'] = TRUE;
      $arr_return['error']['header'] = $this->pObj->pi_getLL('error_tsTemplate_h1');
      $arr_return['error']['prompt'] = $this->pObj->pi_getLL('error_tsTemplate_prompt');
      return $arr_return;
    }
    // RETURN, if there isn't any TypoScript configuration


    //////////////////////////////////////////////////////////////////////
    //
    // Loop Tables
    
    $arr_prompt = FALSE;
    foreach($this->pObj->conf['tables.'] as $dot_table => $arr_table)
    {
      $str_table = substr($dot_table, 0, -1);
      //var_dump('controller 137', $str_table);
      $arr_return = $this->table($str_table);
      if($arr_return['error']['status'])
      {
        return $arr_return;
      }
      $arr_prompt[] = $arr_return['data']['prompt'];
    }
    // Loop Tables

    if(is_array($arr_prompt))
    {
      $str_prompt = implode('<br /><br />', $arr_prompt);
    }
    $arr_return['data']['prompt'] = $str_prompt;

    if(!is_array($arr_prompt))
    {
      $arr_return['error']['status'] = TRUE;
      $arr_return['error']['header'] = $this->pObj->pi_getLL('error_h1');
      $arr_return['error']['prompt'] = $this->pObj->pi_getLL('error_prompt');
      return $arr_return;
    }
    return $arr_return;
  }
















/**
 * Process ...
 *
 * @param	[type]		$str_table: ...
 * @return	void
 */
  function table($str_table)
  {
    $rows       = FALSE;
    $arr_return = FALSE;
    $arr_prompt = FALSE;

    //////////////////////////////////////////////////////////////////////
    //
    // DRS - Development Reporting System
    
    if ($this->pObj->b_drs_sql)
    {
      t3lib_div::devlog('[INFO/SQL] table: \''.$str_table.'\'', $this->pObj->extKey, 0);
    }
    // DRS - Development Reporting System


    //////////////////////////////////////////////////////////////////////
    //
    // Database Connect
    
    //$arr_database = $this->pObj->conf['tables.'][$str_table.'.']['database.'];
    //$arr_result   = $this->pObj->objModel->connect($arr_database);
    //$res          = $arr_result['data']['res'];
    // if(is_array($arr_result['error'])
    // {
    //    ...
    // }
    // Database Connect


    //////////////////////////////////////////////////////////////////////
    //
    // Get Data
    
    $arr_result = $this->pObj->objModel->getdata($str_table);
    if($arr_result['error']['status'])
    {
      return $arr_result;
    }
    $rows                  = $arr_result['data']['rows'];
    $arr_prompt['getdata'] = $arr_result['data']['prompt'];
    unset($arr_result);
    // Get Data


    //////////////////////////////////////////////////////////////////////
    //
    // Relation?
    
    $arr_result = $this->relation($str_table, $rows);
    if($arr_result['error']['status'])
    {
      return $arr_result;
    }
    $rows                   = $arr_result['data']['rows'];
    $arr_prompt['relation'] = $arr_result['data']['prompt'];
    unset($arr_result);
    // Relation?


    //////////////////////////////////////////////////////////////////////
    //
    // Wrap Data
    
    $arr_result = $this->wrapdata($str_table, $rows);
    if($arr_result['error']['status'])
    {
      return $arr_result;
    }
    $rows                   = $arr_result['data']['rows'];
    $arr_prompt['wrapdata'] = $arr_result['data']['prompt'];
    unset($arr_result);
    // Wrap Data


    //////////////////////////////////////////////////////////////////////
    //
    // sql2typo Data
    
    $arr_result = $this->sql2typo($str_table, $rows);
    if($arr_result['error']['status'])
    {
      return $arr_result;
    }
    $rows                   = $arr_result['data']['rows'];
    $arr_prompt['sql2typo'] = $arr_result['data']['prompt'];
    unset($arr_result);
    // sql2typo Data


    //////////////////////////////////////////////////////////////////////
    //
    // Write Data
    
    $arr_result = $this->pObj->objModel->writedata($str_table, $rows);
    if($arr_result['error']['status'])
    {
      return $arr_result;
    }
    $arr_prompt['writedata'] = $arr_result['data']['prompt'];
    //var_dump('controller 242', $arr_prompt);
    unset($arr_result);
    // Write Data


    //////////////////////////////////////////////////////////////////////
    //
    // Database Disconnect
    
    //$arr_result = $this->pObj->objModel->disconnect($res);
    // Database Disconnect

    $str_prompt = implode(' ', $arr_prompt);
    $arr_return['data']['prompt'] = $str_prompt;
    return $arr_return;
  }
















/**
 * Process ...
 *
 * @param	[type]		$str_table: ...
 * @param	[type]		$arr_data: ...
 * @return	void
 */
  function wrapdata($str_table, $rows)
  {
    $arr_stdWrap = FALSE;
    $arr_return['data']['rows'] = $rows;


    //////////////////////////////////////////////////////////////////////
    //
    // Do we have a stdWrap?
    
    foreach($this->pObj->conf['tables.'][$str_table.'.']['fields.'] as $dot_field => $arr_field)
    {
      $str_field = substr($dot_field, 0, -1);
      if(is_array($arr_field['stdWrap.']))
      {
        $arr_stdWrap[] = $str_field;
      }
    }
    // Do we have a stdWrap?


    //////////////////////////////////////////////////////////////////////
    //
    // RETURN in case of no stdWrap
    
    if(!is_array($arr_stdWrap))
    {
      // DRS - Development Reporting System
      if ($this->pObj->b_drs_sql)
      {
        $str_configure = $this->pObj->pi_getLL('phrase_configure_alternate');
        t3lib_div::devlog('[INFO/SQL] There is no field with a stdWrap.', $this->pObj->extKey, 0);
        t3lib_div::devlog('[HELP/SQL] '.$str_configure.':<br />tables.'.$str_table.'.your_field.stdWrap', $this->pObj->extKey, 1);
      }
      // DRS - Development Reporting System
      return $arr_return;
    }
    // RETURN in case of no stdWrap


    //////////////////////////////////////////////////////////////////////
    //
    // Wrap Data
    
    // Loop through all fields with a stdWrap
    foreach($arr_stdWrap as $fieldWiComma)
    {
      $str_conf     = $this->pObj->conf['tables.'][$str_table.'.']['fields.'][$fieldWiComma.'.']['stdWrap'];
      $arr_conf     = $this->pObj->conf['tables.'][$str_table.'.']['fields.'][$fieldWiComma.'.']['stdWrap.'];
      // DRS - Development Reporting System
      if ($this->pObj->b_drs_sql)
      {
        $str_configure = $this->pObj->pi_getLL('phrase_configure_alternate');
        t3lib_div::devlog('[INFO/SQL] '.$str_field.' has a stdWrap.', $this->pObj->extKey, 0);
        t3lib_div::devlog('[HELP/SQL] '.$str_configure.':<br />tables.'.$str_table.'.'.$fieldWiComma.'.stdWrap', $this->pObj->extKey, 1);
      }
      // DRS - Development Reporting System
      // Loop through all rows
      foreach($rows as $key => $row)
      {
        //var_dump('controller 340', $row);
        $arr_curr_conf = $arr_conf;
        if(!isset($arr_curr_conf['value']))
        {
          $arr_curr_conf['value'] = $row[$fieldWiComma];
        }
        $arr_result = $this->stdWrap($str_conf, $arr_curr_conf);
        $value      = $arr_result['data']['value'];
        unset($arr_result);
        $rows[$key][$fieldWiComma] = $value;
      }
      // Loop through all rows
    }
    // Loop through all fields with a stdWrap
    // Wrap Data


    $arr_return['data']['rows'] = $rows;
    return $arr_return;
  }
















/**
 * Process ...
 *
 * @param	[type]		$str_table: ...
 * @param	[type]		$arr_data: ...
 * @return	void
 */
  function sql2typo($str_table, $rows)
  {
    $arr_sql2typo = FALSE;
    $arr_return['data']['rows'] = $rows;


    //////////////////////////////////////////////////////////////////////
    //
    // Do we have a sql2typo configuration?
    
    foreach($this->pObj->conf['tables.'][$str_table.'.']['fields.'] as $dot_field => $arr_field)
    {
      $str_field = substr($dot_field, 0, -1);
      if(is_array($arr_field['sql2typo.']))
      {
        $arr_fields[] = $str_field;
      }
    }
    // Do we have a sql2typo configuration?

//var_dump('controller 425', $arr_fields);
    //////////////////////////////////////////////////////////////////////
    //
    // RETURN in case of no sql2typo
    
    if(!is_array($arr_fields))
    {
      // DRS - Development Reporting System
      if ($this->pObj->b_drs_sql)
      {
        $str_configure = $this->pObj->pi_getLL('phrase_configure_alternate');
        t3lib_div::devlog('[INFO/SQL] There is no field with an array sql2typo.', $this->pObj->extKey, 0);
        t3lib_div::devlog('[HELP/SQL] '.$str_configure.':<br />tables.'.$str_table.'.your_field.sql2typo', $this->pObj->extKey, 1);
      }
      // DRS - Development Reporting System
      return $arr_return;
    }
    // RETURN in case of no sql2typo


    //////////////////////////////////////////////////////////////////////
    //
    // Wrap Data
    
    // Loop through all fields with a stdWrap
    foreach($arr_fields as $fieldWiComma)
    {
      $arr_sql2typo = $this->pObj->conf['tables.'][$str_table.'.']['fields.'][$fieldWiComma.'.']['sql2typo.'];
      // DRS - Development Reporting System
      if ($this->pObj->b_drs_sql)
      {
        $str_configure = $this->pObj->pi_getLL('phrase_configure_alternate');
        t3lib_div::devlog('[INFO/SQL] '.$str_field.' has an array sql2typo.', $this->pObj->extKey, 0);
        t3lib_div::devlog('[HELP/SQL] '.$str_configure.':<br />tables.'.$str_table.'.'.$fieldWiComma.'.sql2typo', $this->pObj->extKey, 1);
      }
      // DRS - Development Reporting System
      // Loop through all rows
      foreach($rows as $key => $row)
      {
        if($arr_sql2typo['strtotime'])
        {
          $rows[$key][$fieldWiComma] = strtotime($row[$fieldWiComma]);
        }
        if($arr_sql2typo['str_replace'])
        {
          if(is_array($arr_sql2typo['str_replace.']))
          {
            $value = $row[$fieldWiComma];
            $value = str_replace("\r", '\\r',  $value);
            $value = str_replace("\n", '\\n',  $value);
            $str_devider = $arr_sql2typo['str_replace.']['devider'];
            if(!$str_devider)
            {
              $str_devider = '|';
            }
            foreach($arr_sql2typo['str_replace.'] as $arr_replace)
            {
              list($needle, $replace) = explode($str_devider, $arr_replace);
              $value = str_replace($needle, $replace, $value);
            }
            $value = str_replace('\\r', "\r",  $value);
            $value = str_replace('\\n', "\n",  $value);
            $rows[$key][$fieldWiComma] = $value;
          }
        }
      }
      // Loop through all rows
    }
    // Loop through all fields with a stdWrap
    // Wrap Data


    $arr_return['data']['rows'] = $rows;
    return $arr_return;
  }
















/**
 * Process ...
 *
 * @param  [type]    $str_table: ...
 * @param  [type]    $arr_data: ...
 * @return  void
 */
  function relation($str_table, $rows)
  {
    $arr_sql2typo = FALSE;
    $arr_return['data']['rows'] = $rows;


    //////////////////////////////////////////////////////////////////////
    //
    // Do we have a relation?
    
    foreach($this->pObj->conf['tables.'][$str_table.'.']['fields.'] as $dot_field => $arr_field)
    {
      $str_field = substr($dot_field, 0, -1);
      if(is_array($arr_field['relation.']))
      {
        $arr_fields[] = $str_field;
      }
    }
    // Do we have a relation?

//var_dump('controller 538', $arr_fields);
    //////////////////////////////////////////////////////////////////////
    //
    // RETURN in case of no relation
    
    if(!is_array($arr_fields))
    {
      // DRS - Development Reporting System
      if ($this->pObj->b_drs_sql)
      {
        $str_configure = $this->pObj->pi_getLL('phrase_configure_alternate');
        t3lib_div::devlog('[INFO/SQL] There is no field with an array relation.', $this->pObj->extKey, 0);
        t3lib_div::devlog('[HELP/SQL] '.$str_configure.':<br />tables.'.$str_table.'.your_field.relation', $this->pObj->extKey, 1);
      }
      // DRS - Development Reporting System
      return $arr_return;
    }
    // RETURN in case of no relation


    //////////////////////////////////////////////////////////////////////
    //
    // Relation Data
    
    // Loop through all fields with a stdWrap
    foreach($arr_fields as $fieldWiComma)
    {
      //////////////////////////////////////////////////////////////////////
      //
      // DRS - Development Reporting System
      
      if ($this->pObj->b_drs_sql)
      {
        $str_configure = $this->pObj->pi_getLL('phrase_configure_alternate');
        t3lib_div::devlog('[INFO/SQL] '.$str_field.' has an array relation.', $this->pObj->extKey, 0);
        t3lib_div::devlog('[HELP/SQL] '.$str_configure.':<br />tables.'.$str_table.'.'.$fieldWiComma.'.relation', $this->pObj->extKey, 1);
      }
      // DRS - Development Reporting System


      //////////////////////////////////////////////////////////////////////
      //
      // Datas from relation table
      
      $arr_relation = $this->pObj->conf['tables.'][$str_table.'.']['fields.'][$fieldWiComma.'.']['relation.'];
      $arr_result   = $this->pObj->objModel->getrelation_query($arr_relation);
      $query        = $arr_result['data']['query'];
      $res          = $GLOBALS['TYPO3_DB']->sql_query($query);
      // Datas from relation table


      //////////////////////////////////////////////////////////////////////
      //
      // DRS - Development Reporting System
      
      $error         = $GLOBALS['TYPO3_DB']->sql_error();
      $affected_rows = $GLOBALS['TYPO3_DB']->sql_affected_rows();
      if ($error != '')
      {
        if ($this->pObj->b_drs_error)
        {
          t3lib_div::devlog('[ERROR/SQL] '.$query,  $this->pObj->extKey, 3);
          t3lib_div::devlog('[ERROR/SQL] '.$error,  $this->pObj->extKey, 3);
          t3lib_div::devlog('[ERROR/SQL] ABORT.',   $this->pObj->extKey, 3);
        }
        $str_header  = $this->pObj->pi_getLL('error_sql_h1');
        if ($this->pObj->b_drs_error)
        {
          $str_warn    = $this->pObj->pi_getLL('drs_security');
          $str_prompt  = $error.'<br /><br />';
          $str_prompt .= $query;
        }
        else
        {
          $str_prompt = $this->pObj->pi_getLL('drs_sql_prompt');
        }
        $arr_return['error']['status'] = TRUE;
        $arr_return['error']['warn']   = $str_warn;
        $arr_return['error']['header'] = $str_header;
        $arr_return['error']['prompt'] = $str_prompt;
        return $arr_return;
      }
      if ($this->pObj->b_drs_sql)
      {
        t3lib_div::devlog('[INFO/SQL] '.$arr_relation['table'].': '.$this->pObj->pi_getLL('phrase_sql_affected_rows').' #'.$affected_rows, $this->pObj->extKey, 0);
      }
      // DRS - Development Reporting System


      //////////////////////////////////////////////////////////////////////
      //
      // Building Rows
      
      // Converter Rows
      $arr_idConverter = FALSE;
      while ($row_converter = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res))
      {
        $arr_idConverter[$row_converter[$arr_relation['field']]] = $row_converter['uid'];
      }
      // Converter Rows
      
      // Loop through all rows
      foreach($rows as $key => $row)
      {
        $srce_value = $row[$fieldWiComma];
        $dest_value = $arr_idConverter[$srce_value];
        $rows[$key][$fieldWiComma] = $dest_value;
      }
      // Loop through all rows
      // Building Rows
    }
    // Loop through all fields with a stdWrap
    // Relation Data


    $arr_return['data']['rows'] = $rows;
    return $arr_return;
  }
















/**
 * Process ...
 *
 * @param  [type]    $str_table: ...
 * @param  [type]    $arr_data: ...
 * @return  void
 */
  function stdWrap($str_conf, $arr_conf)
  {
    $arr_return = FALSE;

    //////////////////////////////////////
    //
    // Prepaire array for method cObjGet
    
    if (!$str_conf)
    {
      $str_conf = 'TEXT';
    }
    $lCObjArr['10']  = $str_conf;
    $lCObjArr['10.'] = $arr_conf;
    // Prepaire array for method cObjGet


    //////////////////////////////////////
    //
    // cObjGet
    
    $value = $this->pObj->local_cObj->cObjGet($lCObjArr, FALSE);
    // cObjGet

    $arr_return['data']['value'] = $value;
    return $arr_return;

  }
















}


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/browser/pi1/class.tx_sql2typo_pi1_controller.php'])  {
  include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/browser/pi1/class.tx_sql2typo_pi1_controller.php']);
}

?>