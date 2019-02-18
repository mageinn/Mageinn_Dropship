<?php
/**
 * Mageinn
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageinn.com license that is
 * available through the world-wide-web at this URL:
 * https://mageinn.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 */
namespace Mageinn\Dropship\Model\Batch\Export\File;

/**
 * Class Email
 * @package Mageinn\Dropship\Model\Batch\Export\File
 */
class Email
{
    const SETTING_NAME_FROM = 'from';
    const SETTING_NAME_SUBJECT = 'subject';
    const SETTING_NAME_FILE_NAME = 'filename';

    /**
     * @var string
     */
    protected $template;

    /**
     * @var string
     */
    protected $recipient;

    /**
     * @var array
     */
    protected $sender;

    /**
     * @var string
     */
    protected $fileName;

    /**
     * @var string
     */
    protected $subject;

    /**
     * @var string
     */
    protected $body;

    /**
     * @var string
     */
    protected $ccRecipient;

    /**
     * @var string
     */
    protected $bccRecipient;

    /**
     * @var array
     */
    protected $templateVars;

    /**
     * @var \Mageinn\Dropship\Helper\Data
     */
    protected $vendorHelper;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var array
     */
    protected $notes;

    /**
     * Email constructor.
     * @param \Mageinn\Dropship\Helper\Data $helper
     * @param \Magento\Store\Model\StoreManagerInterface $manager
     */
    public function __construct(
        \Mageinn\Dropship\Helper\Data $helper,
        \Magento\Store\Model\StoreManagerInterface $manager
    ) {
        $this->vendorHelper = $helper;
        $this->storeManager = $manager;
        $this->notes = [];
    }

    /**
     * @return string
     */
    public function getTemplate()
    {
        return $this->template;
    }

    /**
     * @param $template
     * @return $this
     */
    public function setTemplate($template)
    {
        $this->template = $template;
        return $this;
    }

    /**
     * @return string
     */
    public function getRecipient()
    {
        return $this->recipient;
    }

    /**
     * @param string $recipient
     * @return Email
     */
    public function setRecipient($recipient)
    {
        $this->recipient = $recipient;
        return $this;
    }

    /**
     * @return array
     */
    public function getSender()
    {
        return $this->sender;
    }

