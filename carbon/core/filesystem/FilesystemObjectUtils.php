<?php

namespace carbon\core\filesystem;

use carbon\core\filesystem\directory\Directory;

// Prevent direct requests to this set_file due to security reasons
defined('CARBON_CORE_INIT') or die('Access denied!');

class FilesystemObjectUtils implements FilesystemObjectFlags {

    /**
     * Get the path of a filesystem object as a string. The path isn't validated unless $validate is set to true.
     *
     * @param FilesystemObject|string $path Filesystem object instance or a path string.
     * @param bool $validate [optional] True if the path should be validated, false to skip any validation.
     * @param mixed|null $default [optional] Default value returned if the path couldn't be determined,
     * possibly because the $path param was invalid.
     *
     * @return string|mixed The path of the filesystem object as a string. Or the $default param value on failure.
     * The $default value will also be returned if the path was invalid while $validate was set to true.
     */
    public static function asPath($path, $validate = false, $default = null) {
        // Return the path if it's a string already
        if(is_string($path))
            return $path;

        // Return the path if it's a filesystem object instance
        if($path instanceof FilesystemObject) {
            // Validate the path
            if($validate)
                if(!$path->isValid())
                    return $default;

            // Return the path
            return $path->getPath();
        }

        // Return the default value
        return $default;
    }

    /**
     * Get the paths of a list of filesystem object as an array of strings.
     *
     * @param Array|FilesystemObject|string $paths An array with filesystem object instances or path strings.
     * Or a single filesystem object instance or path string. The array may contain multiple other arrays.
     * @param bool $ignoreInvalid True to ignore invalid items in the array, this will prevent the default value from
     * being returned.
     * @param mixed|null $default [optional] The default value returned on failure.
     *
     * @return Array|mixed|null An array of path strings, or the $default value on failure.
     */
    public static function asPathArray($paths, $ignoreInvalid = false, $default = null) {
        // Create an array to push all the paths in
        $out = Array();

        // Make sure $paths isn't null
        if($paths === null)
            return null;

        // Create an array of the $paths param
        if(!is_array($paths))
            $paths = Array($paths);

        // Process each filesystem object
        $pathsCount = sizeof($paths);
        for($i = 0; $i < $pathsCount; $i++) {
            $path = $paths[$i];

            // Check whether $path is an array
            if(is_array($path)) {
                // Get all paths from the array and make sure the result is valid
                if(($arrayPaths = self::asPathArray($path, $ignoreInvalid)) === null)
                    return $default;

                // Push the paths into the array
                $out = array_merge($out, $arrayPaths);
                continue;
            }

            // Get the path as a string
            $path = self::asPath($path, false);

            // Check whether the path is valid
            if(is_string($path)) {
                array_push($out, $path);
                continue;
            }

            // Check whether we should return the default value because the conversion of this path failed
            if(!$ignoreInvalid)
                return $default;
        }

        // Return the array of paths
        return $out;
    }

    /**
     * Combine a parent and child path, where the parent path is the base path, and the child path is relative to the
     * base path.
     *
     * @param FilesystemObject|string $parent Parent path or filesystem object instance to use as base.
     * @param string $child [optional] The child path relative to the parent path. Null to just use the parent path.
     * @param mixed|null $default [optional] An optional default value, that will be returned if the $path or $child
     * param was invalid.
     *
     * @return string|mixed|null
     */
    public static function getCombinedPath($parent, $child = null, $default = null) {
        // Convert the path into a absolute path string, return the $default value if failed
        if(($parent = self::getAbsolutePath(self::asPath($parent, false))) === null)
            return $default;

        // Check whether we should suffix the child path, if not, return the path
        if(empty($child))
            return $parent;

        // Make sure the $child param is a string
        if(!is_string($child))
            return $default;

        // Trim directory separators from both paths
        // TODO: Is the coded below unnecessary?
        $parent = rtrim($parent, '/\\');
        $child = ltrim($child, '/\\');

        // Combine and return the base path with the child path
        return $parent . DIRECTORY_SEPARATOR . $child;
    }

    /**
     * Check whether a filesystem object exists.
     *
     * @param FilesystemObject|string $path Filesystem object instance or a path string.
     *
     * @return bool True if the filesystem object exists, false otherwise.
     */
    public static function exists($path) {
        // Convert the path into a string, return false if failed
        if(($path = self::asPath($path, false)) === null)
            return false;

        // Check if the object exists, return the result
        return file_exists($path);
    }

    /**
     * Check whether a filesystem object exists and is a file.
     *
     * @param FilesystemObject|string $path Filesystem object instance or a path string.
     *
     * @return bool True if the filesystem object exists and is a file, false otherwise.
     */
    public static function isFile($path) {
        // Convert the path into a string, return false if failed
        if(($path = self::asPath($path, false)) === null)
            return false;

        // Check if the object exists and is a file, return the result
        return is_file($path);
    }

