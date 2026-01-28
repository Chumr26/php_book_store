<?php

declare(strict_types=1);

require_once __DIR__ . '/../Model/connect.php';
require_once __DIR__ . '/../Model/Books.php';
require_once __DIR__ . '/../Model/VectorSearch.php';

function buildEmbeddingInput(array $book): string
{
    $name = trim((string)($book['ten_sach'] ?? ''));
    $description = trim((string)($book['mo_ta'] ?? ''));
    $category = trim((string)($book['category_name'] ?? ''));
    $author = trim((string)($book['author_name'] ?? ''));
    $publisher = trim((string)($book['publisher_name'] ?? ''));

    $parts = [
        'Tên sách: ' . $name,
        'Mô tả: ' . $description,
        'Danh mục: ' . $category,
        'Tác giả: ' . $author,
        'Nhà xuất bản: ' . $publisher
    ];

    return implode("\n", $parts);
}

try {
    $booksModel = new Books($conn);
    $vectorSearch = new VectorSearch();

    if (!$vectorSearch->validateVectorIndex()) {
        throw new RuntimeException('Atlas vector index book_vector_index not found on collection cdphp.book.');
    }

    $books = $booksModel->getBooksForVectorIndex();
    if (empty($books)) {
        echo "No books found for indexing.\n";
        exit(0);
    }

    echo "Clearing Mongo collection before reindex...\n";
    $vectorSearch->clearCollection();

    $batchSize = 20;
    $batch = [];
    $processed = 0;

    foreach ($books as $book) {
        $embeddingInput = buildEmbeddingInput($book);
        $embedding = $vectorSearch->embedText($embeddingInput);

        $doc = [
            'product_id' => (int)$book['id_sach'],
            'name' => $book['ten_sach'] ?? '',
            'description' => $book['mo_ta'] ?? '',
            'category' => $book['category_name'] ?? '',
            'author' => $book['author_name'] ?? '',
            'publisher' => $book['publisher_name'] ?? '',
            'vector_embedding' => $embedding
        ];

        $batch[] = $doc;
        $processed++;

        if (count($batch) >= $batchSize) {
            $vectorSearch->upsertDocuments($batch);
            $batch = [];
            echo "Indexed {$processed}/" . count($books) . " books...\n";
        }
    }

    if (!empty($batch)) {
        $vectorSearch->upsertDocuments($batch);
    }

    echo "Reindex completed. Total indexed: {$processed}.\n";
    exit(0);
} catch (Exception $e) {
    fwrite(STDERR, "Reindex failed: " . $e->getMessage() . "\n");
    exit(1);
}
