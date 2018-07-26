<?php

namespace Digipost\Signature\API\XML\Thirdparty\XMLdSig;

/**
 * Class representing ObjectType
 *
 *
 * XSD Type: ObjectType
 */
class ObjectType
{

    private $content = [];

    /**
     * @property string $id
     */
    private $id = null;

    /**
     * @property string $mimeType
     */
    private $mimeType = null;

    /**
     * @property string $encoding
     */
    private $encoding = null;

    /**
     * Gets as id
     *
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Sets a new id
     *
     * @param string $id
     * @return self
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * Gets as mimeType
     *
     * @return string
     */
    public function getMimeType()
    {
        return $this->mimeType;
    }

    /**
     * Sets a new mimeType
     *
     * @param string $mimeType
     * @return self
     */
    public function setMimeType($mimeType)
    {
        $this->mimeType = $mimeType;
        return $this;
    }

    /**
     * Gets as encoding
     *
     * @return string
     */
    public function getEncoding()
    {
        return $this->encoding;
    }

    /**
     * Sets a new encoding
     *
     * @param string $encoding
     * @return self
     */
    public function setEncoding($encoding)
    {
        $this->encoding = $encoding;
        return $this;
    }

  /**
   * @return array
   */
  public function getContent(): array
  {
    return $this->content;
  }

  /**
   * @param array $content
   * @return ObjectType
   */
  public function setContent(... $content)
  {
    $this->content = $content;
    return $this;
  }
}

