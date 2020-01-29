<?php
/**
 * Copyright (C) 2020 Licentia, Unipessoal LDA
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <https://www.gnu.org/licenses/>.
 *
 * @title      Licentia Panda - Magento® Sales Automation Extension
 * @package    Licentia
 * @author     Bento Vilas Boas <bento@licentia.pt>
 * @copyright  Copyright (c) Licentia - https://licentia.pt
 * @license    GNU General Public License V3
 * @modified   29/01/20, 15:22 GMT
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
