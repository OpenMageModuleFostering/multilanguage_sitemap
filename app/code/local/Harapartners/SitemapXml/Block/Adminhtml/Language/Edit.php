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

class Harapartners_SitemapXml_Block_Adminhtml_Language_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{

    public function __construct()
    {
        parent::__construct();
        
        $this->_objectId = 'language_id';
        $this->_blockGroup = 'sitemapxml';
        $this->_controller = 'adminhtml_Language';
        
        $this->_updateButton('save', 'label', Mage::helper('sitemapxml')->__('Save Language Map'));
        $this->_updateButton('delete', 'label', Mage::helper('sitemapxml')->__('Delete Language Map'));
    }

    public function getHeaderText()
    {
        if (Mage::registry('sitemapxml_language_data') && Mage::registry('sitemapxml_language_data')->getId()) {
            return Mage::helper('sitemapxml')->__('Edit Record Map');
        } else {
            return Mage::helper('sitemapxml')->__('Add Record Map');
        }
    }

    protected function _prepareLayout()
    {
        $this->setChild('back_button', $this->getLayout()->createBlock('adminhtml/widget_button')->setData(array(
            'label' => Mage::helper('sitemapxml')->__('Back') , 
            'onclick' => "setLocation('" . $this->getUrl('*/*/index') . "')" , 
            'class' => 'back'
        )));
        
        return parent::_prepareLayout();
    }

    public function getBackButtonHtml()
    {
        return $this->getChildHtml('back_button');
    }

    public function getSaveUrl()
    {
        return $this->getUrl('*/*/save', array(
            '_current' => true
        ));
    }

}