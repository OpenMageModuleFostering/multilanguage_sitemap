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
class Harapartners_SitemapXml_Helper_Store_Catalog extends Mage_Core_Helper_Abstract
{
    protected $_catToStoreCache = array();
    protected $_productToStoreCache = array();

    public function getCategoriesToStore()
    {
        if (empty($this->_catToStoreCache)) {
            $baseCatToStoreIds = array();
            $storeGroups = Mage::getModel('core/store_group')->getCollection();
            foreach ($storeGroups as $storeGroup) {
                $storeIds = $storeGroup->getStoreIds();
                $baseCat = $storeGroup->getRootCategoryId();
                
                foreach ($storeIds as $storeId) {
                    $baseCatToStoreIds[$baseCat][$storeId] = true;
                }
            }
            
            $categoryToStoreIds = array();
            $collection = Mage::getModel('catalog/category')->getCollection();
            foreach ($collection as $category) {
                $path = $category->getPath();
                $pathExploded = explode('/', $path);
                $baseCat = $pathExploded[1];
                
                $categoryToStoreIds[$category->getId()] = $baseCatToStoreIds[$baseCat];
            }
            
            $this->_catToStoreCache = $categoryToStoreIds;
        }
        return $this->_catToStoreCache;
    }

    public function getProductsToStore()
    {
        if (empty($this->_productToStoreCache)) {
            $websiteToProductId = array();
            $resourceHelper = Mage::getModel('catalog/product_website')->getResource();
            $productWebsites = $resourceHelper->getWebsites(Mage::getModel('catalog/product')->getCollection()->getAllIds());
            foreach ($productWebsites as $productId => $websiteIds) {
                foreach ($websiteIds as $websiteId) {
                    $websiteToProductIds[$websiteId][] = $productId;
                }
            }
            
            $websiteToStoreIds = array();
            $collection = Mage::getModel('core/store')->getCollection();
            foreach ($collection as $store) {
                $storeId = $store->getId();
                $websiteId = $store->getWebsiteId();
                
                $websiteToStoreIds[$websiteId][] = $storeId;
            }
            
            $productToStoreIds = array();
            foreach ($websiteToProductIds as $websiteId => $productIds) {
                foreach ($productIds as $productId) {
                    foreach ($websiteToStoreIds[$websiteId] as $storeId)
                        $productToStoreIds[$productId][$storeId] = true;
                }
            }
            
            $this->_productToStoreCache = $productToStoreIds;
        }
        return $this->_productToStoreCache;
    }
}