<?php
namespace Elsnertech\HomeCategoryProduct\Block;

use Magento\Catalog\Block\Product\Context;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Sales\Model\ResourceModel\Report\Bestsellers\CollectionFactory as BestSellersCollectionFactory;
use Magento\Framework\View\Element\Template;
use Magento\Store\Model\StoreManagerInterface;

class BestSellerProducts extends \Magento\Catalog\Block\Product\ListProduct
{
    protected $_bestSellersCollectionFactory;
    protected $_productCollectionFactory;
    protected $_storeManager;
    protected $urlHelper;

    public function __construct(
        Context $context,
        CollectionFactory $productCollectionFactory,
        StoreManagerInterface $storeManager,
        BestSellersCollectionFactory $bestSellersCollectionFactory,
        \Magento\Framework\Url\Helper\Data $urlHelper
    ) {
        $this->_bestSellersCollectionFactory = $bestSellersCollectionFactory;
        $this->_storeManager = $storeManager;
        $this->_productCollectionFactory =$productCollectionFactory;
        $this->urlHelper = $urlHelper;
        parent::__construct($context);
    }
    /**
     * get collection of best-seller products
     * @return mixed
     */
    public function getProductCollection()
    {
        $productIds = [];
        $bestSellers = $this->_bestSellersCollectionFactory->create()
            ->setPeriod('month');
        foreach ($bestSellers as $product) {
            $productIds[] = $product->getProductId();
        }
        $collection = $this->_productCollectionFactory->create()->addIdFilter($productIds);
        $collection->addMinimalPrice()
            ->addFinalPrice()
            ->addTaxPercents()
            ->addAttributeToSelect('*')
            ->addStoreFilter($this->getStoreId())
            ->setPageSize(count($productIds));
        return $collection;
    }

     public function getStoreId(){
        return $this->_storeManager->getStore()->getId();
    }

    // public function getAddToCartPostParams(\Magento\Catalog\Model\Product $product)
    // {
    //     $url = $this->getAddToCartUrl($product);
    //     return [
    //         'action' => $url,
    //         'data' => [
    //             'product' => $product->getEntityId(),
    //             \Magento\Framework\App\Action\Action::PARAM_NAME_URL_ENCODED =>
    //                 $this->urlHelper->getEncodedUrl($url),
    //         ]
    //     ];
    // }
}