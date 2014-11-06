<?php

/**
 * FileSystemObject.php
 * The FileSystemObject class, which is used to manage objects in the filesystem.
 *
 * @author Tim Visee
 * @website http://timvisee.com/
 * @copyright Copyright © Tim Visee 2014. All rights reserved.
 */

namespace carbon\core\filesystem;

use carbon\core\cache\simplecache\SimpleCache;
use carbon\core\filesystem\directory\Directory;
use carbon\core\filesystem\file\File;
use carbon\core\filesystem\symboliclink\SymbolicLink;
use carbon\core\util\StringUtils;

// Prevent direct requests to this set_file due to security reasons
defined('CARBON_CORE_INIT') or die('Access denied!');

/**
 * Filesystem Object class.
 * This class references to an object in the filesystem based on it's file path.
 * This class could be used to manage objects in the filesystem.
 *
 * @package carbon\core\io
 * @author Tim Visee
 */
class FilesystemObject implements FilesystemObjectFlags {

    /** @var string Path */
    protected $path = '';
    /** @var SimpleCache Used for simple caching */
    protected $cache;

    // TODO: Should we keep these cache methods in this updated class?
    /** Defines the cache key used for the normalized path cache */
    const CACHE_NORMALIZED_PATH = 1;
    /** Defines the cache key used for the absolute path cache */
    const CACHE_ABSOLUTE_PATH = 2;
    /** Defines the cache key used for the canonical path cache */
    const CACHE_CANONICAL_PATH = 3;

    /**
     * FileSystemObject constructor. The path of a filesystem object has to be supplied while constructing the object.
     * The object doesn't need to exist. An optional child path relative to the $path param may be supplied.
     *
     * @param FilesystemObject|string $path Path to the filesystem object or the instance of another filesystem object
     * to use it's path.
     * @param string|null $child [optional] An optional child path relative to the $path param.
     * Null to just use the $path param as path.
     *
     * @throws \Exception Throws an exception if the $path or $child param is invalid.
     */
    public function __construct($path = '', $child = null) {
        // Initialize the simple cache
        $this->cache = new SimpleCache();

        // TODO: Should we generate and use the canonical path?

        // Set the path, throw an exception on error
        if(!$this->setPath($path, $child, false))
            // TODO: Invalid path, throw a custom exception!
            throw new \Exception();
    }

    /**
     * Get the path of the filesystem object as a string.
     *
     * @return string Path
     */
    public function getPath() {
        return $this->path;
    }

    /**
     * Set the path of the filesystem object. An optional child path relative to the $path param may be supplied.
     *
     * @param FilesystemObject|string $path Path to the filesystem object or the instance of another filesystem object
     * to use it's path.
     * @param string|null $child [optional] An optional child path relative to the $path param.
     * Null to just use the $path param as path.
     * @param bool $flushCache [optional] True to flush the cache, false otherwise.
     *
     * @return bool True on success, false on failure.
     */
    protected function setPath($path, $child = null, $flushCache = true) {
        // Combine the two paths and make sure it's valid
        if(($path = FilesystemObjectUtils::getCombinedPath($path, $child)) === null)
            return false;

        // Set the path
        $this->path = $path;

        // Flush the cache, and return true
        if($flushCache)
            $this->flushCache();
        return true;
    }

    /**
     * Check whether the filesystem object exists.
     *
     * @return bool True if the filesystem object exists, false otherwise.
     */
    public function exists() {
        return FilesystemObjectUtils::exists($this);
    }

    /**
     * Check whether the filesystem object exists and is a file.
     *
     * @return bool True if the filesystem object exists and is a file, false otherwise.
     */
    public function isFile() {
        return FilesystemObjectUtils::isFile($this);
    }

    /**
     * Check whether the filesystem object exists and is a directory.
     *
     * @return bool True if the filesystem object exists and is a directory, false otherwise.
     */
    public function isDirectory() {
        return FilesystemObjectUtils::isDirectory($this);
    }

    /**
     * Check whether the filesystem object exists and is a symbolic link.
     *
     * @return bool True if the filesystem object exists and is a symbolic link, false otherwise.
     */
    public function isSymbolicLink() {
        return FilesystemObjectUtils::isSymbolicLink($this);
    }

