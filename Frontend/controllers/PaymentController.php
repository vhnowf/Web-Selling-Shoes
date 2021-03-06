<?php
require_once 'controllers/Controller.php';
require_once 'models/Order.php';
require_once 'models/OrderDetail.php';
require_once 'libraries/PHPMailer/src/PHPMailer.php';
require_once 'libraries/PHPMailer/src/SMTP.php';
require_once 'libraries/PHPMailer/src/Exception.php';

class PaymentController extends Controller
{

    protected function sendMail($email, $body)
    {
        // Instantiation and passing `true` enables exceptions
        $mail = new \PHPMailer\PHPMailer\PHPMailer(true);

        try {
            //Server settings
            $mail->SMTPDebug = \PHPMailer\PHPMailer\SMTP::DEBUG_OFF;                      // Enable verbose debug output
            $mail->isSMTP();
            // Send using SMTP
            //host miễn phí của gmail
            $mail->Host = 'smtp.gmail.com';                    // Set the SMTP server to send through
            $mail->SMTPAuth = true;                                   // Enable SMTP authentication
            //username gmail của chính bạn
            $mail->Username = 'vohoangnamntm@gmail.com';                     // SMTP username
            //password cho ứng dụng, ko phải password của tài khoảng
//    đăng nhập gmail
//    tạo mật khẩu ứng dụng tại link:
// https://myaccount.google.com/ - menu Bảo mật
            $mail->Password = 'eukqfxdeqhktkznk';                               // SMTP password
            $mail->SMTPSecure = \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;         // Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` also accepted
            $mail->Port = 587;                                    // TCP port to connect to

            //Recipients
            $mail->setFrom('abc@gmail.com', 'ABC');
            //setting mail người gửi
            $mail->addAddress($email, 'Skytheme');     // Add a recipient
//    $mail->addAddress('ellen@example.com');               // Name is optional
//    $mail->addReplyTo('info@example.com', 'Information');
//    $mail->addCC('cc@example.com');
//    $mail->addBCC('bcc@example.com');

            // Attachments
//      $mail->addAttachment('rose.jpeg');         // Add attachments
//    $mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name

            // Content
            $mail->isHTML(true);                                  // Set email format to HTML
            $mail->Subject = 'Confirm your bill';
            $mail->Body = $body;
//    $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

            $mail->send();
            echo 'Message has been sent';
        } catch (Exception $e) {
            echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }
    }
    public function index()
    {
        //nếu giỏ hàng trống thì ko cho phép truy cập trang này
        if (!isset($_SESSION['cart'])) {
            $_SESSION['error'] = 'Bạn chưa có sản phẩm nào trong giỏ hàng';
            header("Location: index.php");
            exit();
        }
        if (isset($_POST['submit'])) {
            $fullname = $_POST['fullname'];
            $address = $_POST['address'];
            $address .= ' ' . $_POST['district'];
            $address .= ' ' . $_POST['province'];
            $mobile = $_POST['mobile'];
            $email = $_POST['email'];
            $note = $_POST['note'];
            $method = $_POST['method'];
            if (empty($fullname) || empty($address) || empty($mobile)) {
                $this->error = 'Fullname, address, mobile ko đc để trống';
            }

            if (empty($this->error)) {
                $order_model = new Order();
                $order_model->fullname = $fullname;
                $order_model->address = $address;
                $order_model->mobile = $mobile;
                $order_model->email = $email;
                $order_model->note = $note;
                $price_total = 0;
                foreach ($_SESSION['cart'] as $cart) {
                    $price_total += $cart['quantity'] * $cart['current_price'];
                }
                $order_model->price_total = $price_total;
                //mặc định là chưa thanh toán
                $order_model->payment_status = 0;
                //lưu vào bảng orders
                $order_id = $order_model->insertOrder();
                if ($order_id > 0) {
                    //lưu vào bảng order_details
                    $order_detail = new OrderDetail();
                    $order_detail->order_id = $order_id;
                    foreach ($_SESSION['cart'] AS $product_id => $cart) {
                        $order_detail->product_id = $product_id;
                        $order_detail->quantity = $cart['quantity'];
                        $order_detail->insert();
                    }

                    //xóa thông tin giỏ hàng đi
//          unset($_SESSION['cart']);
                    //trường hợp chọn phương thức thanh toán là COD thì chuyển tới trang cảm ơn
                    if ($method == 1) {
                        $order_model->id = $order_id;
                        //lấy nội dung mail từ template có sẵn
                        $body = $this->render('views/payments/mail_template_order.php', ['order' => $order_model]);
                        // echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
                        // print_r($body);
                        // echo "</pre>";
                        // die;
//gửi mail xác nhận đã thanh toán
                        unset($_SESSION['cart']);
                        unset($SESSION['totalQuantity']);
                            unset($_SESSION['totalItemPrice']);
                            unset($_SESSION['totalPrice']);
                        $this->sendMail($email, $body);
                        $url_redirect = $_SERVER['SCRIPT_NAME'] . '/cam-on.html';
                        header("Location: $url_redirect");
                        exit();
                    }
                    //trường hợp ngược lại là thanh toán trực tuyến, thì cần lưu thông tin order và chuyển hướng đến trang thanh toán trực tuyến
                    //lưu thông tin thanh toán vào session để tới trang thanh toán
                    $order = $order_model->getOrder($order_id);
                    //xóa thông tin giỏ hàng
                    unset($_SESSION['cart']);
                    $_SESSION['order'] = $order;
                    $_SESSION['success'] = 'Lưu thông tin thanh toán thành công';
                    //chuyển hướng sang màn hình chọn phương thức thanh toán
                    $url_redirect = $_SERVER['SCRIPT_NAME'] . '/phuong-thuc-thanh-toan.html';
                    header("Location: $url_redirect");
                    exit();
                } else {
                    $this->error = 'Lưu thông tin thanh toán thất bại';
                }
            }
        }

        $this->content = $this->render('views/payments/index.php');
        require_once 'views/layouts/main.php';
    }
    public function thank()
    {
        //xóa thông tin giỏ hàng đi
        unset($_SESSION['cart']);
        $this->content = $this->render('views/payments/thank.php');
        require_once 'views/layouts/main.php';
    }

