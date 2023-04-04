<?php
/**
 * aheadWorks Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://ecommerce.aheadworks.com/AW-LICENSE.txt
 *
 * =================================================================
 *                 MAGENTO EDITION USAGE NOTICE
 * =================================================================
 * This software is designed to work with Magento community edition and
 * its use on an edition other than specified is prohibited. aheadWorks does not
 * provide extension support in case of incorrect edition use.
 * =================================================================
 *
 * @category   AW
 * @package    AW_AdvancedReviews
 * @version    2.4.0
 * @copyright  Copyright (c) 2010-2012 aheadWorks Co. (http://www.aheadworks.com)
 * @license    http://ecommerce.aheadworks.com/AW-LICENSE.txt
 */


class AW_AdvancedReviews_Block_Ajax_Reviews extends AW_AdvancedReviews_Block_Product_Reviews
{
    protected $_collection = null;

    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('advancedreviews/ajax/reviews.phtml');
    }

    public function setCollection($collection)
    {
        $this->_collection = $collection;
        return $this;
    }

    public function getCollection()
    {
        if (!$this->_collection) {
            $limit = Mage::helper('advancedreviews')->confProductPageReviewsCount();
            $collection = Mage::getResourceModel('advancedreviews/review')->getFilteredReviews(
                $this->getRequest()->getParam('id'), null, 1, $limit, null, 'DESC',
                $this->getRequest()->getParam('customerId')
            );
            $this->_collection = $collection;
        }
        return $this->_collection;
    }

    public function getRatingVotes($id)
    {
        return $votesCollection = Mage::getModel('rating/rating_option_vote')
            ->getResourceCollection()
            ->setReviewFilter($id)
            ->setStoreFilter(Mage::app()->getStore()->getId())
            ->addRatingInfo(Mage::app()->getStore()->getId())
            ->load();
    }

    public function avgRatingVotes($id)
    {
        $votesCollection = $this->getRatingVotes($id);
        $avgRating = 0;
        if (count($votesCollection)) {
            $totalRating = 0;
            foreach ($votesCollection as $vote) {
                $totalRating += $vote->getPercent();
            }
            $avgRating = $totalRating / count($votesCollection);
        }
        return $avgRating;
    }

    public function getGoogleMarkupBlock()
    {
        $block = $this->getLayout()->createBlock('advancedreviews/ajax_googlemarkup');
        if ($product = $this->getProduct()) {
            $block->setProduct($product);
        } else {
            return false;
        }

        return $block;
    }

    public function getGoogleMarkupBlockHtml()
    {
        $html = '';
        if ($block = $this->getGoogleMarkupBlock()) {
            $html = $block->toHtml();
        }

        return $html;
    }
}