    /**
     * Check whether a filesystem object exists and is a directory.
     *
     * @param FilesystemObject|string $path Filesystem object instance or a path string.
     *
     * @return bool True if the filesystem object exists and is a directory, false otherwise.
     */
    public static function isDirectory($path) {
        // Convert the path into a string, return false if failed
        if(($path = self::asPath($path, false)) === null)
            return false;

        // Check if the object exists and is a directory, return the result
        return is_dir($path);
    }

    /**
     * Check whether a filesystem object exists and is a symbolic link.
     *
     * @param FilesystemObject|string $path Filesystem object instance or a path string.
     *
     * @return bool True if the filesystem object exists and is a symbolic link, false otherwise.
     */
    public static function isSymbolicLink($path) {
        // Convert the path into a string, return false if failed
        if(($path = self::asPath($path, false)) === null)
            return false;

        // Check if the object exists and is a symbolic link, return the result
        return is_link($path);
    }

    /**
     * Check whether a file or directory exists and is readable.
     *
     * @param FilesystemObject|string $path Filesystem object instance or a path string.
     *
     * @return bool True if the file or directory exists and is readable, false otherwise.
     * False will also be returned if the path was invalid.
     */
    public static function isReadable($path) {
        // Convert the file into a path string, return the $default value if failed
        if(($path = self::asPath($path, false)) === null)
            return false;

        // Check whether the file is readable, return the result
        return is_readable($path);
    }

    /**
     * Check whether a file or directory exists and is writable.
     *
     * @param FilesystemObject|string $path Filesystem object instance or a path string.
     *
     * @return bool True if the file or directory exists and is writable, false otherwise.
     * False will also be returned if the path was invalid.
     */
    public static function isWritable($path) {
        // Convert the file into a path string, return the $default value if failed
        if(($path = self::asPath($path, false)) === null)
            return false;

        // Check whether the file is writable, return the result
        return is_writable($path);
    }

    /**
     * Alias of {@link FilesystemObjectUtils::isWritable()}.
     * Check whether a file or directory exists and is writable.
     *
     * @param FilesystemObject|string $path Filesystem object instance or a path string.
     *
     * @return bool True if the file or directory exists and is writable, false otherwise.
     * False will also be returned if the path was invalid.
     */
    public static function isWriteable($path) {
        return self::isWritable($path);
    }

    /**
     * Get the basename of a filesystem object.
     * For files, this will return the file name with it's extension.
     * For directories and symbolic links, this will return the name of the directory or symbolic link.
     * The $default value will be returned on failure.
     *
     * @param FilesystemObject|string $path Filesystem object instance or a path string.
     * @param string|null $suffix [optional] Suffix to omit from the basename. Null to ignore this feature.
     * @param mixed|null $default [optional] A default value that will be returned on failure.
     *
     * @return string|mixed|null Basename of the filesystem object, or the $default value on failure.
     */
    public static function getBasename($path, $suffix = null, $default = null) {
        // Convert the path into a string, return the default value if failed
        if(($path = self::asPath($path, false)) === null)
            return $default;

        // Get and return the basename
        return basename($path, $suffix);
    }

    /**
     * Get the parent directory of a filesystem object.
     * This will return the directory the filesystem object is located in.
     * Running this method against the root directory will fail because it doesn't have a prent directory,
     * and will return the $default value.
     *
     * @param FilesystemObject|string $path Filesystem object instance or a path string.
     * @param mixed|null $default [optional] A default value that will be returned if the filesystem object doesn't
     * have a parent directory and on failure.
     *
     * @return Directory|mixed|null The parent directory as Directory instance, or the $default value if the object
     * doesn't have a parent directory or on failure.
     */
    public static function getParent($path, $default = null) {
        // Convert the path into a string, return the default value if failed
        if(($path = self::asPath($path, false)) === null)
            return $default;

        // Get the parent directory path
        $parent = dirname($path);

        // Make sure there's a parent to return
        if($parent === '.' || empty($parent))
            return $default;

        // Return the parent directory as Directory instance
        return new Directory($parent);
    }

    /**
     * Check whether the filesystem object has a parent directory.
     * The root directory of the system doesn't have a parent directory, and thus will return false.
     *
     * @param FilesystemObject|string $path Filesystem object instance or a path string.
     *
     * @return bool True if the object has a parent directory, false otherwise. False will also be returned on failure.
     */
    public static function hasParent($path) {
        // Convert the path into a string, return false if failed
        if(($path = self::asPath($path, false)) === null)
            return false;

        // Get the parent directory
        $parent = trim(dirname($path));

        // Check whether the parent directory is a real parent, return the result
        return $parent === '.' || empty($parent);
    }

