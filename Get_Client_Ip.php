<?php
/**
 * Get Client Ip Library
 * =====================
 *
 * Get_Client_Ip is a lightweight PHP class for detecting client real ip address.
 * It uses specific HTTP headers to detect the real/original (not private/reserved range) client ip address.
 *
 * @author      Aleksey Pevnev <pevnev@mail.ru>
 *
 * @license     Code and contributions have 'MIT License'
 *
 * @link        GitHub Repo:  https://github.com/worm/Get_Client_Ip
 *
 * @version     1.0.0
 */

class Get_Client_Ip
{
    /**
     * Stores the version number of the current release.
     */
    const VERSION   = '1.0.0';

    /**
     * All possible HTTP headers that represent the
     * IP address string.
     *
     * @var array
     */
    protected static $ipServerHeaders = array(
        'HTTP_X_FORWARDED_FOR',
        'X_FORWARDED_FOR',
        'HTTP_X_FORWARDED',
        'HTTP_X_CLUSTER_CLIENT_IP',
        'HTTP_FORWARDED_FOR',
        'HTTP_FORWARDED',
        'X-REAL-IP',
        'VIA',
        'HTTP_CLIENT_IP',
        'REMOTE_ADDR'
    );

    /**
     * HTTP headers in the PHP-flavor.
     *
     * @var array
     */
    protected $serverHeaders = array();

    /**
     * Construct an instance of this class.
     *
     * @param array  $headers   Specify the headers as injection. Should be PHP _SERVER flavored.
     *                          If left empty, will use the global _SERVER['HTTP_*'] vars instead.
     */
    public function __construct(
        array $headers = null
    ) {
        $this->setServerHeaders($headers);
    }

    /**
     * Get the current script version.
     *
     * @return string The version number in semantic version format.
     */
    public static function getScriptVersion()
    {
        return self::VERSION;
    }

    /**
     * Set the HTTP Headers. Must be PHP-flavored. This method will reset existing headers.
     *
     * @param array $serverHeaders The headers to set. If null, then using PHP's _SERVER to extract
     *                           the headers. The default null is left for backwards compatibilty.
     */
    public function setServerHeaders($serverHeaders = null)
    {
        // use global _SERVER if $httpHeaders aren't defined
        if (!is_array($serverHeaders) || !count($serverHeaders)) {
            $serverHeaders = $_SERVER;
        }

        // clear existing headers
        $this->serverHeaders = array();

        // Only headers with IP.
        foreach (self::getIpServerHeaders() as $key) {
            if (array_key_exists($key, $serverHeaders) === true) {
                $this->serverHeaders[$key] = $serverHeaders[$key];
            }
        }
    }

    /**
     * Retrieves the HTTP headers.
     *
     * @return array
     */
    public function getServerHeaders()
    {
        return $this->serverHeaders;
    }

    /**
     * Get all possible HTTP headers that
     * can contain the User-Agent string.
     *
     * @return array List of HTTP headers.
     */
    public function getIpServerHeaders()
    {
        return self::$ipServerHeaders;
    }

    /**
     * Ensures an ip address is both a valid IP and does not fall within
     * a private network range.
     *
     * @param $ip
     *
     * @return bool
     */
    public function validate_ip($ip)
    {
        if (!filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 | FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) === false) {
            return true;
        }

        return false;
    }

    /**
     * @param null  $serverHeaders
     *
     * @return bool|string
     */
    public function getClientIp($serverHeaders = null)
    {
        if ($serverHeaders) {
            $this->setServerHeaders($serverHeaders);
        }

        foreach ($this->getIpServerHeaders() as $ipHeader) {
            if (isset($this->serverHeaders[$ipHeader])) {
                foreach (explode(',', $this->serverHeaders[$ipHeader]) as $ip) {
                    $ip = trim($ip);
                    if (self::validate_ip($ip)) {
                        return $ip;
                    }
                }
            }
        }

        return false;
    }
}