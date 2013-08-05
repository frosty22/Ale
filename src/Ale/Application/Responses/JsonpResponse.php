<?php

namespace Ale\Application\Responses;

use Nette;

/**
 * Jsonp response
 *
 * @copyright Copyright (c) 2013 Ledvinka Vít
 * @author Ledvinka Vít, frosty22 <ledvinka.vit@gmail.com>
 *
 */
class JsonpResponse extends \Nette\Application\Responses\JsonResponse
{

	/**
	 * @var string
	 */
	public static $callbackName = "callback";


	/**
	 * @param Nette\Http\IRequest $httpRequest
	 * @param Nette\Http\IResponse $httpResponse
	 * @throws \Nette\Application\BadRequestException
	 */
	public function send(Nette\Http\IRequest $httpRequest, Nette\Http\IResponse $httpResponse)
	{
		$httpResponse->setContentType($this->contentType);
		$httpResponse->setExpiration(FALSE);

		$callback = $httpRequest->getQuery(self::$callbackName);
		if (is_null($callback)) {
			throw new \Nette\Application\BadRequestException("Invalid JSONP request.");
		}

		echo $callback . "(" . Nette\Utils\Json::encode($this->payload) . ")";
	}


}
