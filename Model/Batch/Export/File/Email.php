<?php
namespace Mageinn\Vendor\Model\Batch\Export\File;

/**
 * Class Email
 * @package Mageinn\Vendor\Model\Batch\Export\File
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
     * @var \Mageinn\Vendor\Helper\Data
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
     * @param \Mageinn\Vendor\Helper\Data $helper
     * @param \Magento\Store\Model\StoreManagerInterface $manager
     */
    public function __construct(
        \Mageinn\Vendor\Helper\Data $helper,
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
     * @param string $template
     * @return Email
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
     * @param string $email
     * @param string $name
     * @return Email
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
     * @param string $fileName
     * @return Email
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
     * @param string $subject
     * @return Email
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
     * @param string $body
     * @return Email
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
     * @param string $ccRecipient
     * @return Email
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
     * @param string $bccRecipient
     * @return Email
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
     * @return Email
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
     * @return Email
     */
    public function addTemplateVar($name, $value)
    {
        $this->templateVars[$name] = $value;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getNotes()
    {
        return implode(PHP_EOL, $this->notes);
    }

    /**
     * @param mixed $notes
     * @return Email
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
     * Clears the notes array
     */
    public function clearNotes()
    {
        $this->notes = [];
    }

    /**
     * Prepares the email components for a vendor
     *
     * @param $vendor
     * @param $contents
     * @param $transportBuilder
     * @return bool
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
     * Prepares the email components for a general email
     *
     * @param $recipient
     * @param $componentsArray
     * @param $contents
     * @param $transportBuilder
     * @return bool
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
     * Creates an email message based on the set components
     *
     * @param \Mageinn\Core\Magento\Mail\Template\TransportBuilder $transportBuilder
     * @param $contents
     * @return bool|\Magento\Framework\Mail\TransportInterface
     */
    protected function _prepareTransport($transportBuilder, $contents)
    {
        $transport = $transportBuilder
            ->setTemplateIdentifier($this->getTemplate())
            ->setTemplateOptions([
                'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                'store' => $this->storeManager->getStore()->getId()])
            ->setTemplateVars($this->getTemplateVars());

        // If recipient or sender are not set, the email will not be sent
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
     * Returns the global config value for the requested email components
     *
     * @param $settingName
     * @return bool|mixed
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
        // If there is a string result, we trim it so that there are no preceding or trailing spaces
        return $setting ? trim($setting) : $setting;
    }

    /**
     * Checks if an email is set properly
     *
     * @param $email
     * @param $settingName
     * @return bool|mixed
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
     * Returns an email component from the components array
     * If the component is not in the array, it will try to get the default value
     *
     * @param $componentArray
     * @param $settingName
     * @param bool $isEmailFormat
     * @return bool|mixed
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
     * Checks the emails if there are multiple emails for a setting
     *
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
     * Checks a single email string to be valid and adds a note if it is not
     *
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
