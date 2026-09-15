<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerTest\Zed\CmsSlotGui\Communication\Table;

use Spryker\Zed\CmsSlotGui\Communication\Table\SlotTable;
use Symfony\Component\HttpFoundation\Request;

class SlotTableMock extends SlotTable
{
    /**
     * @return $this
     */
    protected function init()
    {
        $this->request = new Request();
        $config = $this->newTableConfiguration();
        $config->setPageLength($this->getLimit());
        $config = $this->configure($config);
        $this->setConfiguration($config);

        return $this;
    }

    /**
     * @return array<string, mixed>
     */
    public function getPreparedConfig(): array
    {
        return $this->init()->prepareConfig();
    }
}
