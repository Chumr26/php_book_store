<?php

/**
 * PublisherController
 * Handles publisher detail viewing
 */

require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../Model/Publishers.php';
require_once __DIR__ . '/../Model/Books.php';
require_once __DIR__ . '/../Model/Pagination.php';
require_once __DIR__ . '/helpers/SessionHelper.php';

class PublisherController extends BaseController
{
    private $publishersModel;
    private $booksModel;

    public function __construct($conn)
    {
        parent::__construct($conn);
        $this->publishersModel = new Publishers($conn);
        $this->booksModel = new Books($conn);
    }

    /**
     * Show publisher detail
     * @param int $id Publisher ID
     */
    public function detail($id)
    {
        try {
            SessionHelper::start();

            // Validate ID
            $id = (int)$id;
            if ($id <= 0) {
                SessionHelper::setFlash('error', 'ID nhà xuất bản không hợp lệ.');
                header('Location: index.php?page=books');
                exit;
            }

            // Get publisher details
            $publisher = $this->publishersModel->getPublisherById($id);

            if (!$publisher) {
                SessionHelper::setFlash('error', 'Không tìm thấy nhà xuất bản.');
                header('Location: index.php?page=books');
                exit;
            }

            // Pagination for books
            $page = isset($_GET['p']) ? (int)$_GET['p'] : 1;
            $limit = 12;

            // Get books by publisher
            $totalBooks = $this->booksModel->countBooksByPublisher($id);
            $books = $this->booksModel->getBooksByPublisher($id, $page, $limit);

            // Initialize pagination
            $pagination = new Pagination($totalBooks, $limit, $page);

            return [
                'publisher' => $publisher,
                'books' => $books,
                'total_books' => $totalBooks,
                'pagination' => $pagination,
                'page_title' => 'Nhà xuất bản: ' . ($publisher['ten_nxb'] ?? 'Chi tiết')
            ];
        } catch (Exception $e) {
            error_log("PublisherController::detail Error: " . $e->getMessage());
            SessionHelper::setFlash('error', 'Có lỗi xảy ra khi tải thông tin nhà xuất bản.');
            header('Location: index.php?page=books');
            exit;
        }
    }
}
