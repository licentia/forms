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

namespace Licentia\Forms\Model;

use Licentia\Forms\Api\Data\FormElementsInterface;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class Forms
 *
 * @package Licentia\Forms\Model
 */
class FormElements extends \Magento\Framework\Model\AbstractModel implements FormElementsInterface
{

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'panda_form_element';

    /**
     * Parameter name in event
     *
     * In observe method you can use $observer->getEvent()->getObject() in this case
     *
     * @var string
     */
    protected $_eventObject = 'form_element';

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {

        $this->_init(\Licentia\Forms\Model\ResourceModel\FormElements::class);
    }

    /**
     * @return string
     */
    public function getIdAttribute()
    {

        return \Licentia\Forms\Model\Forms::FIELD_IDENTIFIER . $this->getData('entry_code');
    }

    /**
     * @return \Magento\Framework\Model\AbstractModel
     */
    public function afterDelete()
    {

        $entryCode = $this->getData('entry_code');

        $this->getResource()
             ->getConnection()
             ->update(
                 $this->getResource()->getTable('panda_forms_entries'),
                 [
                     'field_' . $entryCode => new \Zend_Db_Expr('NULL'),
                 ],
                 [
                     'form_id=?' => $this->getData('form_id'),
                 ]
             );

        return parent::afterDelete();
    }

    /**
     * @return $this|\Magento\Framework\Model\AbstractModel
     * @throws LocalizedException
     */
    public function validateBeforeSave()
    {

        parent::validateBeforeSave();

        if ($this->getCode() && !$this->getOrigData('code')) {
            $unique = $this->getCollection()
                           ->addFieldToFilter('form_id', $this->getFormId())
                           ->addFieldToFilter('code', $this->getCode());

            if ($unique->count() > 0) {
                throw new LocalizedException(__('Duplicated value for the field: Code'));
            }
        }

        return $this;
    }

    /**
     * @return \Magento\Framework\Model\AbstractModel
     */
    public function beforeSave()
    {

        #$this->setData('options', preg_replace('#\s+#', ',', trim($this->getData('options'))));

        if (!$this->getId()) {
            $collection = $this->getCollection();

            $elements = $collection->addFieldToFilter('form_id', $this->getData('form_id'))
                                   ->setOrder('sort_order', 'ASC')
                                   ->getData();

            $fields = array_combine(range(1, Forms::FORMS_MAX_NUMBER_FIELDS), range(1, Forms::FORMS_MAX_NUMBER_FIELDS));
            foreach ($elements as $element) {
                unset($fields[$element['entry_code']]);
            }

            $entryCode = reset($fields);

            $this->setData('entry_code', $entryCode);
        }

        $fields = ['min_length', 'max_length', 'max_number', 'min_number'];

        foreach ($fields as $field) {
            if (strlen($this->getData($field)) == 0) {
                $this->unsetData($field);
            }
        }

        return parent::beforeSave();
    }

    /**
     * Get element_id
     *
     * @return string
     */
    public function getElementId()
    {

        return $this->getData(self::ELEMENT_ID);
    }

    /**
     * Set element_id
     *
     * @param string $element_id
     *
     * @return FormElements \Licentia\Forms\Api\Data\FormElementsInterface
     */
    public function setElementId($element_id)
    {

        return $this->setData(self::ELEMENT_ID, $element_id);
    }

    /**
     * Get form_id
     *
     * @return string
     */
    public function getFormId()
    {

        return $this->getData(self::FORM_ID);
    }

