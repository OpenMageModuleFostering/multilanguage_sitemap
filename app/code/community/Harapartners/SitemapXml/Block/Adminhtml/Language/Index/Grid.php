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

class Harapartners_SitemapXml_Block_Adminhtml_Language_Index_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('netsuiteLanguageGrid');
    }

    protected function _prepareCollection()
    {
        $model = Mage::getModel('sitemapxml/language');
        $collection = $model->getCollection();
        $this->setCollection($collection);
        parent::_prepareCollection();
        return $this;
    }

    protected function _getStore()
    {
        $storeId = (int) $this->getRequest()->getParam('store', 0);
        return Mage::app()->getStore($storeId);
    }

    protected function _prepareColumns()
    {
        $this->addColumn('language_id', array(
            'header' => Mage::helper('sitemapxml')->__('Language Map ID') , 
            'align' => 'right' , 
            'width' => '50px' , 
            'index' => 'language_id'
        ));
        
        $this->addColumn('store_view_id', array(
            'header' => Mage::helper('sitemapxml')->__('Store View Id') , 
            'align' => 'right' , 
            'width' => '50px' , 
            'index' => 'store_view_id' , 
            'type' => 'options' , 
            'options' => Mage::getModel('sitemapxml/source_store')->getStoreIds()
        ));
        
        $this->addColumn('language_code', array(
            'header' => Mage::helper('sitemapxml')->__('Language Code') , 
            'align' => 'right' , 
            'width' => '50px' , 
            'index' => 'language_code'
        ));

        return parent::_prepareColumns();
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array(
            'store' => $this->getRequest()->getParam('store') , 
            'id' => $row->getId()
        ));
    }

}