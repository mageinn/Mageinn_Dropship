define([
    'underscore',
    'Magento_Ui/js/form/element/abstract',
    'uiRegistry',
    'domReady!'
], function (_, AbstractField, uiRegistry) {
    "use strict";

    /**
     * Return the UI Component
     */
    return AbstractField.extend({

        /**
         * Initialize field component, and store a reference to the dependent fields.
         */
        initialize: function () {
            this._super();

            uiRegistry.promise('index = url_title').done(_.bind(function () {
                this.setUrlRequired();
            }, this));
        },

        /**
         * Change currently selected option
         */
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
