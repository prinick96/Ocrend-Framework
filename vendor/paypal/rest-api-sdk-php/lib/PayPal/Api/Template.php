<?php

namespace PayPal\Api;

use PayPal\Common\PayPalModel;

/**
 * Class Template
 *
 * Invoicing Template
 *
 * @package PayPal\Api
 *
 * @property string template_id
 * @property string name
 * @property bool default
 * @property \PayPal\Api\TemplateData template_data
 * @property \PayPal\Api\TemplateSettings[] settings
 * @property string unit_of_measure
 * @property bool custom
 */
class Template extends PayPalModel
{
    /**
     * Unique identifier id of the template.
     *
     * @param string $template_id
     * 
     * @return $this
     */
    public function setTemplateId($template_id)
    {
        $this->template_id = $template_id;
        return $this;
    }

    /**
     * Unique identifier id of the template.
     *
     * @return string
     */
    public function getTemplateId()
    {
        return $this->template_id;
    }

    /**
     * Name of the template.
     *
     * @param string $name
     * 
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Name of the template.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Indicates that this template is merchant's default. There can be only one template which can be a default.
     *
     * @param bool $default
     * 
     * @return $this
     */
    public function setDefault($default)
    {
        $this->default = $default;
        return $this;
    }

    /**
     * Indicates that this template is merchant's default. There can be only one template which can be a default.
     *
     * @return bool
     */
    public function getDefault()
    {
        return $this->default;
    }

    /**
     * Customized invoice data which is saved as template
     *
     * @param \PayPal\Api\TemplateData $template_data
     * 
     * @return $this
     */
    public function setTemplateData($template_data)
    {
        $this->template_data = $template_data;
        return $this;
    }

    /**
     * Customized invoice data which is saved as template
     *
     * @return \PayPal\Api\TemplateData
     */
    public function getTemplateData()
    {
        return $this->template_data;
    }

    /**
     * Settings for each template
     *
     * @param \PayPal\Api\TemplateSettings[] $settings
     * 
     * @return $this
     */
    public function setSettings($settings)
    {
        $this->settings = $settings;
        return $this;
    }

    /**
     * Settings for each template
     *
     * @return \PayPal\Api\TemplateSettings[]
     */
    public function getSettings()
    {
        return $this->settings;
    }

    /**
     * Append Settings to the list.
     *
     * @param \PayPal\Api\TemplateSettings $templateSettings
     * @return $this
     */
    public function addSetting($templateSettings)
    {
        if (!$this->getSettings()) {
            return $this->setSettings(array($templateSettings));
        } else {
            return $this->setSettings(
                array_merge($this->getSettings(), array($templateSettings))
            );
        }
    }

    /**
     * Remove Settings from the list.
     *
     * @param \PayPal\Api\TemplateSettings $templateSettings
     * @return $this
     */
    public function removeSetting($templateSettings)
    {
        return $this->setSettings(
            array_diff($this->getSettings(), array($templateSettings))
        );
    }

    /**
     * Unit of measure for the template, possible values are Quantity, Hours, Amount.
     *
     * @param string $unit_of_measure
     * 
     * @return $this
     */
    public function setUnitOfMeasure($unit_of_measure)
    {
        $this->unit_of_measure = $unit_of_measure;
        return $this;
    }

    /**
     * Unit of measure for the template, possible values are Quantity, Hours, Amount.
     *
     * @return string
     */
    public function getUnitOfMeasure()
    {
        return $this->unit_of_measure;
    }

    /**
     * Indicates whether this is a custom template created by the merchant. Non custom templates are system generated
     *
     * @param bool $custom
     * 
     * @return $this
     */
    public function setCustom($custom)
    {
        $this->custom = $custom;
        return $this;
    }

    /**
     * Indicates whether this is a custom template created by the merchant. Non custom templates are system generated
     *
     * @return bool
     */
    public function getCustom()
    {
        return $this->custom;
    }

}
