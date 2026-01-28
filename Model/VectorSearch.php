<?php

/**
 * VectorSearch Service
 *
 * Provides embedding generation (Gemini) and MongoDB Atlas Vector Search access.
 */
class VectorSearch
{
    private $mongoManager;
    private $mongoDb;
    private $mongoCollection;
    private $mongoIndex;
    private $geminiApiKey;
    private $geminiModel;
    private $geminiBaseUrl;
    private $timeoutSeconds;

    public function __construct($config = [])
    {
        $baseConfig = $this->loadConfig();
        if (is_array($config) && !empty($config)) {
            $baseConfig = array_merge($baseConfig, $config);
        }

        $this->mongoDb = $baseConfig['mongo_db'] ?? '';
        $this->mongoCollection = $baseConfig['mongo_collection'] ?? '';
        $this->mongoIndex = $baseConfig['mongo_vector_index'] ?? '';
        $this->geminiApiKey = $baseConfig['gemini_api_key'] ?? '';
        $this->geminiModel = $baseConfig['gemini_model'] ?? 'gemini-embedding-001';
        $this->geminiBaseUrl = rtrim($baseConfig['gemini_base_url'] ?? 'https://generativelanguage.googleapis.com/v1beta/models', '/');
        $this->timeoutSeconds = (int)($baseConfig['timeout_seconds'] ?? 30);

        if (empty($baseConfig['mongo_uri'])) {
            throw new RuntimeException('Missing MongoDB URI in config/vector_search.local.php.');
        }

        if (empty($this->mongoDb) || empty($this->mongoCollection) || empty($this->mongoIndex)) {
            throw new RuntimeException('Invalid MongoDB configuration in config/vector_search.local.php.');
        }

        if (empty($this->geminiApiKey)) {
            throw new RuntimeException('Missing gemini_api_key in config/vector_search.local.php.');
        }

        if (!class_exists('MongoDB\\Driver\\Manager')) {
            throw new RuntimeException('MongoDB PHP extension is not installed.');
        }

        $this->mongoManager = new MongoDB\Driver\Manager($baseConfig['mongo_uri']);
    }

    /**
     * Load configuration from local config file (required)
     *
     * @return array
     */
    private function loadConfig()
    {
        $localConfigFile = __DIR__ . '/../config/vector_search.local.php';

        $localFileConfig = [];
        if (file_exists($localConfigFile)) {
            $localFileConfig = include $localConfigFile;
            if (!is_array($localFileConfig)) {
                throw new RuntimeException('Invalid vector search config in config/vector_search.local.php (expected a PHP array).');
            }
        }

        if (empty($localFileConfig)) {
            throw new RuntimeException('Missing config/vector_search.local.php.');
        }

        return $localFileConfig;
    }

    private function logMessage($message)
    {
        error_log($message);

        $logDir = __DIR__ . '/../tmp/logs';
        if (!is_dir($logDir)) {
            @mkdir($logDir, 0775, true);
        }

        $logFile = $logDir . '/vector_search.log';
        $line = '[' . date('Y-m-d H:i:s') . '] ' . $message . PHP_EOL;
        @file_put_contents($logFile, $line, FILE_APPEND);
    }

    /**
     * Validate Atlas Vector Search index exists
     *
     * @return bool
     */
    public function validateVectorIndex()
    {
        try {
            $command = new MongoDB\Driver\Command([
                'listSearchIndexes' => $this->mongoCollection
            ]);

            $cursor = $this->mongoManager->executeCommand($this->mongoDb, $command);
            foreach ($cursor as $index) {
                if (($index->name ?? '') === $this->mongoIndex) {
                    return true;
                }
            }

            return false;
        } catch (Exception $e) {
            $this->logMessage('Vector index validation failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Clear collection before reindexing
     */
    public function clearCollection()
    {
        $bulk = new MongoDB\Driver\BulkWrite();
        $bulk->delete([], ['limit' => 0]);
        $this->mongoManager->executeBulkWrite($this->mongoDb . '.' . $this->mongoCollection, $bulk);
    }

    /**
     * Upsert documents into MongoDB
     *
     * @param array $documents
     */
    public function upsertDocuments(array $documents)
    {
        if (empty($documents)) {
            return;
        }

        $bulk = new MongoDB\Driver\BulkWrite();
        foreach ($documents as $doc) {
            if (!isset($doc['product_id'])) {
                continue;
            }

            $bulk->update(
                ['product_id' => $doc['product_id']],
                ['$set' => $doc],
                ['upsert' => true]
            );
        }

        $this->mongoManager->executeBulkWrite($this->mongoDb . '.' . $this->mongoCollection, $bulk);
    }

    /**
     * Generate embedding using Gemini API
     *
     * @param string $text
     * @return array
     */
    public function embedText($text)
    {
        $url = $this->geminiBaseUrl . '/' . $this->geminiModel . ':embedContent?key=' . urlencode($this->geminiApiKey);

        $payload = json_encode([
            'content' => [
                'parts' => [
                    ['text' => $text]
                ]
            ]
        ]);

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json'
        ]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, $this->timeoutSeconds);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        if ($response === false || $httpCode >= 400) {
            $this->logMessage('Gemini embedding request failed: ' . ($curlError ?: $response));
            throw new RuntimeException('Gemini embedding request failed.');
        }

        $data = json_decode($response, true);
        if (!isset($data['embedding']['values']) || !is_array($data['embedding']['values'])) {
            $this->logMessage('Gemini embedding response missing values: ' . $response);
            throw new RuntimeException('Gemini embedding response missing values.');
        }

        return $data['embedding']['values'];
    }

    /**
     * Semantic search and return ordered product IDs
     *
     * @param string $query
     * @param int $limit
     * @return int[]
     */
    public function searchProductIds($query, $limit = 10)
    {
        $limit = max(1, (int)$limit);
        $embedding = $this->embedText($query);

        $pipeline = [
            [
                '$vectorSearch' => [
                    'index' => $this->mongoIndex,
                    'path' => 'vector_embedding',
                    'queryVector' => $embedding,
                    'numCandidates' => max(100, $limit * 20),
                    'limit' => $limit
                ]
            ],
            [
                '$project' => [
                    '_id' => 0,
                    'product_id' => 1
                ]
            ]
        ];

        $command = new MongoDB\Driver\Command([
            'aggregate' => $this->mongoCollection,
            'pipeline' => $pipeline,
            'cursor' => new stdClass()
        ]);

        $cursor = $this->mongoManager->executeCommand($this->mongoDb, $command);
        $ids = [];
        foreach ($cursor as $doc) {
            if (isset($doc->product_id)) {
                $ids[] = (int)$doc->product_id;
            }
        }

        return $ids;
    }
}
