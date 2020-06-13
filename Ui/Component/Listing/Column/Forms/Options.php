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

namespace Licentia\Forms\Ui\Component\Listing\Column\Forms;

use Licentia\Forms\Model\FormsFactory;
use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class Options
 */
class Options implements OptionSourceInterface
{

    /**
     * @var formsFactory
     */
    protected $formsFactory;

    /**
     * Options constructor.
     *
     * @param formsFactory $formsFactory
     */
    public function __construct(FormsFactory $formsFactory
    ) {

        $this->formsFactory = $formsFactory;
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {

        return $this->formsFactory->create()->toOptionArray();
    }
}
