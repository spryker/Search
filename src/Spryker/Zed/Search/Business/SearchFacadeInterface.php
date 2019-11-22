<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Search\Business;

use Generated\Shared\Transfer\LocaleTransfer;
use Psr\Log\LoggerInterface;
use Spryker\Zed\Search\Dependency\Plugin\PageMapInterface;

interface SearchFacadeInterface
{
    /**
     * Specification:
     * - Loads index definition json files from the folders
     * - Installs Elasticsearch indexes and mapping types based on the loaded index definitions if they not exists already
     * - For each configured store a separated index name is generated
     * - The index is created for only the current store
     * - The name of the index is automatically prefixed with the store name + underscore
     * - Generates IndexMap class for each mapping type
     * - The generated IndexMaps are not store specific and has the class name of the mapping types suffixed with "IndexMap"
     * - The generated files will be removed and re-created always when install runs
     *
     * @api
     *
     * @param \Psr\Log\LoggerInterface $messenger
     *
     * @return void
     */
    public function install(LoggerInterface $messenger);

    /**
     * Specification:
     * - Returns the total number of documents in the current index
     *
     * @api
     *
     * @return int
     */
    public function getTotalCount();

    /**
     * Specification:
     * - Returns the metadata information from the current index
     * - Returns empty array if the index is not installed
     *
     * @api
     *
     * @return array
     */
    public function getMetaData();

    /**
     * Specification:
     * - Removes the current index if no indexName is passed.
     * - Removes the passed indexName.
     *
     * @api
     *
     * @deprecated Use `\Spryker\Zed\SearchElasticsearch\Business\SearchElasticsearchFacadeInterface::deleteIndex()` instead.
     *
     * @return \Elastica\Response
     */
    public function delete();

    /**
     * Specification:
     * - Returns a document from the current index with the given key in the given mapping type
     *
     * @api
     *
     * @param string $key
     * @param string $type
     *
     * @return \Elastica\Document
     */
    public function getDocument($key, $type);

    /**
     * Specification:
     * - Runs a simple full text search for the given search string
     * - Returns the raw result set ordered by relevance
     *
     * @api
     *
     * @param string $searchString
     * @param int|null $limit
     * @param int|null $offset
     *
     * @return array|\Elastica\ResultSet|mixed (@deprecated Only mixed will be supported with the next major)
     */
    public function searchKeys($searchString, $limit = null, $offset = null);

    /**
     * @api
     *
     * @deprecated Use transformPageMapToDocumentByMapperName() instead.
     *
     * Specification:
     * - Transforms a raw data array into an Elasticsearch "page" mapping type document
     * - The transformation is based on the given page map what configures which data goes into which field
     *
     * @param \Spryker\Zed\Search\Dependency\Plugin\PageMapInterface $pageMap
     * @param array $data
     * @param \Generated\Shared\Transfer\LocaleTransfer $localeTransfer
     *
     * @return array
     */
    public function transformPageMapToDocument(PageMapInterface $pageMap, array $data, LocaleTransfer $localeTransfer);

    /**
     * Specification:
     * - Transforms a raw data array into an Elasticsearch "page" mapping type document
     * - The transformation is based on the given page map plugin name what configures which data goes into which field
     *
     * @api
     *
     * @param array $data
     * @param \Generated\Shared\Transfer\LocaleTransfer $localeTransfer
     * @param string $mapperName
     *
     * @throws \Spryker\Zed\Search\Business\Exception\InvalidPropertyNameException
     *
     * @return array
     */
    public function transformPageMapToDocumentByMapperName(array $data, LocaleTransfer $localeTransfer, $mapperName);

    /**
     * Specification:
     * - Loads index definition json files from the folders
     * - Generates IndexMap class for each mapping type
     * - The generated IndexMaps are not store specific and has the class name of the mapping types suffixed with "IndexMap"
     * - The generated files will be removed and re-created always when the command runs
     *
     * @api
     *
     * @deprecated Use `\Spryker\Zed\Search\Business\SearchFacadeInterface::generateSourceMap()` instead.
     *
     * @param \Psr\Log\LoggerInterface $messenger
     *
     * @return void
     */
    public function generatePageIndexMap(LoggerInterface $messenger);

    /**
     * Specification:
     * - Loads schema definition json files.
     * - Creates or updates map classes by found schema definition files.
     * - The generated map classes are not store specific.
     * - Previously generated files will be removed.
     *
     * @api
     *
     * @param \Psr\Log\LoggerInterface $messenger
     *
     * @return void
     */
    public function generateSourceMap(LoggerInterface $messenger): void;

