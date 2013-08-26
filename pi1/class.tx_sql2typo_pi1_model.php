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
 *   54: class tx_sql2typo_pi1_model
 *   81:     function __construct($parentObj)
 *  116:     function connect($arr_database)
 *  144:     function disconnect($res)
 *  172:     function getdata($str_table)
 *  203:     function writedata($str_table)
 *  236:     function getdata_query($str_table)
 *  264:     function writedata_query($str_table)
 *
 * TOTAL FUNCTIONS: 7
 * (This index is automatically created/updated by the extension "extdeveval")
 *
 */


 /**
  * The class tx_sql2typo_pi1_model is the model class for sql2typo.
  *
  * @author    Dirk Wildt <wildt.at.die-netzmacher.de>
  * @package    TYPO3
  * @subpackage    tx_sql2auto_pi1
  */
class tx_sql2typo_pi1_model
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
 * @param  object    The parent object
 * @return  void
 */
  function __construct($parentObj)
  {
    // Set the Parent Object
    $this->pObj = $parentObj;

  }























/**
 * Process ...
 *
 * @param  [type]    $str_table: ...
 * @return  void
 */
  function connect($arr_database)
  {
    $arr_return = FALSE;

    return $arr_return;
  }
















/**
 * Process ...
 *
 * @param  [type]    $str_table: ...
 * @return  void
 */
  function disconnect($res)
  {
    $arr_return = FALSE;

    return $arr_return;
  }
















/**
 * The method gets the data out of the sql database. Properties of the select query is
 * configured by TypoScript.
 *
 * @param  [string]    $str_table: The name of the current table
 * @return  [array]    $arr_return: data|value provides the select statement. If there is an error, 
 *                     the array error provides the error prompt.
 */
  function getdata($str_table)
  {
    $arr_return = FALSE;


    //////////////////////////////////////////////////////////////////////
    //
    // SELECT statement
    
    $arr_result = $this->getdata_query($str_table);
    if($arr_return['error']['status'])
    {
      return $arr_return;
    }
    $query  = $arr_result['data']['query'];
    //$select_fields = $arr_result['data']['select_fields'];
    //$from_table    = $arr_result['data']['from_table'];
    //$where_clause  = $arr_result['data']['andWhere'];
    unset($arr_result);
    // SELECT statement


    //////////////////////////////////////////////////////////////////////
    //
    // Execute Query
    
    //$GLOBALS['TYPO3_DB']->exec_SELECTquery($select_fields, $from_table, $where_clause, $groupBy='', $orderBy='', $limit='');
    $res   = $GLOBALS['TYPO3_DB']->sql_query($query);
    // Execute Query


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
        $str_prompt = '<p style="border: 2px dotted red; font-weight:bold;text-align:center; padding:1em;">'.$this->pObj->pi_getLL('drs_sql_prompt').'</p>';
      }
      $arr_return['error']['status'] = TRUE;
      $arr_return['error']['warn']   = $str_warn;
      $arr_return['error']['header'] = $str_header;
      $arr_return['error']['prompt'] = $str_prompt;
      return $arr_return;
    }
    if ($this->pObj->b_drs_sql)
    {
      t3lib_div::devlog('[INFO/SQL] '.$str_table.': '.$this->pObj->pi_getLL('phrase_sql_affected_rows').' #'.$affected_rows, $this->pObj->extKey, 0);
    }
    // DRS - Development Reporting System


    //////////////////////////////////////////////////////////////////////
    //
    // Convert form ISO to UTF8?
    
    $bool_convert = $this->pObj->conf['tables.'][$str_table.'.']['source.']['iso2utf'];
    if ($bool_convert)
    {
      if ($this->pObj->b_drs_sql)
      {
        t3lib_div::devlog('[INFO/SQL] Values will be converted from ISO to UTF8.', $this->pObj->extKey, 0);
        $str_configure = $this->pObj->pi_getLL('phrase_configure_alternate');
        t3lib_div::devlog('[HELP/SQL] '.$str_configure.':<br />tables.'.$str_table.'.source.iso2utf = 0', $this->pObj->extKey, 1);
      }
    }
    if (!$bool_convert)
    {
      if ($this->pObj->b_drs_sql)
      {
        t3lib_div::devlog('[INFO/SQL] Values won\'t be converted from ISO to UTF8.', $this->pObj->extKey, 0);
        $str_configure = $this->pObj->pi_getLL('phrase_configure_alternate');
        t3lib_div::devlog('[HELP/SQL] '.$str_configure.':<br />tables.'.$str_table.'.source.iso2utf = 1', $this->pObj->extKey, 1);
      }
    }
    // Convert form ISO to UTF8?


    //////////////////////////////////////////////////////////////////////
    //
    // Building Rows
    
    $rows = FALSE;
    while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res))
    {
      if($bool_convert)
      {
        foreach($row as $key => $value)
        {
          $row[$key] = utf8_encode($value);
        }
      }
      $rows[] = $row;
    }
    $arr_return['data']['rows']   = $rows;
    $arr_return['data']['prompt'] = $str_table.': '.$this->pObj->pi_getLL('phrase_sql_affected_rows').' #'.$affected_rows;
    // Building Rows


    return $arr_return;
  }
















