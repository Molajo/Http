<?php
/**
 * Session Class
 *
 * @package   Molajo
 * @copyright 2013 Amy Stephen. All rights reserved.
 * @license   http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace Molajo\Http\Session;

defined('MOLAJO') or die;

use Exception;
use Molajo\Http\Session\Exception\SessionException;
use Molajo\Http\Session\Api\SessionInterface;

/**
 * Session Interface
 *
 * @package   Molajo
 * @license   http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright 2013 Amy Stephen. All rights reserved.
 * @since     1.0
 */
class Session implements SessionInterface
{
    /**
     * Returns the Current Session Status
     *
     * 0 = PHP_SESSION_DISABLED
     * 1 = PHP_SESSION_NONE
     * 2 = PHP_SESSION_ACTIVE
     *
     * @return string
     * @since   1.0
     */
    public function getStatus()
    {
        return session_status();
    }

    /**
     * Set the Current Session Save Path
     *
     * @param string $path
     *
     * @return string
     * @since   1.0
     */
    public function setSavePath($path)
    {
        return session_save_path($path);
    }

    /**
     * Get the Current Session Save Path
     *
     * @return string
     * @since   1.0
     */
    public function getSavePath()
    {
        return session_save_path();
    }

    /**
     * Does session exist?
     *
     * @return bool
     * @since   1.0
     */
    public function start()
    {
        if (session_id()) {
        } else {
            @session_start();

            return session_id();
        }

        return true;
    }

    /**
     * Returns Session ID
     *
     * @return string
     * @since   1.0
     * @throws  SessionException
     */
    public function getSessionId()
    {
        if (session_id()) {
        } else {
            $this->start();
        }

        return session_id();
    }

    /**
     * Regenerate Session ID
     *
     * @return string
     * @since   1.0
     * @throws  SessionException
     */
    public function regenerateSessionId()
    {
        if (session_id()) {
        } else {
            $this->start();
        }

        @session_regenerate_id(true);

        return session_id();
    }

    /**
     * Returns Session Name
     *
     * @return string
     * @since   1.0
     * @throws  SessionException
     */
    public function getSessionName()
    {
        if (session_name()) {
        } else {
            $this->start();
        }

        return session_name();
    }

    /**
     * Set Session Name
     *
     * @param string $name
     *
     * @return string
     * @since   1.0
     * @throws  SessionException
     */
    public function setSessionName($name)
    {
        if (session_name()) {
        } else {
            $this->start();
        }

        if (is_string($name) &&
            preg_match('#[^0-9.][^.]*\z#A', $name)
        ) {
        } else {

            throw new SessionException
            ('Session setSessionName Invalid Name: ' . $name);
        }

        session_name($name);

        return session_name();
    }

    /**
     * Does session exist?
     *
     * @param string $key
     *
     * @return bool
     * @since   1.0
     */
    public function exists($key)
    {
        if (session_id()) {
        } else {
            $this->getSessionId();
        }

        if (isset($_SESSION[$key])) {
        } else {
            return false;
        }

        return true;
    }

    /**
     * Set session
     *
     * @param int   $key
     * @param mixed $value
     *
     * @return mixed
     * @since   1.0
     * @throws  SessionException
     */
    public function set($key, $value)
    {
        if (session_id()) {
        } else {
            $this->start();
        }

        $key   = (string) $key;
        $value = serialize($value);

        try {

            $key            = htmlspecialchars($key);
            $value          = htmlspecialchars($value);
            $_SESSION[$key] = $value;

        } catch (Exception $e) {

            throw new SessionException
            ('Session Set Error: ' . $e->getMessage());
        }

        return $this;
    }

    /**
     * Gets the value stored in a session
     *
     * @param int $key
     *
     * @return mixed
     * @since   1.0
     * @throws  SessionException
     */
    public function get($key)
    {
        if (session_id()) {
        } else {
            $this->start();
        }

        $key = (string) $key;

        if ($this->exists($key) === true) {
        } else {
            $this->key = $_SESSION[$key];
        }

        $decoded = htmlspecialchars_decode($_SESSION[$key]);

        $value = @unserialize($decoded);

        if ($decoded === false && serialize(false) != $decoded) {
            throw new SessionException
                ('Session could not be retrieved for ' . $key);
        }

        return $value;
    }

    /**
     * Check CSRF Token
     *
     * @param string $key
     * @param string $request_method
     * @param string $request_key
     *
     * @return mixed
     * @since   1.0
     * @throws  SessionException
     */
    public function token($key = '', $request_method, $request_key)
    {
        if (session_id()) {
        } else {
            throw new SessionException
            ('Session required for CSFR Token');
        }

        $key = (string) $key;

        if (isset($_SESSION[$key])) {
        } else {
            $_SESSION[$key] = sha1(serialize($_SERVER) . mt_rand(0, 0xffffffff));
        }

        if ($this->exists($key) === true) {
        } else {
            $this->destroy();
            throw new SessionException
            ('Session Invalid or missing CSRF Token ' . $key);
        }

        $token = $_SESSION[$key];

        if (in_array($request_method, array('POST', 'PUT', 'DELETE'))) {

            if ($token == $request_key) {
            } else {
                $this->destroy();
                throw new SessionException
                ('Session Invalid or missing CSRF Token ' . $key);
            }
        }

        return $token;
    }

    /**
     * Delete a session
     *
     * @param int $key
     *
     * @return mixed
     * @since   1.0
     * @throws  SessionException
     */
    public function delete($key)
    {
        if (session_id()) {
        } else {
            $this->start();
        }

        $key = (string) $key;

        if ($this->exists($key) === true) {
        } else {
            throw new SessionException
            ('Session could not be deleted for ' . $key);
        }

        unset($_SESSION[(string) $key]);

        return $this;
    }

    /**
     * Destroy Session
     *
     * @return  object
     * @since   1.0
     * @throws  SessionException
     */
    public function destroy()
    {
        if (session_id()) {
            session_unset();
            session_destroy();
            $_SESSION = null;
        }

        return $this;
    }
}
