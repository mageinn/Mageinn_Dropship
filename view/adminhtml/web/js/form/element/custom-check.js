/**
 * Mageinn
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageinn.com license that is
 * available through the world-wide-web at this URL:
 * https://mageinn.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 */
define([
    'underscore',
    'Magento_Ui/js/form/element/abstract',
    'uiRegistry',
    'domReady!'
], function (_, AbstractField, uiRegistry) {
    "use strict";


    return AbstractField.extend({


        initialize: function () {
            this._super();

            uiRegistry.promise('index = url_title').done(_.bind(function () {
                this.setUrlRequired();
            }, this));
        },


        onUpdate: function () {
            this.setUrlRequired();

            return this._super();
        },

        setUrlRequired: function () {
            var urlTitle = uiRegistry.get('index = url_title');
            if (urlTitle.value()) {
                uiRegistry.get('index = url', function (input) {
                    input.validation['required-entry'] = true;
                    input.required(true);
                });
            } else {
                uiRegistry.get('index = url', function (input) {
                    input.validation['required-entry'] = false;
                    input.required(false);
                });
            }
        }
    });
});