/**
 * Process ...
 *
 * @param  [type]    $str_table: ...
 * @return  void
 */
  function writedata($str_table, $srce_rows)
  {
    $arr_return = FALSE;

    //////////////////////////////////////////////////////////////////////
    //
    // Get destination fields
    
    foreach($this->pObj->conf['tables.'][$str_table.'.']['fields.'] as $dot_field => $arr_field)
    {
      $str_field = substr($dot_field, 0, -1);
      if(isset($arr_field['destination']))
      {
        $arr_convert[$str_field] = $arr_field['destination'];
      }
      if(!isset($arr_field['destination']))
      {
        $arr_convert[$str_field] = $str_field;
      }
    }
    // Get destination fields


    //////////////////////////////////////////////////////////////////////
    //
    // Get destination rows
    
    // Loop through all rows
    $dest_rows     = FALSE;
    $bool_firstRow = TRUE;
    foreach($srce_rows as $key => $srce_row)
    {
      // Loop through all fields
      foreach($srce_row as $srce_field => $srce_value)
      {
        if($bool_firstRow)
        {
          // DRS - Development Reporting System
          if ($this->pObj->b_drs_sql)
          {
            $str_configure = $this->pObj->pi_getLL('phrase_configure_alternate');
            t3lib_div::devlog('[INFO/SQL] $dest_row['.$arr_convert[$srce_field].'] = $srce_row['.$srce_field.']<br />'.
              '$dest_row['.$arr_convert[$srce_field].'] = '.$srce_row[$srce_field], $this->pObj->extKey, 0);
            t3lib_div::devlog('[HELP/SQL] '.$str_configure.':<br />tables.'.$str_table.'.'.$srce_field.'.destination'.
              ' = '.$arr_convert[$srce_field], $this->pObj->extKey, 1);
          }
          // DRS - Development Reporting System
        }
        $dest_rows[$key][$arr_convert[$srce_field]] = $srce_row[$srce_field];
      }
      $bool_firstRow = FALSE;
      // Loop through all fields
    }
    // Loop through all rows
    // Get destination rows


    //////////////////////////////////////////////////////////////////////
    //
    // Get Queries
    
    $arr_result = $this->writedata_query($str_table, $dest_rows);
    //var_dump('model 340', $arr_result['data']['query']);
    // Get Queries


    //////////////////////////////////////////////////////////////////////
    //
    // Execute Queries
    
    $arr_prompt     = FALSE;
    $arr_queries    = array('before', 'query', 'after');
    $arr_queries    = array_intersect($arr_queries, array_keys($arr_result['data']['query']));
    $str_dest_table = $this->pObj->conf['tables.'][$str_table.'.']['destination.']['table'];
    // Loop throuhg the array with the queries
    foreach($arr_queries as $str_query_type)
    {
      //var_dump('model 354', $str_query_type);
      // Execute Query
      $query = $arr_result['data']['query'][$str_query_type];
      $res   = $GLOBALS['TYPO3_DB']->sql_query($query);
      // Execute Query
      
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
          $str_prompt = '<p style="border: 2px dotted red; font-weight:bold;text-align:center; padding:1em;">'.$this->pObj->pi_getLL('drs_sql_prompt').'</p>';
        }
        $arr_return['error']['status'] = TRUE;
        $arr_return['error']['warn']   = $str_warn;
        $arr_return['error']['header'] = $str_header;
        $arr_return['error']['prompt'] = $str_prompt;
        return $arr_return;
      }
      $arr_prompt[] = $str_dest_table.': '.$this->pObj->pi_getLL('phrase_sql_affected_rows').' #'.$affected_rows;
      if ($this->pObj->b_drs_sql)
      {
        t3lib_div::devlog('[INFO/SQL] '.$str_table.': '.$query, $this->pObj->extKey, 0);
        t3lib_div::devlog('[INFO/SQL] '.$str_table.': '.$this->pObj->pi_getLL('phrase_sql_affected_rows').' #'.$affected_rows, $this->pObj->extKey, 0);
      }
      // DRS - Development Reporting System
    }
    // Loop through the array with the queries
    // Execute Queries

    $prompt = implode('<br />', $arr_prompt);
    $arr_return['data']['prompt'] = $prompt;
    return $arr_return;
  }
















