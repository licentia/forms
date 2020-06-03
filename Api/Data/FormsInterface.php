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
 * @modified   03/06/20, 16:18 GMT
 *
 */

namespace Licentia\Forms\Api\Data;

/**
 * Interface FormsInterface
 *
 * @package Licentia\Forms\Api\Data
 */
interface FormsInterface
{

    /**
     *
     */
    const ENABLE_TEMPLATE = 'enable_template';

    /**
     *
     */
    const MANAGE_SUBSCRIPTION = 'manage_subscription';

    /**
     *
     */
    const ENTRY_TYPE = 'entry_type';

    /**
     *
     */
    const CODE = 'code';

    /**
     *
     */
    const NOTIFICATIONS = 'notifications';

    /**
     *
     */
    const TEMPLATE = 'template';

    /**
     *
     */
    const CAN_EDIT = 'can_edit';

    /**
     *
     */
    const TO_DATE = 'to_date';

    /**
     *
     */
    const MAX_ENTRIES = 'max_entries';

    /**
     *
     */
    const ACTIVE = 'is_active';

    /**
     *
     */
    const REGISTERED_ONLY = 'registered_only';

    /**
     *
     */
    const TITLE = 'title';

    /**
     *
     */
    const STORE_ID = 'store_id';

    /**
     *
     */
    const SUCCESS_PAGE = 'success_page';

    /**
     *
     */
    const NAME = 'name';

    /**
     *
     */
    const DESCRIPTION = 'description';

    /**
     *
     */
    const ENTRIES = 'entries';

    /**
     *
     */
    const FROM_DATE = 'from_date';

    /**
     *
     */
    const SUBMIT_LABEL = 'submit_label';

    /**
     *
     */
    const UPDATE_LABEL = 'update_label';

    /**
     *
     */
    const FORM_ID = 'form_id';

    /**
     *
     */
    const SUBSCRIBERS = 'subscribers';

    /**
     *
     */
    const SUCCESS_MESSAGE = 'success_message';

    /**
     *
     */
    const CSS_CLASS = 'css_class';

    /**
     * @return string
     */
    public function getFormId();

    /**
     * Set form_id
     *
     * @param string $form_id
     *
     * @return FormsInterface
     */

    public function setFormId($form_id);

    /**
     * Get name
     *
     * @return string|null
     */

    public function getName();

    /**
     * Set name
     *
     * @param string $name
     *
     * @return FormsInterface
     */

    public function setName($name);

    /**
     * Get title
     *
     * @return string|null
     */

    public function getTitle();

    /**
     * Set title
     *
     * @param string $title
     *
     * @return FormsInterface
     */

    public function setTitle($title);

    /**
     * Get description
     *
     * @return string|null
     */

    public function getDescription();

    /**
     * Set description
     *
     * @param string $description
     *
     * @return FormsInterface
     */

    public function setDescription($description);

    /**
     * Get active
     *
     * @return string|null
     */

    public function getActive();

    /**
     * Set active
     *
     * @param string $active
     *
     * @return FormsInterface
     */

    public function setActive($active);

    /**
     * Get can_edit
     *
     * @return string|null
     */

    public function getCanEdit();

    /**
     * Set can_edit
     *
     * @param string $can_edit
     *
     * @return FormsInterface
     */

    public function setCanEdit($can_edit);

    /**
     * Get from_date
     *
     * @return string|null
     */

    public function getFromDate();

    /**
     * Set from_date
     *
     * @param string $from_date
     *
     * @return FormsInterface
     */

    public function setFromDate($from_date);

    /**
     * Get to_date
     *
     * @return string|null
     */

    public function getToDate();

    /**
     * Set to_date
     *
     * @param string $to_date
     *
     * @return FormsInterface
     */

    public function setToDate($to_date);

    /**
     * Get registered_only
     *
     * @return string|null
     */

    public function getRegisteredOnly();

    /**
     * Set registered_only
     *
     * @param string $registered_only
     *
     * @return FormsInterface
     */

    public function setRegisteredOnly($registered_only);

    /**
     * Get entries
     *
     * @return string|null
     */

    public function getEntries();

    /**
     * Set entries
     *
     * @param string $entries
     *
     * @return FormsInterface
     */

    public function setEntries($entries);

    /**
     * Get subscribers
     *
     * @return string|null
     */

    public function getSubscribers();

    /**
     * Set subscribers
     *
     * @param string $subscribers
     *
     * @return FormsInterface
     */

    public function setSubscribers($subscribers);

    /**
     * Get max_entries
     *
     * @return string|null
     */

    public function getMaxEntries();

    /**
     * Set max_entries
     *
     * @param string $max_entries
     *
     * @return FormsInterface
     */

    public function setMaxEntries($max_entries);

    /**
     * Get submit_label
     *
     * @return string|null
     */

    public function getSubmitLabel();

    /**
     * Set submit_label
     *
     * @param string $submit_label
     *
     * @return FormsInterface
     */

    public function setSubmitLabel($submit_label);

    /**
     * Get update_label
     *
     * @return string|null
     */

    public function getUpdateLabel();

    /**
     * Set update_label
     *
     * @param string $update_label
     *
     * @return FormsInterface
     */

    public function setUpdateLabel($update_label);

    /**
     * Get css_class
     *
     * @return string|null
     */

    public function getCssClass();

    /**
     * Set css_class
     *
     * @param string $css_class
     *
     * @return FormsInterface
     */

    public function setCssClass($css_class);

    /**
     * Get success_page
     *
     * @return string|null
     */

    public function getSuccessPage();

    /**
     * Set success_page
     *
     * @param string $success_page
     *
     * @return FormsInterface
     */

    public function setSuccessPage($success_page);

    /**
     * Get success_message
     *
     * @return string|null
     */

    public function getSuccessMessage();

    /**
     * Set success_message
     *
     * @param string $success_message
     *
     * @return FormsInterface
     */

    public function setSuccessMessage($success_message);

    /**
     * Get enable_template
     *
     * @return string|null
     */

    public function getEnableTemplate();

    /**
     * Set $enable_template
     *
     * @param string $enable_template
     *
     * @return FormsInterface
     */

    public function setEnableTemplate($enable_template);

    /**
     * Get success_message
     *
     * @return string|null
     */

    public function getTemplate();

    /**
     * Set success_message
     *
     * @param string $template
     *
     * @return FormsInterface
     */

    public function setTemplate($template);

    /**
     * Get manage_subscription
     *
     * @return string|null
     */

    public function getManageSubscription();

    /**
     * Set manage_subscription
     *
     * @param string $subscription
     *
     * @return FormsInterface
     */

    public function setManageSubscription($subscription);

    /**
     * Get entry_type
     *
     * @return string|null
     */

    public function getEntryType();

    /**
     * Set entry_type
     *
     * @param string $entryType
     *
     * @return FormsInterface
     */

    public function setEntryType($entryType);

    /**
     * Get code
     *
     * @return string
     */
    public function getCode();

    /**
     * Set code
     *
     * @param string $code
     *
     * @return FormsInterface
     */
    public function setCode($code);

    /**
     * Get notifications emails
     *
     * @return string
     */
    public function getNotifications();

    /**
     * Set notifications emails
     *
     * @param string $emails
     *
     * @return FormsInterface
     */
    public function setNotifications($emails);
}
