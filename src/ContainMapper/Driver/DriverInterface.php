<?php
/**
 * Contain Project
 *
 * This source file is subject to the BSD license bundled with
 * this package in the LICENSE.txt file. It is also available
 * on the world-wide-web at http://www.opensource.org/licenses/bsd-license.php.
 * If you are unable to receive a copy of the license or have
 * questions concerning the terms, please send an email to
 * me@andrewkandels.com.
 *
 * @category    akandels
 * @package     contain
 * @author      Andrew Kandels (me@andrewkandels.com)
 * @copyright   Copyright (c) 2012 Andrew P. Kandels (http://andrewkandels.com)
 * @license     http://www.opensource.org/licenses/bsd-license.php BSD License
 * @link        http://andrewkandels.com/contain
 */

namespace ContainMapper\Driver;

use Contain\Entity\EntityInterface;

/**
 * Interface for a data mapper's driver (e.g.: MongoDB).
 *
 * @category    akandels
 * @package     contain
 * @copyright   Copyright (c) 2012 Andrew P. Kandels (http://andrewkandels.com)
 * @license     http://www.opensource.org/licenses/bsd-license.php BSD License
 */
interface DriverInterface
{
    /**
     * Post-hydration callback.
     *
     * @param EntityInterface $entity
     * @param mixed           $values Values we returned
     *
     * @return self
     */
    public function hydrate(EntityInterface $entity, $values);

    /**
     * Returns true if the data entity has been persisted to the data store
     * this driver is responsible for.
     *
     * @param EntityInterface $entity
     *
     * @return bool
     */
    public function isPersisted(EntityInterface $entity);

    /**
     * May be invoked by the mapper if the driver supports an atomic, or
     * more efficient incrementor method as opposed to the typical
     * persist().
     *
     * @param EntityInterface $entity
     * @param string          $query  Path to the property
     * @param int             $inc    Amount to increment by (+|-)
     *
     * @return self
     */
    public function increment(EntityInterface $entity, $query, $inc);

    /**
     * May be invoked by the mapper if the driver supports an atomic, or
     * more efficient way of appending items to the end of an array property.
     *
     * @param EntityInterface          $entity      Contain Data Entity
     * @param string                   $query       Path to the property
     * @param mixed|array|\Traversable $value       Value(s) to push
     * @param bool                     $ifNotExists Only add if it doesn't exist (if supported)
     *
     * @return self
     */
    public function push(EntityInterface $entity, $query, $value, $ifNotExists = false);

    /**
     * Persists an entity into the data source the driver is responsible for.
     *
     * @param EntityInterface $entity Entity to persist
     *
     * @return self
     */
    public function persist(EntityInterface $entity);

    /**
     * Finds a single entity from some criteria.
     *
     * @param mixed $criteria Search criteria
     *
     * @return EntityInterface|false
     */
    public function findOne($criteria = null);

    /**
     * Finds multiple entities from some criteria.
     *
     * @param mixed $criteria Search criteria
     *
     * @return EntityInterface[]|\Traversable
     */
    public function find($criteria = null);

    /**
     * Deletes an entity.
     *
     * @param EntityInterface $entity
     *
     * @return self
     */
    public function delete(EntityInterface $entity);
}

