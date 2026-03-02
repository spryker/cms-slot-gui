<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\CmsSlotGui\Communication\Controller;

use Spryker\Zed\Kernel\Communication\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * @method \Spryker\Zed\CmsSlotGui\Communication\CmsSlotGuiCommunicationFactory getFactory()
 */
class TemplateListController extends AbstractController
{
    public function indexAction(Request $request): array
    {
        return $this->viewResponse([
            'templateListTable' => $this->getFactory()->createTemplateListTable()->render(),
            'slotListTable' => $this->getFactory()->createSlotListTable()->render(),
        ]);
    }

    public function tableAction(): JsonResponse
    {
        return $this->jsonResponse(
            $this->getFactory()->createTemplateListTable()->fetchData(),
        );
    }
}
