<?php declare(strict_types=1);

namespace Rector\Doctrine\Rector\Property;

use PhpParser\Node;
use PhpParser\Node\Stmt\Property;
use Ramsey\Uuid\UuidInterface;
use Rector\BetterPhpDocParser\PhpDocInfo\PhpDocInfo;
use Rector\BetterPhpDocParser\PhpDocNode\Doctrine\Property_\ColumnTagValueNode;
use Rector\BetterPhpDocParser\PhpDocNode\Doctrine\Property_\GeneratedValueTagValueNode;
use Rector\BetterPhpDocParser\PhpDocNode\JMS\SerializerTypeTagValueNode;
use Rector\PHPStan\Type\FullyQualifiedObjectType;
use Rector\Rector\AbstractRector;
use Rector\RectorDefinition\RectorDefinition;

/**
 * @sponsor Thanks https://spaceflow.io/ for sponsoring this rule - visit them on https://github.com/SpaceFlow-app
 *
 * @see \Rector\Doctrine\Tests\Rector\Property\AddUuidAnnotationsToIdPropertyRector\AddUuidAnnotationsToIdPropertyRectorTest
 */
final class AddUuidAnnotationsToIdPropertyRector extends AbstractRector
{
    public function getDefinition(): RectorDefinition
    {
        return new RectorDefinition('Add uuid annotations to $id property');
    }

    /**
     * @return string[]
     */
    public function getNodeTypes(): array
    {
        return [Property::class];
    }

    /**
     * @param Property $node
     */
    public function refactor(Node $node): ?Node
    {
        if (! $this->isDoctrineProperty($node)) {
            return null;
        }

        if (! $this->isName($node, 'id')) {
            return null;
        }

        $this->changeVarToUuidInterface($node);

        /** @var PhpDocInfo $phpDocInfo */
        $phpDocInfo = $this->getPhpDocInfo($node);
        $this->removeGeneratedValueTag($phpDocInfo);
        $this->changeColumnTypeToUuidBinary($phpDocInfo);
        $this->changeSerializerTypeToString($phpDocInfo);

        $this->docBlockManipulator->updateNodeWithPhpDocInfo($node, $phpDocInfo);

        return $node;
    }

    private function changeVarToUuidInterface(Property $property): void
    {
        $uuidObjectType = new FullyQualifiedObjectType(UuidInterface::class);
        $this->docBlockManipulator->changeVarTag($property, $uuidObjectType);
    }

    private function removeGeneratedValueTag(PhpDocInfo $phpDocInfo): void
    {
        $generatedTagValueNode = $phpDocInfo->getByType(GeneratedValueTagValueNode::class);
        if ($generatedTagValueNode === null) {
            return;
        }

        $phpDocInfo->removeTagValueNodeFromNode($generatedTagValueNode);
    }

    private function changeColumnTypeToUuidBinary(PhpDocInfo $phpDocInfo): void
    {
        $columnTagValueNode = $phpDocInfo->getByType(ColumnTagValueNode::class);
        if ($columnTagValueNode === null) {
            return;
        }

        $columnTagValueNode->changeType('uuid_binary');
    }

    private function changeSerializerTypeToString(PhpDocInfo $phpDocInfo): void
    {
        /** @var SerializerTypeTagValueNode|null $serializerTypeTagValueNode */
        $serializerTypeTagValueNode = $phpDocInfo->getByType(SerializerTypeTagValueNode::class);
        if ($serializerTypeTagValueNode === null) {
            return;
        }

        if ($serializerTypeTagValueNode->getName() === 'string') {
            return;
        }

        $serializerTypeTagValueNode->changeName('string');
    }
}
