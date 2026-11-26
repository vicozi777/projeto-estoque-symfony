<?php

namespace App\Repository;

use App\Entity\Categoria;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Categoria>
 */
class CategoriaRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Categoria::class);
    }

    /** @return array<int, array{id:int, nome:string, total:int|string}> */
    public function comTotalAtivos(): array
    {
        return $this->createQueryBuilder('c')
            ->select('c.id, c.nome, COUNT(p.id) AS total')
            ->leftJoin('c.produtos', 'p', 'WITH', 'p.ativo = true')
            ->groupBy('c.id, c.nome')
            ->orderBy('c.nome', 'ASC')
            ->getQuery()
            ->getArrayResult();
    }
}