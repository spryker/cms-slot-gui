<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerTest\Zed\CmsSlotGui\Communication\Table;

use Codeception\Test\Unit;
use Orm\Zed\CmsSlot\Persistence\SpyCmsSlotQuery;
use Orm\Zed\CmsSlot\Persistence\SpyCmsSlotTemplateQuery;
use Spryker\Zed\CmsSlotGui\Communication\Controller\SlotListController;
use Spryker\Zed\CmsSlotGui\Communication\Table\SlotTable;
use Spryker\Zed\CmsSlotGui\Dependency\Facade\CmsSlotGuiToTranslatorFacadeInterface;
use Spryker\Zed\Gui\Communication\Table\AbstractTable;

/**
 * Auto-generated group annotations
 *
 * @group SprykerTest
 * @group Zed
 * @group CmsSlotGui
 * @group Communication
 * @group Table
 * @group MasterDetailTableConfigurationTest
 * Add your own group annotations below this line
 */
class MasterDetailTableConfigurationTest extends Unit
{
    /**
     * @var string
     */
    protected const ATTRIBUTE_MASTER_DETAIL = 'data-master-detail';

    /**
     * @var \SprykerTest\Zed\CmsSlotGui\CmsSlotGuiCommunicationTester
     */
    protected $tester;

    /**
     * The slot table is rendered by the template list page, so it can neither inherit the ajax URL from
     * the current request nor be loaded before a template is selected.
     */
    public function testSlotTableIsPickedUpByTheTableLibraryWithItsOwnAjaxUrl(): void
    {
        // Arrange
        $slotTableMock = new SlotTableMock(
            SpyCmsSlotQuery::create(),
            $this->createMock(CmsSlotGuiToTranslatorFacadeInterface::class),
        );

        // Act
        $config = $slotTableMock->getPreparedConfig();

        // Assert
        $this->assertSame(SlotTable::TABLE_IDENTIFIER, $config['tableId']);
        $this->assertStringContainsString(AbstractTable::TABLE_CLASS, $config['class']);
        $this->assertStringContainsString(SlotTable::TABLE_CLASS, $config['class']);
        $this->assertSame('/cms-slot-gui/slot-list/', $config['baseUrl']);
        $this->assertSame('table', $config['url']);
        $this->assertSame(0, $config['tableAttributes']['data-defer-loading']);
    }

    public function testTemplateTableConfiguresMasterDetailFeatureForTheSlotTable(): void
    {
        // Arrange
        $templateTableMock = new TemplateTableMock(SpyCmsSlotTemplateQuery::create());

        // Act
        $config = $templateTableMock->getPreparedConfig();

        // Assert
        $masterDetailConfig = $config['tableAttributes'][static::ATTRIBUTE_MASTER_DETAIL];
        $this->assertSame('#' . SlotTable::TABLE_IDENTIFIER, $masterDetailConfig['detailTableSelector']);
        $this->assertSame(SlotListController::PARAM_ID_CMS_SLOT_TEMPLATE, $masterDetailConfig['param']);
        $this->assertArrayHasKey($masterDetailConfig['colId'], $config['header']);
        $this->assertArrayHasKey($masterDetailConfig['colLabel'], $config['header']);
    }
}
