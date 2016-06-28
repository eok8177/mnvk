<?php

namespace AppBundle\Repository;

use Gedmo\Tree\Entity\Repository\NestedTreeRepository;

/**
 * CategoryRepository
 */
class CategoryRepository extends NestedTreeRepository
{
	public function getList()
	{
		$query = $this->createQueryBuilder('c');

		$query
			->where('c.id > 1')
			->orderBy('c.root', 'ASC')
			->addOrderBy('c.lft', 'ASC')
		;

		return $query->getQuery()->getResult();
	}
}
