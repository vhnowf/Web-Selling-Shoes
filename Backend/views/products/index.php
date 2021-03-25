<?php
require_once 'helpers/Helper.php';
?>
<!--form search-->
<form action="" method="GET">
    <div class="form-group">
        <label for="title">Nhập title</label>
        <input type="text" name="title" value="<?php echo isset($_GET['title']) ? $_GET['title'] : '' ?>" id="title"
               class="form-control"/>
    </div>
    <div class="form-group">
        <label for="title">Chọn danh mục</label>
        <select name="category_id" class="form-control">
            <?php foreach ($categories as $category):
                $selected = '';
                if (isset($_GET['category_id']) && $category['id'] == $_GET['category_id']) {
                    $selected = 'selected';
                }
                ?>
                <option value="<?php echo $category['id'] ?>" <?php echo $selected; ?>>
                    <?php echo $category['name'] ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="form-group">
        <label for="title">Sắp xếp</label>
        <select name="orderBy" class="form-control">
            <option value="" >Chọn </option>
            <option value="price-asc" <?php if (isset($_GET['orderBy']) && $_GET['orderBy'] === 'price-asc') echo "selected";?>>Giá tăng dần</option>
            <option value="price-desc" <?php if (isset($_GET['orderBy']) && $_GET['orderBy'] === 'price-desc') echo "selected";?>>Giá giảm dần</option>
            <option value="alpha-asc"  <?php if (isset($_GET['orderBy']) && $_GET['orderBy'] === 'alpha-asc') echo "selected";?>>Từ A-Z</option>
            <option value="alpha-desc" <?php if (isset($_GET['orderBy']) && $_GET['orderBy'] === 'alpha-desc') echo "selected";?>>Từ Z-A</option>
        </select>
    </div>
    <div class="form-group">
        <label for="title">Lọc theo giá</label>
        <select name="price" class="form-control">
            <option value="" >Chọn</option>
            <option value="<1tr" <?php if (isset($_GET['price']) && $_GET['price'] === '<1tr') echo "selected";?>><1,000,000</option>
            <option value="1tr-1.5tr" <?php if (isset($_GET['price']) && $_GET['price'] === '1tr-1.5tr') echo "selected";?>>1,000,000-1,500,000</option>
            <option value="1.5tr-2tr"  <?php if (isset($_GET['price']) && $_GET['price'] === '1.5tr-2tr') echo "selected";?>>1,500,000-2,000,000</option>
            <option value=">2tr" <?php if (isset($_GET['price']) && $_GET['price'] === '>2tr') echo "selected";?>>>2,000,000</option>
        </select>
    </div>
    <input type="hidden" name="controller" value="product"/>
    <input type="hidden" name="action" value="index"/>
    <input type="submit" name="search" value="Tìm kiếm" class="btn btn-primary"/>
    <a href="index.php?controller=product" class="btn btn-default">Xóa filter</a>
</form>


<h2>Danh sách sản phẩm</h2>
    <a href="index.php?controller=product&action=create" class="btn btn-success">
        <i class="fa fa-plus"></i> Thêm mới
    </a>
<table class="table table-bordered">
    <tr>
        <th>ID</th>
        <th>Category name</th>
        <th>Title</th>
        <th>Avatar</th>
        <th>Price</th>
        <th>Amount</th>
        <th>Status</th>
        <th>Created_at</th>
        <th>Updated_at</th>
        <th></th>
    </tr>
    <?php if (!empty($products)): ?>
        <?php foreach ($products as $product): ?>
            <tr>
                <td><?php echo $product['id'] ?></td>
                <td><?php echo $product['category_name'] ?></td>
                <td><?php echo $product['title'] ?></td>
                <td>
                    <?php if (!empty($product['avatar'])): ?>
                        <img height="80" src="assets/uploads/<?php echo $product['avatar'] ?>"/>
                    <?php endif; ?>
                </td>
                <td><?php echo number_format($product['current_price']) ?></td>
                <td><?php echo $product['amount'] ?></td>
                <td><?php echo Helper::getStatusText($product['status']) ?></td>
                <td><?php echo date('d-m-Y H:i:s', strtotime($product['created_at'])) ?></td>
                <td><?php echo !empty($product['updated_at']) ? date('d-m-Y H:i:s', strtotime($product['updated_at'])) : '--' ?></td>
                <td>
                    <?php
                    $url_detail = "index.php?controller=product&action=detail&id=" . $product['id'];
                    $url_update = "index.php?controller=product&action=update&id=" . $product['id'];
                    $url_delete = "index.php?controller=product&action=delete&id=" . $product['id'];
                    ?>
                    <a title="Chi tiết" href="<?php echo $url_detail ?>"><i class="fa fa-eye"></i></a> &nbsp;&nbsp;
                    <a title="Update" href="<?php echo $url_update ?>"><i class="fa fa-pencil-alt"></i></a> &nbsp;&nbsp;
                    <a title="Xóa" href="<?php echo $url_delete ?>" onclick="return confirm('Are you sure delete?')"><i class="fa fa-trash"></i></a>
                </td>
            </tr>
        <?php endforeach; ?>

    <?php else: ?>
        <tr>
            <td colspan="9">No data found</td>
        </tr>
    <?php endif; ?>
</table>
<?php echo $pages; ?>