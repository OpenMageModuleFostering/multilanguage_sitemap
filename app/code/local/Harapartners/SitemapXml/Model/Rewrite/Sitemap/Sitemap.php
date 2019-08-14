<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Sitemap
 * @copyright   Copyright (c) 2012 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Sitemap model
 *
 * @method Mage_Sitemap_Model_Resource_Sitemap _getResource()
 * @method Mage_Sitemap_Model_Resource_Sitemap getResource()
 * @method string getSitemapType()
 * @method Mage_Sitemap_Model_Sitemap setSitemapType(string $value)
 * @method string getSitemapFilename()
 * @method Mage_Sitemap_Model_Sitemap setSitemapFilename(string $value)
 * @method string getSitemapPath()
 * @method Mage_Sitemap_Model_Sitemap setSitemapPath(string $value)
 * @method string getSitemapTime()
 * @method Mage_Sitemap_Model_Sitemap setSitemapTime(string $value)
 * @method int getStoreId()
 * @method Mage_Sitemap_Model_Sitemap setStoreId(int $value)
 *
 * @category    Mage
 * @package     Mage_Sitemap
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Harapartners_Sitemapxml_Model_Rewrite_Sitemap_Sitemap extends Mage_Sitemap_Model_Sitemap
{
    const LANGUAGE_FILENAME = 'language.xml';
    
    protected $_xmlGenLang;

    /**
     * Generate XML file
     *
     * @return Mage_Sitemap_Model_Sitemap
     */
    public function generateXml()
    {
        if (! $this->_isLanguageEnabled()) {
            return parent::generateXml();
        }
        
        $io = new Varien_Io_File();
        $io->setAllowCreateFolders(true);
        $io->open(array(
            'path' => $this->getPath()
        ));
        
        if ($io->fileExists($this->getSitemapFilename()) && ! $io->isWriteable($this->getSitemapFilename())) {
            Mage::throwException(Mage::helper('sitemap')->__('File "%s" cannot be saved. Please, make sure the directory "%s" is writeable by web server.', $this->getSitemapFilename(), $this->getPath()));
        }
        
        $io->streamOpen($this->getSitemapFilename());
        
        $io->streamWrite('<?xml version="1.0" encoding="UTF-8"?>' . "\n");
        $io->streamWrite('<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:xhtml="http://www.w3.org/1999/xhtml">');
        
        $storeId = $this->getStoreId();
        $date = Mage::getSingleton('core/date')->gmtDate('Y-m-d');
        $baseUrl = Mage::app()->getStore($storeId)->getBaseUrl(Mage_Core_Model_Store::URL_TYPE_LINK);
        
        /**
         * Generate categories sitemap
         */
        $changefreq = (string) Mage::getStoreConfig('sitemap/category/changefreq', $storeId);
        $priority = (string) Mage::getStoreConfig('sitemap/category/priority', $storeId);
        $collection = Mage::getResourceModel('sitemap/catalog_category')->getCollection($storeId);
        foreach ($collection as $item) {
            $xml = sprintf('<url><loc>%s</loc><lastmod>%s</lastmod><changefreq>%s</changefreq><priority>%.1f</priority></url>', htmlspecialchars($baseUrl . $item->getUrl()), $date, $changefreq, $priority);
            $newXml = $this->_insertLanguageXml($baseUrl, $item, 'category', $xml);
            $io->streamWrite($newXml);
        }
        unset($collection);
        

                /**
         * Generate products sitemap
         */
        $changefreq = (string) Mage::getStoreConfig('sitemap/product/changefreq', $storeId);
        $priority = (string) Mage::getStoreConfig('sitemap/product/priority', $storeId);
        $collection = Mage::getResourceModel('sitemap/catalog_product')->getCollection($storeId);
        $collection = $this->_joinByOtherStoreIds($collection, $this->_getOtherStoreIds($storeId));
        foreach ($collection as $item) {
            $xml = sprintf('<url><loc>%s</loc><lastmod>%s</lastmod><changefreq>%s</changefreq><priority>%.1f</priority></url>', htmlspecialchars($baseUrl . $item->getUrl()), $date, $changefreq, $priority);
            $newXml = $this->_insertLanguageXml($baseUrl, $item, 'product', $xml);
            $io->streamWrite($newXml);
        }
        unset($collection);
        
        /**
         * Generate cms pages sitemap
         */
        $changefreq = (string) Mage::getStoreConfig('sitemap/page/changefreq', $storeId);
        $priority = (string) Mage::getStoreConfig('sitemap/page/priority', $storeId);
        $collection = Mage::getResourceModel('sitemap/cms_page')->getCollection($storeId);
        foreach ($collection as $item) {
            $xml = sprintf('<url><loc>%s</loc><lastmod>%s</lastmod><changefreq>%s</changefreq><priority>%.1f</priority></url>', htmlspecialchars($baseUrl . $item->getUrl()), $date, $changefreq, $priority);
            $io->streamWrite($xml);
        }
        unset($collection);
        
        $io->streamWrite('</urlset>');
        $io->streamClose();
        
        $this->setSitemapTime(Mage::getSingleton('core/date')->gmtDate('Y-m-d H:i:s'));
        $this->save();
        
        return $this;
    }

    protected function _insertLanguageXml($baseUrl, $item, $itemType, $xml)
    {
        if (! $item->getId() || ! $item->getUrl()) {
            return $xml;
        }
        
        $insertXml = $this->_getLanguageXml($baseUrl, $item, $itemType);
        
        $count = 0;
        $returnXml = str_replace('</url>', ' ' . $insertXml . ' </url>', $xml, $count);
        $oldKeep = substr($xml, 0, strlen($xml) - 6);
        $returnXml = $oldKeep . ' ' . $insertXml . ' </url>';
        $count = 1;
        if ($count != 1) {
            //error
            return $xml;
        } else {
            return $returnXml;
        }
    
    }

    protected function _getLanguageXml($baseUrl, $item, $itemType)
    {
        return $this->_getXmlLangGen()->getUrlBlock($baseUrl, $item, $itemType, false);
    }

    /**
     * Enter description here...
     *
     * @return Harapartners_SitemapXml_Model_Source_Xml
     */
    protected function _getXmlLangGen()
    {
        if (! isset($this->_xmlGenLang)) {
            $this->_xmlGenLang = Mage::getModel('sitemapxml/source_xml');
        }
        
        return $this->_xmlGenLang;
    }

    protected function _isLanguageEnabled()
    {
        $siteMapFilename = $this->getSitemapFilename();
        if ($siteMapFilename !== self::LANGUAGE_FILENAME) {
            //return false;
        }
        
        return Mage::helper('sitemapxml')->isLanguageEnabled();
    }

    protected function _getOtherStoreIds($oldStoreId)
    {
        $allStores = Mage::app()->getStores();
        foreach(Mage::getModel('sitemapxml/language')->getCollection() as $site){
        	$sites[$site->getStoreViewId()] = true;
        }
        
        $collection = Mage::getModel('sitemapxml/language')->getCollection();
        foreach ($allStores as $storeId => $val) {
            if ($storeId != $oldStoreId && isset($sites[$storeId])) {
                $otherStores[] = $storeId;
            }
        }
        
        return $otherStores;
    }

    protected function _joinByOtherStoreIds($currentCollection, $newStoreIds)
    {
        if (empty($newStoreIds)) {
            return $currentCollection;
        }
        
        foreach ($currentCollection as $product) {
            $id = $product->getId();
            $ids[$id] = true;
        }
        

        foreach ($newStoreIds as $storeId) {
            $tempCollection = Mage::getResourceModel('sitemap/catalog_product')->getCollection($storeId);
            
            foreach ($tempCollection as $product) {
                $id = $product->getId();
                if (! isset($ids[$id])) {
                    $ids[$id] = true;
                    $currentCollection[] = $product;
                }
            }
        }
        return $currentCollection;
    }
}
