<div class="container-fluid">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Thêm nhà xuất bản</h1>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Thông tin nhà xuất bản</h6>
                </div>
                <div class="card-body">
                    <form action="index.php?page=publisher_create" method="POST">
                        <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">

                        <div class="form-group">
                            <label for="ten_nxb">Tên nhà xuất bản <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="ten_nxb" name="ten_nxb" required>
                        </div>

                        <div class="form-group row">
                            <div class="col-md-6">
                                <label for="dien_thoai">Điện thoại</label>
                                <input type="text" class="form-control" id="dien_thoai" name="dien_thoai">
                            </div>
                            <div class="col-md-6">
                                <label for="email">Email</label>
                                <input type="email" class="form-control" id="email" name="email">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="website">Website</label>
                            <input type="url" class="form-control" id="website" name="website" placeholder="https://...">
                        </div>

                        <div class="form-group">
                            <label for="dia_chi">Địa chỉ</label>
                            <textarea class="form-control" id="dia_chi" name="dia_chi" rows="3"></textarea>
                        </div>

                        <div class="d-flex justify-content-end">
                            <a href="index.php?page=publishers" class="btn btn-secondary mr-2">Hủy</a>
                            <button type="submit" class="btn btn-primary">Lưu NXB</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>