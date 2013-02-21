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
 * @category    Magento
 * @package     Magento_CatalogSearch
 * @subpackage  integration_tests
 * @copyright   Copyright (c) 2013 X.commerce, Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Mage_CatalogSearch_Helper_DataTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_CatalogSearch_Helper_Data
     */
    protected $_helper;

    protected function setUp()
    {
        $this->_helper = Mage::helper('Mage_CatalogSearch_Helper_Data');
    }

    protected function tearDown()
    {
        $this->_helper = null;
    }

    public function testGetResultUrl()
    {
        $this->assertStringEndsWith('/catalogsearch/result/', $this->_helper->getResultUrl());

        $query = uniqid();
        $this->assertStringEndsWith("/catalogsearch/result/?q={$query}", $this->_helper->getResultUrl($query));
    }

    public function testGetAdvancedSearchUrl()
    {
        $this->assertStringEndsWith('/catalogsearch/advanced/', $this->_helper->getAdvancedSearchUrl());
    }

    public function testCheckNotesResult()
    {
        $this->assertInstanceOf('Mage_CatalogSearch_Helper_Data', $this->_helper->checkNotes());
    }

    /**
     * @magentoConfigFixture current_store catalog/search/search_type 1
     * @magentoConfigFixture current_store catalog/search/max_query_words 3
     */
    public function testCheckNotesEscapesHtmlWhenQueryIsCut()
    {
        /** @var $mock Mage_CatalogSearch_Helper_Data */
        $mock = $this->getMock(
            'Mage_CatalogSearch_Helper_Data',
            array('getQueryText'), array(Mage::getObjectManager()->get('Mage_Core_Model_Translate'))
        );
        $mock->expects($this->any())
            ->method('getQueryText')
            ->will($this->returnValue('five <words> here <being> tested'));

        $mock->checkNotes();

        $notes = implode($mock->getNoteMessages());
        $this->assertContains('&lt;being&gt;', $notes);
        $this->assertNotContains('<being>', $notes);
    }
}