    /**
     * Check whether the file or directory exists and is readable.
     *
     * @return bool True if the file or directory exists and is readable, false otherwise.
     * False will also be returned if the path was invalid.
     */
    public function isReadable() {
        return FilesystemObjectUtils::isReadable($this);
    }

    /**
     * Check whether the file or directory exists and is writable.
     *
     * @return bool True if the file or directory exists and is writable, false otherwise.
     * False will also be returned if the path was invalid.
     */
    public function isWritable() {
        return FilesystemObjectUtils::isWritable($this);
    }

    /**
     * Alias of {@link FileSystemObject::isWritable()}.
     * Check whether the file or directory exists and is writable.
     *
     * @return bool True if the file or directory exists and is writable, false otherwise.
     * False will also be returned if the path was invalid.
     */
    public function isWriteable() {
        return FilesystemObjectUtils::isWriteable($this);
    }

    /**
     * Get the basename of the filesystem object.
     * For files, this will return the file name with it's extension.
     * For directories and symbolic links, this will return the name of the directory or symbolic link.
     *
     * @param string|null $suffix [optional] Suffix to omit from the basename. Null to ignore this feature.
     *
     * @return string|null Basename of the filesystem object or null on failure.
     */
    public function getBasename($suffix = null) {
        return FilesystemObjectUtils::getBasename($this, $suffix);
    }

    /**
     * Get the parent directory of the filesystem object.
     * This will return the directory the filesystem object is located in.
     * Calling this method on the root directory will fail because the root doesn't have a parent directory,
     * and will return the $default value.
     *
     * @return Directory|null The parent directory as Directory instance, or null if there's no parent directory and on
     * failure.
     */
    public function getParent() {
        return FilesystemObjectUtils::getParent($this);
    }

    /**
     * Check whether the filesystem object has a parent directory.
     *
     * @return bool True if the filesystem object has a parent directory, false otherwise.
     */
    public function hasParent() {
        return FilesystemObjectUtils::hasParent($this);
    }

    /**
     * Get the normalized path of the filesystem object.
     * This will remove unicode whitespaces and any kind of self referring or parent referring paths.
     * The filesystem object doesn't need to exist.
     *
     * @return string|null A normalized path of the filesystem object, or null on failure.
     */
    public function getNormalizedPath() {
        // Return the normalized path if it's cached
        if($this->cache->has(self::CACHE_NORMALIZED_PATH))
            return $this->cache->get(self::CACHE_NORMALIZED_PATH, $this->path);

        // Get the normalized path
        $path = FilesystemObjectUtils::getNormalizedPath($this);

        // Cache and return the normalized path
        $this->cache->set(self::CACHE_NORMALIZED_PATH, $path);
        return $path;
    }

    /**
     * Get the absolute path of the filesystem object.
     * A canonicalized version of the absolute path will be returned if the filesystem object exists.
     *
     * @return string|null Absolute path of the filesystem object or null on failure.
     */
    public function getAbsolutePath() {
        // Return the absolute path if it's cached
        if($this->cache->has(self::CACHE_ABSOLUTE_PATH))
            return $this->cache->get(self::CACHE_ABSOLUTE_PATH, $this->path);

        // Get the absolute path
        $path = FilesystemObjectUtils::getAbsolutePath($this);

        // Cache and return the absolute path
        $this->cache->set(self::CACHE_ABSOLUTE_PATH, $path);
        return $path;
    }

    /**
     * Get the canonicalized path of the filesystem object. The canonicalized path will be absolute.
     * A path which is invalid or doesn't exist will be canonicalized as far as that's possible.
     *
     * @return string|null Canonicalized path of the filesystem object, or null if failed to canonicalize the path.
     */
    // TODO: Improve this method!
    public function getCanonicalPath() {
        // Return the canonical path if it's cached
        if($this->cache->has(self::CACHE_CANONICAL_PATH))
            return $this->cache->get(self::CACHE_CANONICAL_PATH, null);

        // Get the canonicalized path
        $path = FilesystemObjectUtils::getCanonicalPath($this);

        // Cache and return the canonical path
        $this->cache->set(self::CACHE_CANONICAL_PATH, $path);
        return $path;
    }

