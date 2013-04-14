<?php
/**
 * Session Interface
 *
 * @package   Molajo
 * @copyright 2013 Amy Stephen. All rights reserved.
 * @license   http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace Molajo\Http\Session\Api;

defined('MOLAJO') or die;

use Molajo\Http\Session\Exception\SessionException;

/**
 * Session Interface
 *
 * @package   Molajo
 * @license   http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright 2013 Amy Stephen. All rights reserved.
 * @since     1.0
 */
interface SessionInterface
{
    /**
     * Returns the Current Session Status
     *
     * PHP_SESSION_DISABLED if sessions are disabled.
     * PHP_SESSION_NONE if sessions are enabled, but none exists.
     * PHP_SESSION_ACTIVE if sessions are enabled, and one exists.
     *
     * @return string
     * @since   1.0
     * @throws  SessionException;
     */
    public function getStatus();

    /**
     * Set the Current Session Save Path
     *
     * @param string $path
     *
     * @return string
     * @since   1.0
     * @throws  SessionException;
     */
    public function setSavePath($path);

    /**
     * Get the Current Session Save Path
     *
     * @return string
     * @since   1.0
     * @throws  SessionException;
     */
    public function getSavePath();

    /**
     * Start Session
     *
     * @return  object
     * @since   1.0
     * @throws  SessionException;
     */
    public function start();

    /**
     * Gets the value of Session Id
     *
     * @return mixed
     * @since   1.0
     * @throws  SessionException;
     */
    public function getSessionId();

    /**
     * Regenerate Session Id
     *
     * @return mixed
     * @since   1.0
     * @throws  SessionException;
     */
    public function regenerateSessionId();

    /**
     * Gets the value of Session Name
     *
     * @return mixed
     * @since   1.0
     * @throws  SessionException;
     */
    public function getSessionName();

    /**
     * Gets the value of Session Name
     *
     * @param string $name
     *
     * @return mixed
     * @since   1.0
     * @throws  SessionException;
     */
    public function setSessionName($name);

    /**
     * Does session exist?
     *
     * @param string $key
     *
     * @return mixed
     * @since   1.0
     * @throws  SessionException;
     */
    public function exists($key);

    /**
     * Set session
     *
     * @param int   $key
     * @param mixed $value
     *
     * @return mixed
     * @since   1.0
     * @throws  SessionException;
     */
    public function set($key, $value);

    /**
     * Gets the value stored in a session
     *
     * @param int $key
     *
     * @return mixed
     * @since   1.0
     * @throws  SessionException;
     */
    public function get($key);

    /**
     * CSRF Token create and validate
     *
     * @param int    $key
     * @param string $request_method
     * @param string $request_key
     *
     * @return mixed
     * @since   1.0
     * @throws  SessionException;
     */
    public function token($key, $request_method, $request_key);

    /**
     * Delete a session
     *
     * @param int $key
     *
     * @return mixed
     * @since   1.0
     * @throws  SessionException;
     */
    public function delete($key);

    /**
     * Destroy Session
     *
     * @return mixed
     * @since   1.0
     * @throws  SessionException;
     */
    public function destroy();
}
