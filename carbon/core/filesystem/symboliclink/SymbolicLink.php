<?php

namespace carbon\core\filesystem\symboliclink;

use carbon\core\filesystem\directory\Directory;
use carbon\core\filesystem\file\File;
use carbon\core\filesystem\FilesystemObject;
use carbon\core\filesystem\symboliclink\SymbolicLinkUtils;

// Prevent direct requests to this set_file due to security reasons
defined('CARBON_CORE_INIT') or die('Access denied!');

class SymbolicLink extends FilesystemObject {

    // TODO: Ability to create a symbolic link, with a static function.

    /**
     * Get the SymbolicLink instance from a FileSystemObject instance or the path a string from a symbolic link.
     * If the filesystem object is an existing file or directory, null will be returned.
     * The symbolic link or link path has to be valid.
     *
     * @param \carbon\core\filesystem\FilesystemObject|string $link Filesystem object instance or the path of a symbolic link as a string.
     *
     * @return SymbolicLink|null The link as a SymbolicLink instance.
     * Or null if the link couldn't be cast to a SymbolicLink instance.
     */
    public static function asSymbolicLink($link) {
        return SymbolicLinkUtils::asSymbolicLink($link, null);
    }

    /**
     * Get the name of the symbolic link without any path information.
     * Alias of {@link FileSystemObject::getBasename()}
     *
     * @return string Name of the symbolic link.
     *
     * @see FileSystemObject::getBasename();
     */
    public function getLinkName() {
        return $this->getBasename();
    }

    /**
     * Get the target of the symbolic link.
     *
     * @return \carbon\core\filesystem\directory\Directory|\carbon\core\filesystem\file\File|SymbolicLink|\carbon\core\filesystem\FilesystemObject|null
     * The target will be returned as Directory, File or SymbolicLink instance if the target exist.
     * If the target doesn't exist it will be returned as Path instance instead.
     * Null will be returned if the symbolic link was invalid.
     */
    public function getTarget() {
        return SymbolicLinkUtils::getTarget($this);
    }

    /**
     * Check whether the symbolic link exists.
     *
     * @return bool True if the symbolic link exists, false otherwise.
     * False will be returned if the path of the symbolic link is invalid.
     */
    public function exists() {
        return SymbolicLink::exists($this);
    }

    /**
     * Check whether the symbolic link is valid. The link doesn't need to exist.
     * The file may not be an existing file or directory.
     *
     * @return bool True if the symbolic link seems to be valid, false otherwise.
     */
    public function isValid() {
        return SymbolicLinkUtils::isValid($this);
    }
}
