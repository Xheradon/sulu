<?php

/*
 * This file is part of Sulu.
 *
 * (c) Sulu GmbH
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Sulu\Bundle\AdminBundle\Controller;

use FOS\RestBundle\View\View;
use FOS\RestBundle\View\ViewHandler;
use HandcraftedInTheAlps\RestRoutingBundle\Routing\ClassResourceInterface;
use Sulu\Bundle\AdminBundle\Entity\Collaboration;
use Sulu\Bundle\AdminBundle\Entity\CollaborationRepository;
use Sulu\Component\Rest\ListBuilder\CollectionRepresentation;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class CollaborationController implements ClassResourceInterface
{
    private static $resourceKey = 'collaborations';

    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    /**
     * @var CollaborationRepository
     */
    private $collaborationRepository;

    /**
     * @var ViewHandler
     */
    private $viewHandler;

    /**
     * @var string
     */
    private $secret;

    public function __construct(
        TokenStorageInterface $tokenStorage,
        CollaborationRepository $collaborationRepository,
        ViewHandler $viewHandler,
        string $secret
    ) {
        $this->tokenStorage = $tokenStorage;
        $this->collaborationRepository = $collaborationRepository;
        $this->viewHandler = $viewHandler;
        $this->secret = $secret;
    }

    public function cputAction(Request $request)
    {
        $collaboration = $this->collaborationRepository->find(
            $this->getResourceKey($request),
            $this->getId($request),
            $this->getConnectionId($request)
        ) ?? $this->createCollaboration($request);

        $collaborations = \array_values(\array_filter(
            $this->collaborationRepository->update($collaboration),
            function (Collaboration $collaboration) use ($request) {
                return $collaboration->getConnectionId() !== $this->getConnectionId($request);
            }
        ));

        return $this->viewHandler->handle(
            View::create(new CollectionRepresentation($collaborations, static::$resourceKey))
        );
    }

    public function cdeleteAction(Request $request)
    {
        $collaborations = \array_values($this->collaborationRepository->delete($this->createCollaboration($request)));

        return $this->viewHandler->handle(
            View::create(new CollectionRepresentation($collaborations, static::$resourceKey))
        );
    }

    private function createCollaboration(Request $request)
    {
        $user = $this->tokenStorage->getToken()->getUser();

        return new Collaboration(
            $this->getConnectionId($request),
            $user->getId(),
            $user->getUserName(),
            $user->getFullName(),
            $this->getResourceKey($request),
            $this->getId($request)
        );
    }

    /**
     * @return string | int
     */
    private function getId(Request $request)
    {
        return $request->query->get('id');
    }

    private function getResourceKey(Request $request): string
    {
        return $request->query->get('resourceKey');
    }

    private function getConnectionId(Request $request)
    {
        return \sha1($request->getSession()->getId() . $this->secret);
    }
}
