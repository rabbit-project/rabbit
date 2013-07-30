<?php
namespace Rabbit\Session;


use Rabbit\Lang\Singleton;
use Rabbit\Session\Exception\SessionException;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\Handler\NativeSessionHandler;
use Symfony\Component\HttpFoundation\Session\Storage\NativeSessionStorage;
use Symfony\Component\HttpFoundation\Session\Storage\SessionStorageInterface;

class SessionManager {

	private function __construct(){ }
	private function __clone(){ }

	/**
	 * @param string                  $namespace
	 * @param SessionStorageInterface $storage
	 *
	 * @return Session
	 */
	public static function session($namespace, SessionStorageInterface $storage = null) {
		if($storage==null)
			$storage = new NativeSessionStorage();
		$session = new Session($storage);
		$session->setName($namespace);
		return $session;
	}

}