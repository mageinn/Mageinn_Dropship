<?php
namespace Mageinn\Vendor\Magento\Mail\Template;

/**
 * Class TransportBuilder
 * @package Mageinn\Vendor\Magento\Mail\Template
 */
class TransportBuilder extends \Magento\Framework\Mail\Template\TransportBuilder
{
    /**
     * Adds an attachment to the email
     *
     * @param $body
     * @param null $filename
     * @param string $mimeType
     * @param string $disposition
     * @param string $encoding
     * @return $this
     */
    public function addAttachment(
        $body,
        $filename = null,
        $mimeType = \Zend_Mime::TYPE_TEXT,
        $disposition = \Zend_Mime::DISPOSITION_ATTACHMENT,
        $encoding = \Zend_Mime::ENCODING_BASE64
    ) {
        $this->message->createAttachment($body, $mimeType, $disposition, $encoding, $filename);

        return $this;
    }
}
