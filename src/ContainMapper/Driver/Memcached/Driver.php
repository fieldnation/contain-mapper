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

namespace ContainMapper\Driver\Memcached;

use Contain\Entity\EntityInterface;
use ContainMapper\Exception;
use ContainMapper;

/**
 * Memcached Data Source Driver
 *
 * @category    akandels
 * @package     contain
 * @copyright   Copyright (c) 2012 Andrew P. Kandels (http://andrewkandels.com)
 * @license     http://www.opensource.org/licenses/bsd-license.php BSD License
 */
class Driver extends ContainMapper\Driver\AbstractDriver
{
    /**
     * {@inheritDoc}
     */
    public function persist(EntityInterface $entity)
    {
        if (!$primary = $this->getPrimaryScalarId($entity)) {
            if ($entity->primary()) {
                throw new Exception\InvalidArgumentException('$entity has primary properties '
                    . 'defined; but has no values assigned'
                );
            }

            $primary = uniqid('', true);
            $entity->setExtendedProperty('_id', $primary);
        }

        $options = $this->getOptions(array(
            'expiration' => 86400,
        ));
        $expiration = $options['expiration'];

        $this->getConnection()->getConnection()->set($primary, $entity->export(), $expiration);

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function delete(EntityInterface $entity)
    {
        // anything to do?
        if (!$id = $this->getPrimaryScalarId($entity)) {
            return $this;
        }

        $options = $this->getOptions(array(
            'time' => 0,
        ));
        $seconds = $options['time'];

        $this->getConnection()->getConnection()->delete($id, $seconds);

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function findOne($criteria = null)
    {
        return $this->getConnection()->getConnection()->get(
            $criteria
        );
    }

    /**
     * {@inheritDoc}
     */
    public function find($criteria = null)
    {
        $results = $this->getConnection()
            ->getConnection()
            ->getMulti($criteria);

        if ($this->skip !== null) {
            $results = array_slice($results, $this->skip);
        }

        if ($this->limit > 0) {
            $results = array_slice($results, 0, $this->limit);
        }

        return $results;
    }
}
