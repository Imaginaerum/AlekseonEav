<?php
/**
 * Copyright © Alekseon sp. z o.o.
 * http://www.alekseon.com/
 */

namespace Alekseon\AlekseonEav\Block\Adminhtml\Attribute\Edit\Tab;

use Magento\Backend\Block\Widget\Form\Generic;
use Alekseon\AlekseonEav\Model\Adminhtml\System\Config\Source\InputType;
use Alekseon\AlekseonEav\Model\Adminhtml\System\Config\Source\Scopes;

/**
 * Class General
 * @package Alekseon\AlekseonEav\Block\Adminhtml\Attribute\Edit
 */
abstract class General extends Generic
{

    /**
     * @return mixed
     */
    private $attribute;
    /**
     * @var InputType
     */
    private $inputTypeSource;
    /**
     * @var Scopes
     */
    private $scopesSource;
    /**
     * @var \Magento\Config\Model\Config\Source\Yesno
     */
    private $yesNoSource;
    /**
     * @var \Alekseon\AlekseonEav\Model\Attribute\InputTypeRepository
     */
    private $inputTypeRepository;

    /**
     * General constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Magento\Config\Model\Config\Source\Yesno $yesNoSource
     * @param \Alekseon\AlekseonEav\Model\Attribute\InputTypeRepository $inputTypeRepository
     * @param InputType $inputTypeSource
     * @param Scopes $scopesSource
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Config\Model\Config\Source\Yesno $yesNoSource,
        \Alekseon\AlekseonEav\Model\Attribute\InputTypeRepository $inputTypeRepository,
        InputType $inputTypeSource,
        Scopes $scopesSource,
        array $data = []
    ) {
        $this->inputTypeSource = $inputTypeSource;
        $this->yesNoSource = $yesNoSource;
        $this->scopesSource = $scopesSource;
        $this->inputTypeRepository = $inputTypeRepository;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * @return mixed
     */
    public function getAttributeObject()
    {
        if (null === $this->attribute) {
            return $this->_coreRegistry->registry('current_attribute');
        }
        return $this->attribute;
    }

    /**
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _prepareForm() // @codingStandardsIgnoreLine
    {
        $attributeObject = $this->getAttributeObject();

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create(
            [
                'data' => [
                    'id' => 'edit_form',
                    'action' => $this->getData('action'),
                    'method' => 'post'
                ]
            ]
        );

        $baseFieldset = $form->addFieldset('base_fieldset', ['legend' => __('Attribute Properties')]);

        if ($attributeObject->getAttributeId()) {
            $baseFieldset->addField('id', 'hidden', ['name' => 'id']);
        }

        $baseFieldset->addField(
            'frontend_label',
            'text',
            [
                'name' => 'frontend_label',
                'label' => __('Frontend Label'),
                'title' => __('Frontend label'),
                'required' => true,
            ]
        );

        $validateClass = sprintf(
            'validate-code validate-length maximum-length-%d',
            \Alekseon\AlekseonEav\Model\Attribute::ATTRIBUTE_CODE_MAX_LENGTH
        );

        $baseFieldset->addField(
            'attribute_code',
            'text',
            [
                'name' => 'attribute_code',
                'label' => __('Attribute Code'),
                'title' => __('Attribute Code'),
                'note' => __(
                    'This is used internally. Make sure you don\'t use spaces or more than %1 symbols.',
                    \Alekseon\AlekseonEav\Model\Attribute::ATTRIBUTE_CODE_MAX_LENGTH
                ),
                'class' => $validateClass,
                'required' => true
            ]
        );

        $baseFieldset->addField(
            'frontend_input',
            'select',
            [
                'name' => 'frontend_input',
                'label' => __('Input Type for Store Owner'),
                'title' => __('Input Type for Store Owner'),
                'value' => InputType::INPUT_TYPE_TEXT,
                'values' => $this->inputTypeSource->toOptionArray()
            ]
        );

        $baseFieldset->addField(
            'scope',
            'select',
            [
                'name' => 'scope',
                'label' => __('Scope'),
                'title' => __('Scope'),
                'value' => SCOPES::SCOPE_GLOBAL,
                'values' => $this->scopesSource->toOptionArray()
            ]
        );

        $baseFieldset->addField(
            'is_required',
            'select',
            [
                'name' => 'is_required',
                'label' => __('Is Required'),
                'title' => __('Is Required'),
                'values' => $this->yesNoSource->toOptionArray()
            ]
        );

        $baseFieldset->addField(
            'visible_in_grid',
            'select',
            [
                'name' => 'visible_in_grid',
                'label' => __('Visible In Grid'),
                'title' => __('Visible In Grid'),
                'values' => $this->yesNoSource->toOptionArray()
            ]
        );

        $baseFieldset->addField(
            'sort_order',
            'text',
            [
                'name' => 'sort_order',
                'label' => __('Sort Order'),
                'title' => __('Sort Order'),
                'value' => '0',
            ]
        );

        $baseFieldset->addField(
            'note',
            'text',
            [
                'name' => 'note',
                'label' => __('Note'),
                'title' => __('Note'),
            ]
        );

        if ($attributeObject->getId()) {
            $form->getElement('attribute_code')->setDisabled(true);
            $form->getElement('frontend_input')->setDisabled(true);
        }

        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * Initialize form fileds values
     *
     * @return $this
     */
    protected function _initFormValues() // @codingStandardsIgnoreLine
    {
        $this->getForm()->addValues($this->getAttributeObject()->getData());
        return parent::_initFormValues();
    }
}