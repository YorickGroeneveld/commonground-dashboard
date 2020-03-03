<?php

// api/src/Filter/RegexpFilter.php

namespace App\Filter;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\AbstractContextAwareFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use Doctrine\ORM\QueryBuilder;

final class RegexpFilter extends AbstractContextAwareFilter
{
    protected function filterProperty(string $property, $value, QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, string $operationName = null)
    {
        // otherwise filter is applied to order and page as well
        if (
                !$this->isPropertyEnabled($property, $resourceClass) ||
                !$this->isPropertyMapped($property, $resourceClass)
                ) {
            return;
        }

        $parameterName = $queryNameGenerator->generateParameterName($property); // Generate a unique parameter name to avoid collisions with other filters
        $queryBuilder
                ->andWhere(sprintf('REGEXP(o.%s, :%s) = 1', $property, $parameterName))
                ->setParameter($parameterName, $value);
    }

    // This function is only used to hook in documentation generators (supported by Swagger and Hydra)
    public function getDescription(string $resourceClass): array
    {
        if (!$this->properties) {
            return [];
        }

        $description = [];
        foreach ($this->properties as $property => $strategy) {
            $description["regexp_$property"] = [
                'property' => $property,
                'type'     => 'string',
                'required' => false,
                'swagger'  => [
                    'description' => 'Filter for an exact match using a [Regular expression](https://en.wikipedia.org/wiki/Regular_expression).',
                    'name'        => $property,
                    'type'        => 'string',
                ],
            ];
        }

        return $description;
    }
}