    /**
     * @param $email
     * @param string $name
     * @return $this
     */
    public function setSender($email, $name = '')
    {
        $this->sender['email'] = $email;
        $this->sender['name'] = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function getFileName()
    {
        return $this->fileName;
    }

    /**
     * @param $fileName
     * @return $this
     */
    public function setFileName($fileName)
    {
        $this->fileName = $fileName;
        return $this;
    }

    /**
     * @return string
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * @param $subject
     * @return $this
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;
        return $this;
    }

    /**
     * @return string
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * @param $body
     * @return $this
     */
    public function setBody($body)
    {
        $this->body = $body;
        return $this;
    }

    /**
     * @return string
     */
    public function getCcRecipient()
    {
        return $this->ccRecipient;
    }

    /**
     * @param $ccRecipient
     * @return $this
     */
    public function setCcRecipient($ccRecipient)
    {
        $this->ccRecipient = $ccRecipient;
        return $this;
    }

    /**
     * @return string
     */
    public function getBccRecipient()
    {
        return $this->bccRecipient;
    }

    /**
     * @param $bccRecipient
     * @return $this
     */
    public function setBccRecipient($bccRecipient)
    {
        $this->bccRecipient = $bccRecipient;
        return $this;
    }

    /**
     * @return array
     */
    public function getTemplateVars()
    {
        return $this->templateVars;
    }

    /**
     * @return $this
     */
    public function setTemplateVars()
    {
        $this->templateVars['body'] = $this->getBody();
        $this->templateVars['subject'] = $this->getSubject();
        return $this;
    }

    /**
     * @param $name
     * @param $value
     * @return $this
     */
    public function addTemplateVar($name, $value)
    {
        $this->templateVars[$name] = $value;
        return $this;
    }

    /**
     * @return string
     */
    public function getNotes()
    {
        return implode(PHP_EOL, $this->notes);
    }

    /**
     * @param $notes
     * @return $this
     */
    public function setNotes($notes)
    {
        $this->notes = $notes;
        return $this;
    }

    /**
     * @param $note
     * @return $this
     */
    public function addNote($note)
    {
        $this->notes[] = $note;
        return $this;
    }

    /**
     * @return void
     */
    public function clearNotes()
    {
        $this->notes = [];
    }

    /**
     * @param $vendor
     * @param $contents
     * @param $transportBuilder
     * @return bool
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function prepareEmailForVendor($vendor, $contents, $transportBuilder)
    {
        $this->setRecipient($vendor->getEmail())
            ->setTemplate($this->vendorHelper->getBatchOrderExportConfig('notification_template'))
            ->setSender($this->vendorHelper->getBatchOrderExportConfig('email_sender'))
            ->setFileName($this->vendorHelper->getBatchOrderExportConfig('file_name'))
            ->setSubject($this->vendorHelper->getBatchOrderExportConfig('email_subject'))
            ->setBody('')
            ->setTemplateVars()
            ->setCcRecipient('')
            ->setBccRecipient('');

        return $this->_prepareTransport($transportBuilder, $contents);
    }

    /**
     * @param $recipient
     * @param $componentsArray
     * @param $contents
     * @param $transportBuilder
     * @return bool
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function prepareGeneralEmail($recipient, $componentsArray, $contents, $transportBuilder)
    {
        $this->setRecipient($this->_checkEmail($recipient, 'recipient'))
            ->setTemplate($this->vendorHelper->getBatchOrderExportConfig('notification_template'))
            ->setSender($this->_getEmailComponent($componentsArray, self::SETTING_NAME_FROM, true))
            ->setFileName($this->_getEmailComponent($componentsArray, self::SETTING_NAME_FILE_NAME))
            ->setSubject($this->_getEmailComponent($componentsArray, self::SETTING_NAME_SUBJECT))
            ->setBody($this->_getEmailComponent($componentsArray, 'body'))
            ->setTemplateVars()
            ->setCcRecipient($this->_getEmailComponent($componentsArray, 'cc', true))
            ->setBccRecipient($this->_getEmailComponent($componentsArray, 'bcc', true));

        if ($this->getFileName() && $this->getFileName() == '-') {
            $this->addTemplateVar('body', $contents);
        }

        return $this->_prepareTransport($transportBuilder, $contents);
    }

    /**
     * @param $transportBuilder
     * @param $contents
     * @return bool
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    protected function _prepareTransport($transportBuilder, $contents)
    {
        $transport = $transportBuilder
            ->setTemplateIdentifier($this->getTemplate())
            ->setTemplateOptions([
                'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                'store' => $this->storeManager->getStore()->getId()])
            ->setTemplateVars($this->getTemplateVars());

        if ($this->getRecipient()) {
            $transport->addTo($this->getRecipient());
        } else {
            return false;
        }
        $sender = $this->getSender();
        if (isset($sender['email']) && $sender['email']) {
            $transport->setFrom($this->getSender());
        } else {
            return false;
        }
        if ($this->getFileName() && $this->getFileName() != '-') {
            $transport->addAttachment($contents, $this->getFileName());
        }
        if ($this->getCcRecipient()) {
            $transport->addCc($this->getCcRecipient());
        }
        if ($this->getBccRecipient()) {
            $transport->addBcc($this->getBccRecipient());
        }

        return $transport->getTransport();
    }

    /**
     * @param $settingName
     * @return bool|mixed|string
     */
    protected function _getDefaultValue($settingName)
    {
        switch ($settingName) {
            case self::SETTING_NAME_FROM:
                $setting = $this->vendorHelper->getBatchOrderExportConfig('email_sender');
                break;
            case self::SETTING_NAME_SUBJECT:
                $setting = $this->vendorHelper->getBatchOrderExportConfig('email_subject');
                break;
            case self::SETTING_NAME_FILE_NAME:
                $setting = $this->vendorHelper->getBatchOrderExportConfig('file_name');
                break;
            default:
                $setting = false;
                break;
        }
        return $setting ? trim($setting) : $setting;
    }

    /**
     * @param $email
     * @param $settingName
     * @return array|bool|string
     */
    protected function _checkEmail($email, $settingName)
    {
        $result = false;
        if (is_array($email)) {
            if (isset($email[$settingName]) && $email[$settingName]) {
                if (is_array($email[$settingName])) {
                    $result = $this->_checkMultipleEmails($email[$settingName], $settingName);
                } else {
                    $result = $this->_checkSingleEmail($email[$settingName], $settingName);
                }
            }
        } else {
            $result = $this->_checkSingleEmail($email, $settingName);
        }

        return $result;
    }

    /**
     * @param $componentArray
     * @param $settingName
     * @param bool $isEmailFormat
     * @return bool|mixed|string
     */
    protected function _getEmailComponent($componentArray, $settingName, $isEmailFormat = false)
    {
        $result = false;
        if ($isEmailFormat) {
            $result = $this->_checkEmail($componentArray, $settingName);
        } else {
            if (isset($componentArray[$settingName]) && $componentArray[$settingName]) {
                $result = trim($componentArray[$settingName]);
            }
        }

        if (!$result) {
            $result = $this->_getDefaultValue($settingName);
        }

        return $result;
    }

    /**
     * @param $emailArray
     * @param $settingName
     * @return array
     */
    protected function _checkMultipleEmails($emailArray, $settingName)
    {
        $validEmails = [];

        foreach ($emailArray as $email) {
            if ($validEmail = $this->_checkSingleEmail($email, $settingName)) {
                $validEmails[] = $validEmail;
            }
        }

        return $validEmails;
    }

    /**
     * @param $email
     * @param $settingName
     * @return bool|string
     */
    protected function _checkSingleEmail($email, $settingName)
    {
        $result = false;
        $trimmedEmail = trim($email);
        if (filter_var($trimmedEmail, FILTER_VALIDATE_EMAIL)) {
            $result = $trimmedEmail;
        } else {
            $this->addNote(sprintf('"%s" setting "%s" is not set properly.', $settingName, $email));
        }

        return $result;
    }
}
