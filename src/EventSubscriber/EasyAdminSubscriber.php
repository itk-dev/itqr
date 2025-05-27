<?php

namespace App\EventSubscriber;

use App\Entity\Tenant\QrVisualConfig;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityPersistedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityUpdatedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

class EasyAdminSubscriber implements EventSubscriberInterface
{
    private string $uploadsPath;
    private SluggerInterface $slugger;

    public function __construct(string $uploadsPath, SluggerInterface $slugger)
    {
        $this->uploadsPath = $uploadsPath;
        $this->slugger = $slugger;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            BeforeEntityPersistedEvent::class => ['handleFileUpload'],
            BeforeEntityUpdatedEvent::class => ['handleFileUpload'],
        ];
    }

    public function handleFileUpload($event): void
    {
        $entity = $event->getEntityInstance();

        if (!($entity instanceof QrVisualConfig)) {
            return;
        }

        $logo = $entity->getLogo();
        if ($logo instanceof UploadedFile) {
            $originalFilename = pathinfo($logo->getClientOriginalName(), PATHINFO_FILENAME);
            $safeFilename = $this->slugger->slug($originalFilename);
            $newFilename = $safeFilename.'-'.uniqid().'.'.$logo->guessExtension();

            try {
                $logo->move(
                    $this->uploadsPath,
                    $newFilename
                );
            } catch (FileException $e) {
                // ... handle exception if something happens during file upload
            }

            $entity->setLogo($newFilename);
        }
    }
}
