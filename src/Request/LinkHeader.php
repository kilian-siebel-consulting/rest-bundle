<?php
namespace Ibrows\RestBundle\Request;

class LinkHeader
{
    /**
     * @var string
     */
    private $originalHeader;

    /**
     * @var string
     */
    private $value;

    /**
     * @var string
     */
    private $relation;

    /**
     * @var string
     */
    private $reverseRelation;

    /**
     * @var string
     */
    private $title;

    /**
     * @var string
     */
    private $anchor;

    /**
     * @var array<string, string>
     */
    private $extensions;

    /**
     * @var array<string, mixed>
     */
    private $urlParameters;

    /**
     * @var object
     */
    private $resource;

    /**
     * LinkHeader constructor.
     *
     * @param string $originalHeader
     */
    public function __construct($originalHeader)
    {
        $this
            ->setOriginalHeader($originalHeader)
            ->setExtensions([])
            ->parse()
        ;
    }

    private function parse()
    {
        $this
            ->parseValue()
            ->parseMetadata()
        ;

        return $this;
    }

    /**
     * Parse Value
     *
     * @return LinkHeader
     */
    private function parseValue()
    {
        if(preg_match('/^\s*<([^>]*)>/', $this->getOriginalHeader(), $valueParts)) {
            $this->setValue($valueParts[1]);
        }

        return $this;
    }

    /**
     * Parse MetaData
     *
     * @return LinkHeader
     */
    private function parseMetadata()
    {
        if(preg_match_all('/;\s*([a-zA-Z0-9]+)="([^"]+)"/', $this->getOriginalHeader(), $metaParts) > 0) {
            for($i = 0; $i < count($metaParts[0]); $i++) {
                $this->applyMetadata($metaParts[1][$i], $metaParts[2][$i]);
            }
        }

        return $this;
    }

    /**
     * Apply MetaData
     *
     * @param string $key
     * @param string $value
     *
     * @return LinkHeader
     */
    private function applyMetadata($key, $value)
    {
        switch($key) {
            case 'rel':
                $this->setRelation($value);
                break;
            case 'rev':
                $this->setReverseRelation($value);
                break;
            case 'title':
                $this->setTitle($value);
                break;
            case 'anchor':
                $this->setAnchor($value);
                break;
            default:
                $this->addExtension($key, $value);
                break;
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getOriginalHeader()
    {
        return $this->originalHeader;
    }

    /**
     * @param string $originalHeader
     *
     * @return LinkHeader
     */
    private function setOriginalHeader($originalHeader)
    {
        $this->originalHeader = $originalHeader;
        return $this;
    }

    /**
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param string $value
     *
     * @return LinkHeader
     */
    private function setValue($value)
    {
        $this->value = $value;
        return $this;
    }

    /**
     * @return string
     */
    public function getRelation()
    {
        return $this->relation;
    }

    /**
     * @param string $relation
     *
     * @return LinkHeader
     */
    private function setRelation($relation)
    {
        $this->relation = $relation;
        return $this;
    }

    /**
     * @return string
     */
    public function getReverseRelation()
    {
        return $this->reverseRelation;
    }

    /**
     * @param string $reverseRelation
     *
     * @return LinkHeader
     */
    private function setReverseRelation($reverseRelation)
    {
        $this->reverseRelation = $reverseRelation;
        return $this;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $title
     *
     * @return LinkHeader
     */
    private function setTitle($title)
    {
        $this->title = $title;
        return $this;
    }

    /**
     * @return string
     */
    public function getAnchor()
    {
        return $this->anchor;
    }

    /**
     * @param string $anchor
     *
     * @return LinkHeader
     */
    private function setAnchor($anchor)
    {
        $this->anchor = $anchor;
        return $this;
    }

    /**
     * @return array
     */
    public function getExtensions()
    {
        return $this->extensions;
    }

    /**
     * @param string $extension
     * @param string $value
     *
     * @return LinkHeader
     */
    private function addExtension($extension, $value)
    {
        $this->extensions[$extension] = $value;
        return $this;
    }

    /**
     * @param string $extension
     *
     * @return string
     */
    public function getExtension($extension)
    {
        return isset($this->extensions[$extension])
            ? $this->extensions[$extension]
            : null;
    }

    /**
     * @param array $extensions
     *
     * @return LinkHeader
     */
    private function setExtensions($extensions)
    {
        $this->extensions = $extensions;
        return $this;
    }

    /**
     * @return array
     */
    public function getUrlParameters()
    {
        return $this->urlParameters;
    }

    /**
     * @param array<string, mixed> $urlParameters
     *
     * @return LinkHeader
     */
    public function setUrlParameters(array $urlParameters)
    {
        $this->urlParameters = $urlParameters;
        return $this;
    }

    /**
     * @return object
     */
    public function getResource()
    {
        return $this->resource;
    }

    /**
     * @param object $resource
     *
     * @return LinkHeader
     */
    public function setResource($resource)
    {
        $this->resource = $resource;
        return $this;
    }
}