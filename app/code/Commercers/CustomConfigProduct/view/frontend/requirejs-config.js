var config = {
    config: {
        mixins: {
            'Magento_ConfigurableProduct/js/configurable': {
                'Commercers_CustomConfigProduct/js/model/attswitch': true
            },
            'Magento_Swatches/js/swatch-renderer': {
                'Commercers_CustomConfigProduct/js/model/swatch-attswitch': true
            }
        }
    }
};