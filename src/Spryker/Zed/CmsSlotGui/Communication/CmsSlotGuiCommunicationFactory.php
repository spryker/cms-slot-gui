<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\CmsSlotGui\Communication;

use Orm\Zed\CmsSlot\Persistence\SpyCmsSlotQuery;
use Orm\Zed\CmsSlot\Persistence\SpyCmsSlotTemplateQuery;
use Spryker\Zed\CmsSlotGui\CmsSlotGuiDependencyProvider;
use Spryker\Zed\CmsSlotGui\Communication\Form\ToggleActiveCmsSlotForm;
use Spryker\Zed\CmsSlotGui\Communication\Table\SlotTable;
use Spryker\Zed\CmsSlotGui\Communication\Table\TemplateTable;
use Spryker\Zed\CmsSlotGui\Dependency\Facade\CmsSlotGuiToCmsSlotFacadeInterface;
use Spryker\Zed\CmsSlotGui\Dependency\Facade\CmsSlotGuiToTranslatorFacadeInterface;
use Spryker\Zed\Kernel\Communication\AbstractCommunicationFactory;
use Symfony\Component\Form\FormInterface;

class CmsSlotGuiCommunicationFactory extends AbstractCommunicationFactory
{
    public function createTemplateListTable(): TemplateTable
    {
        return new TemplateTable(
            $this->getCmsSlotTemplateQuery(),
        );
    }

    public function createSlotListTable(?int $idSlotTemplate = null): SlotTable
    {
        return new SlotTable(
            $this->getCmsSlotQuery(),
            $this->getTranslatorFacade(),
            $idSlotTemplate,
        );
    }

    public function createToggleActiveCmsSlotForm(): FormInterface
    {
        return $this->getFormFactory()->create(ToggleActiveCmsSlotForm::class);
    }

    public function getCmsSlotFacade(): CmsSlotGuiToCmsSlotFacadeInterface
    {
        return $this->getProvidedDependency(CmsSlotGuiDependencyProvider::FACADE_CMS_SLOT);
    }

    public function getCmsSlotTemplateQuery(): SpyCmsSlotTemplateQuery
    {
        return $this->getProvidedDependency(CmsSlotGuiDependencyProvider::PROPER_QUERY_CMS_SLOT_TEMPLATE);
    }

    public function getCmsSlotQuery(): SpyCmsSlotQuery
    {
        return $this->getProvidedDependency(CmsSlotGuiDependencyProvider::PROPER_QUERY_CMS_SLOT);
    }

    public function getTranslatorFacade(): CmsSlotGuiToTranslatorFacadeInterface
    {
        return $this->getProvidedDependency(CmsSlotGuiDependencyProvider::FACADE_TRANSLATOR);
    }
}