/**
 * The method builds a select statement. Table, fields and an optinonal andWhere have to be
 * configured in the TypoScript.
 *
 * @param  [string]    $str_table: The name of the current table
 * @return  [array]    $arr_return: data provides the select statement, select_fields, andWhere.
 *                     If there is an error, the array error provides the error prompt.
 */
  function getdata_query($str_table)
  {
    $arr_return = FALSE;
    $str_query  = FALSE;


    //////////////////////////////////////////////////////////////////////
    //
    // Get Fields
    
    // RETURN, if there isn't any field
    if(!is_array($this->pObj->conf['tables.'][$str_table.'.']['fields.']))
    {
      $str_header  = $this->pObj->pi_getLL('error_fields_h1');
      $str_prompt  = $this->pObj->pi_getLL('error_fields_prompt');
      $str_info    = $this->pObj->pi_getLL('phrase_configure').' tables.'.$str_table.'.fields';
      $arr_return['error']['status'] = TRUE;
      $arr_return['error']['header'] = $str_header;
      $arr_return['error']['prompt'] = $str_prompt.'<br /><br />'.$str_info;
      if ($this->pObj->b_drs_error)
      {
        $str_prompt = $this->pObj->pi_getLL('config_error_prompt');
        t3lib_div::devlog('[ERROR/MODEL] '.$str_prompt, $this->pObj->extKey, 3);
        t3lib_div::devlog('[ERROR/MODEL] '.$str_info,   $this->pObj->extKey, 1);
      }
      return $arr_return;
    }
    // RETURN, if there isn't any field
    
    $arr_fields = FALSE;
    foreach($this->pObj->conf['tables.'][$str_table.'.']['fields.'] as $dot_field => $arr_field)
    {
      $arr_fields[] = "`".substr($dot_field, 0, -1)."`";
    }
    $str_fields = implode(', ', $arr_fields);  // `BRG_Id`, `BRG_Text`
    $arr_return['data']['select_fields'] = $str_fields;
    // Get Fields


    //////////////////////////////////////////////////////////////////////
    //
    // Get Fields
    
    $arr_return['data']['from_table'] = $str_table;
    // Get Fields


    //////////////////////////////////////////////////////////////////////
    //
    // Get andWhere
    
    $str_andWhere  = FALSE;
    $bool_andWhere = FALSE;
    if(is_array($this->pObj->conf['tables.'][$str_table.'.']['source.']['sql.']['andWhere.']))
    {
      $bool_andWhere = TRUE;
    }
    if($bool_andWhere)
    {
      $str_conf     = $this->pObj->conf['tables.'][$str_table.'.']['source.']['sql.']['andWhere'];
      $arr_conf     = $this->pObj->conf['tables.'][$str_table.'.']['source.']['sql.']['andWhere.'];
      $arr_result   = $this->pObj->objController->stdWrap($str_conf, $arr_conf);
      $str_andWhere = $arr_result['data']['value'];
      unset($arr_result);
      // Wrap andWhere
    }
    if($str_andWhere)
    {
      $str_andWhere = ' AND '.$str_andWhere;
      $arr_return['data']['andWhere'] = $str_andWhere;
      if ($this->pObj->b_drs_sql)
      {
        $str_configure = $this->pObj->pi_getLL('phrase_configure_alternate');
        t3lib_div::devlog('[INFO/SQL] andWhere statement: '.$str_andWhere.'\'', $this->pObj->extKey, 0);
        t3lib_div::devlog('[HELP/SQL] '.$str_configure.':<br />tables.'.$str_table.'.source.sql.andWhere', $this->pObj->extKey, 1);
      }
    }
    if(!$str_andWhere)
    {
      if ($this->pObj->b_drs_sql)
      {
        t3lib_div::devlog('[INFO/SQL] We don\'t have any andWhere statement.', $this->pObj->extKey, 0);
        t3lib_div::devlog('[HELP/SQL] '.$str_configure.':<br />tables.'.$str_table.'.source.sql.andWhere', $this->pObj->extKey, 1);
      }
    }
    // Get andWhere


    //////////////////////////////////////////////////////////////////////
    //
    // SELECT statement
    
    $str_query = "SELECT ".$str_fields." FROM `".$str_table."` WHERE 1".$str_andWhere;
    $arr_return['data']['query'] = $str_query;
    if ($this->pObj->b_drs_sql)
    {
      t3lib_div::devlog('[INFO/SQL] '.$str_query.'\'', $this->pObj->extKey, 0);
    }
    // SELECT statement


    return $arr_return;
  }
















