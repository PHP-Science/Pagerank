<?php

declare(strict_types=1);

namespace PhpScience\PageRank\Strategy;

use PhpScience\PageRank\Builder\NodeBuilder;
use PhpScience\PageRank\Builder\NodeCollectionBuilder;
use PhpScience\PageRank\Data\NodeCollectionInterface;

class MemoryStrategy implements NodeDataStrategyInterface
{
    private NodeBuilder           $nodeBuilder;
    private NodeCollectionBuilder $nodeCollectionBuilder;

    private array $previousRanks = [];
    private array $nodeListMap;

    public function __construct(
        NodeBuilder $nodeBuilder,
        NodeCollectionBuilder $nodeCollectionBuilder,
        array $nodeListMap
    ) {
        $this->nodeBuilder = $nodeBuilder;
        $this->nodeCollectionBuilder = $nodeCollectionBuilder;
        $this->nodeListMap = $nodeListMap;
    }

    public function getIncomingNodeIds(int $nodeId): array
    {
        return $this->nodeListMap[$nodeId]['in'];
    }

    public function countOutgoingNodes(int $nodeId): int
    {
        return count($this->nodeListMap[$nodeId]['out']);
    }

    public function getPreviousRank(int $nodeId): float
    {
        return $this->previousRanks[$nodeId];
    }

    public function updateNodes(NodeCollectionInterface $collection): void
    {
        foreach ($collection->getNodes() as $item) {
            $this->previousRanks[$item->getId()] = $item->getRank();
        }
    }

    public function getNodeCollection(): NodeCollectionInterface
    {
        $nodes = [];

        foreach ($this->nodeListMap as $nodeMap) {
            $nodes[] = $this->nodeBuilder->build($nodeMap);
        }

        return $this->nodeCollectionBuilder->build($nodes);
    }
}
