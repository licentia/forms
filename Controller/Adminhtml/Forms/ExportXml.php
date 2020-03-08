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

namespace Licentia\Forms\Controller\Adminhtml\Forms;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\ResponseInterface;

/**
 * Class ExportXml
 *
 * @package Licentia\Forms\Controller\Adminhtml\Forms
 */
class ExportXml extends \Licentia\Forms\Controller\Adminhtml\Forms
{

    /**
     * Export review product detail report to CSV format
     *
     * @return ResponseInterface
     * @throws \Exception
     */
    public function execute()
    {

        parent::execute();

        $form = $this->registry->registry('panda_form');

        $name = preg_replace('/\W/', '_', $form->getName());
        $name = strtolower($name) . '-' . date('Y-m-d H_i_s');

        $fileName = 'form_entries' . $name . '.xml';
        $content = $this->_view->getLayout()
                               ->createBlock(
                                   'Licentia\Forms\Block\Adminhtml\Forms\Grid'
                               )
                               ->getXml();

        return $this->fileFactory->create($fileName, $content, DirectoryList::VAR_DIR);
    }
}