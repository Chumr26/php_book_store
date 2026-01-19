<?php

/**
 * AuthorController
 * Handles author detail viewing
 */

require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../Model/Authors.php';
require_once __DIR__ . '/../Model/Books.php';
require_once __DIR__ . '/../Model/Pagination.php';
require_once __DIR__ . '/helpers/SessionHelper.php';

class AuthorController extends BaseController
{
    private $authorsModel;
    private $booksModel;

    public function __construct($conn)
    {
        parent::__construct($conn);
        $this->authorsModel = new Authors($conn);
        $this->booksModel = new Books($conn);
    }

    /**
     * Show author detail
     * @param int $id Author ID
     */
    public function detail($id)
    {
        try {
            SessionHelper::start();

            // Validate ID
            $id = (int)$id;
            if ($id <= 0) {
                SessionHelper::setFlash('error', 'ID tác giả không hợp lệ.');
                header('Location: index.php?page=books');
                exit;
            }

            // Get author details
            $author = $this->authorsModel->getAuthorById($id);

            if (!$author) {
                SessionHelper::setFlash('error', 'Không tìm thấy tác giả.');
                header('Location: index.php?page=books');
                exit;
            }

            // Pagination for books
            $page = isset($_GET['p']) ? (int)$_GET['p'] : 1;
            $limit = 12;

            // Get books by author
            $totalBooks = $this->booksModel->countBooksByAuthor($id);
            $books = $this->booksModel->getBooksByAuthor($id, $page, $limit);

            // Initialize pagination
            $pagination = new Pagination($totalBooks, $limit, $page);

            return [
                'author' => $author,
                'books' => $books,
                'total_books' => $totalBooks,
                'pagination' => $pagination,
                'page_title' => 'Tác giả: ' . ($author['ten_tac_gia'] ?? 'Chi tiết')
            ];
        } catch (Exception $e) {
            error_log("AuthorController::detail Error: " . $e->getMessage());
            SessionHelper::setFlash('error', 'Có lỗi xảy ra khi tải thông tin tác giả.');
            header('Location: index.php?page=books');
            exit;
        }
    }
}
