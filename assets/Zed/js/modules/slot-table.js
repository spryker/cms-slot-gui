/**
 * Copyright (c) 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

'use strict';

var dataTable = require('ZedGuiModules/libs/data-table');

var SlotTable = function (options) {
    var _self = this;
    this.ajaxBaseUrl = '';
    this.paramIdCmsSlotTemplate = '';
    this.ownershipColumnId = '';
    this.slotTableClass = '';
    this.slotTable = {};

    this.init = function () {
        this.slotTable = $(this.slotTableClass);
    };

    this.load = function (rowIndex) {
        var ajaxUrl = this.ajaxBaseUrl + '?' + this.paramIdCmsSlotTemplate + '=' + rowIndex;
        this.slotTable.data('ajax', ajaxUrl);

        this.slotTable.DataTable({
            destroy: true,
            ajax: {
                cache: false
            },
            lengthChange: false,
            autoWidth: false,
            language: dataTable.defaultConfiguration.language,
            drawCallback: function() {
                var api = this.api();

                _self.displayOwnershipColumn(api);
                _self.activationHandler();
            },
        });
    };

    this.displayOwnershipColumn = function (api) {
        var ownershipColumnIndex = null;
        var ownerships = [];
        var ownershipColumn = null;

        api.columns().header().each(function (element, index) {
            if ($(element).attr("id") === _self.ownershipColumnId) {
                ownershipColumnIndex = index;
            }
        });

        if (ownershipColumnIndex !== null) {
            ownershipColumn = api.table().columns(ownershipColumnIndex);
            ownershipColumn.visible(true);
            var ownershipsArray = ownershipColumn.data()[0];

            ownerships = ownershipsArray.filter(function (value, index, self) {
                return self.indexOf(value) === index;
            });

            if (ownerships.length === 1) {
                ownershipColumn.visible(false);
            }
        }
    };

    this.activationHandler = function () {
        $('.js-slot-activation').on('click', function(event) {
            event.preventDefault();
            var url = $(this).attr('href');

            $.get(url, function (response) {
                if (response.success) {
                    _self.slotTable.DataTable().ajax.reload(null, false);
                }
            });

            return false;
        });
    }
};

/**
 * Open public methods
 */
module.exports = SlotTable;
