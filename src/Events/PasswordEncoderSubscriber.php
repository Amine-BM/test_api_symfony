<?php

namespace App\Events;

use ApiPlatform\Core\EventListener\EventPriorities;
use App\Entity\User;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class PasswordEncoderSubscriber implements EventSubscriberInterface
{
    /**
     * @var UserPasswordEncoderInterface
     */
    private $encoder;

    public function __construct(UserPasswordHasherInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::VIEW => ['hashPassword', EventPriorities::PRE_WRITE]
        ];
    }

    public function hashPassword(ViewEvent $event)
    {
        $result = $event->getControllerResult();
        $method = $event->getRequest()->getMethod();

        if ($result instanceof User && $method === "POST") {
            /** * @param User $result */
            $hash = $this->encoder->hashPassword($result, $result->getPassword());
            $result->setPassword($hash);
        }
    }
}
