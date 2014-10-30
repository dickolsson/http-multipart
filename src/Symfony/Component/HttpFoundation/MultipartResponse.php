<?php

namespace Symfony\Component\HttpFoundation;

class MultipartResponse extends Response
{
    /**
     * @var string
     */
    public $subtype;

    /**
     * @var string
     */
    public $boundary;

    /**
     * @var Response[]
     */
    protected $parts;

    /**
     * Constructor.
     */
    public function __construct($subtype = null, $status = 200, $headers = array())
    {
        parent::__construct(null, $status, $headers);

        $this->subtype = $subtype ?: 'mixed';
        $this->boundary = md5(microtime());
    }

    /**
     * {@inheritdoc}
     */
    public function prepare(Request $request)
    {
        $this->headers->set('Content-Type', "multipart/{$this->subtype}; boundary=\"{$this->boundary}\"");
        $this->headers->set('Transfer-Encoding', 'chunked');

        return parent::prepare($request);
    }

    /**
     * Sets a part of the multipart response.
     *
     * @param Response $response A response object to be part of the multipart response.
     *
     * @return MultipartResponse
     */
    public function setPart(Response $response)
    {
        $this->parts[] = $response;

        return $this;
    }

    /**
     * Sets multiple parts of the multipart response.
     *
     * @param Response[] $responses A response object to be part of the multipart response.
     *
     * @return MultipartResponse
     */
    public function setParts(array $responses)
    {
        foreach ($responses as $response) {
            $this->setPart($response);
        }
        return $this;
    }

    /**
     * Sends content for the current web response.
     *
     * @return Response
     */
    public function sendContent()
    {
        foreach ($this->parts as $part) {
            echo "--{$this->boundary}\r\n";
            echo "{$part->headers}\r\n";
            $part->sendContent();
            echo "\r\n";
        }
        echo "--{$this->boundary}--";

        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * @throws \LogicException when the content is not null
     */
    public function setContent($content)
    {
        if (null !== $content) {
            throw new \LogicException('The content cannot be set on a MultipartResponse instance.');
        }
    }

    /**
     * {@inheritdoc}
     *
     * @return false
     */
    public function getContent()
    {
        return false;
    }
}
