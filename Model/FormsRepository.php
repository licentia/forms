<?php
/**
 * Copyright (C) Licentia, Unipessoal LDA
 *
 * NOTICE OF LICENSE
 *  
 *  This source file is subject to the EULA
 *  that is bundled with this package in the file LICENSE.txt.
 *  It is also available through the world-wide-web at this URL:
 *  https://www.greenflyingpanda.com/panda-license.txt
 *  
 *  @title      Licentia Panda - Magento® Sales Automation Extension
 *  @package    Licentia
 *  @author     Bento Vilas Boas <bento@licentia.pt>
 *  @copyright  Copyright (c) Licentia - https://licentia.pt
 *  @license    https://www.greenflyingpanda.com/panda-license.txt
 *
 */

namespace Licentia\Forms\Model;

use Licentia\Forms\Api\Data\FormsInterfaceFactory;
use Licentia\Forms\Api\Data\FormsSearchResultsInterfaceFactory;
use Licentia\Forms\Api\FormsRepositoryInterface;
use Licentia\Forms\Model\ResourceModel\Forms as ResourceForms;
use Licentia\Forms\Model\ResourceModel\Forms\CollectionFactory as FormsCollectionFactory;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class FormsRepository
 *
 * @package Licentia\Forms\Model
 */
class FormsRepository implements FormsRepositoryInterface
{

    /**
     * @var
     */
    protected $FormsFactory;

    /**
     * @var DataObjectHelper
     */
    protected $dataObjectHelper;

    /**
     * @var
     */
    protected $FormsCollectionFactory;

    /**
     * @var FormsSearchResultsInterfaceFactory
     */
    protected $searchResultsFactory;

    /**
     * @var FormsFactory
     */
    protected $formsFactory;

    /**
     * @var FormsCollectionFactory
     */
    protected $formsCollectionFactory;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var ResourceForms
     */
    protected $resource;

    /**
     * @var DataObjectProcessor
     */
    protected $dataObjectProcessor;

    /**
     * @var FormsInterfaceFactory
     */
    protected $dataFormsFactory;

    /**
     * @param ResourceForms                      $resource
     * @param FormsFactory                       $formsFactory
     * @param FormsInterfaceFactory              $dataFormsFactory
     * @param FormsCollectionFactory             $formsCollectionFactory
     * @param FormsSearchResultsInterfaceFactory $searchResultsFactory
     * @param DataObjectHelper                   $dataObjectHelper
     * @param DataObjectProcessor                $dataObjectProcessor
     * @param StoreManagerInterface              $storeManager
     */
    public function __construct(
        ResourceForms $resource,
        FormsFactory $formsFactory,
        FormsInterfaceFactory $dataFormsFactory,
        FormsCollectionFactory $formsCollectionFactory,
        FormsSearchResultsInterfaceFactory $searchResultsFactory,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        StoreManagerInterface $storeManager
    ) {

        $this->resource = $resource;
        $this->formsFactory = $formsFactory;
        $this->formsCollectionFactory = $formsCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataFormsFactory = $dataFormsFactory;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->storeManager = $storeManager;
    }

    /**
     * {@inheritdoc}
     */
    public function save(
        \Licentia\Forms\Api\Data\FormsInterface $forms
    ) {

        /* if (empty($forms->getStoreId())) {
            $storeId = $this->storeManager->getStore()->getId();
            $forms->setStoreId($storeId);
        } */
        try {
            $this->resource->save($forms);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(
                __(
                    'Could not save the forms: %1',
                    $exception->getMessage()
                )
            );
        }

        return $forms;
    }

    /**
     * {@inheritdoc}
     */
    public function getById($formsId)
    {

        $forms = $this->formsFactory->create();
        $forms->load($formsId);
        if (!$forms->getId()) {
            throw new NoSuchEntityException(__('Forms with id "%1" does not exist.', $formsId));
        }

        return $forms;
    }

    /**
     * {@inheritdoc}
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $criteria
    ) {

        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($criteria);

        $collection = $this->formsCollectionFactory->create();
        foreach ($criteria->getFilterGroups() as $filterGroup) {
            foreach ($filterGroup->getFilters() as $filter) {
                $condition = $filter->getConditionType() ?: 'eq';
                $collection->addFieldToFilter($filter->getField(), [$condition => $filter->getValue()]);
            }
        }
        $searchResults->setTotalCount($collection->getSize());
        $sortOrders = $criteria->getSortOrders();
        if ($sortOrders) {
            /** @var SortOrder $sortOrder */
            foreach ($sortOrders as $sortOrder) {
                $collection->addOrder(
                    $sortOrder->getField(),
                    ($sortOrder->getDirection() == SortOrder::SORT_ASC) ? 'ASC' : 'DESC'
                );
            }
        }
        $collection->setCurPage($criteria->getCurrentPage());
        $collection->setPageSize($criteria->getPageSize());
        $items = [];

        foreach ($collection as $formsModel) {
            $formsData = $this->dataFormsFactory->create();
            $this->dataObjectHelper->populateWithArray(
                $formsData,
                $formsModel->getData(),
                \Licentia\Forms\Api\Data\FormsInterface::class
            );
            $items[] = $this->dataObjectProcessor->buildOutputDataArray(
                $formsData,
                \Licentia\Forms\Api\Data\FormsInterface::class
            );
        }
        $searchResults->setItems($items);

        return $searchResults;
    }

    /**
     * {@inheritdoc}
     */
    public function delete(
        \Licentia\Forms\Api\Data\FormsInterface $forms
    ) {

        try {
            $this->resource->delete($forms);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(
                __(
                    'Could not delete the Forms: %1',
                    $exception->getMessage()
                )
            );
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById($formsId)
    {

        return $this->delete($this->getById($formsId));
    }
}
