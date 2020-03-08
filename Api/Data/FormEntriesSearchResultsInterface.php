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

namespace Licentia\Forms\Api\Data;

/**
 * Interface FormEntriesSearchResultsInterface
 *
 * @package Licentia\Forms\Api\Data
 */
interface FormEntriesSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{

    /**
     * Get FormEntries list.
     *
     * @return \Licentia\Forms\Api\Data\FormEntriesInterface[]
     */

    public function getItems();

    /**
     * Set entry_id list.
     *
     * @param \Licentia\Forms\Api\Data\FormEntriesInterface[] $items
     *
     * @return $this
     */

    public function setItems(array $items);
}