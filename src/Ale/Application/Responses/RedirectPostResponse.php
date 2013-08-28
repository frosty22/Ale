<?php

namespace Ale\Application\Responses;

use \Nette\Application\Responses;
use \Nette\Http;

/**
 * Redirect post data response.
 *
 * @author Martin Sadovy
 * @author Vít Ledvinka
 */
class RedirectPostResponse extends \Nette\Object implements \Nette\Application\IResponse
{

	/**
	 * Redirect message
	 * @var string
	 */
	public static $message = "Probíhá přesměrování, prosím vyčkejte 5 vteřin nebo klikněte na následující odkaz.";


	/**
	 * Redirect link text
	 * @var string
	 */
	public static $linkText = "Kliknutím přesměrujete IHNED";


	/** @var string */
	private $url;


	/**
	 * @var array
	 */
	private $getParams;


	/**
	 * @var array
	 */
	private $postParams;


	/** @var int */
	private $code;


	/**
	 * @param string $url
	 * @param array $getParams
	 * @param array $postParams
	 * @param int $code
	 */
	public function __construct($url, array $getParams = array(), array $postParams = array(), $code = Responses\IResponse::S303_POST_GET)
	{
		$this->url = (string) $url;
		$this->getParams = $getParams;
		$this->postParams = $postParams;
		$this->code = (int) $code;
	}


	/**
	 * @return string
	 */
	final public function getUrl()
	{
		return $this->url;
	}


	/**
	 * @return int
	 */
	final public function getCode()
	{
		return $this->code;
	}


	/**
	 * @return array
	 */
	final public function getGetParams()
	{
		return $this->getParams;
	}


	/**
	 * @return array
	 */
	final public function getPostParams()
	{
		return $this->postParams;
	}


	/**
	 * Generate full query.
	 * @return string
	 */
	public function getTargetUrl()
	{
		$url = $this->getUrl();

		if (!$this->getGetParams()) {
			return $url;
		}

		if (strpos($this->getUrl(), "?") === FALSE) {
			$url .= "?";
		}

		return $url . http_build_query($this->getGetParams());
	}


	/**
	 * Sends response to output.
	 * @param \Nette\Http\IRequest $httpRequest
	 * @param \Nette\Http\IResponse $httpResponse
	 * @return void
	 */
	public function send(Http\IRequest $httpRequest, Http\IResponse $httpResponse)
	{
		// Create form
		$form = \Nette\Utils\Html::el("form", array(
			"action" => $this->getTargetUrl(),
			"method" => "POST"
		));

		// Add inputs like hidden field
		foreach ($this->getPostParams() as $name => $value) {
			$input = \Nette\Utils\Html::el("input", array("type" => "hidden",
															 "name" => $name,
															 "value" => $value));
			$form->add($input);
		}

		// Add button like submit
		$button = \Nette\Utils\Html::el("input", array("type" => "submit", "value" => self::$linkText));
		$form->add($button);

		// Create body
		$body = \Nette\Utils\Html::el("body", array("id" => "RedirectPostResponse"))
					->add(self::$message)
					->add($form);

		// Create head with UTF-8 charset tag
		$head = \Nette\Utils\Html::el("head")
					->setHtml(\Nette\Utils\Html::el("meta", array("charset" => "utf-8")));

		// Create autosubmit script
		$autosubmit = '<script type="text/JavaScript"> window.setTimeout("document.forms[0].submit()", 3000); </script>';

		// Create HTML and render it
		echo \Nette\Utils\Html::el("html")
					->add($head)
					->add($body)
					->add($autosubmit)
					->render();
	}

}
