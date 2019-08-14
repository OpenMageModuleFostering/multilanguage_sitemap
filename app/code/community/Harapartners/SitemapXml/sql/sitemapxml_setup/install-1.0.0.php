<?php
/*
 * NOTICE OF LICENSE
 *
 * This source file is subject to the End User Software Agreement (EULA).
 * It is also available through the world-wide-web at this URL:
 * http://www.harapartners.com/license
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to eula@harapartners.com so we can send you a copy immediately.
 * 
 */
$installer = $this;
$installer->startSetup();
$table = $installer->getConnection()
    ->newTable($installer->getTable('sitemapxml/sitemap_language'))
    ->addColumn('language_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        	'identity'  => true,
        	'unsigned'  => true,
        	'nullable'  => false,
        	'primary'   => true,
    ), 'Auto Increment Language Map ID')
    ->addColumn('store_view_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
    		'unsigned' 	=> true,
    		'nullable'  => false,
    ), 'Record Map Status')
	->addColumn('language_code', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
    		'nullable'  => false,
	), 'Language Code')
	->addIndex(
			$installer->getIdxName('sitemapxml/sitemap_language', array('store_view_id')),
	        array('store_view_id'),
	        array('type' => Varien_Db_Adapter_Interface::INDEX_TYPE_INDEX)
	)
    ->setComment('Sitemap Xml Language Map');
$installer->getConnection()->createTable( $table );
$installer->endSetup();
