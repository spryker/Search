<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Unit\Spryker\Client\Search\Plugin\Elasticsearch\ResultFormatter;

use Elastica\ResultSet;
use Generated\Shared\Search\PageIndexMap;
use Generated\Shared\Transfer\FacetConfigTransfer;
use Generated\Shared\Transfer\FacetSearchResultTransfer;
use Generated\Shared\Transfer\FacetSearchResultValueTransfer;
use Generated\Shared\Transfer\RangeSearchResultTransfer;
use Spryker\Client\Search\Dependency\Plugin\SearchConfigInterface;
use Spryker\Client\Search\Plugin\Elasticsearch\QueryExpander\FacetQueryExpanderPlugin;
use Spryker\Client\Search\Plugin\Elasticsearch\ResultFormatter\FacetResultFormatterPlugin;
use Spryker\Client\Search\SearchFactory;
use Spryker\Shared\Search\SearchConstants;

/**
 * @group Unit
 * @group Spryker
 * @group Client
 * @group Search
 * @group Plugin
 * @group Elasticsearch
 * @group ResultFormatter
 * @group FacetResultFormatterPluginTest
 */
class FacetResultFormatterPluginTest extends AbstractResultFormatterPluginTest
{

    /**
     * @dataProvider resultFormatterDataProvider
     *
     * @param \Spryker\Client\Search\Dependency\Plugin\SearchConfigInterface $searchConfig
     * @param array $aggregationResult
     * @param array $expectedResult
     *
     * @return void
     */
    public function testFormatResultShouldReturnCorrectFormat(SearchConfigInterface $searchConfig, array $aggregationResult, array $expectedResult)
    {
        /** @var \Spryker\Client\Search\SearchFactory|\PHPUnit_Framework_MockObject_MockObject $searchFactoryMock */
        $searchFactoryMock = $this->getMockBuilder(SearchFactory::class)
            ->setMethods(['getSearchConfig'])
            ->getMock();
        $searchFactoryMock
            ->method('getSearchConfig')
            ->willReturn($searchConfig);

        $facetResultFormatterPlugin = new FacetResultFormatterPlugin();
        $facetResultFormatterPlugin->setFactory($searchFactoryMock);

        /** @var \Elastica\ResultSet|\PHPUnit_Framework_MockObject_MockObject $resultSetMock */
        $resultSetMock = $this->getMockBuilder(ResultSet::class)
            ->disableOriginalConstructor()
            ->setMethods(['getAggregations'])
            ->getMock();
        $resultSetMock
            ->method('getAggregations')
            ->willReturn($aggregationResult);

        $formattedResult = $facetResultFormatterPlugin->formatResult($resultSetMock);

        $this->assertEquals($expectedResult, $formattedResult);
    }

    /**
     * @return array
     */
    public function resultFormatterDataProvider()
    {
        return [
            'empty result set' => $this->getEmptyResultTestData(),
            'string facet result set' => $this->getStringFacetResultTestData(),
            'multiple string facet result set' => $this->getMultiStringFacetResultTestData(),
            'integer facet result set' => $this->getIntegerFacetResultTestData(),
            'multiple integer facet result set' => $this->getMultiIntegerFacetResultTestData(),
            'category result set' => $this->getCategoryResultTestData(),
            'filtered result set' => $this->getFilteredResultTestData(),
        ];
    }

    /**
     * @return array
     */
    protected function getEmptyResultTestData()
    {
        $searchConfig = $this->createStringSearchConfig();
        $aggregationResult = [];
        $expectedResult = [];

        return [$searchConfig, $aggregationResult, $expectedResult];
    }

