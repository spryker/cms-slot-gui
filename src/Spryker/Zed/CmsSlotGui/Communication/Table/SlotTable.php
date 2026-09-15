<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\CmsSlotGui\Communication\Table;

use Orm\Zed\CmsSlot\Persistence\Map\SpyCmsSlotTableMap;
use Orm\Zed\CmsSlot\Persistence\SpyCmsSlotQuery;
use Spryker\Service\UtilText\Model\Url\Url;
use Spryker\Zed\CmsSlotGui\Communication\Controller\ActivateSlotController;
use Spryker\Zed\CmsSlotGui\Communication\Form\ToggleActiveCmsSlotForm;
use Spryker\Zed\CmsSlotGui\Dependency\Facade\CmsSlotGuiToTranslatorFacadeInterface;
use Spryker\Zed\Gui\Communication\Table\AbstractTable;
use Spryker\Zed\Gui\Communication\Table\TableConfiguration;

class SlotTable extends AbstractTable
{
    /**
     * @var string
     */
    public const TABLE_CLASS = 'js-cms-slot-list-table';

    public const string TABLE_IDENTIFIER = 'slot-list-table';

    /**
     * @uses \Spryker\Zed\CmsSlotGui\Communication\Controller\SlotListController::tableAction()
     */
    protected const string BASE_URL = '/cms-slot-gui/slot-list/';

    protected const COL_ID_CMS_SLOT = SpyCmsSlotTableMap::COL_ID_CMS_SLOT;

    protected const COL_NAME = SpyCmsSlotTableMap::COL_NAME;

    protected const COL_DESCRIPTION = SpyCmsSlotTableMap::COL_DESCRIPTION;

    protected const COL_CONTENT_PROVIDER = SpyCmsSlotTableMap::COL_CONTENT_PROVIDER_TYPE;

    protected const COL_STATUS = SpyCmsSlotTableMap::COL_IS_ACTIVE;

    /**
     * @var string
     */
    protected const COL_ACTIONS = 'actions';

    /**
     * @var string
     */
    protected const VALUE_COL_ID_CMS_SLOT = 'ID';

    /**
     * @var string
     */
    protected const VALUE_COL_NAME = 'Name';

    /**
     * @var string
     */
    protected const VALUE_COL_DESCRIPTION = 'Description';

    /**
     * @var string
     */
    protected const VALUE_COL_CONTENT_PROVIDER = 'Content Provider';

    /**
     * @var string
     */
    protected const VALUE_COL_STATUS = 'Status';

    /**
     * @var string
     */
    protected const VALUE_COL_ACTIONS = 'Actions';

    /**
     * @var string
     */
    protected const URL_ACTIVATE_BUTTON = '/cms-slot-gui/activate-slot/activate';

    /**
     * @var string
     */
    protected const URL_DEACTIVATE_BUTTON = '/cms-slot-gui/activate-slot/deactivate';

    /**
     * @var string
     */
    protected const COL_NAME_WRAPPER = '<span data-content-provider="%s"/>%s</span>';

    /**
     * @var int|null
     */
    protected $contentProviderTypesNumber;

    /**
     * @var \Orm\Zed\CmsSlot\Persistence\SpyCmsSlotQuery
     */
    protected $cmsSlotQuery;

    /**
     * @var \Spryker\Zed\CmsSlotGui\Dependency\Facade\CmsSlotGuiToTranslatorFacadeInterface
     */
    protected $translatorFacade;

    /**
     * @var int|null
     */
    protected $idCmsSlotTemplate;

    public function __construct(
        SpyCmsSlotQuery $cmsSlotQuery,
        CmsSlotGuiToTranslatorFacadeInterface $translatorFacade,
        ?int $idCmsSlotTemplate = null
    ) {
        $this->cmsSlotQuery = $cmsSlotQuery;
        $this->translatorFacade = $translatorFacade;
        $this->idCmsSlotTemplate = $idCmsSlotTemplate;
    }

    /**
     * @param \Spryker\Zed\Gui\Communication\Table\TableConfiguration $config
     *
     * @return \Spryker\Zed\Gui\Communication\Table\TableConfiguration
     */
    protected function configure(TableConfiguration $config)
    {
        $this->setTableIdentifier(static::TABLE_IDENTIFIER);
        $this->tableClass .= ' ' . static::TABLE_CLASS;
        // The table is rendered by the template list page, so the ajax URL has to be set explicitly
        // instead of being derived from the module and controller of the current request.
        $this->baseUrl = static::BASE_URL;

        $config = $this->setHeader($config);
        $config = $this->setSortable($config);
        $config = $this->setSearchable($config);

        $config->setDefaultSortField(static::COL_ID_CMS_SLOT, TableConfiguration::SORT_ASC);

        $config->addRawColumn(static::COL_ACTIONS);
        $config->addRawColumn(static::COL_STATUS);
        $config->addRawColumn(static::COL_NAME);

        // The table is loaded by the master-detail feature of the template table as soon as a template is
        // selected, so the initial request would always run without a template ID and return no rows.
        $config->setTableAttributes([
            'data-defer-loading' => 0,
        ]);

        return $config;
    }

