<?php
/**
 * @author Antoine Pouch <ant-1@pouch.name>
 * @package gestionlite
 * @module model
 */

/**
 * Interface for persistent objects
 */
interface persistent
{
    /**
     * CRUD method : creates an instance from an array of parameters
     *
     * @param mixed $params The parameters to create the object
     * @return persistent The created instance
     */
    public static function create($params);

    /**
     * CRUD method : retrieve an instance from an ID
     *
     * @param integer $id The Object's ID
     * @return persistent The instance
     */
    public static function retrieve($id);

    /**
     * CRUD method : updates an instance from an array of parameters
     *
     * @param persistent $instance The instance to update
     * @param mixed $params The parameters to update for the instance
     * @return persistent The Updated instance
     */
    public static function update($instance, $params);

    /**
     * CRUD method : delete an object
     *
     * @param mixed $params The parameters to create the object
     */
    public static function delete($instance);

    /**
     * Saves the object into the persistence
     *
     * @return persistent The current object
     */
    public function save();
}

?>