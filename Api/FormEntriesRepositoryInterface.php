<?php
/*
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

namespace Licentia\Forms\Api;

/**
 * Interface FormEntriesRepositoryInterface
 *
 * @package Licentia\Forms\Api
 */
interface FormEntriesRepositoryInterface
{

    /**
     * Save FormEntries
     *
     * @param \Licentia\Forms\Api\Data\FormEntriesInterface $formEntries
     *
     * @return \Licentia\Forms\Api\Data\FormEntriesInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */

    public function save(
        \Licentia\Forms\Api\Data\FormEntriesInterface $formEntries
    );

    /**
     * Retrieve FormEntries
     *
     * @param string $formentriesId
     *
     * @return \Licentia\Forms\Api\Data\FormEntriesInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */

    public function getById($formentriesId);

    /**
     * Retrieve FormEntries ready to display
     *
     * @param string $formentriesId
     *
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */

    public function getByIdDisplay($formentriesId);

    /**
     * Retrieve FormEntries for specified form code
     *
     * @param string $code
     * @param int    $storeId
     *
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */

    public function getListByCode(string $code, $storeId = null);

    /**
     * Retrieve FormEntries matching the specified criteria.
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     *
     * @return \Licentia\Forms\Api\Data\FormEntriesSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */

    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    );

    /**
     * Delete FormEntries
     *
     * @param \Licentia\Forms\Api\Data\FormEntriesInterface $formEntries
     *
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */

    public function delete(
        \Licentia\Forms\Api\Data\FormEntriesInterface $formEntries
    );

    /**
     * Delete FormEntries by ID
     *
     * @param string $formentriesId
     *
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */

    public function deleteById($formentriesId);
}
