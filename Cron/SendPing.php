<?php

declare(strict_types=1);

namespace Lotsofpixels\CronjobMonitor\Cron;

use Exception;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\HTTP\Adapter\Curl;
use Psr\Log\LoggerInterface;
use Magento\Framework\Validator\Url;

/**
 *
 */
class SendPing
{
    /**
     * @var ScopeConfigInterface
     */
    protected $storeConfig;
    /**
     * @var Curl
     */
    protected $curlClient;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var Url
     */
    protected $url;


    /**
     * @param ScopeConfigInterface $storeConfig
     * @param Curl $curl
     * @param LoggerInterface $logger
     * @param Url $url
     */
    public function __construct(
        ScopeConfigInterface $storeConfig,
        Curl $curl,
        LoggerInterface $logger,
        Url $url


    ) {
        $this->storeConfig = $storeConfig;
        $this->curlClient = $curl;
        $this->logger = $logger;
        $this->url = $url;
    }

    /**
     * Cron entry point
     */
    public function execute(): void
    {
        if ($this->storeConfig->getValue('cronjobmonitor/general/enabled')) {
            $pingUrl = $this->storeConfig->getValue('cronjobmonitor/general/ping_url');
            if ($this->url->isValid($pingUrl)) {
                try {
                    $this->curlClient->setOptions(['timeout' => 5]);
                    $this->curlClient->write('GET', $pingUrl);
                    $this->curlClient->read();
                } catch (Exception $ex) {
                    $this->logger->critical($ex->getMessage());
                }
            }
        }
    }

}
