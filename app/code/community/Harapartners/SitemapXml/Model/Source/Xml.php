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
class Harapartners_SitemapXml_Model_Source_Xml
{
    const XML_PATH_VALIDATE_URL = 'sitemap/language/validate_url';
    
    protected $_links;
    protected $_validator;

    public function getLinks()
    {
        if (empty($this->_links)) {
            $collection = Mage::getModel('sitemapxml/language')->getCollection();
            
            $links[] = $this->_getXDefualtLink();
            foreach ($collection as $language) {
                $storeId = $language->getStoreViewId();
                $langCode = $language->getLanguageCode();
                $href = Mage::app()->getStore($storeId)->getBaseUrl(Mage_Core_Model_Store::URL_TYPE_LINK, false);
                $links[] = array(
                    'lang_code' => $langCode , 
                    'href' => $href , 
                    'store_id' => $storeId
                );
            }
            
            $this->_links = $links;
        }
        
        return $this->_links;
    }

    protected function _getXDefualtLink()
    {
        $xUrl = Mage::getStoreConfig('sitemap/language/x_default_url');
        if (empty($xUrl)) {
            $xUrl = Mage::getBaseUrl();
        }
        
        return array(
            'lang_code' => 'x-default' , 
            'href' => $xUrl
        );
    }

    protected function _getValidator()
    {
        if (! isset($validator)) {
            $this->_validator = Mage::getModel('sitemapxml/controller_validator');
        }
        
        return $this->_validator;
    }

    public function getUrlBlock($baseUrl, $item, $itemType, $prettyPrint = false)
    {
        $conditionalLineEnding = '';
        $conditionalTab = '';
        if ($prettyPrint) {
            $conditionalLineEnding = PHP_EOL;
            $conditionalTab = "\t";
        }
        
        $links = $this->getLinks();
        
        $string = '';
        //$string = '<url>' . $conditionalLineEnding;
        //$string = "$conditionalTab <loc>{$baseUrl}</loc>" . $conditionalLineEnding;
        $suffixPaths = $this->_getSuffixPathsById($itemType, $item->getId());
        foreach ($links as $link) {
            if ($link['lang_code'] != 'x-default') {
                if (! $this->_isPathEnabled($item->getId(), $link['store_id'], $itemType)) {
                    continue;
                }
            }
            
            $suffixpath = ! empty($suffixPaths[$link['store_id']]) ? $suffixPaths[$link['store_id']] : $item->getUrl();
            $readyLink = $link['href'] . $suffixpath;
            if (! $this->_isLinkValid($readyLink)) {
                continue;
            }
            
            $string .= $conditionalTab . "<xhtml:link rel=\"alternate\" hreflang=\"{$link['lang_code']}\" href=\"{$readyLink}\" />" . $conditionalLineEnding;
        }
        
        return $string;
    }

    protected function _getSuffixPathsById($type, $id)
    {
        $collection = Mage::getModel('core/url_rewrite')->getCollection();
        switch ($type) {
            case 'product':
                $collection->filterAllByProductId($id);
                break;
            case 'category':
                $this->_addCollectionFilterToCollection($collection, $id);
                break;
            default:
                return array();
        }
        
        $paths = array();
        foreach ($collection as $urlRewrite) {
            // TODO only Newest ??
            $storeId = $urlRewrite->getStoreId();
            $path = $urlRewrite->getRequestPath();
            $paths[$storeId] = $path;
        }
        
        return $paths;
    }

    protected function _isPathEnabled($id, $storeId, $type)
    {
        //return true;
        if ($type == 'category') {
            $attributeType = 'catalog/category';
            $attributeCode = 'is_active';
            $attributeValues = array(
                1 // enabled
            );
            
            if (! $this->_isCategoryInStoreView($id, $storeId)) {
                // Special Check for Category
                return false;
            }
        } elseif ($type == 'product') {
            $attributeType = 'catalog/product';
            $attributeCode = 'status';
            $attributeValues = array(
                1 // enabled
            );
            
            if (! $this->_isProductInStoreView($id, $storeId)) {
                // Special Check for Category
                return false;
            }
        }
        
        /* $collection Mage_Eav_Model_Entity_Collection */
        $collection = Mage::getModel($attributeType)->getCollection();
        $collection->addFieldToFilter('entity_id', $id);
        $collection->setStore($storeId);
        $collection->addFieldToFilter($attributeCode, array(
            in => ($attributeValues)
        ));
        
        $firstValue = $collection->getFirstItem();
        if ($firstValue->getId()) {
            return true;
        }
        
        return false;
    }

    // == Special Validation == //
    protected function _addCollectionFilterToCollection($collection, $id)
    {
        $collection->getSelect()->where('id_path LIKE ?', "category/{$id}");
        return $this;
    }

    protected function _isCategoryInStoreView($id, $storeId)
    {
        $catsToStore = $this->_getCatsToStore();
        $storeIds = $catsToStore[$id];
        return ! empty($storeIds[$storeId]);
    }

    protected function _getCatsToStore()
    {
        return Mage::helper('sitemapxml/store_catalog')->getCategoriesToStore();
    }

    protected function _isProductInStoreView($id, $storeId)
    {
        $productsToStore = $this->_getProductsToStore();
        $storeIds = $productsToStore[$id];
        return ! empty($storeIds[$storeId]);
    }

    protected function _getProductsToStore()
    {
        return Mage::helper('sitemapxml/store_catalog')->getProductsToStore();
    }

    // == END Special Validation == //
    //    protected function _joinEnabledFilter($collection, $type){
    //        switch($type){
    //            case 'product':
    //                $collection->getSelect()->join(array('p_status'=>'catalog_product_entity_int'),'p_status.entity_id=core_url.product_id AND p_status.store_id = core_url.store_id');
    //                $collection->addFieldToFilter('p_status.value',2);
    //                break;
    //            case 'category':
    //                break;
    //            default:
    //        }
    //    }
    

    protected function _isLinkValid($link)
    {
        if (! Mage::getStoreConfig(self::XML_PATH_VALIDATE_URL)) {
            // Are we validating URLs?
            return true;
        }
        
        $url = $link;
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_NOBODY, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        
        curl_exec($ch);
        $retcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if (200 == $retcode) {
            // All's well
            return true;
        } else {
            // not so much
            return false;
        }
        
        return true;
    }
}
