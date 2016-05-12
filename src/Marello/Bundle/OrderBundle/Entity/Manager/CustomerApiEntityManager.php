<?php

namespace Marello\Bundle\OrderBundle\Entity\Manager;

use Oro\Bundle\SoapBundle\Entity\Manager\ApiEntityManager;

class CustomerApiEntityManager extends ApiEntityManager
{

    /**
     * {@inheritdoc}
     */
    protected function getSerializationConfig()
    {
        $addressConfig = [
            'exclusion_policy' => 'all',
            'fields'           => [
                'namePrefix'   => [],
                'firstName'    => [],
                'middleName'   => [],
                'lastName'     => [],
                'nameSuffix'   => [],
                'street'       => [],
                'street2'      => [],
                'city'         => [],
                'country'      => [],
                'region'       => [],
                'organization' => [],
                'postalCode'   => [],
                'phone'        => [],
            ],
        ];

        $config = [
            'exclusion_policy' => 'all',
            'fields'           => [
                'id'         => [],
                'namePrefix' => [],
                'firstName'  => [],
                'middleName' => [],
                'lastName'   => [],
                'nameSuffix' => [],
                'email'      => [],
                'address'    => $addressConfig,
                'addresses'  => $addressConfig,
                'createdAt'  => [],
                'updatedAt'  => [],
            ],
        ];

        return $config;
    }
}
