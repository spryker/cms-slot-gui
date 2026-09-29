/**
 * Copyright (c) 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

'use strict';

var SlotTable = require('./slot-table');

$(document).ready(function () {
    var slotTable = new SlotTable({
        slotTableSelector: '.js-cms-slot-list-table',
    });

    slotTable.init();
    window.CmsSlotGui_SlotTable = slotTable;
});