/**
 * The method builds a select statement. Table, fields and an optinonal andWhere have to be
 * configured in the TypoScript.
 *
 * @param  [string]    $str_table: The name of the current table
 * @return  [array]    $arr_return: data provides the select statement, select_fields, andWhere.
 *                     If there is an error, the array error provides the error prompt.
 */
  function getrelation_query($arr_relation)
  {
    $arr_return = FALSE;
    $str_query  = FALSE;


    //////////////////////////////////////////////////////////////////////
    //
    // SELECT statement
    
    $str_fields = '`uid`, `'.$arr_relation['field'].'`';
    $str_table  = '`'.$arr_relation['table'].'`';
    $str_query = "SELECT ".$str_fields." FROM ".$str_table;
    $arr_return['data']['query'] = $str_query;
    if ($this->pObj->b_drs_sql)
    {
      t3lib_div::devlog('[INFO/SQL] '.$str_query.'\'', $this->pObj->extKey, 0);
    }
    // SELECT statement


    return $arr_return;
  }
















/**
 * Process ...
 *
 * @param  [type]    $str_table: ...
 * @return  void
 */
  function writedata_query($str_table, $rows)
  {
    
    //////////////////////////////////////////////////////////////////////
    //
    // SQL before import
    
    $str_sqlBeforeImport = $this->pObj->conf['tables.'][$str_table.'.']['destination.']['sql.']['beforeImport'];
    if($str_sqlBeforeImport)
    {
      $arr_return['data']['query']['before'] = $str_sqlBeforeImport;
    }
    // SQL before import


    //////////////////////////////////////////////////////////////////////
    //
    // SQL import
    
    // Get table
    $dest_table = $this->pObj->conf['tables.'][$str_table.'.']['destination.']['table'];
    if(!$dest_table or $$dest_table == '')
    {
      // ERROR
    }
    // Get table
    
    // Get source fields
    reset($rows);
    $firstKey = key($rows);
    $arr_fields = array_keys($rows[$firstKey]);
    $str_fields = implode('`, `', $arr_fields);
    $str_fields = '`'.$str_fields.'`';
    // Get source fields
    
    // Prepend destination fields from TypoScript
    unset($arr_fields);
    foreach($this->pObj->conf['tables.'][$str_table.'.']['destination.']['fields.'] as $dot_field => $arr_field)
    {
      $str_field    = substr($dot_field, 0, -1);
      $str_conf     = $arr_field['stdWrap'];
      $arr_conf     = $arr_field['stdWrap.'];
      $arr_result   = $this->pObj->objController->stdWrap($str_conf, $arr_conf);
      $arr_fields[$str_field] = $arr_result['data']['value'];
    }
    // Prepend destination fields from TypoScript

    // Prepend destination values from TypoScript
    if(is_array($arr_fields))
    {
      $str_prepend_fields = implode('`, `', array_keys($arr_fields));
      $str_prepend_fields = '`'.$str_prepend_fields.'`, ';
      $arr_values         = $GLOBALS['TYPO3_DB']->fullQuoteArray($arr_fields, $dest_table, $noQuote=FALSE);
      $str_prepend_values = implode(", ", $arr_values);
      $str_prepend_values = $str_prepend_values.', ';
    }
    // Prepend destination values from TypoScript
    
    // Query part fields
    $str_query = 'INSERT INTO `'.$dest_table.'` ('.$str_prepend_fields.$str_fields.') VALUES'."\n".'(%values);';
    // Query part fields
    
    // Query part values
    // Loop through all rows
    $arr_values = FALSE;
    foreach($rows as $elements)
    {
      $elements     = $GLOBALS['TYPO3_DB']->fullQuoteArray($elements, $dest_table, $noQuote=FALSE);
      $str_values   = implode(", ", $elements);
      $arr_values[] = $str_prepend_values.$str_values;
    }
    $str_values = implode ('),'."\n".'(', $arr_values);
    // Loop through all rows
    
    $str_query = str_replace('%values', $str_values, $str_query);
    $arr_return['data']['query']['query'] = $str_query;
    // SQL before import


    //////////////////////////////////////////////////////////////////////
    //
    // SQL after import
    
    $str_sqlAfterImport  = $this->pObj->conf['tables.'][$str_table.'.']['destination.']['sql.']['afterImport'];
    if($str_sqlAfterImport)
    {
      $arr_return['data']['query']['after'] = $str_sqlBeforeImport;
    }
    // SQL after import

    //var_dump('model 590', $arr_return);
    return $arr_return;
  }
















}


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/browser/pi1/class.tx_sql2typo_pi1_model.php'])  {
  include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/browser/pi1/class.tx_sql2typo_pi1_model.php']);
}

?>