    /**
     * Canonicalize the path.
     */
    public function canonicalize() {
        $this->setPath($this->getCanonicalPath());
    }

    /**
     * Delete the filesystem object if it exists.
     * Directories will only be deleted if they're empty or if the $recursive param is set to true.
     *
     * @param resource $context [optional] See the unlink() function for documentation.
     * @param bool $recursive [optional] True to delete directories recursively.
     * This option should be true if directories with contents should be deleted.
     *
     * @return int Number of deleted filesystem objects, a negative number will be returned if the $path param was
     * invalid.
     *
     * @see unlink()
     */
    public function delete($context = null, $recursive = false) {
        return FilesystemObjectUtils::delete($this, $context, $recursive);
    }

    /**
     * Flush the cache
     */
    public function flushCache() {
        $this->cache->flush();
    }

    /**
     * Get a File, Directory, SymbolicLink or FileSystemObject instance.
     * A File instance will be returned if the filesystem object is a file.
     * A Directory instance will be returned if the filesystem object is a directory.
     * A SymbolicLink instance will be returned if the filesystem object is a symbolic link.
     * A FileSystemObject instance will be returned if it couldn't be determined whether the filesystem object is a
     * file, directory or symbolic link. This is usually the case when the filesystem object doesn't exist.
     * The supplied filesystem object doesn't need to exist.
     * An optional child path relative to the $path param may be supplied.
     *
     * @param FilesystemObject|string $path Path to the filesystem object or the instance of another filesystem object
     * to use it's path.
     * @param string|null $child [optional] An optional child path relative to the $path param.
     * Null to just use the $path param as path.
     *
     * @return File|Directory|SymbolicLink|FilesystemObject|null File, Directory, SymbolicLink or FileSystemObject
     * instance, or null if the $path was invalid.
     */
    public static function from($path, $child = null) {
        // Create a filesystem object instance and make sure it's valid
        if(($path = FilesystemObjectUtils::getCombinedPath($path, $child)) === null)
            return null;

        // Return a File instance if the filesystem object is a file
        if(FilesystemObjectUtils::isFile($path))
            return new File($path);

        // Return a Directory instance if the filesystem object is a directory
        if(FilesystemObjectUtils::isDirectory($path))
            return new Directory($path);

        // Return a SymbolicLink instance if the filesystem object is a symbolic link
        if(FilesystemObjectUtils::isSymbolicLink($path))
            return new SymbolicLink($path);

        // Return as filesystem object instance
        return new FilesystemObject($path);
    }

    /**
     * Check whether the filesystem object path is valid. The filesystem object doesn't need to exist.
     *
     * @return bool True if the path of the filesystem object seems to be valid, false otherwise.
     */
    // TODO: Create static function of this, and check the path on construction.
    public function isValid() {
        return FilesystemObjectUtils::isValid($this);
    }

    /**
     * Compare this filesystem object with an other filesystem object.
     *
     * @param FilesystemObject|string $other The other filesystem object instance.
     * The path of a filesystem object may be supplied if $sameType is set to false to just compare the paths.
     * @param bool $sameType [optional] True to make sure both instances are from the same type,
     * false to just compare the paths.
     *
     * @return bool True if this filesystem object is equal with $other, false otherwise.
     * False will also be returned on failure.
     */
    // TODO: Improve the quality of this method!
    public function equals($other, $sameType = true) {
        // Make sure the types are equal
        if($sameType && !(get_class() === get_class($other)))
            return false;

        // Convert $other into a string, return false if failed
        if(($other = FilesystemObjectUtils::asPath($other, false)) === null)
            return false;

        // Compare the paths, return the result
        return StringUtils::equals($this->getPath(), $other, false, true);
    }

    /**
     * Convert the path to a string. The output of {@link getPath()} will be returned.
     *
     * @return string Path as a string.
     */
    public function __toString() {
        return $this->path;
    }

    /**
     * Clone this instance.
     *
     * @return FilesystemObject Cloned instance
     */
    public function __clone() {
        // Get the class type
        $class = get_class($this);

        // Clone and return the instance
        return new $class($this->path);
    }

    // TODO: Take a look at the getCanonicalPath, getAbsolutePath and canonicalize methods!
    // TODO: Method to convert anything possible into a path
}
