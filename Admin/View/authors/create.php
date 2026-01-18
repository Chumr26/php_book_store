<div class="container-fluid">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Thêm tác giả mới</h1>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Thông tin tác giả</h6>
                </div>
                <div class="card-body">
                    <form action="index.php?page=author_create" method="POST">
                        <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">

                        <div class="form-group">
                            <label for="ten_tacgia">Tên tác giả <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="ten_tacgia" name="ten_tacgia" required>
                        </div>

                        <div class="form-group row">
                            <div class="col-md-6">
                                <label for="but_danh">Bút danh</label>
                                <input type="text" class="form-control" id="but_danh" name="but_danh">
                            </div>
                            <div class="col-md-6">
                                <label for="quoc_tich">Quốc tịch</label>
                                <input type="text" class="form-control" id="quoc_tich" name="quoc_tich">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="ngay_sinh">Ngày sinh</label>
                            <input type="date" class="form-control" id="ngay_sinh" name="ngay_sinh">
                        </div>

                        <div class="form-group">
                            <label for="tieu_su">Tiểu sử</label>
                            <textarea class="form-control" id="tieu_su" name="tieu_su" rows="5"></textarea>
                        </div>

                        <div class="d-flex justify-content-end">
                            <a href="index.php?page=authors" class="btn btn-secondary mr-2">Hủy</a>
                            <button type="submit" class="btn btn-primary">Lưu tác giả</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>