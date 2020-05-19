<?php

namespace Marello\Bundle\Magento2Bundle\Connector;

use Marello\Bundle\Magento2Bundle\Transport\RestTransport;
use Oro\Bundle\ImportExportBundle\Context\ContextInterface;
use Oro\Bundle\IntegrationBundle\Provider\AbstractConnector;

abstract class AbstractMagento2Connector extends AbstractConnector
{
    /**
     * @var RestTransport
     */
    protected $transport;

    /**
     * {@inheritdoc}
     */
    protected function initializeFromContext(ContextInterface $context)
    {
        parent::initializeFromContext($context);
        $context->setValue('channel', $this->channel);
    }

    /**
     * {@inheritdoc}
     */
    protected function validateConfiguration()
    {
        if (!$this->transport instanceof RestTransport) {
            throw new \LogicException(
                'Option "transport" should be "' . RestTransport::class . '"'
            );
        }
    }
}
