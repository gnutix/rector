<?php declare(strict_types=1);

namespace Rector\NodeTypeResolver\PHPStan\Scope\NodeVisitor;

use PhpParser\Node;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Stmt\Expression;
use PhpParser\Node\Stmt\Nop;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitorAbstract;
use Rector\PhpParser\Node\BetterNodeFinder;

/**
 * Skips performance trap in PHPStan: https://github.com/phpstan/phpstan/issues/254
 */
final class RemoveDeepChainMethodCallNodeVisitor extends NodeVisitorAbstract
{
    /**
     * @warning might cause bugs with fluent interfaces like https://github.com/rectorphp/rector/issues/1646
     * @var int
     */
    private const NESTED_CHAIN_METHOD_CALL_LIMIT = 15;

    /**
     * @var BetterNodeFinder
     */
    private $betterNodeFinder;

    /**
     * @var Expression|null
     */
    private $nodeToRemove;

    public function __construct(BetterNodeFinder $betterNodeFinder)
    {
        $this->betterNodeFinder = $betterNodeFinder;
    }

    /**
     * @return int|Node|null
     */
    public function enterNode(Node $node)
    {
        if (! $node instanceof Expression) {
            return null;
        }

        if ($node->expr instanceof MethodCall && $node->expr->var instanceof MethodCall) {
            $nestedChainMethodCalls = $this->betterNodeFinder->findInstanceOf([$node->expr], MethodCall::class);
            if (count($nestedChainMethodCalls) > self::NESTED_CHAIN_METHOD_CALL_LIMIT) {
                $this->nodeToRemove = $node;

                return NodeTraverser::DONT_TRAVERSE_CHILDREN;
            }
        }

        return null;
    }

    /**
     * @return int|Node|Node[]|null
     */
    public function leaveNode(Node $node)
    {
        if ($node === $this->nodeToRemove) {
            // keep any node, so we don't remove it permanently
            $nopNode = new Nop();
            $nopNode->setAttributes($node->getAttributes());
            return $nopNode;
        }

        return $node;
    }
}