    protected function setHeader(TableConfiguration $config): TableConfiguration
    {
        $header = [
            static::COL_ID_CMS_SLOT => static::VALUE_COL_ID_CMS_SLOT,
            static::COL_NAME => static::VALUE_COL_NAME,
            static::COL_DESCRIPTION => static::VALUE_COL_DESCRIPTION,
            static::COL_CONTENT_PROVIDER => static::VALUE_COL_CONTENT_PROVIDER,
            static::COL_STATUS => static::VALUE_COL_STATUS,
            static::COL_ACTIONS => static::VALUE_COL_ACTIONS,
        ];

        if (!$this->isContentProviderColumnVisible()) {
            unset($header[static::COL_CONTENT_PROVIDER]);
        }

        $config->setHeader($header);

        return $config;
    }

    protected function setSortable(TableConfiguration $config): TableConfiguration
    {
        $sortable = [
            static::COL_ID_CMS_SLOT,
            static::COL_NAME,
            static::COL_STATUS,
        ];

        if ($this->isContentProviderColumnVisible()) {
            $sortable[] = static::COL_CONTENT_PROVIDER;
        }

        $config->setSortable($sortable);

        return $config;
    }

    protected function setSearchable(TableConfiguration $config): TableConfiguration
    {
        $searchable = [
            static::COL_ID_CMS_SLOT,
            static::COL_NAME,
            static::COL_DESCRIPTION,
        ];

        if ($this->isContentProviderColumnVisible()) {
            $searchable[] = static::COL_CONTENT_PROVIDER;
        }

        $config->setSearchable($searchable);

        return $config;
    }

    protected function isContentProviderColumnVisible(): bool
    {
        if ($this->contentProviderTypesNumber === null) {
            $this->contentProviderTypesNumber = $this->cmsSlotQuery
                ->select(static::COL_CONTENT_PROVIDER)
                ->distinct()
                ->count();
        }

        return $this->contentProviderTypesNumber > 1;
    }

    protected function prepareData(TableConfiguration $config): array
    {
        if (!$this->idCmsSlotTemplate) {
            return [];
        }

        $this->cmsSlotQuery
            ->clear()
            ->useSpyCmsSlotToCmsSlotTemplateQuery()
                ->filterByFkCmsSlotTemplate($this->idCmsSlotTemplate)
            ->endUse();

        $slotResults = $this->runQuery($this->cmsSlotQuery, $config);
        $results = [];

        foreach ($slotResults as $slot) {
            $results[] = [
                static::COL_ID_CMS_SLOT => $this->formatInt($slot[SpyCmsSlotTableMap::COL_ID_CMS_SLOT]),
                static::COL_NAME => $this->getName($slot),
                static::COL_DESCRIPTION => $slot[SpyCmsSlotTableMap::COL_DESCRIPTION],
                static::COL_CONTENT_PROVIDER => $this->getContentProvider($slot),
                static::COL_STATUS => $this->getStatus($slot),
                static::COL_ACTIONS => $this->buildLinks($slot),
            ];

            if (!$this->isContentProviderColumnVisible()) {
                unset($results[static::COL_CONTENT_PROVIDER]);
            }
        }

        return $results;
    }

    protected function getName(array $slot): string
    {
        return sprintf(
            static::COL_NAME_WRAPPER,
            $slot[static::COL_CONTENT_PROVIDER],
            $slot[SpyCmsSlotTableMap::COL_NAME],
        );
    }

    protected function buildLinks(array $slot): string
    {
        $statusToggleButton = $this->generateFormButton(
            $this->getUrlActivate($slot[SpyCmsSlotTableMap::COL_ID_CMS_SLOT]),
            'Activate',
            ToggleActiveCmsSlotForm::class,
            ['class' => 'btn-view js-slot-activation'],
        );

        if ($slot[static::COL_STATUS]) {
            $statusToggleButton = $this->generateFormButton(
                $this->getUrlDeactivate($slot[SpyCmsSlotTableMap::COL_ID_CMS_SLOT]),
                'Deactivate',
                ToggleActiveCmsSlotForm::class,
                ['class' => 'btn-danger js-slot-activation'],
            );
        }

        $buttons = [
            $statusToggleButton,
        ];

        return implode(' ', $buttons);
    }

    protected function getStatus(array $slot): string
    {
        if ($slot[SpyCmsSlotTableMap::COL_IS_ACTIVE]) {
            return $this->generateLabel('Active', 'label-primary');
        }

        return $this->generateLabel('Inactive', 'label-danger');
    }

    protected function getContentProvider(array $slot): string
    {
        return $this->translatorFacade->trans($slot[SpyCmsSlotTableMap::COL_CONTENT_PROVIDER_TYPE]);
    }

    protected function getUrlActivate(int $idCmsSlot): string
    {
        return Url::generate(static::URL_ACTIVATE_BUTTON, [ActivateSlotController::PARAM_ID_CMS_SLOT => $idCmsSlot])->build();
    }

    protected function getUrlDeactivate(int $idCmsSlot): string
    {
        return Url::generate(static::URL_DEACTIVATE_BUTTON, [ActivateSlotController::PARAM_ID_CMS_SLOT => $idCmsSlot])->build();
    }
}
