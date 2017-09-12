<?php

namespace MarelloEnterprise\Bundle\AddressBundle\Distance\Chain\Element\StraightLine;

use Marello\Bundle\AddressBundle\Entity\MarelloAddress;
use MarelloEnterprise\Bundle\AddressBundle\Distance\Chain\Element\AbstractAddressesDistanceCalculatorChainElement;
use MarelloEnterprise\Bundle\AddressBundle\Entity\MarelloEnterpriseAddress;
use Oro\Bundle\EntityBundle\ORM\DoctrineHelper;

class StraightLineAddressesDistanceCalculatorChainElement extends AbstractAddressesDistanceCalculatorChainElement
{
    /**
     * @var DoctrineHelper
     */
    protected $doctrineHelper;

    /**
     * @param DoctrineHelper $doctrineHelper
     */
    public function __construct(DoctrineHelper $doctrineHelper)
    {
        $this->doctrineHelper = $doctrineHelper;
    }
    
    /**
     * {@inheritdoc}
     */
    protected function getDistance(
        MarelloAddress $originAddress,
        MarelloAddress $destinationAddress,
        $unit = 'metric'
    ) {
        $repository = $this->doctrineHelper
            ->getEntityManagerForClass(MarelloEnterpriseAddress::class)
            ->getRepository(MarelloEnterpriseAddress::class);
        $originGeocodedAddress = $repository->findOneBy(['address' => $originAddress]);
        $destinationGeocodedAddress = $repository->findOneBy(['address' => $destinationAddress]);
        $this->checkCoordinates([$originGeocodedAddress, $destinationGeocodedAddress]);
        
        
        $lat1 = $originGeocodedAddress->getLatitude();
        $lon1 = $originGeocodedAddress->getLongitude();
        $lat2 = $destinationGeocodedAddress->getLatitude();
        $lon2 = $destinationGeocodedAddress->getLongitude();
        
        $theta = $lon1 - $lon2;
        $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2))
            * cos(deg2rad($theta));
        $dist = acos($dist);
        $dist = rad2deg($dist);
        $miles = $dist * 60 * 1.1515;
        $unit = strtolower($unit);

        if ($unit == "metric") {
            return ($miles * 1.609344);
        } else {
            return $miles;
        }
    }
    
    /**
     * @param MarelloEnterpriseAddress[] $eeAddresses
     * @throws \Exception
     */
    protected function checkCoordinates(array $eeAddresses)
    {
        foreach ($eeAddresses as $eeAddress) {
            if (null === $eeAddress->getLatitude() || null === $eeAddress->getLongitude()) {
                throw new \Exception(sprintf('No coordinates found for "%s"', $eeAddress->getAddress()));
            }
        }
    }
}
