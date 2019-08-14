<?php
/**
 * NOTICE OF LICENSE
 *
 * This source file is subject to the End User Software Agreement (EULA).
 * It is also available through the world-wide-web at this URL:
 * http://www.harapartners.com/license
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to eula@harapartners.com so we can send you a copy immediately.
 */

class Harapartners_SitemapXml_Block_Adminhtml_Language_Index extends Mage_Adminhtml_Block_Widget_Grid_Container {
	/**
	 * Constructor
	 */
	public function __construct() {
		parent::__construct ();
		$this->_blockGroup = 'sitemapxml';
		$this->_controller = 'adminhtml_language_index';
		$this->_headerText = Mage::helper ( 'sitemapxml' )->__ ( 'Langauge Map Entries' );
	}	
}