    public function payment()
    {

        $this->content = $this->render('libraries/nganluong/index.php');

        require_once 'views/layouts/main.php';
    }
    public function online(){
        $this->content = $this->render('libraries/nganluong/index.php');
        echo $this->content;
    }
    public function order(){
        if (!isset($_SESSION['cart'])) {
            $_SESSION['error'] = 'Bạn chưa có sản phẩm nào trong giỏ hàng';
            header("Location: index.php");
            exit();
        }
        if(isset($_POST['submit'])){
            $fullname = $_POST['fullname'];
            $address = $_POST['address'];
            $address .= ' ' . $_POST['district'];
            $address .= ' ' . $_POST['province'];
            $mobile = $_POST['mobile'];
            $email = $_POST['email'];
            $note = $_POST['note'];
            $method = $_POST['method'];
            if(empty($fullname) || empty($address) || empty($mobile)){
                $this->error = "Phải nhập đầy đủ";
            }else if (strlen($fullname) < 3){
                $this->error = "Phải nhập tên có ít nhất 3 ký tự";
            }else if (!filter_var($email, FILTER_VALIDATE_EMAIL)){
                $this->error = 'Email chưa đúng định dang';
            }
            if (empty($this->error)){
                $order_model = New Order();
                $order_model->user_id = NULL;
                $order_model->fullname = $fullname;
                $order_model->email   = $email;
                $order_model->note  = $note;
                $order_model->address  = $address;
                $order_model->mobile = $mobile;
                $price_total = 0;
                foreach($_SESSION['cart'] AS $cart){
                    $total_item = $cart['quantity'] * $cart['current_price'];
                    $price_total += $total_item;
                }
                $order_model->price_total  = $price_total;
                $order_model->payment_status = 0;
                $order_id = $order_model->insertOrder();
                var_dump($order_id);
                $order_detail_model = new OrderDetail();
                $order_detail_model->order_id  = $order_id;
                foreach($_SESSION['cart'] AS $product_id => $cart){
                    $order_detail_model->product_id = $product_id;
                    $order_detail_model->quantity = $cart['quantity'];
                    $order_detail_model->price = $cart['current_price'];
                    $is_insert = $order_detail_model->insert();
                    var_dump($is_insert);
                }
                if($method == 1){
                    $_SESSION['payment'] = [
                        'price_total' => $price_total,
                        'fullname' => $fullname,
                        'email' => $email,
                        'mobile'=> $mobile,

                    ];
                    header('Location:index.php?controller=payment&action=online');
                    exit();
                } else {

                    $body = $this->render('views/payments/mail_template_order.php',[
                        'fullname' => $fullname,
                        'order_id' => $order_id,
                        'email' => $email,
                        'address' => $address,
                        'mobile' => $mobile,
                        'price_total' => $price_total,
                        'cart' => $_SESSION['cart']
                    ]);
                    $this->sendMail($email, $body);
                    header('Location:index.php?controller=payment&action=thank');
                    exit();
                }
            }
        }
        $this->content = $this->render('views/payments/index.php');

        require_once 'views/layouts/main.php';
    }

}