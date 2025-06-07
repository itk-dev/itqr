<?php

declare(strict_types=1);

namespace App\Security\TenantScope\Doctrine;

use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Class TenantFilterListener
 *
 * Event listener is to enable and configure the tenant filter for all requests.
 */
#[AsEventListener(event: KernelEvents::CONTROLLER, priority: 15)]
readonly class TenantFilterListener
{
    public function __construct(
        private TenantFilterConfigurator $tenantFilterConfigurator
    ) {
    }

    /**
     * Enable the tenant filter for all requests.
     */
    public function onKernelController(ControllerEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $this->tenantFilterConfigurator->configureFilter();
    }
}