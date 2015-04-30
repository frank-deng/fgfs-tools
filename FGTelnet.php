<?php
class SocketException extends Exception {
	public function __construct($message, $code = 0, Exception $previous = null) {
        parent::__construct($message, $code, $previous);
    }

    // custom string representation of object
    public function __toString() {
        return __CLASS__ . ': ' . $this->message;
    }
}
class FGTelnet {
	private $conn = null;

	public function __construct($host = '127.0.0.1', $port) {
		$this->conn = @socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
		if (!$this->conn) {
			throw new SocketException('Failed to initialize socket.');
		}
		if (!@socket_connect($this->conn, $host, $port)) {
			throw new SocketException(socket_strerror(socket_last_error()));
		}
		if (!@socket_write($this->conn, 'data'."\r\n")) {
			throw new SocketException(socket_strerror(socket_last_error()));
		}
	}
	public function __destruct() {
		if (null != $this->conn) {
			@socket_write($this->conn, 'quit'."\r\n");
			@socket_close($this->conn);
		}
	}
	public function set($prop, $value) {
		if (!@socket_write($this->conn, 'set '.$prop.' '.string($value)."\r\n")) {
			throw new SocketException(socket_strerror(socket_last_error()));
		}
	}
	public function get($prop) {
		if (!@socket_write($this->conn, 'get '.$prop."\r\n")) {
			throw new SocketException(socket_strerror(socket_last_error()));
		}
		$result = @socket_read($this->conn, 65535, PHP_BINARY_READ);
		return str_replace("\r\n", '', $result);
	}

	/**
	 * Get the value of specified prop and convert the result to the according types
	 */
	public function getInt($prop) {
		return (int)($this->get($prop));
	}
	public function getFloat($prop) {
		return (float)($this->get($prop));
	}
	public function getBool($prop) {
		return (($this->get($prop) == 'true') ? true : false);
	}
}
?>
