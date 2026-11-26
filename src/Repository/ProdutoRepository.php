<?php

namespace App\Repository;

use App\Entity\Categoria;
use App\Entity\Produto;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Produto>
 */
class ProdutoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Produto::class);
    }

    /** @return Produto[] produtos ativos, filtrados por categoria quando informada */
    public function vitrine(?Categoria $categoria = null): array
    {
        $qb = $this->createQueryBuilder('p')
            ->addSelect('c')
            ->join('p.categoria', 'c')
            ->where('p.ativo = true')
            ->orderBy('p.nome', 'ASC');

        if ($categoria) {
            $qb->andWhere('p.categoria = :categoria')
               ->setParameter('categoria', $categoria);
        }

        return $qb->getQuery()->getResult();
    }
}