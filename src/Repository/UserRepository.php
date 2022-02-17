<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;
use phpDocumentor\Reflection\Types\This;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', \get_class($user)));
        }

        $user->setPassword($newHashedPassword);
        $this->_em->persist($user);
        $this->_em->flush();
    }

    /**
     * Returns an array of User objects
     * @param User $search
     * @return array
     */

    public function findSearch(User $search): array
    {
          $query = $this
              ->createQueryBuilder('n');

                if (!empty($search->names)) {
                $query = $query
                    ->andWhere('names.nom LIKE :nom')
                    ->setParameter('nom', "%{$search->names}%");
                }

                return $query->getQuery()->getResult();

    }


    /*
    public function findOneBySomeField($value): ?User
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */


    /*
    // /**
    // * @return User[]
    // */
    /*
    public function findSearch(User $search): array
    {
        $query = $this
            ->createQueryBuilder('u')
            ->select('c', 'p')
            ->join('p.users', 'c');

        if (!empty($search->nom)) {
            $query = $query
                ->andWhere('p.name LIKE :nom')
                ->setParameter('nom', "%{$search->nom}%");
        }

    }
    */
}