    /**
     * Set form_id
     *
     * @param string $form_id
     *
     * @return FormElements \Licentia\Forms\Api\Data\FormElementsInterface
     */
    public function setFormId($form_id)
    {

        return $this->setData(self::FORM_ID, $form_id);
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {

        return $this->getData(self::NAME);
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return FormElements \Licentia\Forms\Api\Data\FormElementsInterface
     */
    public function setName($name)
    {

        return $this->setData(self::NAME, $name);
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {

        return $this->getData(self::TYPE);
    }

    /**
     * Set type
     *
     * @param string $type
     *
     * @return FormElements \Licentia\Forms\Api\Data\FormElementsInterface
     */
    public function setType($type)
    {

        return $this->setData(self::TYPE, $type);
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getAllowMultiple()
    {

        return $this->getData(self::ALLOW_MULTIPLE);
    }

    /**
     * Set allow multiple
     *
     * @param string $allowMultiple
     *
     * @return FormElements \Licentia\Forms\Api\Data\FormElementsInterface
     */
    public function setAllowMultiple($allowMultiple)
    {

        return $this->setData(self::ALLOW_MULTIPLE, $allowMultiple);
    }

    /**
     * Get show_in_grid
     *
     * @return string
     */
    public function getShowInGrid()
    {

        return $this->getData(self::SHOW_IN_GRID);
    }

    /**
     * Set showInGrid
     *
     * @param string $showInGrid
     *
     * @return FormElements \Licentia\Forms\Api\Data\FormElementsInterface
     */
    public function setShowInGrid($showInGrid)
    {

        return $this->setData(self::SHOW_IN_GRID, $showInGrid);
    }

    /**
     * Get show_in_frontend
     *
     * @return string
     */
    public function getShowInFrontend()
    {

        return $this->getData(self::SHOW_IN_FRONTEND);
    }

    /**
     * Set showInFrontend
     *
     * @param string $showInFrontend
     *
     * @return FormElements \Licentia\Forms\Api\Data\FormElementsInterface
     */
    public function setShowInFrontend($showInFrontend)
    {

        return $this->setData(self::SHOW_IN_FRONTEND, $showInFrontend);
    }

    /**
     * Get stars
     *
     * @return string
     */
    public function getStars()
    {

        return $this->getData(self::STARS);
    }

    /**
     * Set Stars
     *
     * @param string $stars
     *
     * @return FormElements \Licentia\Forms\Api\Data\FormElementsInterface
     */
    public function setStars($stars)
    {

        return $this->setData(self::STARS, $stars);
    }

    /**
     * Get required
     *
     * @return string
     */
    public function getRequired()
    {

        return $this->getData(self::REQUIRED);
    }

    /**
     * Set required
     *
     * @param string $required
     *
     * @return FormElements \Licentia\Forms\Api\Data\FormElementsInterface
     */
    public function setRequired($required)
    {

        return $this->setData(self::REQUIRED, $required);
    }

    /**
     * Get unique
     *
     * @return string
     */
    public function getUnique()
    {

        return $this->getData(self::UNIQUE);
    }

    /**
     * Set unique
     *
     * @param string $unique
     *
     * @return FormElements \Licentia\Forms\Api\Data\FormElementsInterface
     */
    public function setUnique($unique)
    {

        return $this->setData(self::UNIQUE, $unique);
    }

    /**
     * Get default
     *
     * @return string
     */
    public function getDefault()
    {

        return $this->getData(self::DEFAULT_VALUE);
    }

    /**
     * Set default
     *
     * @param string $default
     *
     * @return FormElements \Licentia\Forms\Api\Data\FormElementsInterface
     */
    public function setDefault($default)
    {

        return $this->setData(self::DEFAULT_VALUE, $default);
    }

    /**
     * Get map
     *
     * @return string
     */
    public function getMap()
    {

        return $this->getData(self::MAP);
    }

    /**
     * Set map
     *
     * @param string $map
     *
     * @return FormElements \Licentia\Forms\Api\Data\FormElementsInterface
     */
    public function setMap($map)
    {

        return $this->setData(self::MAP, $map);
    }

    /**
     * Get map_customer
     *
     * @return string
     */
    public function getMapCustomer()
    {

        return $this->getData(self::MAP_CUSTOMER);
    }

    /**
     * Set map_customer
     *
     * @param string $mapCustomer
     *
     * @return FormElements \Licentia\Forms\Api\Data\FormElementsInterface
     */
    public function setMapCustomer($mapCustomer)
    {

        return $this->setData(self::MAP_CUSTOMER, $mapCustomer);
    }

    /**
     * Get placeholder
     *
     * @return string
     */
    public function getPlaceholder()
    {

        return $this->getData(self::PLACEHOLDER);
    }

    /**
     * Set placeholder
     *
     * @param string $placeholder
     *
     * @return FormElements \Licentia\Forms\Api\Data\FormElementsInterface
     */
    public function setPlaceholder($placeholder)
    {

        return $this->setData(self::PLACEHOLDER, $placeholder);
    }

    /**
     * Get css_class
     *
     * @return string
     */
    public function getCssClass()
    {

        return $this->getData(self::CSS_CLASS);
    }

    /**
     * Set css_class
     *
     * @param string $css_class
     *
     * @return FormElements \Licentia\Forms\Api\Data\FormElementsInterface
     */
    public function setCssClass($css_class)
    {

        return $this->setData(self::CSS_CLASS, $css_class);
    }

    /**
     * Get sort_order
     *
     * @return string
     */
    public function getSortOrder()
    {

        return $this->getData(self::SORT_ORDER);
    }

    /**
     * Set sort_order
     *
     * @param string $sort_order
     *
     * @return FormElements \Licentia\Forms\Api\Data\FormElementsInterface
     */
    public function setSortOrder($sort_order)
    {

        return $this->setData(self::SORT_ORDER, $sort_order);
    }

    /**
     * Get checked
     *
     * @return string
     */
    public function getChecked()
    {

        return $this->getData(self::CHECKED);
    }

    /**
     * Set checked
     *
     * @param string $checked
     *
     * @return FormElements \Licentia\Forms\Api\Data\FormElementsInterface
     */
    public function setChecked($checked)
    {

        return $this->setData(self::CHECKED, $checked);
    }

    /**
     * Get disabled
     *
     * @return string
     */
    public function getDisabled()
    {

        return $this->getData(self::DISABLED);
    }

    /**
     * Set disabled
     *
     * @param string $disabled
     *
     * @return FormElements \Licentia\Forms\Api\Data\FormElementsInterface
     */
    public function setDisabled($disabled)
    {

        return $this->setData(self::DISABLED, $disabled);
    }

    /**
     * Get hint
     *
     * @return string
     */
    public function getHint()
    {

        return $this->getData(self::HINT);
    }

    /**
     * Set hint
     *
     * @param string $hint
     *
     * @return FormElements \Licentia\Forms\Api\Data\FormElementsInterface
     */
    public function setHint($hint)
    {

        return $this->setData(self::HINT, $hint);
    }

    /**
     * Get entry_code
     *
     * @return string
     */
    public function getEntryCode()
    {

        return $this->getData(self::ENTRY_CODE);
    }

    /**
     * Set entry_code
     *
     * @param string $entry_code
     *
     * @return FormElements \Licentia\Forms\Api\Data\FormElementsInterface
     */
    public function setEntryCode($entry_code)
    {

        return $this->setData(self::ENTRY_CODE, $entry_code);
    }

    /**
     * Get active
     *
     * @return string
     */
    public function getActive()
    {

        return $this->getData(self::ACTIVE);
    }

    /**
     * Set active
     *
     * @param string $active
     *
     * @return FormElements \Licentia\Forms\Api\Data\FormElementsInterface
     */
    public function setActive($active)
    {

        return $this->setData(self::ACTIVE, $active);
    }

    /**
     * Get options
     *
     * @return string
     */
    public function getOptions()
    {

        return $this->getData(self::OPTIONS);
    }

    /**
     * Set options
     *
     * @param string|array $options
     *
     * @return FormElements \Licentia\Forms\Api\Data\FormElementsInterface
     */
    public function setOptions($options)
    {

        return $this->setData(self::OPTIONS, $options);
    }

    /**
     * Get pattern
     *
     * @return string
     */
    public function getPattern()
    {

        return $this->getData(self::PATTERN);
    }

    /**
     * Set pattern
     *
     * @param string $pattern
     *
     * @return FormElements \Licentia\Forms\Api\Data\FormElementsInterface
     */
    public function setPattern($pattern)
    {

        return $this->setData(self::PATTERN, $pattern);
    }

    /**
     * Get min_number
     *
     * @return string
     */
    public function getMinNumber()
    {

        return $this->getData(self::MIN_NUMBER);
    }

    /**
     * Set min_number
     *
     * @param string $min_number
     *
     * @return FormElements \Licentia\Forms\Api\Data\FormElementsInterface
     */
    public function setMinNumber($min_number)
    {

        return $this->setData(self::MIN_NUMBER, $min_number);
    }

    /**
     * Get max_number
     *
     * @return string
     */
    public function getMaxNumber()
    {

        return $this->getData(self::MAX_NUMBER);
    }

    /**
     * Set max_number
     *
     * @param string $max_number
     *
     * @return FormElements \Licentia\Forms\Api\Data\FormElementsInterface
     */
    public function setMaxNumber($max_number)
    {

        return $this->setData(self::MAX_NUMBER, $max_number);
    }

    /**
     * Get max_length
     *
     * @return string
     */
    public function getMaxLength()
    {

        return $this->getData(self::MAX_LENGTH);
    }

    /**
     * Set max_number
     *
     * @param string $max_length
     *
     * @return FormElements \Licentia\Forms\Api\Data\FormElementsInterface
     */
    public function setMaxLength($max_length)
    {

        return $this->setData(self::MAX_LENGTH, $max_length);
    }

    /**
     * Get min_length
     *
     * @return string
     */
    public function getMinLength()
    {

        return $this->getData(self::MIN_LENGTH);
    }

    /**
     * Set min_number
     *
     * @param string $min_length
     *
     * @return FormElements \Licentia\Forms\Api\Data\FormElementsInterface
     */
    public function setMinLength($min_length)
    {

        return $this->setData(self::MIN_LENGTH, $min_length);
    }

    /**
     * Get min_date
     *
     * @return string
     */
    public function getMinDate()
    {

        return $this->getData(self::MIN_DATE);
    }

    /**
     * Set min_date
     *
     * @param string $min_date
     *
     * @return FormElements \Licentia\Forms\Api\Data\FormElementsInterface
     */
    public function setMinDate($min_date)
    {

        return $this->setData(self::MIN_DATE, $min_date);
    }

    /**
     * Get max_date
     *
     * @return string
     */
    public function getMaxDate()
    {

        return $this->getData(self::MAX_DATE);
    }

    /**
     * Set max_date
     *
     * @param string $max_date
     *
     * @return FormElements \Licentia\Forms\Api\Data\FormElementsInterface
     */
    public function setMaxDate($max_date)
    {

        return $this->setData(self::MAX_DATE, $max_date);
    }

    /**
     * Get html
     *
     * @return string
     */
    public function getHtml()
    {

        return $this->getData(self::HTML);
    }

    /**
     * Set html
     *
     * @param string $html
     *
     * @return \Licentia\Forms\Api\Data\FormElementsInterface
     */
    public function setHtml($html)
    {

        return $this->setData(self::HTML, $html);
    }

    /**
     * Get params
     *
     * @return string
     */
    public function getParams()
    {

        return $this->getData(self::PARAMS);
    }

    /**
     * Set params
     *
     * @param string $params
     *
     * @return \Licentia\Forms\Api\Data\FormElementsInterface
     */
    public function setParams($params)
    {

        return $this->setData(self::PARAMS, $params);
    }

    /**
     * Get extensions
     *
     * @return string
     */
    public function getExtensions()
    {

        return $this->getData(self::EXTENSIONS);
    }

    /**
     * Set extensions
     *
     * @param string $extensions
     *
     * @return \Licentia\Forms\Api\Data\FormElementsInterface
     */
    public function setExtensions($extensions)
    {

        return $this->setData(self::EXTENSIONS, $extensions);
    }

    /**
     * Get max_size
     *
     * @return string
     */
    public function getMaxSize()
    {

        return $this->getData(self::MAX_SIZE);
    }

    /**
     * Set max_size
     *
     * @param string $max_size
     *
     * @return \Licentia\Forms\Api\Data\FormElementsInterface
     */
    public function setMaxSize($max_size)
    {

        return $this->setData(self::MAX_SIZE, $max_size);
    }

    /**
     * Get email_validation
     *
     * @return string
     */
    public function getEmailValidation()
    {

        return $this->getData(self::EMAIL_VALIDATION);
    }

    /**
     * Set email_validation
     *
     * @param string $email_validation
     *
     * @return \Licentia\Forms\Api\Data\FormElementsInterface
     */
    public function setEmailValidation($email_validation)
    {

        return $this->setData(self::EMAIL_VALIDATION, $email_validation);
    }

    /**
     * Get link_expiration
     *
     * @return string
     */
    public function getLinkExpiration()
    {

        return $this->getData(self::LINK_EXPIRATION);
    }

    /**
     * Set link_expiration
     *
     * @param string $link_expiration
     *
     * @return \Licentia\Forms\Api\Data\FormElementsInterface
     */
    public function setLinkExpiration($link_expiration)
    {

        return $this->setData(self::LINK_EXPIRATION, $link_expiration);
    }

    /**
     * Get min_width
     *
     * @return string
     */
    public function getMinWidth()
    {

        return $this->getData(self::MIN_WIDTH);
    }

    /**
     * Set min_width
     *
     * @param string $minWidth
     *
     * @return \Licentia\Forms\Api\Data\FormElementsInterface
     */
    public function setMinWidth($minWidth)
    {

        return $this->setData(self::MIN_WIDTH, $minWidth);
    }

    /**
     * Get max_width
     *
     * @return string
     */
    public function getMaxWidth()
    {

        return $this->getData(self::MAX_WIDTH);
    }

    /**
     * Set max_width
     *
     * @param string $maxWidth
     *
     * @return \Licentia\Forms\Api\Data\FormElementsInterface
     */
    public function setMaxWidth($maxWidth)
    {

        return $this->setData(self::MAX_WIDTH, $maxWidth);
    }

    /**
     * Get min_height
     *
     * @return string
     */
    public function getMinHeight()
    {

        return $this->getData(self::MIN_HEIGHT);
    }

    /**
     * Set min_height
     *
     * @param string $minHeight
     *
     * @return \Licentia\Forms\Api\Data\FormElementsInterface
     */
    public function setMinHeight($minHeight)
    {

        return $this->setData(self::MIN_HEIGHT, $minHeight);
    }

    /**
     * Get max_height
     *
     * @return string
     */
    public function getMaxHeight()
    {

        return $this->getData(self::MAX_HEIGHT);
    }

    /**
     * Set max_height
     *
     * @param string $maxHeight
     *
     * @return \Licentia\Forms\Api\Data\FormElementsInterface
     */
    public function setMaxHeight($maxHeight)
    {

        return $this->setData(self::MAX_HEIGHT, $maxHeight);
    }

    /**
     * Get resize
     *
     * @return string
     */
    public function getResize()
    {

        return $this->getData(self::RESIZE);
    }

    /**
     * Set resize
     *
     * @param string $resize
     *
     * @return \Licentia\Forms\Api\Data\FormElementsInterface
     */
    public function setResize($resize)
    {

        return $this->setData(self::RESIZE, $resize);
    }

    /**
     * Get code
     *
     * @return string
     */
    public function getCode()
    {

        return $this->getData(self::CODE);
    }

    /**
     * Set resize
     *
     * @param string $code
     *
     * @return \Licentia\Forms\Api\Data\FormElementsInterface
     */
    public function setCode($code)
    {

        return $this->setData(self::CODE, $code);
    }

    /**
     * @param $encrypted
     *
     * @return $this
     */
    public function setEncrypted($encrypted)
    {

        return $this->setData('encrypted', $encrypted);
    }

    /**
     * @param $protected
     *
     * @return $this
     */
    public function setProtected($protected)
    {

        return $this->setData('protected', $protected);
    }

    /**
     * @return mixed
     */
    public function getEncrypted()
    {

        return $this->getData('encrypted');
    }

    /**
     * @return mixed
     */
    public function getProtected()
    {

        return $this->getData('protected');
    }

}
