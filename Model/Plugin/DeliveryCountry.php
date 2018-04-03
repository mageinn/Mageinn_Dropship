<?php

namespace Mageinn\Vendor\Model\Plugin;

/**
 * Class DeliveryCountry
 * @package Mageinn\Vendor\Model\Plugin
 */
class DeliveryCountry
{
    const PARAM_DELIVERY = '___delivery';

    const COOKIE_DELIVERY = 'delivery';

    /**
     * @var \Magento\Framework\Stdlib\CookieManagerInterface
     */
    protected $cookieManager;

    /**
     * @var \Magento\Framework\Stdlib\Cookie\CookieMetadataFactory
     */
    protected $cookieMetadata;

    /**
     * @var \Magento\Framework\Session\SessionManagerInterface
     */
    protected $sessionManager;

    /**
     * @var \Magento\Framework\App\Http\Context
     */
    protected $httpContext;

    /**
     * DeliveryCountry constructor.
     * @param \Magento\Framework\Stdlib\CookieManagerInterface $cookieManager
     * @param \Magento\Framework\Stdlib\Cookie\CookieMetadataFactory $cookieMetadata
     * @param \Magento\Framework\Session\SessionManagerInterface $sessionManager
     * @param \Magento\Framework\App\Http\Context $httpContext
     */
    public function __construct(
        \Magento\Framework\Stdlib\CookieManagerInterface $cookieManager,
        \Magento\Framework\Stdlib\Cookie\CookieMetadataFactory $cookieMetadata,
        \Magento\Framework\Session\SessionManagerInterface $sessionManager,
        \Magento\Framework\App\Http\Context $httpContext
    ) {
        $this->cookieManager = $cookieManager;
        $this->cookieMetadata = $cookieMetadata;
        $this->sessionManager = $sessionManager;
        $this->httpContext  = $httpContext;
    }

    /**
     * @param \Magento\Framework\App\FrontController $subject
     * @param \Magento\Framework\App\RequestInterface $request
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Stdlib\Cookie\CookieSizeLimitReachedException
     * @throws \Magento\Framework\Stdlib\Cookie\FailureToSendException
     */
    public function beforeDispatch(
        \Magento\Framework\App\FrontController $subject,
        \Magento\Framework\App\RequestInterface $request
    ) {
        $subject; // hack phpcs
        $deliveryParam = $request->getParam(self::PARAM_DELIVERY);
        $deliveryCookie = $this->cookieManager->getCookie(self::COOKIE_DELIVERY);
        $deliveryCountry = $deliveryParam ? $deliveryParam : $deliveryCookie;

        if (isset($deliveryParam) && $deliveryParam !== $deliveryCookie) {
            $metadata = $this->cookieMetadata
                ->createPublicCookieMetadata()
                ->setDurationOneYear()
                ->setPath($this->sessionManager->getCookiePath())
                ->setDomain($this->sessionManager->getCookieDomain());

            $this->cookieManager->setPublicCookie(self::COOKIE_DELIVERY, $deliveryCountry, $metadata);
        }

        $this->httpContext->setValue(
            self::COOKIE_DELIVERY,
            $deliveryCountry,
            ''
        );
    }
}