    /**
     * Specification:
     * - Creates a Snapshot.
     *
     * @api
     *
     * @deprecated Use `\Spryker\Zed\SearchElasticsearch\Business\SearchElasticsearchFacadeInterface::createSnapshot()` instead.
     *
     * @param string $repositoryName
     * @param string $snapshotName
     * @param array $options
     *
     * @return bool
     */
    public function createSnapshot($repositoryName, $snapshotName, $options = []);

    /**
     * Specification:
     * - Checks if a Snapshot exists.
     *
     * @api
     *
     * @deprecated Use `\Spryker\Zed\SearchElasticsearch\Business\SearchElasticsearchFacadeInterface::existsSnapshot()` instead.
     *
     * @param string $repositoryName
     * @param string $snapshotName
     *
     * @return bool
     */
    public function existsSnapshot($repositoryName, $snapshotName);

    /**
     * Specification:
     * - Deletes a Snapshot.
     *
     * @api
     *
     * @deprecated Use `\Spryker\Zed\SearchElasticsearch\Business\SearchElasticsearchFacadeInterface::deleteSnapshot()` instead.
     *
     * @param string $repositoryName
     * @param string $snapshotName
     *
     * @return bool
     */
    public function deleteSnapshot($repositoryName, $snapshotName);

    /**
     * Specification:
     * - Checks if a Snapshot repository exists.
     *
     * @api
     *
     * @deprecated Use `\Spryker\Zed\SearchElasticsearch\Business\SearchElasticsearchFacadeInterface::existsSnapshotRepository()` instead.
     *
     * @param string $repositoryName
     *
     * @return bool
     */
    public function existsSnapshotRepository($repositoryName);

    /**
     * Specification:
     * - Creates a Snapshot repository.
     *
     * @api
     *
     * @deprecated Use `\Spryker\Zed\SearchElasticsearch\Business\SearchElasticsearchFacadeInterface::createSnapshotRepository()` instead.
     *
     * @param string $repositoryName
     * @param string $type
     * @param array $settings
     *
     * @return bool
     */
    public function createSnapshotRepository($repositoryName, $type = 'fs', $settings = []);

    /**
     * Specification:
     * - Restores a Snapshot.
     *
     * @api
     *
     * @deprecated Use `\Spryker\Zed\SearchElasticsearch\Business\SearchElasticsearchFacadeInterface::restoreSnapshot()` instead.
     *
     * @param string $repositoryName
     * @param string $snapshotName
     * @param array $options
     *
     * @return bool
     */
    public function restoreSnapshot($repositoryName, $snapshotName, $options = []);

    /**
     * Specification:
     * - Closes an Index.
     *
     * @api
     *
     * @deprecated Use `\Spryker\Zed\SearchElasticsearch\Business\SearchElasticsearchFacadeInterface::closeIndex()` instead.
     *
     * @return bool
     */
    public function closeIndex();

    /**
     * Specification:
     * - Opens an Index.
     *
     * @api
     *
     * @deprecated Use `\Spryker\Zed\SearchElasticsearch\Business\SearchElasticsearchFacadeInterface::openIndex()` instead.
     *
     * @return bool
     */
    public function openIndex(): bool;

    /**
     * Specification:
     * - Closes all indices.
     *
     * @api
     *
     * @deprecated Use `\Spryker\Zed\SearchElasticsearch\Business\SearchElasticsearchFacadeInterface::closeIndex()` instead.
     *
     * @return bool
     */
    public function closeAllIndices();

    /**
     * Specification:
     * - Copies one index to another index.
     *
     * @api
     *
     * @deprecated Use `\Spryker\Zed\SearchElasticsearch\Business\SearchElasticsearchFacadeInterface::copyIndex()` instead.
     *
     * @param string $source
     * @param string $target
     *
     * @return bool
     */
    public function copyIndex($source, $target);

    /**
     * Specification:
     * - Installs Elasticsearch indexes and mapping types based on the loaded index definitions if they don't exist.
     * - For each configured store a separated index name is generated.
     * - Only the index for the current store is created.
     * - The name of the index is automatically prefixed with the store name + underscore.
     *
     * @api
     *
     * @deprecated Use `\Spryker\Zed\Search\Business\SearchFacadeInterface::installSources()` instead.
     *
     * @param \Psr\Log\LoggerInterface $messenger
     *
     * @return void
     */
    public function installIndexes(LoggerInterface $messenger): void;

    /**
     * Specification:
     * - Sets up search sources based on the loaded schema definitions if they don't exist.
     *
     * @api
     *
     * @param \Psr\Log\LoggerInterface $messenger
     *
     * @return void
     */
    public function installSources(LoggerInterface $messenger): void;
}
