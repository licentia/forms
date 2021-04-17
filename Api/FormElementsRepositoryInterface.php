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
 * Interface FormElementsRepositoryInterface
 *
 * @package Licentia\Forms\Api
 */
interface FormElementsRepositoryInterface
{

    /**
     * Save FormElements
     *
     * @param \Licentia\Forms\Api\Data\FormElementsInterface $formElements
     *
     * @return \Licentia\Forms\Api\Data\FormElementsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */

    public function save(
        \Licentia\Forms\Api\Data\FormElementsInterface $formElements
    );

    /**
     * Retrieve FormElements
     *
     * @param string $formelementsId
     *
     * @return \Licentia\Forms\Api\Data\FormElementsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */

    public function getById($formelementsId);

    /**
     * Retrieve FormElements matching the specified criteria.
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchSearchCriteria
     *
     * @return \Licentia\Forms\Api\Data\FormElementsSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */

    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $searchSearchCriteria
    );

    /**
     * Delete FormElements
     *
     * @param \Licentia\Forms\Api\Data\FormElementsInterface $formElements
     *
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */

    public function delete(
        \Licentia\Forms\Api\Data\FormElementsInterface $formElements
    );

    /**
     * Delete FormElements by ID
     *
     * @param string $formelementsId
     *
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */

    public function deleteById($formelementsId);
}
