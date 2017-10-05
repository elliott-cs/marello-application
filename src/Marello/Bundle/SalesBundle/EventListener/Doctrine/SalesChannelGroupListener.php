<?php

namespace Marello\Bundle\SalesBundle\EventListener\Doctrine;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Marello\Bundle\InventoryBundle\Entity\WarehouseChannelGroupLink;
use Marello\Bundle\SalesBundle\Entity\SalesChannelGroup;

class SalesChannelGroupListener
{
    /**
     * @var WarehouseChannelGroupLink
     */
    private $systemWarehouseChannelGroupLink;

    /**
     * @param SalesChannelGroup $salesChannelGroup
     * @param LifecycleEventArgs $args
     */
    public function preRemove(SalesChannelGroup $salesChannelGroup, LifecycleEventArgs $args)
    {
        $em = $args->getEntityManager();
        $systemGroup = $em
            ->getRepository(SalesChannelGroup::class)
            ->findOneBy(['system' => true]);
        $systemWarehouseChannelGroupLink = $this->getSystemWarehouseChannelGroupLink($em);

        if (!$systemGroup || !$systemWarehouseChannelGroupLink) {
            return;
        }
        $salesChannels = $salesChannelGroup->getSalesChannels();
        foreach ($salesChannels as $salesChannel) {
            $salesChannel->setGroup($systemGroup);
            $em->persist($salesChannel);
        }
        $systemWarehouseChannelGroupLink->removeSalesChannelGroup($salesChannelGroup);
        
        $em->flush();
    }

    /**
     * @param SalesChannelGroup $salesChannelGroup
     * @param LifecycleEventArgs $args
     */
    public function postPersist(SalesChannelGroup $salesChannelGroup, LifecycleEventArgs $args)
    {
        $em = $args->getEntityManager();
        $systemWarehouseChannelGroupLink = $this->getSystemWarehouseChannelGroupLink($em);

        if ($systemWarehouseChannelGroupLink) {
            $systemWarehouseChannelGroupLink->addSalesChannelGroup($salesChannelGroup);
            
            $em->persist($systemWarehouseChannelGroupLink);
            $em->flush();
        }
    }

    /**
     * @param ObjectManager $entityManager
     * @return WarehouseChannelGroupLink|null
     */
    private function getSystemWarehouseChannelGroupLink(ObjectManager $entityManager)
    {
        if ($this->systemWarehouseChannelGroupLink === null) {
            $this->systemWarehouseChannelGroupLink = $entityManager
                ->getRepository(WarehouseChannelGroupLink::class)
                ->findOneBy(['system' => true]);
        }

        return $this->systemWarehouseChannelGroupLink;
    }
}
