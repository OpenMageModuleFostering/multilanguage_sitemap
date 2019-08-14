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

class Harapartners_SitemapXml_Block_Adminhtml_Language_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{

    protected function _prepareForm()
    {
        $yesno = Mage::getModel('adminhtml/system_config_source_yesno');
        
        $form = new Varien_Data_Form(array(
            'id' => 'edit_form' , 
            'action' => $this->getData('action') , 
            'method' => 'post'
        ));
        
        // -------------------------------- Basic Info -------------------------------- //
        $fieldsetIds = $form->addFieldset('language', array(
            'legend' => Mage::helper('sitemapxml')->__('Language Map Info')
        ));
        
        $fieldsetIds->addField('language_id', 'label', array(
            'label' => Mage::helper('sitemapxml')->__('Language Map ID') , 
            'name' => 'language_id' , 
            'required' => true 
        ));
        
        $fieldsetIds->addField('store_view_id', 'select', array(
            'label'     => Mage::helper('sitemapxml')->__('Record Map Status'),
            'name'      => 'store_view_id',
            'values'    => Mage::getModel('sitemapxml/source_store')->toOptionArray(),
        	'required'	=> true
        ));
        
        $fieldsetIds->addField('language_code', 'text', array(
            'label' => Mage::helper('sitemapxml')->__('Language Code') , 
            'name' => 'language_code',
       		'required'	=> true
        ));
        
        
        if (Mage::registry('sitemapxml_language_data')) {
            $form->setValues(Mage::registry('sitemapxml_language_data')->getData());
        } elseif ($recordData = Mage::getSingleton('adminhtml/session')->getData('sitemapxml_language_data')) {
            $form->setValues($recordData);
            Mage::getSingleton('adminhtml/session')->setData('sitemapxml_language_data', null);
        }
        
        $form->setUseContainer(true);
        $this->setForm($form);
        return parent::_prepareForm();
    }
}