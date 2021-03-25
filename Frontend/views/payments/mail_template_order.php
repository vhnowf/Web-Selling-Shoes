<div class="wrap">
    <h2>Cảm ơn bạn đã đặt hàng, <b> <?php echo $fullname ?></b></h2>
    <p>
        Mã đơn hàng của bạn: <b>#<?php echo $order_id ?> </b>
    </p>
    <p>
        Số tiền cần thanh toán: <b><?php echo $price_total ?> VND</b>
    </p>
    <div>
        <p>
            - Để thanh toán đơn hàng, bạn hãy chuyển khoản theo thông tin sau:
            <br>
            <b>
                BIDV IT PLUS <br>
                112121212121111111 <br>
                Chi nhành Hà Nội <br>
            </b>
            Nội dung chuyển khoản: Thanh toán đơn hàng #<?php echo $order_id ?> </p>
        <p>
            - Hoặc bạn có thể liên hệ trực tiếp với chúng tôi qua số điện thoại:
            <a href="tel:0879123123">0987599921</a>
        </p>
    </div>
    <h4>Thông tin người mua hàng</h4>
    <table border="1" cellpadding="8" cellspacing="0">
        <tbody>
        <tr>
            <th>Họ tên</th>
            <th>Số điện thoại</th>
            <th>Email</th>
            <th>Địa chỉ</th>
        </tr>
        <tr>
            <td><?php echo $fullname ?></td>
            <td><?php echo $mobile  ?> </td>
            <td><?php echo $email  ?></td>
            <td><?php echo $address  ?></td>
        </tr>
        </tbody>
    </table>
    <br>
    <h4>Thông tin đơn hàng</h4>
    <table border="1" cellpadding="8" cellspacing="0">
        <tbody>
        <tr>
            <th width="40%">Tên sản phẩm</th>
            <th width="12%">Số lượng</th>
            <th>Giá</th>
            <th>Thành tiền</th>
        </tr>
        <?php foreach ($cart as $product): ?>
        <tr>

            <td>
                <a href="" class="content-product-a">
<!--                    <img class="product-avatar img-responsive" src="http://localhost/1596790118-product-logo3.png"-->
<!--                         height="80">-->
                    <span class="content-product">
                                                    <?php echo $product['name'] ?>                                                                                </span>
                </a>
            </td>
            <td>
               <?php echo $product['quantity'] ?>
            </td>
            <td>
                <?php echo $product['current_price'] ?>

            </td>
            <td>
                <?php echo $product['quantity'] *  $product['current_price'] ?>VND

            </td>

        </tr>
        <?php endforeach; ?>
        <tr>
            <td colspan="5" style="text-align: right">
                Tổng giá trị đơn hàng:
                <span class="product-price">
                      <?php echo  $price_total ?>
                      VND
                    </span>
            </td>
        </tr>
        </tbody>
    </table>
</div>