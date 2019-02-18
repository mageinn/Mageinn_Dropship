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
    'mage/adminhtml/grid'
], function () {
    'use strict';

    return function (config) {
        var selectedUsers = config.selectedUsers,
            vendorUsers = $H(selectedUsers),
            gridJsObject = window[config.gridJsObjectName],
            tabIndex = 1000;

        $('associated_user').value = Object.toJSON(vendorUsers);

        function registerVendorUser(grid, element, checked)
        {
            if (checked && element.value !== 'on') {
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


        function vendorUserRowClick(grid, event)
        {
            var trElement = Event.findElement(event, 'tr'),
                isInput = Event.element(event).tagName === 'INPUT',
                checked = false,
                checkbox = null;

            if (trElement) {
                checkbox = Element.getElementsBySelector(trElement, 'input');
                if (checkbox[0] && checkbox[0].value !== "on") {
                    checked = isInput ? checkbox[0].checked : !checkbox[0].checked;
                    gridJsObject.setCheckboxChecked(checkbox[0], checked);
                }
            }
        }


        function positionChange(event)
        {
            var element = Event.element(event);

            if (element && element.checkboxElement && element.checkboxElement.checked) {
                vendorUsers.set(element.checkboxElement.value, element.value);
                $('associated_user').value = Object.toJSON(vendorUsers);
            }
        }


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
