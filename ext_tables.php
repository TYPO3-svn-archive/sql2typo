<?php
if (!defined ('TYPO3_MODE')) 
{
  die ('Access denied.');
}



  ////////////////////////////////////////////////////////////////////////////
  // 
  // INDEX
  
  // Enables the Include Static Templates
  // Flexform Configuration
  // Add pagetree icons



  ///////////////////////////////////////
  // 
  // Enables the Include Static Template

t3lib_extMgm::addStaticFile( $_EXTKEY, 'static/', 'SQL to TYPO3' );
  // Enables the Include Static Template


  ///////////////////////////////////////////////////////////
  //
  // Flexform Configuration
  
t3lib_div::loadTCA('tt_content');
$TCA['tt_content']['types']['list']['subtypes_excludelist'][$_EXTKEY.'_pi1'] = 'layout,select_key,pages,recursive';
t3lib_extMgm::addPlugin( array( 'LLL:EXT:sql2typo/locallang_db.xml:tt_content.list_type_pi1', $_EXTKEY . '_pi1', 'EXT:sql2typo/ext_icon.gif' ), 'list_type' );
  // Flexform Configuration



  ////////////////////////////////////////////////////////////////////////////
  // 
  // Add pagetree icons

$TCA['pages']['columns']['module']['config']['items'][ ] = 
  array('AWO Mittelrhein', 'sql2typo', t3lib_extMgm::extRelPath($_EXTKEY) . 'ext_icon.gif' );
t3lib_SpriteManager::addTcaTypeIcon('pages', 'contains-sql2typo',
  t3lib_extMgm::extRelPath($_EXTKEY) . 'ext_icon.gif' );
  // Add pagetree icons



?>