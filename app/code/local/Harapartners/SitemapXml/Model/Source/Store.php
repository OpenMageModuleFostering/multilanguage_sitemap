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
class Harapartners_SitemapXml_Model_Source_Store
{

    public function getStoreIds()
    {
        $stores = Mage::app()->getStores();
        
        $storename = array();
        foreach ($stores as $store) {
            $id = $store->getId();
            $name = $store->getName();
            $storename[$id] = $name;
        }
        
        return $storename;
    }

    protected function _toOptionArray($skipUsed = false)
    {
        $optionArray = array();
        if ($skipUsed) {
            $usedIds = $this->_getUsedIds();
        } else {
            $usedIds = array();
        }
        
        foreach ($this->getStoreIds() as $id => $name) {
            if (isset($usedIds[$id]) && $skipUsed) {
                continue;
            }
            
            $optionArray[] = array(
                'value' => $id , 
                'label' => $name
            );
        }
        return $optionArray;
    }

    public function toOptionArray()
    {
        return $this->_toOptionArray(true);
    }
    
    protected function _getUsedIds(){
    	$collection = Mage::getModel('sitemapxml/language')->getCollection();
    	
    	$usedId = array();
    	foreach($collection as $language){
    		$id = $language->getStoreViewId();
    		$usedId[$id] =true;
    	}
    	
    	return $usedId;
    }
}
