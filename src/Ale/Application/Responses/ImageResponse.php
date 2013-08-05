<?php

namespace Ale\Application\Responses;

/**
 * Image response.
 *
 * @author Martin Sadovy
 * @author Vít Ledvinka
 */
class ImageResponse extends \Nette\Object implements \Nette\Application\IResponse
{
	/** @var string|\Nette\Image */
	private $image;

	/** @var string */
	private $type;


	/**
	 * @param string|\Nette\Image $image image path or Nette\Image instance
	 * @param string $type
	 * @throws \Ale\InvalidArgumentException
	 * @throws \Nette\Application\BadRequestException
	 */
	public function __construct($image, $type = 'jpg')
	{
		if (is_string($image) AND !is_file($image)) {
			throw new \Nette\Application\BadRequestException("File '$image' doesn't exist.");
		} elseif (!$image instanceof \Nette\Image AND !is_string($image)
		) {
			throw new \Ale\InvalidArgumentException('Image must be instance Nette\Image or image path');
		}

		if (is_string($image)) {
			$type = strtolower(pathinfo($image, PATHINFO_EXTENSION));
		}

		$this->image = $image;
		$this->type = $type;
	}


	/**
	 * Returns the path to a file or Nette\Image instance.
	 * @return string|\Nette\Image
	 */
	final public function getImage()
	{
		return $this->image;
	}


	/**
	 * Returns the type of a image.
	 * @return string
	 */
	final public function getType()
	{
		return $this->type;
	}


	/**
	 * Sends response to output.
	 * @param \Nette\Http\IRequest $httpRequest
	 * @param \Nette\Http\IResponse $httpResponse
	 */
	public function send(\Nette\Http\IRequest $httpRequest, \Nette\Http\IResponse $httpResponse)
	{
		if ($this->image instanceof \Nette\Image) {
			echo $this->image->send($this->type, 100);
		} else {
			readfile($this->file);
		}
	}

}