    /**
     * Get the normalized path of a filesystem object.
     * This will remove unicode whitespaces and any kind of self referring or parent referring paths.
     * The filesystem object doesn't need to exist.
     *
     * @param FilesystemObject|string $path Filesystem object instance or a path string.
     * @param mixed|null $default [optional] The default value returned on failure.
     *
     * @return string|null A normalized path of the filesystem object,
     */
    // TODO: Improve the quality of this method!
    public static function getNormalizedPath($path, $default = null) {
        // Convert the path into a string, return the $default value if failed
        if(($path = self::asPath($path, false)) === null)
            return $default;

        // Remove any kind of funky unicode whitespace
        $normalized = preg_replace('#\p{C}+|^\./#u', '', $path);

        // Path remove self referring paths ("/./").
        $normalized = preg_replace('#/\.(?=/)|^\./|\./$#', '', $normalized);

        // Regex for resolving relative paths
        $regex = '#\/*[^/\.]+/\.\.#Uu';
        while(preg_match($regex, $normalized))
            $normalized = preg_replace($regex, '', $normalized);

        // Check whether the path is outside of the defined root path
        if(preg_match('#/\.{2}|\.{2}/#', $normalized))
            return $default;

        // Remove unwanted prefixed directory separators, return the result
        $firstChar = substr($normalized, 0, 1);
        if($firstChar === '\\' || $firstChar === '/')
            return substr($normalized, 0, 1) . trim(substr($normalized, 1), '\\/');
        return rtrim($normalized, '\\/');;
    }

    /**
     * Get the absolute path of a filesystem object. The filesystem object doesn't need to exist.
     * A canonicalized version of the absolute path will be returned if the filesystem object exists.
     *
     * @param FilesystemObject|string $path Filesystem object instance or path to get the absolute path for.
     * @param mixed|null $default [optional] Default value to be returned on failure.
     *
     * @return string|mixed|null The absolute path of the filesystem object, or the $default value if failed.
     */
    // TODO: Improve the quality of this method!
    public static function getAbsolutePath($path, $default = null) {
        // Get the normalized path, return the $default value if failed
        if(($path = self::getNormalizedPath($path)) === null)
            return $default;

        // Try to get the real path using PHPs function, return the result if succeed.
        if(($realPath = realpath($path)) !== false)
            return $realPath;

        // Try to make the path absolute without any system functions
        // Check whether the path is in unix format or not
        $isUnixPath = empty($path) || $path{0} != '/';

        // Detect whether the path is relative, if so prefix the current working directory
        if(strpos($path, ':') === false && $isUnixPath)
            $path = getcwd() . DIRECTORY_SEPARATOR . $path;

        // Put initial separator that could have been lost
        $path = !$isUnixPath ? '/' . $path : $path;

        // Resolve any symlinks
        if(file_exists($path) && linkinfo($path) > 0)
            $path = readlink($path);

        // Return the result
        return $path;
    }

    /**
     * Get the canonical path of a filesystem object. The filesystem object doesn't need to exist.
     *
     * @param FilesystemObject|string $path Filesystem object instance or path to get the canonical path for.
     * @param mixed|null $default [optional] Default value to be returned on failure.
     *
     * @return string|mixed|null The canonicalized path, or the $default value on failure.
     */
    // TODO: Improve the quality of this method!
    public static function getCanonicalPath($path, $default = null) {
        // Convert the path into a string, return the $default value if failed
        if(($path = self::asPath($path, false)) === null)
            return $default;

        // Try to get the real path using PHPs function, return the result if succeed.
        if(($realPath = realpath($path)) !== false)
            return $realPath;

        // Try to canonicalize the path even though it doesn't exist (Inspired by Sven Arduwie, Thanks!)
        // Get the absolute path and make sure it's valid
        if(($path = self::getAbsolutePath($path)) === null)
            return $default;

        // Check whether the path is in unix format or not
        $isUnixPath = empty($path) || $path{0} != '/';

        // Resolve all path parts (single dot, double dot and double delimiters)
        $path = str_replace(Array('/', '\\'), DIRECTORY_SEPARATOR, $path);
        $parts = array_filter(explode(DIRECTORY_SEPARATOR, $path), 'strlen');
        $absolutes = Array();
        foreach($parts as $part) {
            if('.' == $part)
                continue;
            if('..' == $part)
                array_pop($absolutes);
            else
                $absolutes[] = $part;
        }
        $path = implode(DIRECTORY_SEPARATOR, $absolutes);

        // Put initial separator that could have been lost
        if(!$isUnixPath)
            $path = '/' . $path;

        // Return the result
        return $path;
    }

