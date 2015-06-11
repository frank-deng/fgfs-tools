<?php
const CRLF = "\r\n";
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

	public function __construct($host = '127.0.0.1', $port = 5400, $timeout = 1) {
		//Initialize socket
		$this->conn = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
		if (!$this->conn) {
			throw new SocketException('Failed to initialize socket.');
		}

		//Set timeout
		socket_set_option($this->conn, SOL_SOCKET, SO_RCVTIMEO,
			array('sec' => $timeout, 'usec' => 0));
		socket_set_option($this->conn, SOL_SOCKET, SO_SNDTIMEO,
			array('sec' => $timeout, 'usec' => 0));

		//Connect to FlightGear telnet host
		if (!socket_connect($this->conn, $host, $port)) {
			throw new SocketException(socket_strerror(socket_last_error()));
		}

		//Switch to data mode to avoid getting some other things
		//apart from the data wanted
		if (!socket_write($this->conn, 'data'.CRLF)) {
			throw new SocketException(socket_strerror(socket_last_error()));
		}
	}
	public function __destruct() {
		if (null != $this->conn) {
			$this->close();
		}
	}
	public function close() {
		socket_write($this->conn, 'quit'.CRLF);
		socket_close($this->conn);
		$this->conn = null;
	}
	public function set($prop, $value) {
		if (!socket_write($this->conn, 'set '.$prop.' '.(string)$value.CRLF)) {
			throw new SocketException(socket_strerror(socket_last_error()));
		}
	}
	public function run($command) {
		if (!socket_write($this->conn, 'run '.(string)$command.CRLF)) {
			throw new SocketException(socket_strerror(socket_last_error()));
		}
	}
	public function get($prop) {
		if ('' === $prop) {
			return '';
		}
		if (!socket_write($this->conn, 'get '.$prop.CRLF)) {
			throw new SocketException(socket_strerror(socket_last_error()));
		}
		$result = socket_read($this->conn, 65535, PHP_BINARY_READ);
		return str_replace(CRLF, '', $result);
	}

	/**
	 * Get the value of specified prop and convert the result to the
	 * according types.
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
