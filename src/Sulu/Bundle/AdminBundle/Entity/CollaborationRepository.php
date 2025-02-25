<?php

/*
 * This file is part of Sulu.
 *
 * (c) Sulu GmbH
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Sulu\Bundle\AdminBundle\Entity;

use Psr\Cache\CacheItemPoolInterface;

class CollaborationRepository
{
    /**
     * @var CacheItemPoolInterface
     */
    private $cache;

    /**
     * @var int
     */
    private $threshold;

    public function __construct(CacheItemPoolInterface $cache, int $threshold)
    {
        $this->cache = $cache;
        $this->threshold = $threshold;
    }

    public function find(string $resourceKey, string $id, string $connectionId): ?Collaboration
    {
        $cacheItem = $this->cache->getItem($this->getCacheId($resourceKey, $id));

        if (!$cacheItem) {
            return null;
        }

        $collaborations = $cacheItem->get();

        if (!$collaborations) {
            return null;
        }

        return $collaborations[$connectionId] ?? null;
    }

    /**
     * @return Collaboration[]
     */
    public function update(Collaboration $collaboration): array
    {
        $collaboration->updateTime();

        $cacheItem = $this->cache->getItem($this->getCacheIdFromCollaboration($collaboration));
        $value = $cacheItem->get() ?? [];
        $value[$collaboration->getConnectionId()] = $collaboration;

        $value = \array_filter($value, function (Collaboration $collaboration) {
            return $collaboration->getChanged() > \time() - $this->threshold;
        });

        $cacheItem->set($value);

        $this->cache->save($cacheItem);

        return \array_values($value);
    }

    /**
     * @return Collaboration[]
     */
    public function delete(Collaboration $collaboration): array
    {
        $cacheItem = $this->cache->getItem($this->getCacheIdFromCollaboration($collaboration));
        $value = \array_filter($cacheItem->get() ?? [], function (Collaboration $cachedCollaboration) use ($collaboration) {
            return $collaboration->getConnectionId() !== $cachedCollaboration->getConnectionId();
        });
        $cacheItem->set($value);

        $this->cache->save($cacheItem);

        return \array_values($value);
    }

    private function getCacheIdFromCollaboration(Collaboration $collaboration): string
    {
        return $this->getCacheId($collaboration->getResourceKey(), $collaboration->getId());
    }

    /**
     * @param $id string | int
     */
    private function getCacheId(string $resourceKey, $id): string
    {
        return $resourceKey . '_' . $id;
    }
}