    /**
     * Delete a filesystem object if it exists.
     * Directories will only be deleted if they're empty or if the $recursive param is set to true.
     *
     * @param Array|FilesystemObject|string $paths Filesystem object instance or a path string to delete.
     * Or an array with filesystem object instances or path strings. An array may contain multiple other arrays.
     * @param resource $context [optional] See the unlink() function for documentation.
     * @param bool $recursive [optional] True to delete directories recursively.
     * This option should be true if directories with contents should be deleted.
     *
     * @return int Number of deleted filesystem objects, a negative number will be returned on failure.
     *
     * @see unlink()
     */
    public static function delete($paths, $context = null, $recursive = false) {
        // Convert the paths into a string array, return the $default value if failed
        if(($paths = self::asPathArray($paths)) === null)
            return -1;

        // Count the deleted filesystem objects
        $count = 0;

        // Delete each filesystem object
        $size = sizeof($paths);
        for($i = 0; $i < $size; $i++) {
            $path = $paths[$i];

            // Delete directories
            if(self::isDirectory($path)) {
                // Get the current filesystem object as directory instance
                $dir = new Directory($path);

                // If we need to delete the directory recursively, we need to delete the directory contents first
                if($recursive)
                    $count += $dir->deleteContents($context);

                // Delete the directory itself, and return the number of deleted filesystem objects
                $count += @rmdir($path, $context);
                continue;
            }

            // Delete everything else
            if(@unlink($path, $context))
                $count++;
        }

        // Return the number of delete filesystem objects
        return $count;
    }

    /**
     * Rename a file or directory. The filesystem object must exist.
     *
     * @param FileSystemObject|string $oldPath The filesystem object instance or the path of the filesystem object as
     * a string. The filesystem object must be a existing file or directory and can't be a symbolic link.
     * @param FileSystemObject|string $newPath The filesystem object instance of the path of the filesystem object to
     * rename the object to. This filesystem object or path should include the full path. The object may only exist if
     * $overwrite is set to true or the renaming will fail.
     * @param bool $overwrite [optional] True to overwrite the existing filesystem object when the target name already
     * exist, false otherwise.
     * @param resource $context [optional] See the rename() function for documentation.
     *
     * @return bool True if the filesystem object was successfully renamed, false on failure.
     *
     * @see rename();
     */
    // TODO: Check recursiveness when renaming directories with content!
    public static function rename($oldPath, $newPath, $overwrite = false, $context = null) {
        // Convert $path and $target into an absolute path string, return false on failure
        if(($oldPath = self::getAbsolutePath($oldPath)) === null)
            return false;
        if(($newPath = self::getAbsolutePath($newPath)) === null)
            return false;

        // Validate the path and target
        if(!self::exists($oldPath) || self::isSymbolicLink($oldPath) || !self::isValid($newPath))
            return false;

        // Do an overwrite check
        if(self::exists($newPath) && !$overwrite)
            return false;

        // Rename the object, return the result
        if($context !== null)
            return rename($oldPath, $newPath, $context);
        return rename($oldPath, $newPath);
    }

    public static function move($path, $target, $recursive = false) {
        // Convert $path and $target into a path string, return false on failure
        if(($path = self::asPath($path, false)) === null)
            return false;
        if(($target = self::asPath($target, false)) === null)
            return false;

        // Validate the path and target
        if(!self::exists($path) || !self::isValid($target))
            return false;

        // Check whether the $path is a directory
        if(self::isDirectory($path)) {
            // Make sure recursive mode is enabled
            if(!$recursive)
                return false;

            // TODO: Use the self::rename() method to move the filesystem object!
        }
    }

    /**
     * Validate the path or a array of paths of a filesystem object.
     * The objects doesn't need to exist in order to be valid.
     *
     * @param FilesystemObject|string|array $paths Filesystem object instance, the path as a string or an array with
     * paths to check all paths at once.
     * @param bool $allValid [optional] True to make sure all paths are valid when an array of paths is supplied with
     * $paths. False to make sure just one of the paths is valid.
     *
     * @return bool True if the paths of the filesystem objects seems to be valid, false otherwise.
     */
    // TODO: Improve method quality!
    public static function isValid($paths, $allValid = true) {
        // Convert $path into an array of paths, return false if failed
        if(($paths = self::asPathArray($paths)) === null)
            return false;

        // Count the paths that should be validated, keep track whether any paths was valid
        $pathCount = sizeof($paths);
        $anyValid = false;

        // Validate all the paths
        for($i = 0; $i < $pathCount; $i++) {
            // Trim the path, and make sure the path is set
            $path = @trim($paths[$i]);
            if(empty($path)) {
                if($allValid)
                    return false;
                continue;
            }

            // TODO: Use better validation for $path!

            // This path seems to be valid
            $anyValid = true;
        }

        // All or some paths seem to be valid, return the result
        return $allValid ? true : $anyValid;
    }

    // TODO: Implement $context usage better, possibly optionally with a function instead of using function parameters!
}
