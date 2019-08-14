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

class Harapartners_Sitemapxml_Adminhtml_LanguageController extends Mage_Adminhtml_Controller_Action {
	
	/**
	 * View form action
	 */
	public function indexAction() {
		$this->loadLayout ();
		$this->_setActiveMenu ( 'sitemapxml/language' );
		//$this->_addBreadcrumb ( Mage::helper ( 'webservice' )->__ ( 'Product1' ), Mage::helper ( 'webservice' )->__ ( 'Form' ) );
		$this->_addContent ( $this->getLayout ()->createBlock ( 'sitemapxml/adminhtml_language_index' ) );
		$this->renderLayout ();
	}
	
	public function editAction() {
		$id = $this->getRequest ()->getParam ( 'id' );
		$model = Mage::getModel ( 'sitemapxml/language' )->load ( $id );
		
		if ($model->getId () || $id == 0) {
			$data = Mage::getSingleton ( 'adminhtml/session' )->getData ( 'sitemapxml_language_data' );
			if (! empty ( $data )) {
				$model->setData ( $data );
			}
			
			Mage::register ( 'sitemapxml_language_data', $model );
			
			$this->loadLayout ()->_setActiveMenu ( 'sitemapxml/language' );
			
			//$this->_addBreadcrumb ( Mage::helper ( 'sitemapxml' )->__ ( 'Manage Buy X Rules' ), Mage::helper ( 'adminhtml' )->__ ( 'Manage Buy X Rules' ) );
			//$this->_addBreadcrumb ( Mage::helper ( 'sitemapxml' )->__ ( 'Buy X Rule Configuration' ), Mage::helper ( 'adminhtml' )->__ ( 'Buy X Rule Configuration' ) );
			
			$this->getLayout ()->getBlock ( 'head' )->setCanLoadExtJs ( true );
			
			$this->_addContent ( $this->getLayout ()->createBlock ( 'sitemapxml/adminhtml_language_edit' ) );
			
			$this->renderLayout ();
		} else {
			Mage::getSingleton ( 'adminhtml/session' )->addError ( Mage::helper ( 'sitemapxml' )->__ ( 'Language Map does not exist' ) );
			$this->_redirect ( '*/*/' );
		}
	}
	
	public function newAction(){
		return $this->editAction();
	}
	
	public function saveAction() {
		if ($data = $this->getRequest ()->getPost ()) {
			$model = Mage::getModel ( 'sitemapxml/language' );
			$model->setData ( $data )->setId ( $this->getRequest ()->getParam ( 'id' ) );
			
			try {
				$model->save ();
				Mage::getSingleton ( 'adminhtml/session' )->addSuccess ( Mage::helper ( 'sitemapxml' )->__ ( 'Language Map Saved' ) );
				Mage::getSingleton ( 'adminhtml/session' )->setData ( false );
				
				if ($this->getRequest ()->getParam ( 'back' )) {
					$this->_redirect ( '*/*/edit', array ('id' => $model->getId () ) );
					return;
				}
				$this->_redirect ( '*/*/' );
				return;
			} catch ( Exception $e ) {
				Mage::getSingleton ( 'adminhtml/session' )->addError ( $e->getMessage () );
				Mage::getSingleton ( 'adminhtml/session' )->setData ('sitemapxml_language_data', $data );
				$this->_redirect ( '*/*/edit', array ('id' => $this->getRequest ()->getParam ( 'id' ) ) );
				return;
			}
		}
		Mage::getSingleton ( 'adminhtml/session' )->addError ( Mage::helper ( 'sitemapxml' )->__ ( 'Unable to find Language Map to Save' ) );
		$this->_redirect ( '*/*/' );
	}
	
	public function deleteAction() {
		if ($id = $this->getRequest ()->getParam ( 'id' )) {
			$model = Mage::getModel ( 'sitemapxml/language' );
			$model->setId ( $id );
			
			try {
				$model->delete ();
				Mage::getSingleton ( 'adminhtml/session' )->addSuccess ( Mage::helper ( 'sitemapxml' )->__ ( 'Language Map Deleted' ) );
				Mage::getSingleton ( 'adminhtml/session' )->setData ( false );
				
				if ($this->getRequest ()->getParam ( 'back' )) {
					$this->_redirect ( '*/*/');
					return;
				}
				$this->_redirect ( '*/*/' );
				return;
			} catch ( Exception $e ) {
				Mage::getSingleton ( 'adminhtml/session' )->addError ( $e->getMessage () );
				Mage::getSingleton ( 'adminhtml/session' )->setData ('sitemapxml_language_data', $data );
				$this->_redirect ( '*/*/edit', array ('id' => $this->getRequest ()->getParam ( 'id' ) ) );
				return;
			}
		}
		Mage::getSingleton ( 'adminhtml/session' )->addError ( Mage::helper ( 'sitemapxml' )->__ ( 'Unable to find Language Map to Delete' ) );
		$this->_redirect ( '*/*/' );
	}
}
