/**
 * Copyright (c) 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

'use strict';

var masterDetailTable = require('ZedGuiModules/libs/table/master-detail-table');
var tableAccess = require('ZedGuiModules/libs/table/table-access');

var SlotTable = function (options) {
    var _self = this;
    this.slotTableSelector = '';
    this.slotTable = null;
    this.handle = null;
    this.dataTableInit = false;
    this.dataTableInitCallback = function () {};

    $.extend(this, options);

    this.init = function () {
        _self.slotTable = $(_self.slotTableSelector);

        if (!_self.slotTable.length) {
            return;
        }

        _self.activationHandler();
        document.addEventListener(masterDetailTable.MASTER_DETAIL_TABLE_SELECTED, _self.tableLoadHandler);

        tableAccess.requestTable(_self.slotTable[0], function (handle) {
            _self.handle = handle;
        });
    };

    this.tableLoadHandler = function (event) {
        if (_self.dataTableInit || event.detail.detailTableId !== _self.slotTable.attr('id')) {
            return;
        }

        _self.dataTableInit = true;
        _self.dataTableInitCallback();
    };

    this.activationHandler = function () {
        _self.slotTable.on('click', '.js-slot-activation', function (event) {
            event.preventDefault();

            var $that = $(this);
            var $form = $that.closest('form');

            if ($that.data('processing') === true) {
                return;
            }

            $that.data('processing', true);

            $.ajax({
                url: $form[0].action,
                type: 'POST',
                data: $form.serialize(),
                success: function (response) {
                    // The flag only guards against a second submit while this one is in flight, so it is
                    // cleared as soon as the server has answered - waiting for the reload instead would
                    // leave the button dead for good whenever that reload fails.
                    $that.data('processing', false);

                    if (response.success) {
                        _self.refreshSlotTable();

                        return;
                    }

                    _self.showError(response.message);
                },
                error: function (response) {
                    $that.data('processing', false);
                    _self.showError(response.status + ' ' + response.statusText);
                },
            });
        });
    };

    /**
     * @returns {Promise} Resolved once the rows have been loaded again, on whichever page the table is on.
     */
    this.refreshSlotTable = function () {
        if (!_self.handle) {
            return Promise.resolve();
        }

        return _self.handle.refresh();
    };

    /**
     * @param {string} message - Message of the server.
     */
    this.showError = function (message) {
        window.sweetAlert({
            title: 'Error',
            text: message,
            type: 'error',
        });
    };
};

module.exports = SlotTable;
