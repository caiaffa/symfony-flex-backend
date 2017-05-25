<?php
declare(strict_types=1);
/**
 * /src/Rest/ResourceInterfaces.php
 *
 * @author  TLe, Tarmo Leppänen <tarmo.leppanen@protacon.com>
 */
namespace App\Rest;

use App\Entity\EntityInterface;
use App\Rest\DTO\RestDtoInterface;
use Doctrine\Common\Proxy\Proxy;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Interface ResourceInterface
 *
 * @package App\Rest
 * @author  TLe, Tarmo Leppänen <tarmo.leppanen@protacon.com>
 */
interface ResourceInterface
{
    /**
     * Getter method for entity repository.
     *
     * @return Repository
     */
    public function getRepository(): Repository;

    /**
     * Getter for used validator.
     *
     * @return ValidatorInterface
     */
    public function getValidator(): ValidatorInterface;

    /**
     * Getter method for used DTO class for this REST service.
     *
     * @return string
     *
     * @throws \UnexpectedValueException
     */
    public function getDtoClass(): string;

    /**
     * Setter for used DTO class.
     *
     * @param string $dtoClass
     *
     * @return Resource
     */
    public function setDtoClass(string $dtoClass): Resource;

    /**
     * Getter method for current entity name.
     *
     * @return string
     */
    public function getEntityName(): string;

    /**
     * Gets a reference to the entity identified by the given type and identifier without actually loading it,
     * if the entity is not yet loaded.
     *
     * @param string $id The entity identifier.
     *
     * @return Proxy|null
     *
     * @throws \Doctrine\ORM\ORMException
     */
    public function getReference(string $id): ?Proxy;

    /**
     * Getter method for all associations that current entity contains.
     *
     * @return array
     */
    public function getAssociations(): array;

    /**
     * Generic find method to return an array of items from database. Return value is an array of specified repository
     * entities.
     *
     * @param null|array   $criteria
     * @param null|array   $orderBy
     * @param null|integer $limit
     * @param null|integer $offset
     * @param null|array   $search
     *
     * @return EntityInterface[]
     */
    public function find(
        array $criteria = null,
        array $orderBy = null,
        int $limit = null,
        int $offset = null,
        array $search = null
    ): array;

    /**
     * Generic findOne method to return single item from database. Return value is single entity from specified
     * repository.
     *
     * @param string       $id
     * @param null|boolean $throwExceptionIfNotFound
     *
     * @return null|EntityInterface
     *
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function findOne(string $id, bool $throwExceptionIfNotFound = null): ?EntityInterface;

    /**
     * Generic findOneBy method to return single item from database by given criteria. Return value is single entity
     * from specified repository or null if entity was not found.
     *
     * @param array        $criteria
     * @param null|array   $orderBy
     * @param null|boolean $throwExceptionIfNotFound
     *
     * @return null|EntityInterface
     *
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function findOneBy(
        array $criteria,
        array $orderBy = null,
        bool $throwExceptionIfNotFound = null
    ): ?EntityInterface;

    /**
     * Generic count method to return entity count for specified criteria and search terms.
     *
     * @param null|array $criteria
     * @param null|array $search
     *
     * @return integer
     *
     * @throws \InvalidArgumentException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function count(array $criteria = null, array $search = null): int;

    /**
     * Generic method to create new item (entity) to specified database repository. Return value is created entity for
     * specified repository.
     *
     * @param RestDtoInterface $dto
     *
     * @return EntityInterface
     *
     * @throws \Symfony\Component\Validator\Exception\ValidatorException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Doctrine\ORM\ORMInvalidArgumentException
     * @throws \Doctrine\ORM\ORMException
     */
    public function create(RestDtoInterface $dto): EntityInterface;

    /**
     * Generic method to update specified entity with new data.
     *
     * @param string           $id
     * @param RestDtoInterface $dto
     *
     * @return EntityInterface
     *
     * @throws \BadMethodCallException
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @throws \Symfony\Component\Validator\Exception\ValidatorException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Doctrine\ORM\ORMInvalidArgumentException
     * @throws \Doctrine\ORM\ORMException
     */
    public function update(string $id, RestDtoInterface $dto): EntityInterface;

    /**
     * Generic method to delete specified entity from database.
     *
     * @param string $id
     *
     * @return EntityInterface
     *
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Doctrine\ORM\ORMInvalidArgumentException
     * @throws \Doctrine\ORM\ORMException
     */
    public function delete(string $id): EntityInterface;

    /**
     * Generic ids method to return an array of id values from database. Return value is an array of specified
     * repository entity id values.
     *
     * @param null|array $criteria
     * @param null|array $search
     *
     * @return array
     */
    public function getIds(array $criteria = null, array $search = null): array;

    /**
     * Generic method to save given entity to specified repository. Return value is created entity.
     *
     * @param EntityInterface $entity
     * @param null|boolean    $skipValidation
     *
     * @return EntityInterface
     *
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Doctrine\ORM\ORMInvalidArgumentException
     * @throws \Doctrine\ORM\ORMException
     * @throws \Symfony\Component\Validator\Exception\ValidatorException
     */
    public function save(EntityInterface $entity, bool $skipValidation = null): EntityInterface;
}
