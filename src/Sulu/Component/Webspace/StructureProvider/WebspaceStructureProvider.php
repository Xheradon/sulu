<?php

/*
 * This file is part of Sulu.
 *
 * (c) Sulu GmbH
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Sulu\Component\Webspace\StructureProvider;

use Doctrine\Common\Cache\Cache;
use Sulu\Component\Content\Compat\Structure\PageBridge;
use Sulu\Component\Content\Compat\StructureManagerInterface;
use Twig\Environment;
use Twig\Error\LoaderError;

/**
 * Provide templates which are implemented in a single webspace.
 */
class WebspaceStructureProvider implements WebspaceStructureProviderInterface
{
    /**
     * @var Environment
     */
    protected $twig;

    /**
     * @var StructureManagerInterface
     */
    protected $structureManager;

    /**
     * @var Cache
     */
    protected $cache;

    public function __construct(
        Environment $twig,
        StructureManagerInterface $structureManager,
        Cache $cache
    ) {
        $this->twig = $twig;
        $this->structureManager = $structureManager;
        $this->cache = $cache;
    }

    public function getStructures($webspaceKey)
    {
        if (!$this->cache->contains($webspaceKey)) {
            return $this->loadStructures($webspaceKey);
        }

        $keys = $this->cache->fetch($webspaceKey);

        return \array_map(
            function ($key) {
                return $this->structureManager->getStructure($key);
            },
            $keys
        );
    }

    /**
     * Returns and caches structures for given webspace.
     *
     * @param string $webspaceKey
     *
     * @return array
     */
    protected function loadStructures($webspaceKey)
    {
        $structures = [];
        $keys = [];
        foreach ($this->structureManager->getStructures() as $page) {
            /* @var PageBridge $page */
            $template = \sprintf('%s.html.twig', $page->getView());
            if ($this->templateExists($template)) {
                $keys[] = $page->getKey();
                $structures[] = $page;
            }
        }

        $this->cache->save($webspaceKey, $keys);

        return $structures;
    }

    /**
     * checks if a template with given name exists.
     *
     * @param string $template
     *
     * @return bool
     */
    protected function templateExists($template)
    {
        $loader = $this->twig->getLoader();
        if (\method_exists($loader, 'exists')) {
            return $loader->exists($template);
        }

        try {
            // cast possible TemplateReferenceInterface to string because the
            // EngineInterface supports them but Twig_LoaderInterface does not
            $loader->getSource($template)->getCode();
        } catch (LoaderError $e) {
            return false;
        }

        return true;
    }
}
