<?php
namespace Commercers\CustomConfigProduct\Plugin\ConfigurableProduct\Block\Product\View\Type;
use Magento\Framework\Json\EncoderInterface;
use Magento\Framework\Json\DecoderInterface;
class Configurable
{
    protected $jsonEncoder;
    protected $jsonDecoder;
    protected $_productRepository;
    public function __construct(
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        EncoderInterface $jsonEncoder,
        DecoderInterface $jsonDecoder
    ) {
        $this->jsonDecoder = $jsonDecoder;
        $this->jsonEncoder = $jsonEncoder;
        $this->_productRepository = $productRepository;
    }
    public function afterGetJsonConfig(\Magento\ConfigurableProduct\Block\Product\View\Type\Configurable $subject, $result) {
        $jsonResult = json_decode($result, true);
        foreach ($subject->getAllowProducts() as $simpleProduct) {
            $id = $simpleProduct->getId();
            foreach($simpleProduct->getAttributes() as $attribute) {
                if(($attribute->getIsVisible() && $attribute->getIsVisibleOnFront()) || in_array($attribute->getAttributeCode(), ['sku','name','description','short_description']) ) { // <= Here you can put any attribute you want to see dynamic
                $code = $attribute->getAttributeCode();
                    $value = (string)$attribute->getFrontend()->getValue($simpleProduct);
                    $jsonResult['dynamic'][$code][$id] = [
                        'value' => $value
                    ];
                }
            }
        }
        $result = json_encode($jsonResult);
        return $result;
    }

    public function getProductById($id)
    {
        return $this->_productRepository->getById($id);
    }

    public function aroundGetJsonConfig(
        \Magento\ConfigurableProduct\Block\Product\View\Type\Configurable $subject,
        \Closure $proceed
    )
    {
        $sdescription = [];
        $config = $proceed();
        $config = $this->jsonDecoder->decode($config);
        foreach ($subject->getAllowProducts() as $prod) {
            $id = $prod->getId();
            $product = $this->getProductById($id);
            $sdescription[$id] = $product->getDescription();
        }
        $config['sdescription'] = $sdescription;

        return $this->jsonEncoder->encode($config);
    }
}