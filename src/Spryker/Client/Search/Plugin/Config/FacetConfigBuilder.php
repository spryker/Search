<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Client\Search\Plugin\Config;

use Generated\Shared\Transfer\FacetConfigTransfer;
use Spryker\Client\Kernel\AbstractPlugin;

class FacetConfigBuilder extends AbstractPlugin implements FacetConfigBuilderInterface
{

    const TYPE_ENUMERATION = 'enumeration';
    const TYPE_BOOL = 'bool';
    const TYPE_RANGE = 'range';
    const TYPE_PRICE_RANGE = 'price_range';
    const TYPE_CATEGORY = 'category';

    /**
     * @var \Generated\Shared\Transfer\FacetConfigTransfer[]
     */
    protected $facetConfigTransfers = [];

    /**
     * @param \Generated\Shared\Transfer\FacetConfigTransfer $facetConfigTransfer
     *
     * @return $this
     */
    public function addFacet(FacetConfigTransfer $facetConfigTransfer)
    {
        $this->assertFacetConfigTransfer($facetConfigTransfer);

        $this->facetConfigTransfers[$facetConfigTransfer->getName()] = $facetConfigTransfer;

        return $this;
    }

    /**
     * @param string $paramName
     *
     * @return \Generated\Shared\Transfer\FacetConfigTransfer|null
     */
    public function get($paramName)
    {
        return isset($this->facetConfigTransfers[$paramName]) ? $this->facetConfigTransfers[$paramName] : null;
    }

    /**
     * @return \Generated\Shared\Transfer\FacetConfigTransfer[]
     */
    public function getAll()
    {
        return $this->facetConfigTransfers;
    }

    /**
     * @return array
     */
    public function getParamNames()
    {
        return array_keys($this->facetConfigTransfers);
    }

    /**
     * @param array $requestParameters
     *
     * @return \Generated\Shared\Transfer\FacetConfigTransfer[]
     */
    public function getActive(array $requestParameters)
    {
        $activeFacets = [];

        foreach ($this->facetConfigTransfers as $facetName => $facet) {
            if (array_key_exists($facetName, $requestParameters)) {
                $activeFacets[$facetName] = $facet;
            }
        }

        return $activeFacets;
    }

    /**
     * @param array $requestParameters
     *
     * @return array
     */
    public function getActiveParamNames(array $requestParameters)
    {
        return array_keys($this->getActive($requestParameters));
    }

    /**
     * @param \Generated\Shared\Transfer\FacetConfigTransfer $facetConfigTransfer
     *
     * @throws \Spryker\Shared\Transfer\Exception\RequiredTransferPropertyException
     *
     * @return void
     */
    protected function assertFacetConfigTransfer(FacetConfigTransfer $facetConfigTransfer)
    {
        $facetConfigTransfer
            ->requireName()
            ->requireFieldName()
            ->requireParameterName()
            ->requireType();
    }

}