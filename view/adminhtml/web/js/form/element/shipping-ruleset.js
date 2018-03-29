require([
    'jquery'
], function ($) {
    $(window).load(function () {
        $(document).on('change',"[name='product[vendor_id]']",function () {
            reloadShippingRuleset();
        });
    });

    /**
     * Hides loader.
     */
    function hideLoader()
    {
        $('.admin__form-loading-mask').hide();
    }

    /**
     * Shows loader.
     */
    function showLoader()
    {
        $('.admin__form-loading-mask').show();
    }

    /**
     * Build ajax URL
     *
     * @returns {string}
     */
    function getFilterUrl()
    {
        var adminURL = BASE_URL.substr(0, BASE_URL.lastIndexOf('/admin'));
        return adminURL + "/admin/sales/shippingRates/filter";
    }

    /**
     * Load shipping ruleset values and populate attribute
     *
     * @returns {null|void}
     */
    function reloadShippingRuleset()
    {
        var vendor = getVendor();
        if (!vendor) {
            return null;
        }

        var shippingRulesetElem = $("select[name='product[shipping_ruleset]']");

        showLoader();

        $.ajax({
            url: getFilterUrl(),
            type: 'GET',
            data: {vendor: vendor},
            context: document.body
        }).done(function (result) {
            if (result.length) {
                shippingRulesetElem.empty();

                var option;
                for (var i in result) {
                    option = $('<option></option>').attr("value", result[i].value).text(result[i].label);
                    shippingRulesetElem.append(option);
                }
            }

            hideLoader();
        });
    }

    /**
     * Get vendor attribute value
     *
     * @returns {string|null}
     */
    function getVendor()
    {
        return $("select[name='product[vendor_id]']").val();
    }
});
