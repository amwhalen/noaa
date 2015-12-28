<?php

namespace noaa\weather\cache;

class FileCache implements Cache {

    protected $directory;
    protected $data = array();

    /**
     * Constructor
     *
     * @param string $directory The writable directory in which to store cache files. Must exist and be writable.
     */
    public function __construct($directory) {
        $this->directory = realpath($directory);
    }

    /**
     * Fetches an entry from the cache.
     *
     * @param string $id cache id The id of the cache entry to fetch.
     * @return string The cached data or FALSE, if no cache entry exists for the given id.
     */
    function fetch($id) {
        if (!$this->contains($id)) {
            return false;
        }
        return file_get_contents($this->getFilename($id));
    }

    /**
     * Test if an entry exists in the cache.
     *
     * @param string $id cache id The cache id of the entry to check for.
     * @return boolean TRUE if a cache entry exists for the given cache id, FALSE otherwise.
     */
    function contains($id) {
        return !$this->isExpired($id) && file_exists($this->getFilename($id));
    }

    /**
     * Puts data into the cache.
     *
     * @param string $id The cache id.
     * @param string $data The cache entry/data.
     * @param int $lifeTime The lifetime. If != 0, sets a specific lifetime for this cache entry (0 => infinite lifeTime).
     * @return boolean TRUE if the entry was successfully stored in the cache, FALSE otherwise.
     */
    function save($id, $data, $lifeTime = 0) {
        $retval = @file_put_contents($this->getFilename($id), $data);
        if ($retval !== FALSE) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Deletes a cache entry.
     *
     * @param string $id cache id
     * @return boolean TRUE if the cache entry was successfully deleted, FALSE otherwise.
     */
    function delete($id) {
        return unlink($this->getFilename($id));
    }

    /**
     * Returns true if the given cache id has expired
     */
    protected function isExpired($id) {
        $filename = $this->getFilename($id);
        if (file_exists($filename)) {
            $mtime = filemtime($filename);
            // default to ONE HOUR for all objects
            if (time() - $mtime >= 3600) {
                $this->delete($id);
                return true;
            }
        }
        return false;
    }

    /**
     * Returns the filename for the given cache ID
     *
     * @param string $id The cache object ID.
     */
    protected function getFilename($id) {
        return $this->directory . '/' . $id . '.xml';
    }

}