    /**
     * @return array
     */
    protected function getStringFacetResultTestData()
    {
        $searchConfig = $this->createStringSearchConfig();

        $aggregationResult = [
            PageIndexMap::STRING_FACET => [
                PageIndexMap::STRING_FACET . '-name' => [
                    'buckets' => [
                        [
                            'key' => 'foo',
                            PageIndexMap::STRING_FACET . '-value' => [
                                'buckets' => [
                                    ['key' => 'a', 'doc_count' => 1],
                                    ['key' => 'b', 'doc_count' => 2],
                                    ['key' => 'c', 'doc_count' => 3],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];

        $expectedResult = [
            'foo' => (new FacetSearchResultTransfer())
                ->setName('foo')
                ->addValue((new FacetSearchResultValueTransfer())
                    ->setValue('a')
                    ->setDocCount(1))
                ->addValue((new FacetSearchResultValueTransfer())
                    ->setValue('b')
                    ->setDocCount(2))
                ->addValue((new FacetSearchResultValueTransfer())
                    ->setValue('c')
                    ->setDocCount(3)),
        ];

        return [$searchConfig, $aggregationResult, $expectedResult];
    }

    /**
     * @return array
     */
    protected function getMultiStringFacetResultTestData()
    {
        $searchConfig = $this->createMultiStringSearchConfig();

        $aggregationResult = [
            PageIndexMap::STRING_FACET => [
                PageIndexMap::STRING_FACET . '-name' => [
                    'buckets' => [
                        [
                            'key' => 'foo',
                            PageIndexMap::STRING_FACET . '-value' => [
                                'buckets' => [
                                    ['key' => 'a', 'doc_count' => 1],
                                    ['key' => 'b', 'doc_count' => 2],
                                    ['key' => 'c', 'doc_count' => 3],
                                ],
                            ],
                        ],
                        [
                            'key' => 'bar',
                            PageIndexMap::STRING_FACET . '-value' => [
                                'buckets' => [
                                    ['key' => 'd', 'doc_count' => 10],
                                    ['key' => 'e', 'doc_count' => 20],
                                    ['key' => 'f', 'doc_count' => 30],
                                ],
                            ],
                        ],
                        [
                            'key' => 'baz',
                            PageIndexMap::STRING_FACET . '-value' => [
                                'buckets' => [
                                    ['key' => 'g', 'doc_count' => 100],
                                    ['key' => 'h', 'doc_count' => 200],
                                    ['key' => 'i', 'doc_count' => 300],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];

        $expectedResult = [
            'foo' => (new FacetSearchResultTransfer())
                ->setName('foo')
                ->addValue((new FacetSearchResultValueTransfer())
                    ->setValue('a')
                    ->setDocCount(1))
                ->addValue((new FacetSearchResultValueTransfer())
                    ->setValue('b')
                    ->setDocCount(2))
                ->addValue((new FacetSearchResultValueTransfer())
                    ->setValue('c')
                    ->setDocCount(3)),
            'bar' => (new FacetSearchResultTransfer())
                ->setName('bar')
                ->addValue((new FacetSearchResultValueTransfer())
                    ->setValue('d')
                    ->setDocCount(10))
                ->addValue((new FacetSearchResultValueTransfer())
                    ->setValue('e')
                    ->setDocCount(20))
                ->addValue((new FacetSearchResultValueTransfer())
                    ->setValue('f')
                    ->setDocCount(30)),
            'baz' => (new FacetSearchResultTransfer())
                ->setName('baz')
                ->addValue((new FacetSearchResultValueTransfer())
                    ->setValue('g')
                    ->setDocCount(100))
                ->addValue((new FacetSearchResultValueTransfer())
                    ->setValue('h')
                    ->setDocCount(200))
                ->addValue((new FacetSearchResultValueTransfer())
                    ->setValue('i')
                    ->setDocCount(300)),
        ];

        return [$searchConfig, $aggregationResult, $expectedResult];
    }

    /**
     * @return array
     */
    protected function getIntegerFacetResultTestData()
    {
        $searchConfig = $this->createIntegerSearchConfig();

        $aggregationResult = [
            PageIndexMap::INTEGER_FACET => [
                PageIndexMap::INTEGER_FACET . '-name' => [
                    'buckets' => [
                        [
                            'key' => 'foo',
                            PageIndexMap::INTEGER_FACET . '-value' => [
                                'buckets' => [
                                    ['key' => 1, 'doc_count' => 10],
                                    ['key' => 2, 'doc_count' => 20],
                                    ['key' => 3, 'doc_count' => 30],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];

        $expectedResult = [
            'foo' => (new FacetSearchResultTransfer())
                ->setName('foo')
                ->addValue((new FacetSearchResultValueTransfer())
                    ->setValue(1)
                    ->setDocCount(10))
                ->addValue((new FacetSearchResultValueTransfer())
                    ->setValue(2)
                    ->setDocCount(20))
                ->addValue((new FacetSearchResultValueTransfer())
                    ->setValue(3)
                    ->setDocCount(30)),
        ];

        return [$searchConfig, $aggregationResult, $expectedResult];
    }

    /**
     * @return array
     */
    protected function getMultiIntegerFacetResultTestData()
    {
        $searchConfig = $this->createMultiIntegerSearchConfig();

        $aggregationResult = [
            PageIndexMap::INTEGER_FACET => [
                PageIndexMap::INTEGER_FACET . '-name' => [
                    'buckets' => [
                        [
                            'key' => 'foo',
                            PageIndexMap::INTEGER_FACET . '-value' => [
                                'buckets' => [
                                    ['key' => 'a', 'doc_count' => 1],
                                    ['key' => 'b', 'doc_count' => 2],
                                    ['key' => 'c', 'doc_count' => 3],
                                ],
                            ],
                        ],
                        [
                            'key' => 'bar',
                            PageIndexMap::INTEGER_FACET . '-value' => [
                                'buckets' => [
                                    ['key' => 'd', 'doc_count' => 10],
                                    ['key' => 'e', 'doc_count' => 20],
                                    ['key' => 'f', 'doc_count' => 30],
                                ],
                            ],
                        ],
                        [
                            'key' => 'baz',
                            // baz is "ranged" type which uses "stats" aggregation
                            PageIndexMap::INTEGER_FACET . '-stats' => [
                                'min' => 10,
                                'max' => 20,
                            ],
                        ],
                    ],
                ],
            ],
        ];

        $expectedResult = [
            'foo' => (new FacetSearchResultTransfer())
                ->setName('foo')
                ->addValue((new FacetSearchResultValueTransfer())
                    ->setValue('a')
                    ->setDocCount(1))
                ->addValue((new FacetSearchResultValueTransfer())
                    ->setValue('b')
                    ->setDocCount(2))
                ->addValue((new FacetSearchResultValueTransfer())
                    ->setValue('c')
                    ->setDocCount(3)),
            'bar' => (new FacetSearchResultTransfer())
                ->setName('bar')
                ->addValue((new FacetSearchResultValueTransfer())
                    ->setValue('d')
                    ->setDocCount(10))
                ->addValue((new FacetSearchResultValueTransfer())
                    ->setValue('e')
                    ->setDocCount(20))
                ->addValue((new FacetSearchResultValueTransfer())
                    ->setValue('f')
                    ->setDocCount(30)),
            'baz' => (new RangeSearchResultTransfer())
                ->setName('baz')
                ->setMin(10)
                ->setMax(20),
        ];

        return [$searchConfig, $aggregationResult, $expectedResult];
    }

    /**
     * @return array
     */
    protected function getCategoryResultTestData()
    {
        $searchConfig = $this->createCategorySearchConfig();

        $aggregationResult = [
            PageIndexMap::CATEGORY_ALL_PARENTS => [
                'buckets' => [
                    ["key" => 'c1', "doc_count" => 10],
                    ["key" => 'c2', "doc_count" => 20],
                    ["key" => 'c3', "doc_count" => 30],
                ],
            ],
        ];

        $expectedResult = [
            'foo' => (new FacetSearchResultTransfer())
                ->setName('foo')
                ->addValue((new FacetSearchResultValueTransfer())
                    ->setValue('c1')
                    ->setDocCount(10))
                ->addValue((new FacetSearchResultValueTransfer())
                    ->setValue('c2')
                    ->setDocCount(20))
                ->addValue((new FacetSearchResultValueTransfer())
                    ->setValue('c3')
                    ->setDocCount(30)),
        ];

        return [$searchConfig, $aggregationResult, $expectedResult];
    }

    /**
     * @return array
     */
    protected function getFilteredResultTestData()
    {
        $searchConfig = $this->createSearchConfigMock();
        $searchConfig->getFacetConfigBuilder()
            ->addFacet(
                (new FacetConfigTransfer())
                    ->setName('foo')
                    ->setParameterName('foo')
                    ->setFieldName(PageIndexMap::STRING_FACET)
                    ->setType(SearchConstants::FACET_TYPE_ENUMERATION)
                    ->setIsMultiValued(true)
            )
            ->addFacet(
                (new FacetConfigTransfer())
                    ->setName('bar')
                    ->setParameterName('bar')
                    ->setFieldName(PageIndexMap::STRING_FACET)
                ->setType(SearchConstants::FACET_TYPE_ENUMERATION)
            );

        $aggregationResult = [
            PageIndexMap::STRING_FACET => [
                PageIndexMap::STRING_FACET . '-name' => [
                    'buckets' => [
                        [
                            'key' => 'foo',
                            PageIndexMap::STRING_FACET . '-value' => [
                                'buckets' => [
                                    ['key' => 'a', 'doc_count' => 1],
                                    ['key' => 'b', 'doc_count' => 2],
                                    ['key' => 'c', 'doc_count' => 3],
                                ],
                            ],
                        ],
                        [
                            'key' => 'bar',
                            PageIndexMap::STRING_FACET . '-value' => [
                                'buckets' => [
                                    ['key' => 'd', 'doc_count' => 4],
                                    ['key' => 'e', 'doc_count' => 5],
                                    ['key' => 'f', 'doc_count' => 6],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            FacetQueryExpanderPlugin::AGGREGATION_GLOBAL_PREFIX . 'foo' => [
                FacetQueryExpanderPlugin::AGGREGATION_FILTER_NAME => [
                    PageIndexMap::STRING_FACET => [
                        PageIndexMap::STRING_FACET . '-name' => [
                            'buckets' => [
                                [
                                    'key' => 'foo',
                                    PageIndexMap::STRING_FACET . '-value' => [
                                        'buckets' => [
                                            ['key' => 'a', 'doc_count' => 10],
                                            ['key' => 'b', 'doc_count' => 20],
                                            ['key' => 'c', 'doc_count' => 30],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            // this data should never get generated normally, but we want to make sure that it's not used when it's not needed even if it's there
            FacetQueryExpanderPlugin::AGGREGATION_GLOBAL_PREFIX . 'bar' => [
                FacetQueryExpanderPlugin::AGGREGATION_FILTER_NAME => [
                    PageIndexMap::STRING_FACET => [
                        PageIndexMap::STRING_FACET . '-name' => [
                            'buckets' => [
                                [
                                    'key' => 'bar',
                                    PageIndexMap::STRING_FACET . '-value' => [
                                        'buckets' => [
                                            ['key' => 'a', 'doc_count' => 40],
                                            ['key' => 'b', 'doc_count' => 50],
                                            ['key' => 'c', 'doc_count' => 60],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];

        $expectedResult = [
            'foo' => (new FacetSearchResultTransfer())
                ->setName('foo')
                ->addValue((new FacetSearchResultValueTransfer())
                    ->setValue('a')
                    ->setDocCount(10))
                ->addValue((new FacetSearchResultValueTransfer())
                    ->setValue('b')
                    ->setDocCount(20))
                ->addValue((new FacetSearchResultValueTransfer())
                    ->setValue('c')
                    ->setDocCount(30)),
            'bar' => (new FacetSearchResultTransfer())
                ->setName('bar')
                ->addValue((new FacetSearchResultValueTransfer())
                    ->setValue('d')
                    ->setDocCount(4))
                ->addValue((new FacetSearchResultValueTransfer())
                    ->setValue('e')
                    ->setDocCount(5))
                ->addValue((new FacetSearchResultValueTransfer())
                    ->setValue('f')
                    ->setDocCount(6)),
        ];

        return [$searchConfig, $aggregationResult, $expectedResult];
    }

}
