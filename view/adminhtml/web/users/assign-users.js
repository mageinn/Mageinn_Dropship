/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/* global $, $H */

define([
    'mage/adminhtml/grid'
], function () {
    'use strict';

    return function (config) {
        var selectedUsers = config.selectedUsers,
            vendorUsers = $H(selectedUsers),
            gridJsObject = window[config.gridJsObjectName],
            tabIndex = 1000;

        $('associated_user').value = Object.toJSON(vendorUsers);

        /**
         * Register Category Product
         *
         * @param {Object} grid
         * @param {Object} element
         * @param {Boolean} checked
         */
        function registerVendorUser(grid, element, checked)
        {
            if (checked) {
                if (element.positionElement) {
                    element.positionElement.disabled = false;
                    vendorUsers.set(element.value, element.positionElement.value);
                } else {
                    vendorUsers.set(element.value, element.value);
                }
            } else {
                if (element.positionElement) {
                    element.positionElement.disabled = true;
                }
                vendorUsers.unset(element.value);
            }

            $('associated_user').value = Object.toJSON(vendorUsers);

            grid.reloadParams = {
                'selected_users[]': vendorUsers.keys()
            };
        }

        /**
         * Click on product row
         *
         * @param {Object} grid
         * @param {String} event
         */
        function vendorUserRowClick(grid, event)
        {
            var trElement = Event.findElement(event, 'tr'),
                isInput = Event.element(event).tagName === 'INPUT',
                checked = false,
                checkbox = null;

            if (trElement) {
                checkbox = Element.getElementsBySelector(trElement, 'input');

                if (checkbox[0]) {
                    checked = isInput ? checkbox[0].checked : !checkbox[0].checked;
                    gridJsObject.setCheckboxChecked(checkbox[0], checked);
                }
            }
        }

        /**
         * Change product position
         *
         * @param {String} event
         */
        function positionChange(event)
        {
            var element = Event.element(event);

            if (element && element.checkboxElement && element.checkboxElement.checked) {
                vendorUsers.set(element.checkboxElement.value, element.value);
                $('associated_user').value = Object.toJSON(vendorUsers);
            }
        }

        /**
         * Initialize category product row
         *
         * @param {Object} grid
         * @param {String} row
         */
        function vendorUserRowInit(grid, row)
        {
            var checkbox = $(row).getElementsByClassName('checkbox')[0],
                position = $(row).getElementsByClassName('input-text')[0];

            if (checkbox && position) {
                checkbox.positionElement = position;
                position.checkboxElement = checkbox;
                position.disabled = !checkbox.checked;
                position.tabIndex = tabIndex++;
                Event.observe(position, 'keyup', positionChange);
            }
        }

        gridJsObject.rowClickCallback = vendorUserRowClick;
        gridJsObject.initRowCallback = positionChange;
        gridJsObject.checkboxCheckCallback = registerVendorUser;

        if (gridJsObject.rows) {
            gridJsObject.rows.each(function (row) {
                vendorUserRowInit(gridJsObject, row);
            });
        }
    };
});
