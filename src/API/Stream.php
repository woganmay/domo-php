<?php

namespace WoganMay\DomoPHP\API;

/**
 * DomoPHP Streams
 *
 * Utility methods for working with the Streams API
 *
 * @author     Wogan May <wogan.may@gmail.com>
 * @link       https://github.com/woganmay/domo-php
 */
class Stream
{
    private $Client = null;

    /**
     * oAuth Client ID.
     *
     * The Client ID obtained from developer.domo.com
     *
     * @param \WoganMay\DomoPHP\Client $APIClient An instance of the API Client
     */
    public function __construct(\WoganMay\DomoPHP\Client $APIClient)
    {
        $this->Client = $APIClient;
    }

    /**
     * @param int $stream_id The Stream ID to get
     * @return object
     */
    public function getStream($stream_id)
    {
        return $this->Client->getJSON("/v1/streams/$stream_id?fields=all");
    }

    /**
     * @param string $name The name of the stream to create
     * @param array $columns The columns in the schema
     * @param string $updateMethod The update method to use ("REPLACE" or "APPEND")
     * @param string $description The description of the dataset
     * @return object
     */
    public function createStream($name, $columns, $updateMethod = "APPEND", $description = "")
    {
        $body = [
            "dataSet" => [
                'name'        => $name,
                'description' => $description,
                'schema'      => [
                    'columns' => $columns,
                ]
            ],
            "updateMethod" => $updateMethod
        ];

        return $this->Client->postJSON("/v1/streams", $body);
    }

    /**
     * @param int $stream_id The Stream ID to update
     * @param string $updateMethod The new update method ("REPLACE" or "APPEND")
     * @return object
     */
    public function updateStream($stream_id, $updateMethod)
    {
        return $this->Client->putJSON("/v1/streams/$stream_id", [
            'updateMethod' => $updateMethod
        ]);
    }

    /**
     * @param int $stream_id The Stream ID to delete
     * @return bool
     */
    public function deleteStream($stream_id)
    {
        return $this->Client->delete("/v1/streams/$stream_id");
    }

    /**
     * @param int $limit The number of streams to get
     * @param int $offset The offset to apply
     * @param string $sort Field name to sort by
     * @return object
     */
    public function getList($limit = 10, $offset = 0, $sort = 'name')
    {
        return $this->Client->getJSON("/v1/streams?sort=$sort&offset=$offset&limit=$limit");
    }

    /**
     * @param int $stream_id The Stream ID to get an execution from
     * @param int $execution_id The Execution ID to retrieve
     * @return object
     */
    public function getStreamExecution($stream_id, $execution_id)
    {
        return $this->Client->getJSON("/v1/streams/$stream_id/executions/$execution_id");
    }

    /**
     * @param int $stream_id The Stream ID to create a new execution on
     * @return object
     */
    public function createStreamExecution($stream_id)
    {
        return $this->Client->postJSON("/v1/streams/$stream_id/executions");
    }

    /**
     * @param int $stream_id The Stream ID to get executions of
     * @param int $limit The number of executions to get
     * @param int $offset The offset to apply
     * @return object
     */
    public function listStreamExecutions($stream_id, $limit = 10, $offset = 0)
    {
        return $this->Client->getJSON("/v1/streams/$stream_id/executions?offset=$offset&limit=$limit");
    }

    /**
     * @param int $stream_id The Stream ID to upload to
     * @param int $execution_id The Execution ID to upload to
     * @param int $part_id The sequential part number for this execution to stream
     * @param string $data The CSV data part to upload
     * @return mixed
     * @throws \Exception
     */
    public function uploadData($stream_id, $execution_id, $part_id, $data)
    {
        return $this->Client->putCSV("/v1/streams/$stream_id/executions/$execution_id/part/$part_id", $data);
    }

    /**
     * @param int $stream_id The Stream ID to commit
     * @param int $execution_id The Execution ID to commit
     * @return object
     */
    public function commitStreamExecution($stream_id, $execution_id)
    {
        return $this->Client->putJSON("/v1/streams/$stream_id/executions/$execution_id/commit");
    }

    /**
     * @param integer $stream_id The Stream ID to abort
     * @param integer $execution_id The Execution ID to abort
     * @return object
     */
    public function abortStreamExecution($stream_id, $execution_id)
    {
        return $this->Client->putJSON("/v1/streams/$stream_id/executions/$execution_id/abort");
    }


}