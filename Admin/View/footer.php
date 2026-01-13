<footer class="footer mt-auto py-3 bg-light text-center">
    <div class="container">
        <span class="text-muted">Copyright &copy; BookStore <?php echo date('Y'); ?>. All rights reserved.</span>
    </div>
</footer>

<!-- Global Confirmation Modal (Admin) -->
<div class="modal fade" id="globalConfirmModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="globalConfirmTitle">Xác nhận</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p id="globalConfirmMessage">Bạn có chắc chắn muốn thực hiện hành động này?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Hủy</button>
                <button type="button" class="btn btn-danger" id="globalConfirmBtn">Đồng ý</button>
            </div>
        </div>
    </div>
</div>

<!-- Global Message Modal (Admin) -->
<div class="modal fade" id="globalMessageModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="globalMessageTitle">Thông báo</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p id="globalMessageContent"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-dismiss="modal">Đóng</button>
            </div>
        </div>
    </div>
</div>
