<?php

/**
 * File.php
 * The File class, which is used to manage files in the filesystem.
 *
 * @author Tim Visee
 * @website http://timvisee.com/
 * @copyright Copyright © Tim Visee 2014. All rights reserved.
 */

namespace carbon\core\filesystem\file;

use carbon\core\filesystem\FilesystemObject;
use carbon\core\filesystem\permissions\SystemGroup;
use carbon\core\filesystem\permissions\SystemUser;

// Prevent direct requests to this set_file due to security reasons
defined('CARBON_CORE_INIT') or die('Access denied!');

/**
 * File class. This class extends all the features of the FileSystemObject class.
 * This class references to an file in the filesystem based on it's path.
 * This class could be used to manage files in the filesystem.
 *
 * @package carbon\core\io\file
 * @author Tim Visee
 */
class File extends FilesystemObject {

    /**
     * Get the File instance from a FileSystemObject instance or the path a string from a file.
     * If the filesystem object is an existing directory or symbolic link, null will be returned.
     * The file or file path has to be valid.
     *
     * @param \carbon\core\filesystem\FilesystemObject|string $file Filesystem object instance or the path of a file as a string.
     *
     * @return File|null The file as a File instance. Or null if the file couldn't be cast to a File instance.
     */
    public static function asFile($file) {
        return FileUtils::asFile($file, null);
    }

    /**
     * Create the file if it doesn't exist.
     *
     * @return bool True if the file was created, false otherwise.
     * False will also be returned if the file already existed.
     */
    public function createFile() {
        return FileUtils::createFile($this);
    }

    /**
     * Get the name of the file with it's extension without any path information.
     * Alias of {@see FileSystemObject::getBasename()}
     *
     * @return string Name of the file and it's extension.
     *
     * @see FileSystemObject::getBasename();
     */
    public function getFileName() {
        return $this->getBasename();
    }

    /**
     * Get the extension of a file. File names ending with a period, do have an extension.
     *
     * @param bool $withPeriod True to include the period with the returned value, false to exclude the period.
     *
     * @return string|null The file extension as a string.
     * Null will be returned on failure or if the file doesn't have an extension
     */
    public function getExtension($withPeriod = false) {
        return FileUtils::getExtension($this, $withPeriod);
    }

    /**
     * Check whether the file has an extension. File names ending with a period, do have an extension.
     *
     * @return bool True if the file has an extension, false otherwise. False will also be returned on failure.
     */
    public function hasExtension() {
        return FileUtils::hasExtension($this);
    }

    /**
     * Get the file owner.
     *
     * @return SystemUser|null Owner of the file, or null on failure.
     */
    public function getOwner() {
        return FileUtils::getOwner($this);
    }

    /**
     * Get the file group.
     *
     * @return SystemGroup|null Group of the file, or null on failure.
     */
    public function getGroup() {
        return FileUtils::getGroup($this);
    }

    /**
     * Get the size in bytes of the file.
     *
     * @return int|mixed|null File size in bytes, or null on failure.
     */
    public function getSize() {
        return FileUtils::getSize($this);
    }

    /**
     * Get the last access time of the file as a unix timestamp.
     *
     * @return int|null Last access time of the file as a unix timestamp, or null on failure.
     */
    public function getLastAccessTime() {
        return FileUtils::getLastAccessTime($this);
    }

    /**
     * Get the inode change time of the file as unix timestamp.
     *
     * @return int|null Inode change time of the file as unix timestamp, or null on failure.
     */
    public function getChangeTime() {
        return FileUtils::getChangeTime($this);
    }

    /**
     * Get the last modification time of the file as a unix timestamp.
     *
     * @return int|null Last modification time of the file as unix timestamp, or null on failure.
     */
    public function getModificationTime() {
        return FileUtils::getModificationTime($this);
    }

    /**
     * Touch the file and set the modification and action time.
     *
     * @param int|null $time [optional] The modification time to set as a timestamp. Null to use the current time.
     * @param int|null $accessTime [optional] The access time to set as a timestamp. Null to use the $time value.
     *
     * @return bool True on success, false on failure.
     */
    public function touch($time = null, $accessTime = null) {
        return FileUtils::touchFile($this, $time, $accessTime);
    }

    /**
     * Get the file contents of the file.
     *
     * @param resource $context [optional] See PHPs fopen() function for more details.
     * @param int|null $offset [optional] The offset of the file pointer to start reading from measured in bytes from
     * the beginning of the file, or null to ignore this parameter.
     * @param int|null $maxLength [optional] The maximum number of bytes to read from the file, or null to ignore this
     * parameter.
     *
     * @return string|null File contents as a string, or null on failure.
     */
    public function getContents($context = null, $offset = null, $maxLength = null) {
        return FileUtils::getContents($this, $context, $offset, $maxLength, null);
    }

    /**
     * Put contents into the file. This will truncate the file if it exists already.
     * If the file doesn't exist, it will be created.
     *
     * @param string $data The data to put into the file.
     * @param resource $context [optional] See PHPs fopen() function for more details.
     *
     * @return int|null The number of written bytes, or null on failure.
     *
     * @see fopen();
     */
    public function putContents($data, $context = null) {
        return FileUtils::putContents($data, $context);
    }

    /**
     * Append to the file.
     *
     * @param string $data The data to append to the file.
     * @param resource $context [optional] See PHPs fopen() function for more details.
     * @param bool $create [optional] True to attempt to create the file if it doens't exist.
     *
     * @return int|null The amount of bytes appended to the file.
     */
    public function append($data, $context = null, $create = true) {
        return FileUtils::append($this, $data, $context, $create);
    }

    /**
     * Create a file handler for this file.
     *
     * @return FileHandler File handler instance.
     */
    public function createHandler() {
        return new FileHandler($this);
    }

    /**
     * Check whether the file is valid. The file doesn't need to exist.
     * The file may not be an existing directory or symbolic link.
     *
     * @return bool True if the file seems to be valid, false otherwise.
     */
    public function isValid() {
        return FileUtils::isValid($this);
    }

    // TODO: Method to require and require_once a file.
    // TODO: To prepend contents to a file.
}
